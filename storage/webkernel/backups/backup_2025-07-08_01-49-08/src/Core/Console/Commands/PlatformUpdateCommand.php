<?php

declare(strict_types=1);

namespace Webkernel\Core\Console\Commands;

use Illuminate\Console\Command;
use Webkernel\Constants\PlatformUpdater;

/**
 * Webkernel Platform Update Command
 * 
 * Artisan command wrapper for the PlatformUpdater class.
 * Provides command-line interface for updating Webkernel platform.
 * 
 * @author El Moumen Yassine
 * @email yassine@numerimondes.com
 * @website www.numerimondes.com
 * @license MPL-2.0
 */
class PlatformUpdateCommand extends Command
{
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
                            {--register-hooks : Register default post-update hooks}';

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

        // Create updater instance
        $updater = new PlatformUpdater(
            $this->option('remote-repo'),
            $this->option('branch'),
            $this
        );

        // Set options
        if ($this->option('force')) {
            $updater->setForceUpdate(true);
        }

        if ($this->option('dry-run')) {
            $updater->setDryRun(true);
        }

        // Register default hooks if requested
        if ($this->option('register-hooks')) {
            $updater->registerDefaultHooks();
            $this->info('Default post-update hooks registered');
            return self::SUCCESS;
        }

        // Handle different command modes
        if ($this->option('status')) {
            return $this->handleStatus($updater);
        }

        if ($this->option('json')) {
            return $this->handleJsonOutput($updater);
        }

        if ($this->option('log')) {
            return $this->handleLogOutput($updater);
        }

        if ($this->option('clear-cache')) {
            return $this->handleClearCache($updater);
        }

        if ($this->option('backups')) {
            return $this->handleBackups($updater);
        }

        if ($backupPath = $this->option('restore')) {
            return $this->handleRestore($updater, $backupPath);
        }

        if ($this->option('auto-run')) {
            return $this->handleAutoRun($updater);
        }

        // Default: perform update
        return $this->handleUpdate($updater);
    }

    /**
     * Handle status display
     */
    private function handleStatus(PlatformUpdater $updater): int
    {
        $status = $updater->getUpdateStatus();

        $this->info('Current Status:');
        $this->table(
            ['Property', 'Value'],
            [
                ['Local Version', $status['local_version'] ?? 'Unknown'],
                ['Remote Version', $status['remote_version'] ?? 'Unknown'],
                ['Update Needed', $status['update_needed'] ? 'Yes' : 'No'],
                ['Repository', $status['remote_repo']],
                ['Branch', $status['branch']],
                ['Force Update', $status['force_update'] ? 'Yes' : 'No'],
                ['Dry Run', $status['dry_run'] ? 'Yes' : 'No'],
                ['Laravel Environment', $status['is_laravel'] ? 'Yes' : 'No'],
                ['CLI Mode', $status['is_cli'] ? 'Yes' : 'No'],
            ]
        );

        if ($status['update_needed']) {
            $this->warn('An update is available!');
        } else {
            $this->info('No update needed.');
        }

        return self::SUCCESS;
    }

    /**
     * Handle JSON output
     */
    private function handleJsonOutput(PlatformUpdater $updater): int
    {
        $json = $updater->getUpdateStatusJson();
        $this->line($json);
        return self::SUCCESS;
    }

    /**
     * Handle log output
     */
    private function handleLogOutput(PlatformUpdater $updater): int
    {
        $logPath = $updater->getLogFilePath();
        
        if (!file_exists($logPath)) {
            $this->error("Log file not found: {$logPath}");
            return self::FAILURE;
        }

        $this->info("Log file contents:");
        $this->line(file_get_contents($logPath));
        
        return self::SUCCESS;
    }

    /**
     * Handle cache clearing
     */
    private function handleClearCache(PlatformUpdater $updater): int
    {
        $updater->clearVersionCache();
        $this->info('Version cache cleared successfully');
        return self::SUCCESS;
    }

    /**
     * Handle backups listing
     */
    private function handleBackups(PlatformUpdater $updater): int
    {
        $backups = $updater->getAvailableBackups();

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
    private function handleRestore(PlatformUpdater $updater, string $backupPath): int
    {
        $backups = $updater->getAvailableBackups();
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

        if (!$this->confirm('Are you sure you want to proceed?')) {
            $this->info('Restore cancelled');
            return self::SUCCESS;
        }

        if ($updater->restoreFromBackupPath($backupPath)) {
            $this->info('Restore completed successfully');
            return self::SUCCESS;
        } else {
            $this->error('Restore failed');
            return self::FAILURE;
        }
    }

    /**
     * Handle auto-run mode
     */
    private function handleAutoRun(PlatformUpdater $updater): int
    {
        $this->info('Auto-run mode enabled');
        
        if ($updater->autoRun()) {
            $this->info('Auto-run completed successfully');
            return self::SUCCESS;
        } else {
            $this->error('Auto-run failed');
            return self::FAILURE;
        }
    }

    /**
     * Handle update process
     */
    private function handleUpdate(PlatformUpdater $updater): int
    {
        // Register default hooks
        $updater->registerDefaultHooks();

        // Check if update is needed
        if (!$updater->isUpdateNeeded()) {
            $this->info('No update needed. Webkernel is up to date!');
            return self::SUCCESS;
        }

        // Show update information
        $status = $updater->getUpdateStatus();
        $this->warn("Update available!");
        $this->line("Local version:  {$status['local_version']}");
        $this->line("Remote version: {$status['remote_version']}");
        $this->newLine();

        if ($status['dry_run']) {
            $this->info('DRY RUN MODE - No actual changes will be made');
        } else {
            $this->warn('This will:');
            $this->line('  • Create a backup of current installation');
            $this->line('  • Download latest version from repository');
            $this->line('  • Replace current installation');
            $this->line('  • Run post-update actions (composer update, cache clear, etc.)');
            $this->newLine();

            if (!$this->confirm('Do you want to proceed with the update?')) {
                $this->info('Update cancelled');
                return self::SUCCESS;
            }
        }

        // Perform update
        $this->info('Starting update process...');
        
        if ($updater->performUpdate()) {
            $this->info('Update completed successfully!');
            
            $finalStatus = $updater->getUpdateStatus();
            if ($finalStatus['files_updated'] > 0) {
                $this->line("Files updated: {$finalStatus['files_updated']}");
            }
            
            if (!empty($finalStatus['post_update_actions'])) {
                $this->line('Post-update actions completed:');
                foreach ($finalStatus['post_update_actions'] as $action) {
                    $status = $action['success'] ? '✓' : '✗';
                    $this->line("  {$status} {$action['name']}");
                }
            }
            
            return self::SUCCESS;
        } else {
            $this->error('Update failed!');
            
            $finalStatus = $updater->getUpdateStatus();
            if ($finalStatus['error']) {
                $this->error("Error: {$finalStatus['error']}");
            }
            
            return self::FAILURE;
        }
    }
} 