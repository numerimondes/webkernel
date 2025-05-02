<?php

namespace Webkernel\Commands\PackageManager;

use Illuminate\Console\Command;
use Webkernel\Services\WebkernelUpdater;

class WebkernelCheckUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webkernel:check-update
                           {--stable : Only check for stable updates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for Webkernel updates';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $checkStableOnly = $this->option('stable');

        try {
            $updater = new WebkernelUpdater();
            $updateInfo = $updater->getUpdateInfo($checkStableOnly);

            if (!$updateInfo['updateAvailable']) {
                $this->info('Your Webkernel installation is up to date.');
                return 0;
            }

            $this->info('Update available!');
            $this->table(
                ['Current Version', 'Available Version', 'Type'],
                [[
                    $updateInfo['currentVersion'],
                    $updateInfo['availableVersion'],
                    $updateInfo['isStable'] ? 'Stable' : 'Development'
                ]]
            );

            // Check for additional packages if updating the main package
            if (defined('\\Webkernel\\constants\\Application::ADDITIONAL_PACKAGES')) {
                $additionalPackages = array_map('trim', explode(',', \Webkernel\constants\Application::ADDITIONAL_PACKAGES));

                if (!empty($additionalPackages)) {
                    $this->info('The following additional packages will also be updated if available:');
                    $packageRows = [];

                    foreach ($additionalPackages as $package) {
                        if ($package === 'webkernel') {
                            continue;
                        }
                        $packageRows[] = [$package];
                    }

                    if (!empty($packageRows)) {
                        $this->table(['Package'], $packageRows);
                    }
                }
            }

            if ($this->confirm('Do you want to update now?')) {
                $this->call('webkernel:update', [
                    '--force' => true,
                    '--stable' => $checkStableOnly
                ]);
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Error checking for updates: ' . $e->getMessage());
            return 1;
        }
    }
}
