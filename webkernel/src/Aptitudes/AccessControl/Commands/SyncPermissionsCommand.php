<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Commands;

use Illuminate\Console\Command;
use Webkernel\Aptitudes\AccessControl\Models\Permission;
use Webkernel\Aptitudes\AccessControl\Logic\Resources\AccessControlPolicy;

class SyncPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'access:sync-permissions {--fresh : Delete existing permissions before sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discover and sync permissions from all Filament resources';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting permission synchronization...');

        if ($this->option('fresh')) {
            $this->warn('Deleting existing non-system permissions...');
            Permission::where('is_system', false)->delete();
        }

        try {
            // Discover and create permissions
            AccessControlPolicy::discoverAndCreatePermissions();

            $count = Permission::count();
            $this->info("Successfully synchronized permissions. Total permissions: {$count}");

            // Show permissions by module
            $permissions = Permission::all()->groupBy('module');

            $this->newLine();
            $this->table(
                ['Module', 'Permissions Count'],
                $permissions->map(function ($modulePermissions, $module) {
                    return [
                        $module ?: 'System',
                        $modulePermissions->count()
                    ];
                })->values()
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync permissions: ' . $e->getMessage());

            if ($this->option('verbose')) {
                $this->error($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }
}
