<?php

namespace Webkernel\Commands\PackageManager;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class WebkernelCheckUpdate extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'webkernel:update
                            {--local-path= : Custom path to local webkernel directory}
                            {--remote-repo= : Custom remote repository URL}
                            {--revert : Show available backups and revert to selected one}
                            {--automatic : Setup automatic daily updates via cron}';

    /**
     * The console command description.
     */
    protected $description = 'Update Webkernel by comparing and replacing with remote repository version';

    /**
     * Default paths
     */
    private const DEFAULT_LOCAL_PATH = 'packages/webkernel';
    private const DEFAULT_REMOTE_REPO = 'https://github.com/numerimondes/webkernel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Handle revert option
        if ($this->option('revert')) {
            return $this->handleRevert();
        }

        // Handle automatic option
        if ($this->option('automatic')) {
            return $this->handleAutomatic();
        }

        $this->info('[INFO] Webkernel Updater');
        $this->newLine();

        // Get paths
        $localPath = $this->option('local-path')
                    ?? env('WEBKERNEL_LOCAL_PATH')
                    ?? self::DEFAULT_LOCAL_PATH;

        $remoteRepo = $this->option('remote-repo')
                     ?? env('WEBKERNEL_REMOTE_REPO')
                     ?? self::DEFAULT_REMOTE_REPO;

        $this->line("Local path: {$localPath}");
        $this->line("Remote repository: {$remoteRepo}");
        $this->newLine();

        // Check local directory
        if (!File::exists(base_path($localPath))) {
            $this->error("[ERROR] Local webkernel directory not found: {$localPath}");
            $this->error("Make sure you're running this from the correct directory");
            return Command::FAILURE;
        }

        $applicationPhpPath = base_path($localPath . '/src/constants/Application.php');

        // Extract local versions
        $this->line('[INFO] Reading local versions...');
        $localVersions = $this->extractVersionsFromFile($applicationPhpPath);

        if (!$localVersions) {
            $this->error('[ERROR] Could not extract versions from local file');
            return Command::FAILURE;
        }

        $this->info("Local WEBKERNEL_VERSION: {$localVersions['webkernel']}");
        $this->info("Local STABLE_VERSION: {$localVersions['stable']}");
        $this->newLine();

        // Fetch remote Application.php
        $this->line('[INFO] Fetching remote Application.php...');
        $remoteApplicationUrl = "https://raw.githubusercontent.com/numerimondes/webkernel/refs/heads/main/packages/webkernel/src/constants/Application.php";
        $this->line("URL: {$remoteApplicationUrl}");

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ])
                ->get($remoteApplicationUrl);

            if (!$response->successful()) {
                $this->error('[ERROR] Failed to fetch remote Application.php file');
                $this->error('Check your internet connection or remote repository URL');
                $this->line("Response status: {$response->status()}");
                return Command::FAILURE;
            }

            $remoteContent = $response->body();
            $this->line("Content preview: " . substr($remoteContent, 0, 200) . "...");
        } catch (\Exception $e) {
            $this->error("[ERROR] Error fetching remote file: {$e->getMessage()}");
            return Command::FAILURE;
        }

        // Extract remote versions
        $this->line('[INFO] Reading remote versions...');
        $remoteVersions = $this->extractVersionsFromContent($remoteContent);

        if (!$remoteVersions) {
            $this->error('[ERROR] Could not extract versions from remote file');
            return Command::FAILURE;
        }

        $this->info("Remote WEBKERNEL_VERSION: {$remoteVersions['webkernel']}");
        $this->info("Remote STABLE_VERSION: {$remoteVersions['stable']}");
        $this->newLine();

        // Compare versions and update if needed
        $needsUpdate = $this->checkIfUpdateNeeded($localVersions, $remoteVersions);

        if ($needsUpdate) {
            $this->warn('[UPDATE] Update available!');
            $this->newLine();

            // Determine version type and display update info
            $versionInfo = $this->getVersionUpdateInfo($localVersions, $remoteVersions);
            $this->line("Update: {$versionInfo['from']} → {$versionInfo['to']} {$versionInfo['type']}");

            $this->newLine();
            $this->line('[WARNING] This will:');
            $this->line('  * Create a backup in packages/webkernel/.trash/');
            $this->line('  * Replace your entire packages/webkernel directory');
            $this->line('  * Download and install the latest version from the repository');

            $this->newLine();
            $confirm = $this->confirm('Do you want to proceed with the update?', false);

            if (!$confirm) {
                $this->info('Update cancelled by user');
                return Command::SUCCESS;
            }

            $this->newLine();
            return $this->performUpdate($localPath, $remoteRepo);
        } else {
            $this->info('[SUCCESS] Your Webkernel installation is up to date!');
            return Command::SUCCESS;
        }
    }

    /**
     * Extract versions from file path
     */
    private function extractVersionsFromFile(string $filePath): ?array
    {
        if (!File::exists($filePath)) {
            return null;
        }

        $content = File::get($filePath);
        return $this->extractVersionsFromContent($content);
    }

    /**
     * Extract versions from content
     */
    private function extractVersionsFromContent(string $content): ?array
    {
        $webkernelVersion = null;
        $stableVersion = null;

        // Extract WEBKERNEL_VERSION
        if (preg_match("/const\s+WEBKERNEL_VERSION\s*=\s*['\"]([^'\"]+)['\"];/", $content, $matches)) {
            $webkernelVersion = $matches[1];
        }

        // Extract STABLE_VERSION
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

    /**
     * Compare two versions
     */
    private function compareVersions(string $version1, string $version2): int
    {
        // Remove 'v' prefix if present
        $version1 = ltrim($version1, 'v');
        $version2 = ltrim($version2, 'v');

        return version_compare($version1, $version2);
    }

    /**
     * Check if update is needed
     */
    private function checkIfUpdateNeeded(array $localVersions, array $remoteVersions): bool
    {
        $webkernelComparison = $this->compareVersions($remoteVersions['webkernel'], $localVersions['webkernel']);
        $stableComparison = $this->compareVersions($remoteVersions['stable'], $localVersions['stable']);

        $this->line('[INFO] Comparing versions...');

        $this->line("WEBKERNEL_VERSION: {$localVersions['webkernel']} vs {$remoteVersions['webkernel']}");
        if ($webkernelComparison === 1) {
            $this->warn('   -> Update available');
        } elseif ($webkernelComparison === 0) {
            $this->info('   -> Up to date');
        } else {
            $this->line('   -> Local version is newer');
        }

        $this->line("STABLE_VERSION: {$localVersions['stable']} vs {$remoteVersions['stable']}");
        if ($stableComparison === 1) {
            $this->warn('   -> Update available');
        } elseif ($stableComparison === 0) {
            $this->info('   -> Up to date');
        } else {
            $this->line('   -> Local version is newer');
        }

        $this->newLine();

        return ($webkernelComparison === 1 || $stableComparison === 1);
    }

    /**
     * Perform the actual update
     */
    private function performUpdate(string $localPath, string $remoteRepo): int
    {
        $this->line('[INFO] Starting Webkernel update process...');

        try {
            // Create backup
            $this->line('[INFO] Creating backup...');
            $backupPath = $this->createBackupSimple($localPath);
            $this->info("[SUCCESS] Backup created: " . basename($backupPath));

            // Use git to clone only the specific directory
            $this->line('[INFO] Downloading latest Webkernel files...');
            $tempDir = sys_get_temp_dir() . '/webkernel_update_' . uniqid();

            // Clone with sparse checkout to get only packages/webkernel
            $commands = [
                "git clone --filter=blob:none --sparse https://github.com/numerimondes/webkernel.git {$tempDir}",
                "cd {$tempDir} && git sparse-checkout set packages/webkernel",
                "cd {$tempDir} && git checkout main"
            ];

            foreach ($commands as $cmd) {
                $this->line("[EXEC] " . explode(' && ', $cmd)[count(explode(' && ', $cmd)) - 1]);
                $result = shell_exec($cmd . " 2>&1");
                if (strpos($result, 'fatal') !== false || strpos($result, 'error') !== false) {
                    throw new \Exception("Git command failed: {$result}");
                }
            }

            // Replace local files
            $this->line('[INFO] Replacing local files...');
            $sourcePath = $tempDir . '/packages/webkernel';
            $targetPath = base_path($localPath);

            if (!File::exists($sourcePath)) {
                throw new \Exception("Source directory not found: {$sourcePath}");
            }

            // Remove old installation
            if (File::exists($targetPath)) {
                $this->line('[INFO] Removing old files...');
                File::deleteDirectory($targetPath);
            }

            // Copy new files
            $this->line('[INFO] Installing new files...');
            $this->copyDirectorySimple($sourcePath, $targetPath);

            // Cleanup
            $this->deleteDirectory($tempDir);

            $this->newLine();
            $this->info('[SUCCESS] Webkernel update completed successfully!');
            $this->line("Backup available at: " . basename($backupPath));

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("[ERROR] Update failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Create backup of current installation
     */
    private function createBackup(string $localPath): string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $trashDir = base_path('packages/webkernel/.trash');
        $backupPath = $trashDir . "/webkernel_backup_{$timestamp}";

        $this->line("📁 Creating backup...");

        // Ensure trash directory exists
        if (!File::exists($trashDir)) {
            File::makeDirectory($trashDir, 0755, true);
        }

        // Copy directory recursively with progress
        $this->copyDirectory(base_path($localPath), $backupPath, true);

        $this->info("✅ Backup created successfully");

        return $backupPath;
    }

    /**
     * Extract zip file
     */
    private function extractZip(string $zipPath): ?string
    {
        $extractPath = sys_get_temp_dir() . '/webkernel_extract_' . uniqid();

        if (!class_exists('ZipArchive')) {
            $this->error('❌ ZipArchive extension not available');
            return null;
        }

        $zip = new \ZipArchive();

        if ($zip->open($zipPath) === true) {
            $zip->extractTo($extractPath);
            $zip->close();
            return $extractPath;
        }

        return null;
    }

    /**
     * Replace directory contents
     */
    private function replaceDirectory(string $source, string $target, bool $showProgress = false): void
    {
        // Remove existing directory contents (except for potential custom files)
        if (File::exists($target)) {
            $this->line('🗑️ Removing old files...');
            File::deleteDirectory($target);
        }

        // Copy new files
        $this->line('📂 Installing new files...');
        $this->copyDirectory($source, $target, $showProgress);
    }

    /**
     * Copy directory recursively with progress
     */
    private function copyDirectory(string $source, string $target, bool $showProgress = false): void
    {
        if (!File::exists($target)) {
            File::makeDirectory($target, 0755, true);
        }

        if (!File::exists($source)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        if ($showProgress) {
            // Count total files first
            $totalFiles = iterator_count($iterator);
            $iterator->rewind();

            $this->line("📋 Found {$totalFiles} items to copy");
            $progressBar = $this->output->createProgressBar($totalFiles);
            $progressBar->start();
        }

        $fileCount = 0;
        foreach ($iterator as $item) {
            $targetPath = $target . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

            if ($item->isDir()) {
                if (!File::exists($targetPath)) {
                    File::makeDirectory($targetPath, 0755, true);
                }
            } else {
                $targetDir = dirname($targetPath);
                if (!File::exists($targetDir)) {
                    File::makeDirectory($targetDir, 0755, true);
                }
                File::copy($item->getPathname(), $targetPath);
            }

            if ($showProgress) {
                $fileCount++;
                $progressBar->advance();
                if ($fileCount % 10 === 0) {
                    $progressBar->setMessage("Copying: " . basename($item->getPathname()));
                }
            }
        }

        if ($showProgress) {
            $progressBar->finish();
            $this->newLine();
        }
    }

    /**
     * Delete directory recursively
     */
    private function deleteDirectory(string $path): void
    {
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }
    }

    /**
     * Create ZIP backup
     */
    private function createZipBackup(string $sourcePath, string $zipPath): bool
    {
        if (!class_exists('ZipArchive')) {
            $this->warn('⚠️ ZipArchive extension not available, keeping uncompressed backup');
            return false;
        }

        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->warn('⚠️ Could not create ZIP file, keeping uncompressed backup');
            return false;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($sourcePath) + 1);

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        return true;
    }

    /**
     * Handle revert functionality
     */
    private function handleRevert(): int
    {
        $this->info('🔙 Webkernel Backup Revert');
        $this->newLine();

        $trashDir = base_path('packages/webkernel/.trash');

        if (!File::exists($trashDir)) {
            $this->error('❌ No backup directory found');
            return Command::FAILURE;
        }

        // Find all backup directories
        $backups = [];
        $directories = File::directories($trashDir);

        foreach ($directories as $directory) {
            $dirname = basename($directory);
            if (preg_match('/webkernel_backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})/', $dirname, $matches)) {
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
            $this->error('❌ No backup files found');
            return Command::FAILURE;
        }

        // Sort backups by timestamp (newest first)
        usort($backups, function($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });

        $this->line('📁 Available backups:');
        $this->newLine();

        foreach ($backups as $index => $backup) {
            $this->line("  [{$index}] {$backup['readable']} ({$backup['size']})");
        }

        $this->newLine();
        $choice = $this->ask('Select backup to restore (number)', '0');

        if (!is_numeric($choice) || !isset($backups[$choice])) {
            $this->error('❌ Invalid selection');
            return Command::FAILURE;
        }

        $selectedBackup = $backups[$choice];

        $this->warn("⚠️ This will replace your current Webkernel installation with backup from {$selectedBackup['readable']}");
        $confirm = $this->confirm('Are you sure you want to proceed?', false);

        if (!$confirm) {
            $this->info('Operation cancelled');
            return Command::SUCCESS;
        }

        return $this->performRevert($selectedBackup['file']);
    }

    /**
     * Perform the revert operation
     */
    private function performRevert(string $backupPath): int
    {
        $this->line('🔄 Starting revert process...');

        try {
            // Create current backup before reverting
            $localPath = self::DEFAULT_LOCAL_PATH;
            $currentBackupPath = $this->createBackup($localPath);
            $this->info("📁 Current version backed up to: " . basename($currentBackupPath));

            // Replace current installation
            $this->line('🔄 Restoring files...');
            $targetPath = base_path($localPath);

            // Remove current installation
            if (File::exists($targetPath)) {
                File::deleteDirectory($targetPath);
            }

            // Copy backup files
            $this->copyDirectory($backupPath, $targetPath);

            $this->newLine();
            $this->info('✅ Webkernel successfully reverted!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Revert failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Extract ZIP file to temporary directory
     */
    private function extractZipToTemp(string $zipPath): ?string
    {
        if (!class_exists('ZipArchive')) {
            $this->error('❌ ZipArchive extension not available');
            return null;
        }

        $extractPath = sys_get_temp_dir() . '/webkernel_revert_' . uniqid();

        $zip = new \ZipArchive();

        if ($zip->open($zipPath) === true) {
            $zip->extractTo($extractPath);
            $zip->close();
            return $extractPath;
        }

        return null;
    }

    /**
     * Format bytes into human readable format
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get directory size in bytes
     */
    private function getDirectorySize(string $directory): int
    {
        $size = 0;
        $files = File::allFiles($directory);

        foreach ($files as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    /**
     * Get version update information with proper formatting
     */
    private function getVersionUpdateInfo(array $localVersions, array $remoteVersions): array
    {
        $from = $localVersions['webkernel'];
        $to = $remoteVersions['webkernel'];

        // If WEBKERNEL_VERSION equals STABLE_VERSION, it's stable
        if ($remoteVersions['webkernel'] === $remoteVersions['stable']) {
            $type = '(STABLE)';
        } else {
            // If WEBKERNEL_VERSION > STABLE_VERSION, it's testing
            $webkernelComparison = $this->compareVersions($remoteVersions['webkernel'], $remoteVersions['stable']);
            if ($webkernelComparison === 1) {
                $type = '(TESTING)';
            } else {
                $type = '(STABLE)';
            }
        }

        return [
            'from' => $from,
            'to' => $to,
            'type' => $type
        ];
    }

    /**
     * Handle automatic updates setup
     */
    private function handleAutomatic(): int
    {
        $this->info('⏰ Webkernel Automatic Updates Setup');
        $this->newLine();

        $this->line('This will setup a daily cron job to automatically check and update Webkernel.');
        $this->line('The cron job will run at 2:00 AM every day and will:');
        $this->line('  • Check for new versions');
        $this->line('  • Create backups before updating');
        $this->line('  • Automatically install updates if available');
        $this->line('  • Log all activities');

        $this->newLine();
        $confirm = $this->confirm('Do you want to setup automatic daily updates?', false);

        if (!$confirm) {
            $this->info('Automatic updates setup cancelled');
            return Command::SUCCESS;
        }

        try {
            $this->setupCronJob();
            $this->info('✅ Automatic updates successfully configured!');
            $this->line('Webkernel will now check for updates daily at 2:00 AM');
            $this->line('To disable: php artisan webkernel:update --automatic and choose to remove');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Failed to setup automatic updates: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Setup cron job for automatic updates
     */
    private function setupCronJob(): void
    {
        $projectPath = base_path();
        $phpPath = $this->getPhpPath();
        $artisanPath = $projectPath . '/artisan';

        // Command to run
        $command = "cd {$projectPath} && {$phpPath} {$artisanPath} webkernel:update --no-interaction";

        // Cron entry (daily at 2:00 AM)
        $cronEntry = "0 2 * * * {$command} >> {$projectPath}/storage/logs/webkernel-auto-update.log 2>&1";

        // Check if cron entry already exists
        $existingCron = shell_exec('crontab -l 2>/dev/null');

        if ($existingCron && strpos($existingCron, 'webkernel:update') !== false) {
            $this->warn('⚠️ Automatic updates already configured');
            $remove = $this->confirm('Do you want to remove existing automatic updates?', false);

            if ($remove) {
                $this->removeCronJob();
                $this->info('✅ Automatic updates removed');
                return;
            } else {
                $this->info('Keeping existing configuration');
                return;
            }
        }

        // Add new cron entry
        $newCron = trim($existingCron) . "\n" . $cronEntry . "\n";

        // Write to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'webkernel_cron');
        file_put_contents($tempFile, $newCron);

        // Install cron
        $result = shell_exec("crontab {$tempFile} 2>&1");
        unlink($tempFile);

        if ($result !== null && $result !== '') {
            throw new \Exception("Failed to install cron job: {$result}");
        }

        $this->line("📝 Cron job added: {$cronEntry}");
    }

    /**
     * Remove cron job for automatic updates
     */
    private function removeCronJob(): void
    {
        $existingCron = shell_exec('crontab -l 2>/dev/null');

        if (!$existingCron) {
            return;
        }

        // Remove lines containing webkernel:update
        $lines = explode("\n", $existingCron);
        $filteredLines = array_filter($lines, function($line) {
            return strpos($line, 'webkernel:update') === false;
        });

        $newCron = implode("\n", $filteredLines);

        // Write to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'webkernel_cron');
        file_put_contents($tempFile, $newCron);

        // Install cron
        shell_exec("crontab {$tempFile} 2>&1");
        unlink($tempFile);
    }

    /**
     * Get PHP executable path
     */
    private function getPhpPath(): string
    {
        // Try to find PHP executable
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

        // Fallback to 'php' and hope it's in PATH
        return 'php';
    }

    /**
     * Create simple backup with real-time feedback
     */
    private function createBackupSimple(string $localPath): string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $trashDir = base_path('packages/webkernel/.trash');
        $backupPath = $trashDir . "/webkernel_backup_{$timestamp}";

        // Ensure trash directory exists
        if (!File::exists($trashDir)) {
            File::makeDirectory($trashDir, 0755, true);
        }

        $source = base_path($localPath);

        // Show what we're doing
        $this->line("Copying from: {$source}");
        $this->line("Copying to: {$backupPath}");

        // Use rsync with exclusions if available, otherwise use find + cp
        if (shell_exec('which rsync')) {
            $cmd = "rsync -av --exclude='.trash' '{$source}/' '{$backupPath}/'";
            $this->line("[EXEC] Running: rsync (excluding .trash directory)...");
        } else {
            // Create target directory first
            File::makeDirectory($backupPath, 0755, true);
            $cmd = "find '{$source}' -type f -not -path '*/.trash/*' -exec cp --parents {} '{$backupPath}/' \\;";
            $this->line("[EXEC] Running: selective copy (excluding .trash)...");
        }

        // Execute command with real-time output
        $process = popen($cmd . ' 2>&1', 'r');
        $lineCount = 0;

        while (!feof($process)) {
            $line = fgets($process);
            if ($line) {
                $lineCount++;
                // Show every 10th file to avoid spam but show progress
                if ($lineCount % 10 === 0) {
                    $this->line("[PROGRESS] Copied {$lineCount} items...");
                }
            }
        }

        $exitCode = pclose($process);

        if ($exitCode !== 0) {
            throw new \Exception("Backup failed with exit code: {$exitCode}");
        }

        $this->line("[SUCCESS] Backup completed: {$lineCount} items copied");

        return $backupPath;
    }

    /**
     * Simple directory copy without progress bars
     */
    private function copyDirectorySimple(string $source, string $target): void
    {
        $result = shell_exec("cp -r '{$source}' '{$target}' 2>&1");

        if ($result !== null && $result !== '') {
            throw new \Exception("Copy failed: {$result}");
        }
    }
}
