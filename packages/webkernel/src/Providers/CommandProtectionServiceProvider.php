<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Webkernel\Traits\Configurable;

class CommandProtectionServiceProvider extends ServiceProvider
{
    use Configurable;

    /**
     * Bootstrap the service provider.
     */
    public function boot()
    {
        // Initialize configuration dynamically
        $this->initializeConfig();

        // Apply prohibitions to commands
        $this->prohibitDestructiveCommands();
    }

    /**
     * Prohibit destructive commands based on prohibit_commands config.
     */
    protected function prohibitDestructiveCommands()
    {
        // Dynamically get prohibit_commands config
        $commands = $this->getConfig('prohibit_commands', []);

        foreach ($commands as $command => $config) {
            if (isset($config['prohibited']) && $config['prohibited'] && isset($config['class']) && class_exists($config['class'])) {
                $config['class']::prohibit(true);
            }
        }
    }

    /**
     * Register the configuration file for publishing.
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/../config/webkernel.php' => config_path('webkernel.php'),
        ], 'webkernel-config');
    }
}
