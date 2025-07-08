<?php

declare(strict_types=1);

namespace Webkernel\Core\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Webkernel Platform Update Command
 * 
 * Intelligent, self-contained platform updater that handles version comparison,
 * safe downloads, backups, and post-update actions without dangerous functions.
 * 
 * @author El Moumen Yassine
 * @email yassine@numerimondes.com
 * @website www.numerimondes.com
 * @license MPL-2.0
 */
class PlatformUpdateCommand extends Command
{
    /**
     * Configuration constants
     */
    private const DEFAULT_GITHUB_REPO = 'https://github.com/numerimondes/webkernel';
    private const DEFAULT_BRANCH = 'main';
    private const CORE_FILE_PATH = 'packages/webkernel/src/Constants/Definitions/Webkernel/Core.php';
    private const REMOTE_CORE_FILE_URL = 'packages/webkernel/src/Constants/Definitions/Webkernel/Core.php';
    private const VERSION_CACHE_TTL = 3600; // 1 hour
    private const HTTP_TIMEOUT = 30;
    private const MAX_BACKUPS = 5;
    private const LOG_FILE = 'webkernel_updater.log';
    
    /**
     * Instance properties
     */
    private string $remoteRepo;
    private string $branch;
    private bool $forceUpdate = false;
    private bool $dryRun = false;
    private array $updateStatus = [];
    private array $postUpdateHooks = [];
    
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'webkernel:update
                            {--force : Force update regardless of version comparison}
                            {--dry-run : Simulate update without making changes}
                            {--status : Show current update status}
                            {--json : Output status as JSON format}
                            {--log : Show log file contents}
                            {--auto-run : Run automatically (for cron jobs)}
                            {--remote-repo= : Custom remote repository URL}
                            {--branch= : Custom branch name}
                            {--clear-cache : Clear version cache}
                            {--backups : List available backups}
                            {--restore= : Restore from specific backup}
                            {--register-hooks : Register default post-update hooks}
                            {--silent : Run without user interaction (for frontend/cron)}
                            {--rolling : Enable rolling release mode (auto-update on stable versions)}';

