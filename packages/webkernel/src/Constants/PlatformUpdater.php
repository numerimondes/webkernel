<?php

declare(strict_types=1);

namespace Webkernel\Constants;

use Exception;
use Throwable;

/**
 * Platform Updater for Webkernel
 * 
 * Universal updater class compatible with both Laravel and CLI environments.
 * Handles version comparison, updates, backups, and post-update actions.
 * 
 * @author El Moumen Yassine
 * @email yassine@numerimondes.com
 * @website www.numerimondes.com
 * @license MPL-2.0
 */
class PlatformUpdater
{
    /**
     * Configuration constants1
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
    private bool $isLaravel = false;
    private bool $isCLI = false;
    private ?object $command = null;
    private array $postUpdateHooks = [];
    private array $updateStatus = [];
    private string $logPath;
    
    /**
     * Constructor
     */
    public function __construct(
        string $remoteRepo = null,
        string $branch = null,
        ?object $command = null
    ) {
        $this->remoteRepo = $remoteRepo ?? self::DEFAULT_GITHUB_REPO;
        $this->branch = $branch ?? self::DEFAULT_BRANCH;
        $this->command = $command;
        $this->isLaravel = $this->detectLaravel();
        $this->isCLI = PHP_SAPI === 'cli';
        $this->logPath = $this->getLogPath();
        
        $this->initializeUpdateStatus();
    }
    
    /**
     * Detect if Laravel is available
     */
    private function detectLaravel(): bool
    {
        return function_exists('app') && 
               class_exists('Illuminate\Support\Facades\Log') &&
               class_exists('Illuminate\Support\Facades\File');
    }
    
    /**
     * Get log file path
     */
    private function getLogPath(): string
    {
        if ($this->isLaravel) {
            return storage_path('logs/' . self::LOG_FILE);
        }
        
        return sys_get_temp_dir() . '/' . self::LOG_FILE;
    }
    
