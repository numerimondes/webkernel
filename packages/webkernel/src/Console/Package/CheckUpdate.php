<?php
namespace Webkernel\Console\Package;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Webkernel\constants\Application;

class CheckUpdate extends Command
{
    protected $signature = 'webkernel:update
                            {--local-path= : Custom path to local webkernel directory}
                            {--remote-repo= : Custom remote repository URL}
                            {--revert : Show available backups and revert to selected one}
                            {--automatic : Setup automatic daily updates via cron}';

    protected $description = 'Update Webkernel packages by comparing and replacing with remote repository versions';

    private const DEFAULT_LOCAL_PATH = 'packages/webkernel';
    private const DEFAULT_REMOTE_REPO = 'https://github.com/numerimondes/webkernel';
    private const REMOTE_PACKAGES_PATH = 'packages/webkernel/src/Constants/Application.php';

    public function handle()
    {
        if ($this->option('revert')) {
            return $this->handleRevert();
        }
        if ($this->option('automatic')) {
            return $this->handleAutomatic();
        }

        $this->info('[INFO] Webkernel Updater');
        $this->newLine();

        $remoteRepo = $this->option('remote-repo') ?? self::DEFAULT_REMOTE_REPO;
        $packages = Application::getWebkernelPackages();
        $remotePackages = $this->fetchRemotePackages($remoteRepo);
        $updatesPerformed = false;
        $errors = [];

        // Prioritize core webkernel package
        if (isset($packages['webkernel'])) {
            $result = $this->processPackage('webkernel', $packages['webkernel'], $remotePackages, $remoteRepo);
            if ($result['status'] === self::FAILURE) {
                $errors[] = $result['error'];
            } elseif ($result['updated']) {
                $updatesPerformed = true;
            }
        } else {
            $errors[] = 'Core webkernel package not defined in WEBKERNEL_PACKAGES';
        }

        // Process other packages
        foreach ($packages as $packageName => $package) {
            if ($packageName === 'webkernel') {
                continue; // Already processed
            }
            $result = $this->processPackage($packageName, $package, $remotePackages, $remoteRepo);
            if ($result['status'] === self::FAILURE) {
                $errors[] = $result['error'];
            } elseif ($result['updated']) {
                $updatesPerformed = true;
            }
        }

        if ($errors) {
            $this->warn('[WARNING] Errors encountered:');
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
        }

        if ($updatesPerformed) {
            $this->info('[SUCCESS] Webkernel packages update process completed!');
            $this->newLine();
            $restart = $this->confirm('Do you want to restart?', true);
            if ($restart) {
                $this->info('[INFO] Restarting application...');
                $this->call('config:clear');
                $this->call('cache:clear');
                $this->call('route:clear');
                $this->call('view:clear');
                $this->info('[OK] Application restarted successfully!');
            } else {
                $this->info('[OK]');
            }
        } else {
            $this->info('[SUCCESS] All Webkernel packages are up to date!');
            $this->newLine();
            $restart = $this->confirm('Do you want to restart?', false);
            if ($restart) {
                $this->info('[INFO] Restarting application...');
                $this->call('config:clear');
                $this->call('cache:clear');
                $this->call('route:clear');
                $this->call('view:clear');
                $this->info('[OK] Application restarted successfully!');
            } else {
                $this->info('[OK]');
            }
        }

        return empty($errors) ? self::SUCCESS : self::FAILURE;
    }

