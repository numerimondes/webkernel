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
                            {--remote-repo= : Custom remote repository URL}';

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
        $this->info('🔄 Webkernel Updater');
        $this->newLine();

        // Get paths
        $localPath = $this->option('local-path')
                    ?? env('WEBKERNEL_LOCAL_PATH')
                    ?? self::DEFAULT_LOCAL_PATH;

        $remoteRepo = $this->option('remote-repo')
                     ?? env('WEBKERNEL_REMOTE_REPO')
                     ?? self::DEFAULT_REMOTE_REPO;

        $this->line("📂 Local path: {$localPath}");
        $this->line("🌐 Remote repository: {$remoteRepo}");
        $this->newLine();

        // Check local directory
        if (!File::exists(base_path($localPath))) {
            $this->error("❌ Local webkernel directory not found: {$localPath}");
            $this->error("Make sure you're running this from the correct directory");
            return Command::FAILURE;
        }

        $applicationPhpPath = base_path($localPath . '/src/constants/Application.php');

        // Extract local versions
        $this->line('📖 Reading local versions...');
        $localVersions = $this->extractVersionsFromFile($applicationPhpPath);

        if (!$localVersions) {
            $this->error('❌ Could not extract versions from local file');
            return Command::FAILURE;
        }

        $this->info("📦 Local WEBKERNEL_VERSION: {$localVersions['webkernel']}");
        $this->info("🔒 Local STABLE_VERSION: {$localVersions['stable']}");
        $this->newLine();

        // Fetch remote Application.php
        $this->line('🌐 Fetching remote Application.php...');
        $remoteApplicationUrl = "{$remoteRepo}/raw/main/packages/webkernel/src/constants/Application.php";

        try {
            $response = Http::timeout(30)->get($remoteApplicationUrl);

            if (!$response->successful()) {
                $this->error('❌ Failed to fetch remote Application.php file');
                $this->error('Check your internet connection or remote repository URL');
                return Command::FAILURE;
            }

            $remoteContent = $response->body();
        } catch (\Exception $e) {
            $this->error("❌ Error fetching remote file: {$e->getMessage()}");
            return Command::FAILURE;
        }

        // Extract remote versions
        $this->line('📖 Reading remote versions...');
        $remoteVersions = $this->extractVersionsFromContent($remoteContent);

        if (!$remoteVersions) {
            $this->error('❌ Could not extract versions from remote file');
            return Command::FAILURE;
        }

        $this->info("📦 Remote WEBKERNEL_VERSION: {$remoteVersions['webkernel']}");
        $this->info("🔒 Remote STABLE_VERSION: {$remoteVersions['stable']}");
        $this->newLine();

        // Compare versions and update if needed
        $needsUpdate = $this->checkIfUpdateNeeded($localVersions, $remoteVersions);

        if ($needsUpdate) {
            $this->warn('🔄 Update available! Proceeding with update...');
            $this->newLine();
            return $this->performUpdate($localPath, $remoteRepo);
        } else {
            $this->info('✅ Your Webkernel installation is up to date!');
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

        $this->line('🔍 Comparing versions...');

        $this->line("📦 WEBKERNEL_VERSION: {$localVersions['webkernel']} vs {$remoteVersions['webkernel']}");
        if ($webkernelComparison === 1) {
            $this->warn('   → Update available');
        } elseif ($webkernelComparison === 0) {
            $this->info('   → Up to date');
        } else {
            $this->line('   → Local version is newer');
        }

        $this->line("🔒 STABLE_VERSION: {$localVersions['stable']} vs {$remoteVersions['stable']}");
        if ($stableComparison === 1) {
            $this->warn('   → Update available');
        } elseif ($stableComparison === 0) {
            $this->info('   → Up to date');
        } else {
            $this->line('   → Local version is newer');
        }

        $this->newLine();

        return ($webkernelComparison === 1 || $stableComparison === 1);
    }

    /**
     * Perform the actual update
     */
    private function performUpdate(string $localPath, string $remoteRepo): int
    {
        $this->line('🔄 Starting Webkernel update process...');

        try {
            // Create backup
            $backupPath = $this->createBackup($localPath);
            $this->info("📁 Backup created: {$backupPath}");

            // Download and extract remote repository
            $this->line('📥 Downloading remote repository...');
            $zipUrl = "{$remoteRepo}/archive/refs/heads/main.zip";

            $response = Http::timeout(120)->get($zipUrl);

            if (!$response->successful()) {
                $this->error('❌ Failed to download remote repository');
                return Command::FAILURE;
            }

            // Save zip file
            $tempZip = tempnam(sys_get_temp_dir(), 'webkernel_update_');
            file_put_contents($tempZip, $response->body());

            // Extract zip
            $this->line('📦 Extracting files...');
            $extractPath = $this->extractZip($tempZip);

            if (!$extractPath) {
                $this->error('❌ Failed to extract repository files');
                unlink($tempZip);
                return Command::FAILURE;
            }

            // Replace local files
            $this->line('🔄 Replacing local files...');
            $sourcePath = $extractPath . '/webkernel-main/packages/webkernel';
            $targetPath = base_path($localPath);

            $this->replaceDirectory($sourcePath, $targetPath);

            // Cleanup
            unlink($tempZip);
            $this->deleteDirectory($extractPath);

            $this->newLine();
            $this->info('✅ Webkernel update completed successfully!');
            $this->line("📁 Backup available at: {$backupPath}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Update failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Create backup of current installation
     */
    private function createBackup(string $localPath): string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $backupPath = base_path("webkernel_backup_{$timestamp}");

        $this->line("📁 Creating backup...");

        // Copy directory recursively
        $this->copyDirectory(base_path($localPath), $backupPath);

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
    private function replaceDirectory(string $source, string $target): void
    {
        // Remove existing directory contents (except for potential custom files)
        if (File::exists($target)) {
            File::deleteDirectory($target);
        }

        // Copy new files
        $this->copyDirectory($source, $target);
    }

    /**
     * Copy directory recursively
     */
    private function copyDirectory(string $source, string $target): void
    {
        if (!File::exists($target)) {
            File::makeDirectory($target, 0755, true);
        }

        $files = File::allFiles($source);
        $directories = File::directories($source);

        // Create directories first
        foreach ($directories as $directory) {
            $relativePath = str_replace($source, '', $directory);
            File::makeDirectory($target . $relativePath, 0755, true);
        }

        // Copy files
        foreach ($files as $file) {
            $relativePath = str_replace($source, '', $file->getPathname());
            File::copy($file->getPathname(), $target . $relativePath);
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
}
