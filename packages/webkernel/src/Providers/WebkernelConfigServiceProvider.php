<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Application as Artisan;
use Illuminate\Support\Facades\File;

class WebkernelConfigServiceProvider extends ServiceProvider
{
    /**
     * Register the configuration.
     */
    public function register(): void
    {
        // Merge Webkernel config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/webkernel.php',
            'webkernel'
        );

        // Merge configs from platform and packages
        $configPaths = $this->getConfigPaths();
        foreach ($configPaths as $path => $namespace) {
            $configFile = $path . '/webkernel.php';
            if (File::exists($configFile)) {
                $this->mergeConfigFrom($configFile, strtolower(str_replace('\\', '.', $namespace)) . '.webkernel');
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Allow publishing the config file
        $this->publishes([
            __DIR__ . '/../config/webkernel.php' => config_path('webkernel.php'),
        ], 'webkernel-config');

        // Publish platform/package configs
        $configPaths = $this->getConfigPaths();
        $publishes = [];
        foreach ($configPaths as $path => $namespace) {
            $configFile = $path . '/webkernel.php';
            if (File::exists($configFile)) {
                $publishes[$configFile] = config_path(strtolower(str_replace('\\', '.', $namespace)) . '.webkernel.php');
            }
        }
        $this->publishes($publishes, 'platform-config');

        // Apply command prohibitions
        $this->prohibitCommandsFromConfig();
    }

    /**
     * Prohibit commands dynamically based on configuration
     */
    protected function prohibitCommandsFromConfig(): void
    {
        $commands = config('webkernel.prohibit_commands.commands', []);
        $forceKeys = config('webkernel.prohibit_commands.key_to_force_destructive_command', []);

        $hasForceKey = $this->hasValidForceKey($forceKeys);

        foreach ($commands as $commandName => $settings) {
            if (isset($settings['prohibited']) && $settings['prohibited'] && !$hasForceKey) {
                if (isset($settings['class']) && class_exists($settings['class'])) {
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
        return array_keys(array_filter($commands, fn($settings) => isset($settings['prohibited']) && $settings['prohibited']));
    }

    /**
     * Get config paths and their namespaces
     *
     * @return array
     */
    protected function getConfigPaths(): array
    {
        $paths = [];
        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $configPath = $path . '/config';
            if (File::isDirectory($configPath)) {
                $paths[$configPath] = $namespace;
            }
        }

        return $paths;
    }

    /**
     * Get PSR-4 namespaces from composer.json
     *
     * @return array
     */
    protected function getAutoloadNamespaces(): array
    {
        $composerJson = json_decode(File::get(base_path('composer.json')), true);
        $namespaces = [];

        if (isset($composerJson['autoload']['psr-4'])) {
            foreach ($composerJson['autoload']['psr-4'] as $namespace => $path) {
                if (str_starts_with($path, 'platform/') || (str_starts_with($path, 'packages/') && $path !== 'packages/webkernel/src/')) {
                    $namespaces[rtrim($namespace, '\\') . '\\'] = base_path($path);
                }
            }
        }

        return $namespaces;
    }
}
