<?php

namespace Webkernel\Commands\PackageManager;

use Illuminate\Console\Command;
use Webkernel\Services\WebkernelUpdater;

class WebkernelUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webkernel:update
                           {package? : The package to update (default: webkernel)}
                           {--stable : Only update to stable versions}
                           {--force : Force update without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Webkernel to the latest version';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $package = $this->argument('package') ?: 'webkernel';
        $stableOnly = $this->option('stable');
        $force = $this->option('force');

        try {
            $updater = new WebkernelUpdater($package);
            $updateInfo = $updater->getUpdateInfo($stableOnly);

            if (!$updateInfo['updateAvailable']) {
                $this->info('Your Webkernel installation is already up to date.');
                return 0;
            }

            $this->info('Update available:');
            $this->table(
                ['Current Version', 'Available Version', 'Type'],
                [[
                    $updateInfo['currentVersion'],
                    $updateInfo['availableVersion'],
                    $updateInfo['isStable'] ? 'Stable' : 'Development'
                ]]
            );

            if (!$force && !$this->confirm('Do you want to proceed with the update?')) {
                $this->info('Update cancelled.');
                return 0;
            }

            $this->info('Updating Webkernel...');
            $this->output->progressStart(5);

            // Update the package
            $result = $updater->update();
            $this->output->progressAdvance(5);
            $this->output->progressFinish();

            if (is_array($result) && ($result['success'] ?? false)) {
                $this->info('Webkernel has been successfully updated to version ' . $updateInfo['availableVersion']);

                // Show list of updated packages
                if (isset($result['updatedPackages']) && !empty($result['updatedPackages'])) {
                    $this->info('The following packages were updated:');
                    $packageRows = [];

                    foreach ($result['updatedPackages'] as $pkg) {
                        $packageRows[] = [$pkg];
                    }

                    $this->table(['Package'], $packageRows);
                }

                if ($this->confirm('Do you want to clear the cache?', true)) {
                    $this->call('cache:clear');
                    $this->call('config:clear');
                    $this->call('view:clear');
                }

                return 0;
            } else {
                $this->error('Update failed.');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Error during update: ' . $e->getMessage());
            return 1;
        }
    }
}