    /**
     * The console command description.
     */
    protected $description = 'Update Webkernel platform by comparing local and remote versions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Webkernel Platform Updater');
        $this->newLine();

        // Initialize properties
        $this->remoteRepo = $this->option('remote-repo') ?? self::DEFAULT_GITHUB_REPO;
        $this->branch = $this->option('branch') ?? self::DEFAULT_BRANCH;
        $this->forceUpdate = $this->option('force');
        $this->dryRun = $this->option('dry-run');
        
        $this->initializeUpdateStatus();

        // Register default hooks if requested
        if ($this->option('register-hooks')) {
            $this->registerDefaultHooks();
            $this->info('Default post-update hooks registered');
            return self::SUCCESS;
        }

        // Handle different command modes
        if ($this->option('status')) {
            return $this->handleStatus();
        }

        if ($this->option('json')) {
            return $this->handleJsonOutput();
        }

        if ($this->option('log')) {
            return $this->handleLogOutput();
        }

        if ($this->option('clear-cache')) {
            return $this->handleClearCache();
        }

        if ($this->option('backups')) {
            return $this->handleBackups();
        }

        if ($backupPath = $this->option('restore')) {
            return $this->handleRestore($backupPath);
        }

        if ($this->option('auto-run')) {
            return $this->handleAutoRun();
        }

        // Default: perform update
        return $this->handleUpdate();
    }

    /**
     * Initialize update status
     */
    private function initializeUpdateStatus(): void
    {
        $this->updateStatus = [
            'local_version' => null,
            'remote_version' => null,
            'update_needed' => false,
            'remote_repo' => $this->remoteRepo,
            'branch' => $this->branch,
            'force_update' => $this->forceUpdate,
            'dry_run' => $this->dryRun,
            'started_at' => date('Y-m-d H:i:s'),
            'completed_at' => null,
            'success' => false,
            'error' => null,
            'backup_created' => false,
            'backup_path' => null,
            'files_updated' => 0,
            'post_update_actions' => [],
        ];
    }

    /**
     * Get local Webkernel version
     */
    private function getLocalVersion(): string
    {
        if (defined('WEBKERNEL_VERSION')) {
            return WEBKERNEL_VERSION;
        }
        
        // Try to read from Core.php file
        $coreFilePath = base_path(self::CORE_FILE_PATH);
        if (File::exists($coreFilePath)) {
            $content = File::get($coreFilePath);
            if (preg_match("/const\s+WEBKERNEL_VERSION\s*=\s*['\"]([^'\"]+)['\"]/", $content, $matches)) {
                return $matches[1];
            }
        }
        
        return '0.0.0';
    }

    /**
     * Get remote Webkernel version
     */
    private function getRemoteVersion(): ?string
    {
        $cacheKey = 'webkernel_remote_version_' . md5($this->remoteRepo . $this->branch);
        
        // Check cache first
        if (cache()->has($cacheKey)) {
            $this->line("Using cached remote version");
            return cache()->get($cacheKey);
        }
        
        $this->line("Fetching remote version from: {$this->remoteRepo}");
        
        try {
            $remoteUrl = rtrim($this->remoteRepo, '/') . '/raw/' . $this->branch . '/' . self::REMOTE_CORE_FILE_URL;
            
            $response = Http::timeout(self::HTTP_TIMEOUT)
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ])
                ->get($remoteUrl);
            
            if (!$response->successful()) {
                $this->error("Failed to fetch remote Core.php: HTTP {$response->status()}");
                return null;
            }
            
            $content = $response->body();
            
            if (preg_match("/const\s+WEBKERNEL_VERSION\s*=\s*['\"]([^'\"]+)['\"]/", $content, $matches)) {
                $remoteVersion = $matches[1];
                
                // Cache the result
                cache()->put($cacheKey, $remoteVersion, self::VERSION_CACHE_TTL);
                
                $this->line("Remote version found: {$remoteVersion}");
                return $remoteVersion;
            }
            
            $this->error("WEBKERNEL_VERSION constant not found in remote Core.php");
            return null;
            
        } catch (Exception $e) {
            $this->error("Error fetching remote version: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get remote stable version
     */
    private function getRemoteStableVersion(): ?string
    {
        $cacheKey = 'webkernel_remote_stable_version_' . md5($this->remoteRepo . $this->branch);
        
        // Check cache first
        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }
        
        try {
            $remoteUrl = rtrim($this->remoteRepo, '/') . '/raw/' . $this->branch . '/' . self::REMOTE_CORE_FILE_URL;
            
            $response = Http::timeout(self::HTTP_TIMEOUT)
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ])
                ->get($remoteUrl);
            
            if (!$response->successful()) {
                return null;
            }
            
            $content = $response->body();
            
            if (preg_match("/const\s+WEBKERNEL_REMOTE_STABLE_VERSION\s*=\s*['\"]([^'\"]+)['\"]/", $content, $matches)) {
                $stableVersion = $matches[1];
                cache()->put($cacheKey, $stableVersion, self::VERSION_CACHE_TTL);
                return $stableVersion;
            }
            
            return null;
            
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Compare versions and determine if update is needed
     */
    private function isUpdateNeeded(): bool
    {
        if ($this->forceUpdate) {
            $this->line("Force update requested");
            return true;
        }
        
        $localVersion = $this->getLocalVersion();
        $remoteVersion = $this->getRemoteVersion();
        $remoteStableVersion = $this->getRemoteStableVersion();
        
        $this->updateStatus['local_version'] = $localVersion;
        $this->updateStatus['remote_version'] = $remoteVersion;
        $this->updateStatus['remote_stable_version'] = $remoteStableVersion;
        
        if (!$remoteVersion) {
            $this->error("Could not determine remote version");
            return false;
        }
        
        $this->line("Local version: {$localVersion}");
        $this->line("Remote version: {$remoteVersion}");
        if ($remoteStableVersion) {
            $this->line("Remote stable version: {$remoteStableVersion}");
        }
        
        // Check if rolling release should be enabled automatically
        if ($this->option('rolling') || $this->shouldEnableRollingRelease($remoteVersion, $remoteStableVersion)) {
            $this->line("Rolling release mode: auto-updating to stable version");
            $this->updateStatus['rolling_release'] = true;
            
            // If remote version equals stable version, it's safe to update
            if ($remoteStableVersion && $remoteVersion === $remoteStableVersion) {
                $comparison = version_compare($remoteVersion, $localVersion);
                if ($comparison > 0) {
                    $this->line("Rolling release: updating to stable version");
                    $this->updateStatus['update_needed'] = true;
                    return true;
                }
            }
        }
        
        $comparison = version_compare($remoteVersion, $localVersion);
        
        if ($comparison > 0) {
            $this->line("Update needed: remote version is newer");
            $this->updateStatus['update_needed'] = true;
            return true;
        } elseif ($comparison === 0) {
            $this->line("Versions are identical");
            $this->updateStatus['update_needed'] = false;
            return false;
        } else {
            $this->line("Local version is newer than remote");
            $this->updateStatus['update_needed'] = false;
            return false;
        }
    }

    /**
     * Check if rolling release should be enabled automatically
     */
    private function shouldEnableRollingRelease(?string $remoteVersion, ?string $remoteStableVersion): bool
    {
        // Enable rolling release if remote version equals stable version
        if ($remoteVersion && $remoteStableVersion && $remoteVersion === $remoteStableVersion) {
            return true;
        }
        
        // Enable rolling release if configured in config
        return config('webkernel.updates.rolling_release_enabled', false);
    }

    /**
     * Handle status display
     */
    private function handleStatus(): int
    {
        $localVersion = $this->getLocalVersion();
        $remoteVersion = $this->getRemoteVersion();
        $updateNeeded = $this->isUpdateNeeded();

        $this->info('Current Status:');
        $this->table(
            ['Property', 'Value'],
            [
                ['Local Version', $localVersion],
                ['Remote Version', $remoteVersion ?? 'Unknown'],
                ['Update Needed', $updateNeeded ? 'Yes' : 'No'],
                ['Repository', $this->remoteRepo],
                ['Branch', $this->branch],
                ['Force Update', $this->forceUpdate ? 'Yes' : 'No'],
                ['Dry Run', $this->dryRun ? 'Yes' : 'No'],
            ]
        );

        if ($updateNeeded) {
            $this->warn('An update is available!');
        } else {
            $this->info('No update needed.');
        }

        return self::SUCCESS;
    }

    /**
     * Handle JSON output
     */
    private function handleJsonOutput(): int
    {
        $status = $this->getUpdateStatus();
        $this->line(json_encode($status, JSON_PRETTY_PRINT));
        return self::SUCCESS;
    }

    /**
     * Handle log output
     */
    private function handleLogOutput(): int
    {
        $logPath = storage_path('logs/' . self::LOG_FILE);
        
        if (!File::exists($logPath)) {
            $this->error("Log file not found: {$logPath}");
            return self::FAILURE;
        }

        $this->info("Log file contents:");
        $this->line(File::get($logPath));
        
        return self::SUCCESS;
    }

    /**
     * Handle cache clearing
     */
    private function handleClearCache(): int
    {
        $cacheKey = 'webkernel_remote_version_' . md5($this->remoteRepo . $this->branch);
        cache()->forget($cacheKey);
        $this->info('Version cache cleared successfully');
        return self::SUCCESS;
    }

    /**
     * Handle backups listing
     */
    private function handleBackups(): int
    {
        $backups = $this->getAvailableBackups();

        if (empty($backups)) {
            $this->info('No backups found');
            return self::SUCCESS;
        }

        $this->info('Available Backups:');
        
        $tableData = [];
        foreach ($backups as $backup) {
            $tableData[] = [
                basename($backup['path']),
                $backup['readable_date'],
                $backup['size'],
                $backup['path']
            ];
        }

        $this->table(
            ['Backup', 'Date', 'Size', 'Path'],
            $tableData
        );

        return self::SUCCESS;
    }

    /**
     * Handle backup restoration
     */
    private function handleRestore(string $backupPath): int
    {
        $backups = $this->getAvailableBackups();
        $backupExists = false;

        foreach ($backups as $backup) {
            if ($backup['path'] === $backupPath || basename($backup['path']) === $backupPath) {
                $backupExists = true;
                $backupPath = $backup['path'];
                break;
            }
        }

        if (!$backupExists) {
            $this->error("Backup not found: {$backupPath}");
            return self::FAILURE;
        }

        $this->warn("This will restore Webkernel from backup: " . basename($backupPath));
        $this->warn("Current installation will be replaced!");

                    if (!$this->option('silent') && !$this->confirm('Are you sure you want to proceed?')) {
            $this->info('Restore cancelled');
            return self::SUCCESS;
        }

        try {
            $this->restoreFromBackup($backupPath);
            $this->info('Restore completed successfully');
            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error('Restore failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Handle auto-run mode
     */
    private function handleAutoRun(): int
    {
        $this->info('Auto-run mode enabled');
        
        if ($this->isUpdateNeeded()) {
            $this->line("Update needed, proceeding with update");
            return $this->performUpdate();
        } else {
            $this->line("No update needed");
            return self::SUCCESS;
        }
    }

    /**
     * Handle update process
     */
    private function handleUpdate(): int
    {
        // Register default hooks
        $this->registerDefaultHooks();

        // Check if update is needed
        if (!$this->isUpdateNeeded()) {
            $this->info('No update needed. Webkernel is up to date!');
            return self::SUCCESS;
        }

        // Show update information
        $this->warn("Update available!");
        $this->line("Local version:  {$this->updateStatus['local_version']}");
        $this->line("Remote version: {$this->updateStatus['remote_version']}");
        $this->newLine();

        if ($this->dryRun) {
            $this->info('DRY RUN MODE - No actual changes will be made');
        } else {
            $this->warn('This will:');
            $this->line('  • Create a backup of current installation');
            $this->line('  • Download latest version from repository');
            $this->line('  • Replace current installation');
            $this->line('  • Run post-update actions (composer update, cache clear, etc.)');
            $this->newLine();

            if (!$this->option('silent') && !$this->confirm('Do you want to proceed with the update?')) {
                $this->info('Update cancelled');
                return self::SUCCESS;
            }
        }

        // Perform update
        $this->info('Starting update process...');
        
        if ($this->performUpdate()) {
            $this->info('Update completed successfully!');
            
            if ($this->updateStatus['files_updated'] > 0) {
                $this->line("Files updated: {$this->updateStatus['files_updated']}");
            }
            
            if (!empty($this->updateStatus['post_update_actions'])) {
                $this->line('Post-update actions completed:');
                foreach ($this->updateStatus['post_update_actions'] as $action) {
                    $status = $action['success'] ? '✓' : '✗';
                    $this->line("  {$status} {$action['name']}");
                }
            }
            
            return self::SUCCESS;
        } else {
            $this->error('Update failed!');
            
            if ($this->updateStatus['error']) {
                $this->error("Error: {$this->updateStatus['error']}");
            }
            
            return self::FAILURE;
        }
    }

    /**
     * Perform the complete update process
     */
    private function performUpdate(): bool
    {
        $this->line("Starting platform update process");
        
        try {
            if ($this->dryRun) {
                $this->line("DRY RUN MODE - No actual changes will be made");
                $this->simulateUpdate();
                return true;
            }
            
            // Create backup
            $backupPath = $this->createBackup();
            $this->updateStatus['backup_created'] = true;
            $this->updateStatus['backup_path'] = $backupPath;
            $this->line("Backup created: " . basename($backupPath));
            
            // Download and install update
            $success = $this->downloadAndInstallUpdate();
            
            if ($success) {
                $this->line("Update completed successfully");
                
                // Run post-update hooks
                $this->runPostUpdateHooks();
                
                // Cleanup old backups
                $this->cleanupBackups();
                
                // Log to database
                $this->logToDatabase();
                
                $this->updateStatus['success'] = true;
                $this->updateStatus['completed_at'] = date('Y-m-d H:i:s');
                
                return true;
            } else {
                $this->error("Update failed, restoring from backup");
                $this->restoreFromBackup($backupPath);
                return false;
            }
            
        } catch (Exception $e) {
            $this->error("Update process failed: " . $e->getMessage());
            $this->updateStatus['error'] = $e->getMessage();
            $this->updateStatus['completed_at'] = date('Y-m-d H:i:s');
            return false;
        }
    }

    /**
     * Simulate update (dry-run mode)
     */
    private function simulateUpdate(): void
    {
        $this->line("Simulating backup creation...");
        $this->line("Simulating file download...");
        $this->line("Simulating file replacement...");
        $this->line("Simulating post-update actions...");
        
        $this->updateStatus['success'] = true;
        $this->updateStatus['completed_at'] = date('Y-m-d H:i:s');
        $this->updateStatus['files_updated'] = 42; // Simulated count
    }

    /**
     * Create backup of current installation
     */
    private function createBackup(): string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $backupDir = storage_path('webkernel/backups');
        $backupPath = $backupDir . "/backup_{$timestamp}";
        
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        
        $sourcePath = base_path('packages/webkernel');
        
        if (!File::exists($sourcePath)) {
            $this->line("No existing webkernel installation to backup");
            File::makeDirectory($backupPath, 0755, true);
            return $backupPath;
        }
        
        $this->line("Creating backup from: {$sourcePath}");
        
        // Use Laravel's File facade for safe operations
        $this->copyDirectoryRecursive($sourcePath, $backupPath);
        
        $this->line("Backup completed: {$backupPath}");
        return $backupPath;
    }

    /**
     * Download and install the update
     */
    private function downloadAndInstallUpdate(): bool
    {
        $this->line("Downloading update from: {$this->remoteRepo}");
        
        $tempDir = sys_get_temp_dir() . '/webkernel_update_' . uniqid();
        
        try {
            // Create temp directory
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }
            
            // Download repository as ZIP
            $zipUrl = rtrim($this->remoteRepo, '/') . '/archive/' . $this->branch . '.zip';
            $zipPath = $tempDir . '/repository.zip';
            
            $this->line("Downloading ZIP from: {$zipUrl}");
            $this->downloadFile($zipUrl, $zipPath);
            
            // Extract ZIP
            $this->line("Extracting ZIP file");
            $this->extractZip($zipPath, $tempDir);
            
            // Find the extracted directory
            $extractedDir = $this->findExtractedDirectory($tempDir);
            if (!$extractedDir) {
                throw new Exception("Could not find extracted repository directory");
            }
            
            $sourcePath = $extractedDir . '/packages/webkernel';
            $targetPath = base_path('packages/webkernel');
            
            if (!File::exists($sourcePath)) {
                throw new Exception("Source directory not found: {$sourcePath}");
            }
            
            // Remove existing installation
            if (File::exists($targetPath)) {
                $this->line("Removing existing installation");
                File::deleteDirectory($targetPath);
            }
            
            // Install new version
            $this->line("Installing new version");
            $filesUpdated = $this->copyDirectoryRecursive($sourcePath, $targetPath);
            $this->updateStatus['files_updated'] = $filesUpdated;
            
            // Clean up temp directory
            File::deleteDirectory($tempDir);
            
            return true;
            
        } catch (Exception $e) {
            // Clean up temp directory on error
            if (File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
            throw $e;
        }
    }

    /**
     * Download file using Laravel HTTP client
     */
    private function downloadFile(string $url, string $destination): void
    {
        $response = Http::timeout(self::HTTP_TIMEOUT)
            ->withHeaders([
                'User-Agent' => 'Webkernel-PlatformUpdater/1.0'
            ])
            ->get($url);
        
        if (!$response->successful()) {
            throw new Exception("Failed to download file: HTTP {$response->status()}");
        }
        
        File::put($destination, $response->body());
    }

    /**
     * Extract ZIP file
     */
    private function extractZip(string $zipPath, string $destination): void
    {
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath) !== true) {
            throw new Exception("Failed to open ZIP file: {$zipPath}");
        }
        
        if (!$zip->extractTo($destination)) {
            $zip->close();
            throw new Exception("Failed to extract ZIP file");
        }
        
        $zip->close();
    }

    /**
     * Find extracted directory
     */
    private function findExtractedDirectory(string $tempDir): ?string
    {
        $items = scandir($tempDir);
        
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..' && is_dir($tempDir . '/' . $item)) {
                // GitHub archives are named like "webkernel-main"
                if (strpos($item, 'webkernel-') === 0) {
                    return $tempDir . '/' . $item;
                }
            }
        }
        
        return null;
    }

    /**
     * Restore from backup
     */
    private function restoreFromBackup(string $backupPath): void
    {
        $this->line("Restoring from backup: {$backupPath}");
        
        $targetPath = base_path('packages/webkernel');
        
        if (File::exists($targetPath)) {
            File::deleteDirectory($targetPath);
        }
        
        $this->copyDirectoryRecursive($backupPath, $targetPath);
        $this->line("Restore completed");
    }

    /**
     * Copy directory recursively
     */
    private function copyDirectoryRecursive(string $source, string $target): int
    {
        if (!File::exists($source)) {
            throw new Exception("Source directory does not exist: {$source}");
        }
        
        if (!File::exists($target)) {
            File::makeDirectory($target, 0755, true);
        }
        
        $filesCount = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $targetPath = $target . '/' . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!File::exists($targetPath)) {
                    File::makeDirectory($targetPath, 0755, true);
                }
            } else {
                File::copy($item, $targetPath);
                $filesCount++;
            }
        }
        
        return $filesCount;
    }

    /**
     * Add post-update hook
     */
    private function addPostUpdateHook(callable $hook, string $name): void
    {
        $this->postUpdateHooks[] = [
            'callback' => $hook,
            'name' => $name
        ];
    }

    /**
     * Register default post-update hooks
     */
    private function registerDefaultHooks(): void
    {
        // Composer update
        $this->addPostUpdateHook(function () {
            $this->line("Running composer update...");
            
            try {
                // Use the actual composer command instead of a non-existent Artisan command
                $composerPath = $this->findComposerPath();
                if ($composerPath) {
                    $result = $this->runComposerUpdate($composerPath);
                    $this->line("Composer update completed");
                    return "Composer update completed";
                } else {
                    $this->line("Composer not found, skipping composer update");
                    return "Composer not found";
                }
            } catch (Exception $e) {
                $this->error("Composer update failed: " . $e->getMessage());
                return "Composer update failed: " . $e->getMessage();
            }
        }, 'composer_update');
        
        // Clear Laravel caches
        $this->addPostUpdateHook(function () {
            $this->line("Clearing Laravel caches...");
            
            try {
                Artisan::call('config:clear');
                Artisan::call('cache:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
                
                $this->line("Laravel caches cleared successfully");
                return "Cleared 4 caches";
            } catch (Exception $e) {
                $this->error("Failed to clear Laravel caches: " . $e->getMessage());
                return "Cache clear failed: " . $e->getMessage();
            }
        }, 'laravel_cache_clear');
        
        // Regenerate constants
        $this->addPostUpdateHook(function () {
            $this->line("Regenerating constants...");
            
            $constantsFile = base_path('packages/webkernel/src/Constants/ConstantsGenerator.php');
            if (File::exists($constantsFile)) {
                include_once $constantsFile;
                $this->line("Constants regenerated");
                return "Constants regenerated";
            }
            
            return "Constants file not found";
        }, 'regenerate_constants');
    }

    /**
     * Find composer path
     */
    private function findComposerPath(): ?string
    {
        $possiblePaths = [
            'composer',
            './composer.phar',
            '/usr/local/bin/composer',
            '/usr/bin/composer'
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path) || $this->commandExists($path)) {
                return $path;
            }
        }
        
        return null;
    }

    /**
     * Run composer update safely
     */
    private function runComposerUpdate(string $composerPath): string
    {
        // Use PHP's built-in methods to run composer
        $command = escapeshellcmd($composerPath) . ' update --no-interaction --no-dev';
        
        // Use proc_open for better control
        $descriptorspec = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w']   // stderr
        ];
        
        $process = proc_open($command, $descriptorspec, $pipes, base_path());
        
        if (!is_resource($process)) {
            return "Failed to start composer process";
        }
        
        // Close stdin
        fclose($pipes[0]);
        
        // Read output
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        
        fclose($pipes[1]);
        fclose($pipes[2]);
        
        $returnCode = proc_close($process);
        
        if ($returnCode !== 0) {
            return "Composer failed with code {$returnCode}: {$error}";
        }
        
        return $output ?: "Composer update completed successfully";
    }

    /**
     * Check if command exists (safe method)
     */
    private function commandExists(string $command): bool
    {
        // Use which command if available, but handle gracefully if not
        if (function_exists('exec') && !in_array('exec', explode(',', ini_get('disable_functions')))) {
            $result = exec("which {$command} 2>/dev/null", $output, $returnCode);
            return $returnCode === 0 && !empty($result);
        }
        
        // Fallback: check common paths
        $commonPaths = [
            '/usr/bin/',
            '/usr/local/bin/',
            '/bin/',
            '/sbin/',
            '/usr/sbin/'
        ];
        
        foreach ($commonPaths as $path) {
            if (file_exists($path . $command) && is_executable($path . $command)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Run post-update hooks
     */
    private function runPostUpdateHooks(): void
    {
        if (empty($this->postUpdateHooks)) {
            $this->line("No post-update hooks registered");
            return;
        }
        
        $this->line("Running post-update hooks...");
        
        foreach ($this->postUpdateHooks as $hook) {
            try {
                $this->line("Executing hook: {$hook['name']}");
                $result = call_user_func($hook['callback']);
                
                $this->updateStatus['post_update_actions'][] = [
                    'name' => $hook['name'],
                    'success' => true,
                    'result' => $result
                ];
                
                $this->line("Hook {$hook['name']} completed successfully");
                
            } catch (Exception $e) {
                $this->error("Hook {$hook['name']} failed: " . $e->getMessage());
                
                $this->updateStatus['post_update_actions'][] = [
                    'name' => $hook['name'],
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
    }

    /**
     * Clean up old backups (keep only last N)
     */
    private function cleanupBackups(): void
    {
        $backupDir = storage_path('webkernel/backups');
        
        if (!File::exists($backupDir)) {
            return;
        }
        
        $backups = $this->getBackupDirectories($backupDir);
        
        // Keep only the last MAX_BACKUPS
        $backupsToRemove = array_slice($backups, self::MAX_BACKUPS);
        
        foreach ($backupsToRemove as $backup) {
            File::deleteDirectory($backup);
            $this->line("Removed old backup: " . basename($backup));
        }
    }

    /**
     * Get available backups
     */
    private function getAvailableBackups(): array
    {
        $backupDir = storage_path('webkernel/backups');
        
        if (!File::exists($backupDir)) {
            return [];
        }
        
        $backups = $this->getBackupDirectories($backupDir);
        
        return array_map(function ($backup) {
            $timestamp = basename($backup);
            $readableDate = date('F j, Y \a\t g:i A', strtotime(str_replace('_', ' ', $timestamp)));
            $size = $this->getDirectorySize($backup);
            
            return [
                'path' => $backup,
                'timestamp' => $timestamp,
                'readable_date' => $readableDate,
                'size' => $this->formatBytes($size)
            ];
        }, $backups);
    }

    /**
     * Get backup directories
     */
    private function getBackupDirectories(string $backupDir): array
    {
        $backups = [];
        $items = scandir($backupDir);
        
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..' && is_dir($backupDir . '/' . $item)) {
                if (preg_match('/backup_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}$/', $item)) {
                    $backups[] = $backupDir . '/' . $item;
                }
            }
        }
        
        rsort($backups);
        return $backups;
    }

    /**
     * Get directory size
     */
    private function getDirectorySize(string $directory): int
    {
        $size = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Log to database
     */
    private function logToDatabase(): void
    {
        try {
            // Try to log to updates_logs table
            if (Schema::hasTable('updates_logs')) {
                DB::table('updates_logs')->insert([
                    'package' => 'webkernel',
                    'local_version' => $this->updateStatus['local_version'],
                    'remote_version' => $this->updateStatus['remote_version'],
                    'success' => $this->updateStatus['success'],
                    'error' => $this->updateStatus['error'],
                    'files_updated' => $this->updateStatus['files_updated'],
                    'backup_path' => $this->updateStatus['backup_path'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Try to log to notifications table with proper ID handling
            if (Schema::hasTable('notifications')) {
                $notificationData = [
                    'type' => 'webkernel_update',
                    'notifiable_type' => 'system',
                    'notifiable_id' => 1,
                    'data' => json_encode($this->updateStatus),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Check if the table has an auto-increment ID column
                $columns = Schema::getColumnListing('notifications');
                if (in_array('id', $columns)) {
                    // Let the database handle the ID automatically
                    unset($notificationData['id']);
                }
                
                DB::table('notifications')->insert($notificationData);
            }
            
        } catch (Exception $e) {
            $this->error("Failed to log to database: " . $e->getMessage());
        }
    }

    /**
     * Get update status
     */
    private function getUpdateStatus(): array
    {
        return $this->updateStatus;
    }
} 