    /**
     * Get log file path (public accessor)
     */
    public function getLogFilePath(): string
    {
        return $this->getLogPath();
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
            'is_laravel' => $this->isLaravel,
            'is_cli' => $this->isCLI,
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
     * Set force update flag
     */
    public function setForceUpdate(bool $force): self
    {
        $this->forceUpdate = $force;
        $this->updateStatus['force_update'] = $force;
        return $this;
    }
    
    /**
     * Set dry run flag
     */
    public function setDryRun(bool $dryRun): self
    {
        $this->dryRun = $dryRun;
        $this->updateStatus['dry_run'] = $dryRun;
        return $this;
    }
    
    /**
     * Add post-update hook
     */
    public function addPostUpdateHook(callable $hook, string $name = null): self
    {
        $this->postUpdateHooks[] = [
            'callback' => $hook,
            'name' => $name ?? 'hook_' . count($this->postUpdateHooks)
        ];
        return $this;
    }
    
    /**
     * Get local Webkernel version
     */
    public function getLocalVersion(): string
    {
        if (defined('WEBKERNEL_VERSION')) {
            return WEBKERNEL_VERSION;
        }
        
        // Try to read from Core.php file
        $coreFilePath = $this->getBasePath() . '/' . self::CORE_FILE_PATH;
        if ($this->fileExists($coreFilePath)) {
            $content = $this->fileGetContents($coreFilePath);
            if (preg_match("/const\s+WEBKERNEL_VERSION\s*=\s*['\"]([^'\"]+)['\"]/", $content, $matches)) {
                return $matches[1];
            }
        }
        
        return '0.0.0';
    }
    
    /**
     * Get remote Webkernel version
     */
    public function getRemoteVersion(): ?string
    {
        $cacheKey = 'webkernel_remote_version_' . md5($this->remoteRepo . $this->branch);
        
        // Check cache if Laravel is available
        if ($this->isLaravel && $this->cacheHas($cacheKey)) {
            $this->log("Using cached remote version");
            return $this->cacheGet($cacheKey);
        }
        
        $this->log("Fetching remote version from: {$this->remoteRepo}");
        
        try {
            $remoteUrl = rtrim($this->remoteRepo, '/') . '/raw/' . $this->branch . '/' . self::REMOTE_CORE_FILE_URL;
            
            $content = $this->fetchRemoteContent($remoteUrl);
            
            if (preg_match("/const\s+WEBKERNEL_VERSION\s*=\s*['\"]([^'\"]+)['\"]/", $content, $matches)) {
                $remoteVersion = $matches[1];
                
                // Cache the result if Laravel is available
                if ($this->isLaravel) {
                    $this->cachePut($cacheKey, $remoteVersion, self::VERSION_CACHE_TTL);
                }
                
                $this->log("Remote version found: {$remoteVersion}");
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
     * Fetch remote content
     */
    private function fetchRemoteContent(string $url): string
    {
        if ($this->isLaravel) {
            $response = \Illuminate\Support\Facades\Http::timeout(self::HTTP_TIMEOUT)
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ])
                ->get($url);
            
            if (!$response->successful()) {
                throw new Exception("HTTP request failed: {$response->status()}");
            }
            
            return $response->body();
        }
        
        // Fallback to native PHP
        $context = stream_context_create([
            'http' => [
                'timeout' => self::HTTP_TIMEOUT,
                'header' => [
                    'Cache-Control: no-cache, no-store, must-revalidate',
                    'Pragma: no-cache',
                    'Expires: 0'
                ]
            ]
        ]);
        
        $content = file_get_contents($url, false, $context);
        
        if ($content === false) {
            throw new Exception("Failed to fetch content from: {$url}");
        }
        
        return $content;
    }
    
    /**
     * Compare versions and determine if update is needed
     */
    public function isUpdateNeeded(): bool
    {
        if ($this->forceUpdate) {
            $this->log("Force update requested");
            return true;
        }
        
        $localVersion = $this->getLocalVersion();
        $remoteVersion = $this->getRemoteVersion();
        
        $this->updateStatus['local_version'] = $localVersion;
        $this->updateStatus['remote_version'] = $remoteVersion;
        
        if (!$remoteVersion) {
            $this->error("Could not determine remote version");
            return false;
        }
        
        $this->log("Local version: {$localVersion}");
        $this->log("Remote version: {$remoteVersion}");
        
        $comparison = version_compare($remoteVersion, $localVersion);
        
        if ($comparison > 0) {
            $this->log("Update needed: remote version is newer");
            $this->updateStatus['update_needed'] = true;
            return true;
        } elseif ($comparison === 0) {
            $this->log("Versions are identical");
            $this->updateStatus['update_needed'] = false;
            return false;
        } else {
            $this->log("Local version is newer than remote");
            $this->updateStatus['update_needed'] = false;
            return false;
        }
    }
    
    /**
     * Perform the complete update process
     */
    public function performUpdate(): bool
    {
        $this->log("Starting platform update process");
        
        try {
            if ($this->dryRun) {
                $this->log("DRY RUN MODE - No actual changes will be made");
                $this->simulateUpdate();
                return true;
            }
            
            // Create backup
            $backupPath = $this->createBackup();
            $this->updateStatus['backup_created'] = true;
            $this->updateStatus['backup_path'] = $backupPath;
            $this->log("Backup created: " . basename($backupPath));
            
            // Download and install update
            $success = $this->downloadAndInstallUpdate();
            
            if ($success) {
                $this->log("Update completed successfully");
                
                // Run post-update hooks
                $this->runPostUpdateHooks();
                
                // Cleanup old backups
                $this->cleanupBackups();
                
                // Log to database if available
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
        $this->log("Simulating backup creation...");
        $this->log("Simulating Git sparse checkout...");
        $this->log("Simulating file replacement...");
        $this->log("Simulating post-update actions...");
        
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
        $backupDir = $this->getBackupDir();
        $backupPath = $backupDir . "/backup_{$timestamp}";
        
        if (!$this->directoryExists($backupDir)) {
            $this->createDirectory($backupDir, 0755, true);
        }
        
        $sourcePath = $this->getBasePath() . '/packages/webkernel';
        
        if (!$this->directoryExists($sourcePath)) {
            $this->log("No existing webkernel installation to backup");
            $this->createDirectory($backupPath, 0755, true);
            return $backupPath;
        }
        
        $this->log("Creating backup from: {$sourcePath}");
        
        // Use rsync if available, otherwise use cp
        if ($this->commandExists('rsync')) {
            $cmd = "rsync -av --exclude='.git' --exclude='node_modules' --exclude='vendor' '{$sourcePath}/' '{$backupPath}/'";
        } else {
            $cmd = "cp -r '{$sourcePath}' '{$backupPath}'";
        }
        
        $result = shell_exec($cmd . ' 2>&1');
        
        if ($result !== null && strpos($result, 'error') !== false) {
            throw new Exception("Backup failed: {$result}");
        }
        
        $this->log("Backup completed: {$backupPath}");
        return $backupPath;
    }
    
    /**
     * Download and install the update
     */
    private function downloadAndInstallUpdate(): bool
    {
        $this->log("Downloading update from: {$this->remoteRepo}");
        
        $tempDir = sys_get_temp_dir() . '/webkernel_update_' . uniqid();
        
        try {
            // Clone repository with sparse checkout
            $commands = [
                "git clone --filter=blob:none --sparse {$this->remoteRepo} {$tempDir}",
                "cd {$tempDir} && git sparse-checkout set packages/webkernel",
                "cd {$tempDir} && git checkout {$this->branch}"
            ];
            
            foreach ($commands as $cmd) {
                $this->log("Executing: " . explode(' && ', $cmd)[count(explode(' && ', $cmd)) - 1]);
                $result = shell_exec($cmd . " 2>&1");
                
                if (strpos($result, 'fatal') !== false || strpos($result, 'error') !== false) {
                    throw new Exception("Git command failed: {$result}");
                }
            }
            
            $sourcePath = $tempDir . '/packages/webkernel';
            $targetPath = $this->getBasePath() . '/packages/webkernel';
            
            if (!$this->directoryExists($sourcePath)) {
                throw new Exception("Source directory not found: {$sourcePath}");
            }
            
            // Remove existing installation
            if ($this->directoryExists($targetPath)) {
                $this->log("Removing existing installation");
                $this->deleteDirectory($targetPath);
            }
            
            // Install new version
            $this->log("Installing new version");
            $filesUpdated = $this->copyDirectory($sourcePath, $targetPath);
            $this->updateStatus['files_updated'] = $filesUpdated;
            
            // Clean up temp directory
            $this->deleteDirectory($tempDir);
            
            return true;
            
        } catch (Exception $e) {
            // Clean up temp directory on error
            if ($this->directoryExists($tempDir)) {
                $this->deleteDirectory($tempDir);
            }
            throw $e;
        }
    }
    
    /**
     * Restore from backup
     */
    private function restoreFromBackup(string $backupPath): void
    {
        $this->log("Restoring from backup: {$backupPath}");
        
        $targetPath = $this->getBasePath() . '/packages/webkernel';
        
        if ($this->directoryExists($targetPath)) {
            $this->deleteDirectory($targetPath);
        }
        
        $this->copyDirectory($backupPath, $targetPath);
        $this->log("Restore completed");
    }
    
    /**
     * Restore from backup (public method)
     */
    public function restoreFromBackupPath(string $backupPath): bool
    {
        try {
            $this->restoreFromBackup($backupPath);
            return true;
        } catch (Exception $e) {
            $this->error("Restore failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Run post-update hooks
     */
    private function runPostUpdateHooks(): void
    {
        if (empty($this->postUpdateHooks)) {
            $this->log("No post-update hooks registered");
            return;
        }
        
        $this->log("Running post-update hooks...");
        
        foreach ($this->postUpdateHooks as $hook) {
            try {
                $this->log("Executing hook: {$hook['name']}");
                $result = call_user_func($hook['callback'], $this);
                
                $this->updateStatus['post_update_actions'][] = [
                    'name' => $hook['name'],
                    'success' => true,
                    'result' => $result
                ];
                
                $this->log("Hook {$hook['name']} completed successfully");
                
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
     * Register default post-update hooks
     */
    public function registerDefaultHooks(): self
    {
        // Composer update
        $this->addPostUpdateHook(function (PlatformUpdater $updater) {
            $updater->log("Running composer update...");
            $result = shell_exec('composer update --no-interaction 2>&1');
            $updater->log("Composer update completed");
            return $result;
        }, 'composer_update');
        
        // Clear Laravel caches if available
        if ($this->isLaravel) {
            $this->addPostUpdateHook(function (PlatformUpdater $updater) {
                $updater->log("Clearing Laravel caches...");
                $commands = [
                    'config:clear',
                    'cache:clear',
                    'route:clear',
                    'view:clear'
                ];
                
                foreach ($commands as $command) {
                    $result = shell_exec("php artisan {$command} 2>&1");
                    $updater->log("Executed: {$command}");
                }
                
                return "Cleared " . count($commands) . " caches";
            }, 'laravel_cache_clear');
        }
        
        // Regenerate constants
        $this->addPostUpdateHook(function (PlatformUpdater $updater) {
            $updater->log("Regenerating constants...");
            
            $constantsFile = $updater->getBasePath() . '/packages/webkernel/src/Constants/ConstantsGenerator.php';
            if (file_exists($constantsFile)) {
                include_once $constantsFile;
                $updater->log("Constants regenerated");
                return "Constants regenerated";
            }
            
            return "Constants file not found";
        }, 'regenerate_constants');
        
        return $this;
    }
    
    /**
     * Clean up old backups (keep only last N)
     */
    private function cleanupBackups(): void
    {
        $backupDir = $this->getBackupDir();
        
        if (!$this->directoryExists($backupDir)) {
            return;
        }
        
        $backups = $this->getBackupDirectories($backupDir);
        
        // Keep only the last MAX_BACKUPS
        $backupsToRemove = array_slice($backups, self::MAX_BACKUPS);
        
        foreach ($backupsToRemove as $backup) {
            $this->deleteDirectory($backup);
            $this->log("Removed old backup: " . basename($backup));
        }
    }
    
    /**
     * Get available backups
     */
    public function getAvailableBackups(): array
    {
        $backupDir = $this->getBackupDir();
        
        if (!$this->directoryExists($backupDir)) {
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
     * Log to database if available
     */
    private function logToDatabase(): void
    {
        if (!$this->isLaravel) {
            return;
        }
        
        try {
            // Try to log to updates_logs table
            if ($this->tableExists('updates_logs')) {
                \Illuminate\Support\Facades\DB::table('updates_logs')->insert([
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
            
            // Try to log to notifications table
            if ($this->tableExists('notifications')) {
                \Illuminate\Support\Facades\DB::table('notifications')->insert([
                    'type' => 'webkernel_update',
                    'notifiable_type' => 'system',
                    'notifiable_id' => 1,
                    'data' => json_encode($this->updateStatus),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
        } catch (Exception $e) {
            $this->error("Failed to log to database: " . $e->getMessage());
        }
    }
    
    /**
     * Get update status as array
     */
    public function getUpdateStatus(): array
    {
        return $this->updateStatus;
    }
    
    /**
     * Get update status as JSON
     */
    public function getUpdateStatusJson(): string
    {
        return json_encode($this->updateStatus, JSON_PRETTY_PRINT);
    }
    
    /**
     * Clear version cache
     */
    public function clearVersionCache(): void
    {
        if ($this->isLaravel) {
            $cacheKey = 'webkernel_remote_version_' . md5($this->remoteRepo . $this->branch);
            $this->cacheForget($cacheKey);
        }
        $this->log("Version cache cleared");
    }
    
    /**
     * Auto-run method for cron jobs
     */
    public function autoRun(): bool
    {
        if (!$this->isCLI) {
            $this->error("Auto-run is only available in CLI mode");
            return false;
        }
        
        $this->log("Auto-run started");
        
        if ($this->isUpdateNeeded()) {
            $this->log("Update needed, proceeding with update");
            return $this->performUpdate();
        } else {
            $this->log("No update needed");
            return true;
        }
    }
    
    /**
     * Utility methods for file operations
     */
    private function getBasePath(): string
    {
        if ($this->isLaravel) {
            return base_path();
        }
        
        // Try to detect Laravel base path
        $currentDir = __DIR__;
        while ($currentDir !== dirname($currentDir)) {
            if (file_exists($currentDir . '/artisan')) {
                return $currentDir;
            }
            $currentDir = dirname($currentDir);
        }
        
        return getcwd();
    }
    
    private function getBackupDir(): string
    {
        if ($this->isLaravel) {
            return storage_path('webkernel/backups');
        }
        
        return $this->getBasePath() . '/storage/webkernel/backups';
    }
    
    private function fileExists(string $path): bool
    {
        if ($this->isLaravel) {
            return \Illuminate\Support\Facades\File::exists($path);
        }
        
        return file_exists($path);
    }
    
    private function directoryExists(string $path): bool
    {
        if ($this->isLaravel) {
            return \Illuminate\Support\Facades\File::isDirectory($path);
        }
        
        return is_dir($path);
    }
    
    private function fileGetContents(string $path): string
    {
        if ($this->isLaravel) {
            return \Illuminate\Support\Facades\File::get($path);
        }
        
        return file_get_contents($path);
    }
    
    private function createDirectory(string $path, int $mode = 0755, bool $recursive = false): bool
    {
        if ($this->isLaravel) {
            return \Illuminate\Support\Facades\File::makeDirectory($path, $mode, $recursive);
        }
        
        return mkdir($path, $mode, $recursive);
    }
    
    private function deleteDirectory(string $path): bool
    {
        if ($this->isLaravel) {
            return \Illuminate\Support\Facades\File::deleteDirectory($path);
        }
        
        return $this->recursiveDelete($path);
    }
    
    private function recursiveDelete(string $path): bool
    {
        if (!is_dir($path)) {
            return false;
        }
        
        $files = array_diff(scandir($path), ['.', '..']);
        
        foreach ($files as $file) {
            $filePath = $path . '/' . $file;
            
            if (is_dir($filePath)) {
                $this->recursiveDelete($filePath);
            } else {
                unlink($filePath);
            }
        }
        
        return rmdir($path);
    }
    
    private function copyDirectory(string $source, string $target): int
    {
        if (!$this->directoryExists($source)) {
            throw new Exception("Source directory does not exist: {$source}");
        }
        
        if (!$this->directoryExists($target)) {
            $this->createDirectory($target, 0755, true);
        }
        
        $filesCount = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $targetPath = $target . '/' . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!$this->directoryExists($targetPath)) {
                    $this->createDirectory($targetPath, 0755, true);
                }
            } else {
                copy($item, $targetPath);
                $filesCount++;
            }
        }
        
        return $filesCount;
    }
    
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
    
    private function getDirectorySize(string $directory): int
    {
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }
    
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    private function commandExists(string $command): bool
    {
        $result = shell_exec("which {$command} 2>/dev/null");
        return !empty($result);
    }
    
    private function tableExists(string $table): bool
    {
        if (!$this->isLaravel) {
            return false;
        }
        
        try {
            return \Illuminate\Support\Facades\Schema::hasTable($table);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Cache methods (Laravel only)
     */
    private function cacheHas(string $key): bool
    {
        return cache()->has($key);
    }
    
    private function cacheGet(string $key)
    {
        return cache()->get($key);
    }
    
    private function cachePut(string $key, $value, int $ttl): void
    {
        cache()->put($key, $value, $ttl);
    }
    
    private function cacheForget(string $key): void
    {
        cache()->forget($key);
    }
    
    /**
     * Logging methods
     */
    private function log(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] INFO: {$message}" . PHP_EOL;
        
        // Log to Laravel if available
        if ($this->isLaravel) {
            \Illuminate\Support\Facades\Log::info("Webkernel PlatformUpdater: {$message}");
        }
        
        // Log to file
        file_put_contents($this->logPath, $logMessage, FILE_APPEND | LOCK_EX);
        
        // Output to console if CLI
        if ($this->isCLI) {
            echo $logMessage;
        }
        
        // Output to command if available
        if ($this->command && method_exists($this->command, 'line')) {
            $this->command->line("[INFO] {$message}");
        }
    }
    
    private function error(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] ERROR: {$message}" . PHP_EOL;
        
        // Log to Laravel if available
        if ($this->isLaravel) {
            \Illuminate\Support\Facades\Log::error("Webkernel PlatformUpdater: {$message}");
        }
        
        // Log to file
        file_put_contents($this->logPath, $logMessage, FILE_APPEND | LOCK_EX);
        
        // Output to console if CLI
        if ($this->isCLI) {
            fwrite(STDERR, $logMessage);
        }
        
        // Output to command if available
        if ($this->command && method_exists($this->command, 'error')) {
            $this->command->error("[ERROR] {$message}");
        }
    }
}

/**
 * CLI entry point
 */
if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'] ?? '')) {
    $options = getopt('', ['force', 'dry-run', 'status', 'json', 'log', 'auto-run', 'help']);
    
    if (isset($options['help'])) {
        echo "Webkernel Platform Updater\n";
        echo "Usage: php PlatformUpdater.php [options]\n\n";
        echo "Options:\n";
        echo "  --force     Force update regardless of version\n";
        echo "  --dry-run   Simulate update without making changes\n";
        echo "  --status    Show current status\n";
        echo "  --json      Output status as JSON\n";
        echo "  --log       Show log file contents\n";
        echo "  --auto-run  Run automatically (for cron jobs)\n";
        echo "  --help      Show this help\n";
        exit(0);
    }
    
    $updater = new PlatformUpdater();
    
    if (isset($options['force'])) {
        $updater->setForceUpdate(true);
    }
    
    if (isset($options['dry-run'])) {
        $updater->setDryRun(true);
    }
    
    if (isset($options['status'])) {
        $status = $updater->getUpdateStatus();
        if (isset($options['json'])) {
            echo json_encode($status, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "Local version: {$status['local_version']}\n";
            echo "Remote version: {$status['remote_version']}\n";
            echo "Update needed: " . ($status['update_needed'] ? 'Yes' : 'No') . "\n";
            echo "Repository: {$status['remote_repo']}\n";
            echo "Branch: {$status['branch']}\n";
        }
        exit(0);
    }
    
    if (isset($options['log'])) {
        $logPath = $updater->getLogPath();
        if (file_exists($logPath)) {
            echo file_get_contents($logPath);
        } else {
            echo "Log file not found: {$logPath}\n";
        }
        exit(0);
    }
    
    if (isset($options['auto-run'])) {
        $success = $updater->autoRun();
        exit($success ? 0 : 1);
    }
    
    // Default: check and update
    if ($updater->isUpdateNeeded()) {
        $success = $updater->performUpdate();
        exit($success ? 0 : 1);
    } else {
        echo "No update needed.\n";
        exit(0);
    }
}