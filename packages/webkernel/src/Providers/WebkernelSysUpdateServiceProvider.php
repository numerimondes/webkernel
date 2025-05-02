<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Webkernel\Console\Commands\WebkernelCheckUpdateCommand;
use Webkernel\Console\Commands\WebkernelUpdateCommand;

class WebkernelSysUpdateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/webkernel.php', 'webkernel'
        );

        // Register WebkernelUpdater service
        $this->app->singleton(\Webkernel\Services\WebkernelUpdater::class, function ($app) {
            return new \Webkernel\Services\WebkernelUpdater();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                WebkernelCheckUpdateCommand::class,
                WebkernelUpdateCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/webkernel.php' => config_path('webkernel.php'),
            ], 'webkernel-config');
        }

        // Check for updates on application boot if auto-update is enabled
        $this->checkForUpdates();
    }

    /**
     * Check for updates based on user preferences
     *
     * @return void
     */
    protected function checkForUpdates()
    {
        $preference = config('webkernel.updates.auto_update_preference', 'nothing');

        if ($preference === 'nothing') {
            return;
        }

        // Schedule check in background to avoid slowing down the application boot
        $this->app->terminating(function () use ($preference) {
            try {
                $updater = new \Webkernel\Services\WebkernelUpdater();
                $updateAvailable = $updater->checkForUpdates($preference === 'stable');

                if ($updateAvailable && $preference !== 'nothing') {
                    $updater->update();
                }
            } catch (\Exception $e) {
                // Fail silently, log error but don't interrupt the application
                logger()->error('Webkernel auto-update failed: ' . $e->getMessage());
            }
        });
    }
}