    private function fetchRemotePackages(string $remoteRepo): array
    {
        $this->line('[INFO] Fetching remote package list...');
        $url = rtrim($remoteRepo, '/') . '/raw/main/' . self::REMOTE_PACKAGES_PATH;

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ])
                ->get($url);

            if (!$response->successful()) {
                $this->error("[ERROR] Failed to fetch remote Application.php: {$response->status()}");
                return [];
            }

            $content = $response->body();
            $packages = [];

            // Essayer d'abord de parser le tableau WEBKERNEL_PACKAGES
            if (preg_match("/const\s+WEBKERNEL_PACKAGES\s*=\s*\[(.*?)\];/s", $content, $matches)) {
                $packageDefs = $matches[1];

                // Pattern pour extraire chaque package
                $pattern = "/'([^']+)'\s*=>\s*\[\s*'path'\s*=>\s*'([^']+)',\s*'minimum_stable_version_required'\s*=>\s*([^,\]]+)(?:,\s*'dependencies'\s*=>\s*\[[^\]]*\])?\s*\]/";

                if (preg_match_all($pattern, $packageDefs, $packageMatches, PREG_SET_ORDER)) {
                    foreach ($packageMatches as $match) {
                        $version = trim($match[3], "'\"");

                        // Si la version est une constante (comme self::WEBKERNEL_VERSION), on doit l'extraire
                        if (strpos($version, 'self::') !== false) {
                            $constantName = str_replace('self::', '', $version);
                            if (preg_match("/const\s+{$constantName}\s*=\s*['\"]([^'\"]+)['\"];/", $content, $constMatch)) {
                                $version = $constMatch[1];
                            }
                        }

                        $packages[$match[1]] = [
                            'path' => $match[2],
                            'version' => $version
                        ];
                    }
                }
            }

            // Si aucun package trouvé avec la méthode principale, essayer l'approche alternative
            if (empty($packages)) {
                $this->warn('[WARNING] No packages found in remote Application.php');
                $this->line('[DEBUG] Trying alternative parsing...');

                // Définir les packages et leurs versions attendues depuis les constantes
                $packageDefinitions = [
                    'webkernel' => [
                        'path' => 'packages/webkernel',
                        'version_constants' => ['WEBKERNEL_VERSION', 'STABLE_VERSION']
                    ],
                    'webkernel-website-builder' => [
                        'path' => 'packages/webkernel-website-builder',
                        'version_constants' => ['WEBSITE_BUILDER_VERSION']
                    ],
                    'webkernel-video-tools' => [
                        'path' => 'packages/webkernel-video-tools',
                        'version_constants' => ['VIDEO_TOOLS_VERSION']
                    ]
                ];

                // Chercher les constantes de version dans le contenu
                foreach ($packageDefinitions as $packageName => $packageInfo) {
                    foreach ($packageInfo['version_constants'] as $constantName) {
                        $pattern = "/const\s+{$constantName}\s*=\s*['\"]([^'\"]+)['\"];/";
                        if (preg_match($pattern, $content, $matches)) {
                            $packages[$packageName] = [
                                'path' => $packageInfo['path'],
                                'version' => $matches[1]
                            ];
                            break; // Prendre la première version trouvée pour ce package
                        }
                    }
                }

                // Si toujours rien trouvé, essayer de parser les minimum_stable_version_required dans le texte
                if (empty($packages)) {
                    $this->line('[DEBUG] Trying to parse minimum_stable_version_required values...');

                    // Chercher les valeurs de minimum_stable_version_required
                    $versionMatches = [];
                    if (preg_match_all("/'minimum_stable_version_required'\s*=>\s*['\"]([^'\"]+)['\"]/", $content, $versionMatches, PREG_SET_ORDER)) {
                        $versionIndex = 0;
                        foreach (['webkernel', 'webkernel-website-builder', 'webkernel-video-tools'] as $packageName) {
                            if (isset($versionMatches[$versionIndex])) {
                                $packages[$packageName] = [
                                    'path' => "packages/{$packageName}",
                                    'version' => $versionMatches[$versionIndex][1]
                                ];
                                $versionIndex++;
                            }
                        }
                    }
                }
            }

            $this->line("[DEBUG] Found " . count($packages) . " packages in remote repository");
            foreach ($packages as $name => $package) {
                $this->line("  - {$name}: {$package['version']}");
            }

            return $packages;

        } catch (Exception $e) {
            $this->error("[ERROR] Error fetching remote package list: {$e->getMessage()}");
            return [];
        }
    }

    private function processPackage(string $packageName, array $package, array $remotePackages, string $remoteRepo): array
    {
        $localPath = $package['path'];
        $this->line("Processing package: {$packageName}");
        $this->line("Local path: {$localPath}");
        $this->line("Remote repository: {$remoteRepo}");

        if (!isset($remotePackages[$packageName])) {
            $this->warn("[WARNING] Package {$packageName} not found in remote repository");
            return ['status' => self::SUCCESS, 'updated' => false, 'error' => null];
        }

        if (!File::exists(base_path($localPath))) {
            $this->warn("[WARNING] Local directory not found: {$localPath}, will install...");
            $localVersion = '0.0.0';
        } else {
            $localVersion = $this->getLocalVersion($localPath, $package['minimum_stable_version_required']);
            $this->info("Local version: {$localVersion}");
        }

        $remoteVersion = $remotePackages[$packageName]['version'];
        $this->info("Remote version: {$remoteVersion}");

        if ($this->checkIfUpdateNeeded($localVersion, $remoteVersion)) {
            $this->warn("[UPDATE] Update available for {$packageName}!");
            $this->line("Update: {$localVersion} → {$remoteVersion}");
            $this->newLine();
            $this->line('[WARNING] This will:');
            $this->line("  * Create a backup in {$localPath}/.trash/");
            $this->line("  * Replace or install the {$localPath} directory");
            $this->line('  * Download and install the latest version from the repository');
            $this->newLine();
            $confirm = $this->confirm("Do you want to proceed with the update for {$packageName}?", false);
            if (!$confirm) {
                $this->info("Update cancelled for {$packageName}");
                return ['status' => self::SUCCESS, 'updated' => false, 'error' => null];
            }
            $this->newLine();
            $status = $this->performUpdate($localPath, $remoteRepo);
            if ($status === self::FAILURE) {
                return ['status' => self::FAILURE, 'updated' => false, 'error' => "Failed to update {$packageName}"];
            }
            return ['status' => self::SUCCESS, 'updated' => true, 'error' => null];
        }
        $this->info("[SUCCESS] {$packageName} is up to date!");
        return ['status' => self::SUCCESS, 'updated' => false, 'error' => null];
    }

    private function getLocalVersion(string $localPath, string $defaultVersion): string
    {
        $applicationPhpPath = base_path($localPath . '/src/Constants/Application.php');
        $versions = $this->extractVersionsFromFile($applicationPhpPath);
        return $versions['webkernel'] ?? $defaultVersion;
    }

    private function extractVersionsFromFile(string $filePath): ?array
    {
        if (!File::exists($filePath)) {
            return null;
        }
        $content = File::get($filePath);
        return $this->extractVersionsFromContent($content);
    }

    private function extractVersionsFromContent(string $content): ?array
    {
        $webkernelVersion = null;
        $stableVersion = null;
        if (preg_match("/const\s+(?:WEBKERNEL_VERSION|WEBSITE_BUILDER_VERSION|VIDEO_TOOLS_VERSION)\s*=\s*['\"]([^'\"]+)['\"];/", $content, $matches)) {
            $webkernelVersion = $matches[1];
        }
        if (preg_match("/const\s+STABLE_VERSION\s*=\s*['\"]([^'\"]+)['\"];/", $content, $matches)) {
            $stableVersion = $matches[1];
        }
        if ($webkernelVersion && $stableVersion) {
            return [
                'webkernel' => $webkernelVersion,
                'stable' => $stableVersion
            ];
        }
        return null;
    }

    private function compareVersions(string $version1, string $version2): int
    {
        $version1 = ltrim($version1, 'v');
        $version2 = ltrim($version2, 'v');
        return version_compare($version1, $version2);
    }

    private function checkIfUpdateNeeded(string $localVersion, string $remoteVersion): bool
    {
        $comparison = $this->compareVersions($remoteVersion, $localVersion);
        $this->line("Comparing versions: {$localVersion} vs {$remoteVersion}");
        if ($comparison === 1) {
            $this->warn('   -> Update available');
            return true;
        } elseif ($comparison === 0) {
            $this->info('   -> Up to date');
        } else {
            $this->line('   -> Local version is newer');
        }
        return false;
    }

    private function performUpdate(string $localPath, string $remoteRepo): int
    {
        $this->line("[INFO] Starting update process for {$localPath}...");
        try {
            $backupPath = $this->createBackupSimple($localPath);
            $this->info("[SUCCESS] Backup created: " . basename($backupPath));

            $this->line('[INFO] Downloading latest files...');
            $tempDir = sys_get_temp_dir() . '/webkernel_update_' . uniqid();
            $commands = [
                "git clone --filter=blob:none --sparse {$remoteRepo} {$tempDir}",
                "cd {$tempDir} && git sparse-checkout set {$localPath}",
                "cd {$tempDir} && git checkout main"
            ];
            foreach ($commands as $cmd) {
                $this->line("[EXEC] " . explode(' && ', $cmd)[count(explode(' && ', $cmd)) - 1]);
                $result = shell_exec($cmd . " 2>&1");
                if (strpos($result, 'fatal') !== false || strpos($result, 'error') !== false) {
                    throw new Exception("Git command failed: {$result}");
                }
            }

            $sourcePath = $tempDir . '/' . $localPath;
            $targetPath = base_path($localPath);
            if (!File::exists($sourcePath)) {
                throw new Exception("Source directory not found: {$sourcePath}");
            }
            if (File::exists($targetPath)) {
                $this->line('[INFO] Removing old files...');
                File::deleteDirectory($targetPath);
            }
            $this->line('[INFO] Installing new files...');
            $this->copyDirectorySimple($sourcePath, $targetPath);
            $this->deleteDirectory($tempDir);
            $this->newLine();
            $this->info('[SUCCESS] Update completed successfully!');
            $this->line("Backup available at: " . basename($backupPath));
            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error("[ERROR] Update failed for {$localPath}: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    private function createBackupSimple(string $localPath): string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $trashDir = base_path($localPath . '/.trash');
        $backupPath = $trashDir . "/backup_{$timestamp}";
        if (!File::exists($trashDir)) {
            File::makeDirectory($trashDir, 0755, true);
        }
        $source = base_path($localPath);
        if (!File::exists($source)) {
            $this->line("[INFO] No existing files to backup for {$localPath}");
            File::makeDirectory($backupPath, 0755, true);
            return $backupPath;
        }
        $this->line("Copying from: {$source}");
        $this->line("Copying to: {$backupPath}");
        if (shell_exec('which rsync')) {
            $cmd = "rsync -av --exclude='.trash' '{$source}/' '{$backupPath}/'";
            $this->line("[EXEC] Running: rsync (excluding .trash directory)...");
        } else {
            File::makeDirectory($backupPath, 0755, true);
            $cmd = "find '{$source}' -type f -not -path '*/.trash/*' -exec cp --parents {} '{$backupPath}/' \\;";
            $this->line("[EXEC] Running: selective copy (excluding .trash)...");
        }
        $process = popen($cmd . ' 2>&1', 'r');
        $lineCount = 0;
        while (!feof($process)) {
            $line = fgets($process);
            if ($line) {
                $lineCount++;
                if ($lineCount % 10 === 0) {
                    $this->line("[PROGRESS] Copied {$lineCount} items...");
                }
            }
        }
        $exitCode = pclose($process);
        if ($exitCode !== 0) {
            $this->error("[ERROR] Backup failed with exit code: {$exitCode}");
            File::makeDirectory($backupPath, 0755, true);
        } else {
            $this->line("[SUCCESS] Backup completed: {$lineCount} items copied");
        }
        return $backupPath;
    }

    private function copyDirectorySimple(string $source, string $target): void
    {
        $result = shell_exec("cp -r '{$source}' '{$target}' 2>&1");
        if ($result !== null && $result !== '') {
            throw new Exception("Copy failed: {$result}");
        }
    }

    private function deleteDirectory(string $path): void
    {
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }
    }

    private function handleRevert(): int
    {
        $this->info('Webkernel Backup Revert');
        $this->newLine();
        $packages = Application::getWebkernelPackages();
        $this->line('Select package to revert:');
        $packageNames = array_keys($packages);
        foreach ($packageNames as $index => $name) {
            $this->line("  [{$index}] {$name}");
        }
        $choice = $this->ask('Select package (number)', '0');
        if (!is_numeric($choice) || !isset($packageNames[$choice])) {
            $this->error('Invalid selection');
            return self::FAILURE;
        }
        $selectedPackage = $packageNames[$choice];
        $trashDir = base_path($packages[$selectedPackage]['path'] . '/.trash');
        if (!File::exists($trashDir)) {
            $this->error('No backup directory found');
            return self::FAILURE;
        }
        $backups = [];
        $directories = File::directories($trashDir);
        foreach ($directories as $directory) {
            $dirname = basename($directory);
            if (preg_match('/backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})/', $dirname, $matches)) {
                $timestamp = $matches[1];
                $readableDate = date('F j, Y \a\t g:i A', strtotime(str_replace('_', ' ', $timestamp)));
                $size = $this->getDirectorySize($directory);
                $backups[] = [
                    'file' => $directory,
                    'timestamp' => $timestamp,
                    'readable' => $readableDate,
                    'size' => $this->formatBytes($size)
                ];
            }
        }
        if (empty($backups)) {
            $this->error('No backup files found');
            return self::FAILURE;
        }
        usort($backups, function($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });
        $this->line('Available backups:');
        $this->newLine();
        foreach ($backups as $index => $backup) {
            $this->line("  [{$index}] {$backup['readable']} ({$backup['size']})");
        }
        $this->newLine();
        $choice = $this->ask('Select backup to restore (number)', '0');
        if (!is_numeric($choice) || !isset($backups[$choice])) {
            $this->error('Invalid selection');
            return self::FAILURE;
        }
        $selectedBackup = $backups[$choice];
        $this->warn("This will replace your current {$selectedPackage} installation with backup from {$selectedBackup['readable']}");
        $confirm = $this->confirm('Are you sure you want to proceed?', false);
        if (!$confirm) {
            $this->info('Operation cancelled');
            return self::SUCCESS;
        }
        return $this->performRevert($selectedBackup['file'], $packages[$selectedPackage]['path']);
    }

    private function performRevert(string $backupPath, string $localPath): int
    {
        $this->line('Starting revert process...');
        try {
            $currentBackupPath = $this->createBackupSimple($localPath);
            $this->info("Current version backed up to: " . basename($currentBackupPath));
            $this->line('Restoring files...');
            $targetPath = base_path($localPath);
            if (File::exists($targetPath)) {
                File::deleteDirectory($targetPath);
            }
            $this->copyDirectorySimple($backupPath, $targetPath);
            $this->newLine();
            $this->info('Webkernel successfully reverted!');
            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error("Revert failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    private function getDirectorySize(string $directory): int
    {
        $size = 0;
        $files = File::allFiles($directory);
        foreach ($files as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    private function handleAutomatic(): int
    {
        $this->info('Webkernel Automatic Updates Setup');
        $this->newLine();
        $this->line('This will setup a daily cron job to automatically check and update Webkernel packages.');
        $this->line('The cron job will run at 2:00 AM every day and will:');
        $this->line('  • Check for new versions');
        $this->line('  • Create backups before updating');
        $this->line('  • Automatically install updates if available');
        $this->line('  • Log all activities');
        $this->newLine();
        $confirm = $this->confirm('Do you want to setup automatic daily updates?', false);
        if (!$confirm) {
            $this->info('Automatic updates setup cancelled');
            return self::SUCCESS;
        }
        try {
            $this->setupCronJob();
            $this->info('Automatic updates successfully configured!');
            $this->line('Webkernel will now check for updates daily at 2:00 AM');
            $this->line('To disable: php artisan webkernel:update --automatic and choose to remove');
            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error("Failed to setup automatic updates: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    private function setupCronJob(): void
    {
        $projectPath = base_path();
        $phpPath = $this->getPhpPath();
        $artisanPath = $projectPath . '/artisan';
        $command = "cd {$projectPath} && {$phpPath} {$artisanPath} webkernel:update --no-interaction";
        $cronEntry = "0 2 * * * {$command} >> {$projectPath}/storage/logs/webkernel-auto-update.log 2>&1";
        $existingCron = shell_exec('crontab -l 2>/dev/null');
        if ($existingCron && strpos($existingCron, 'webkernel:update') !== false) {
            $this->warn('Automatic updates already configured');
            $remove = $this->confirm('Do you want to remove existing automatic updates?', false);
            if ($remove) {
                $this->removeCronJob();
                $this->info('Automatic updates removed');
                return;
            }
            $this->info('Keeping existing configuration');
            return;
        }
        $newCron = trim($existingCron) . "\n" . $cronEntry . "\n";
        $tempFile = tempnam(sys_get_temp_dir(), 'webkernel_cron');
        file_put_contents($tempFile, $newCron);
        $result = shell_exec("crontab {$tempFile} 2>&1");
        unlink($tempFile);
        if ($result !== null && $result !== '') {
            throw new Exception("Failed to install cron job: {$result}");
        }
        $this->line("Cron job added: {$cronEntry}");
    }

    private function removeCronJob(): void
    {
        $existingCron = shell_exec('crontab -l 2>/dev/null');
        if (!$existingCron) {
            return;
        }
        $lines = explode("\n", $existingCron);
        $filteredLines = array_filter($lines, function($line) {
            return strpos($line, 'webkernel:update') === false;
        });
        $newCron = implode("\n", $filteredLines);
        $tempFile = tempnam(sys_get_temp_dir(), 'webkernel_cron');
        file_put_contents($tempFile, $newCron);
        shell_exec("crontab {$tempFile} 2>&1");
        unlink($tempFile);
    }

    private function getPhpPath(): string
    {
        $phpPaths = [
            PHP_BINARY,
            '/usr/bin/php',
            '/usr/local/bin/php',
            'php'
        ];
        foreach ($phpPaths as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }
        return 'php';
    }
}
