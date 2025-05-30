<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Application as Artisan;

class WebkernelConfigServiceProvider extends ServiceProvider
{
    /**
     * Register the configuration.
     */
    public function register(): void
    {
        // This automatically loads the package config and makes it available as config('webkernel')
        // If user publishes config/webkernel.php, it will override this
        $this->mergeConfigFrom(
            __DIR__ . '/../config/webkernel.php',
            'webkernel'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Allow publishing the config file to config/webkernel.php
        $this->publishes([
            __DIR__ . '/../config/webkernel.php' => config_path('webkernel.php'),
        ], 'webkernel-config');

        // Apply command prohibitions dynamically from config
        $this->prohibitCommandsFromConfig();
    }

    /**
     * Prohibit commands dynamically based on configuration
     */
    protected function prohibitCommandsFromConfig(): void
    {
        $commands = config('webkernel.prohibit_commands.commands', []);
        $forceKeys = config('webkernel.prohibit_commands.key_to_force_destructive_command', []);

        // Check if user has provided force keys via environment or arguments
        $hasForceKey = $this->hasValidForceKey($forceKeys);

        foreach ($commands as $commandName => $settings) {
            if (isset($settings['prohibited']) && $settings['prohibited'] && !$hasForceKey) {
                if (isset($settings['class']) && class_exists($settings['class'])) {
                    // Laravel 12 command prohibition
                    $settings['class']::prohibit();
                }
            }
        }
    }

    /**
     * Check if a valid force key has been provided
     */
    protected function hasValidForceKey(array $validKeys): bool
    {
        // Check command line arguments for --force-webkernel-key CLI
        if ($this->app->runningInConsole()) {
            $arguments = $_SERVER['argv'] ?? [];

            foreach ($arguments as $arg) {
                if (str_starts_with($arg, '--force-webkernel-key=')) {
                    $key = substr($arg, strlen('--force-webkernel-key='));
                    if (in_array($key, $validKeys)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }


    /**
     * Get prohibited commands list for external access
     */
    public function getProhibitedCommands(): array
    {
        $commands = config('webkernel.prohibit_commands.commands', []);

        return array_keys(array_filter($commands, function($settings) {
            return isset($settings['prohibited']) && $settings['prohibited'];
        }));
    }


}
