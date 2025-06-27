<?php

namespace Webkernel\Providers;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class WebkernelCommandServiceProvider extends ServiceProvider
{
    /**
     * Initialize the service provider.
     */
    public function boot(): void
    {
        $this->registerWebkernelCommands();
    }

    /**
     * Register commands if in console mode.
     */
    protected function registerWebkernelCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $commandPaths = $this->getCommandPaths();
            $allCommands = [];

            foreach ($commandPaths as $path => $namespace) {
                $commands = collect(glob("{$path}/*.php"))
                    ->map(fn($file) => $namespace . basename($file, '.php'))
                    ->filter(fn($class) => class_exists($class) && is_subclass_of($class, Command::class))
                    ->toArray();

                $allCommands = array_merge($allCommands, $commands);
            }

            $this->commands($allCommands);
        }
    }

    /**
     * Get command paths and their namespaces
     *
     * @return array
     */
    protected function getCommandPaths(): array
    {
        $paths = [
            base_path('packages/webkernel/src/Console/Commands') => 'Webkernel\\Console\\Commands\\',
            base_path('packages/webkernel/src/Console/Install') => 'Webkernel\\Console\\Install\\',
            base_path('packages/webkernel/src/Console/Package') => 'Webkernel\\Console\\Package\\',
        ];

        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $commandPaths = [
                $path . '/Console' => $namespace . 'Console\\',
                $path . '/Console/Commands' => $namespace . 'Console\\Commands\\',
                $path . '/Console/Install' => $namespace . 'Console\\Install\\',
                $path . '/Console/Package' => $namespace . 'Console\\Package\\',
            ];
            foreach ($commandPaths as $commandPath => $commandNamespace) {
                if (File::isDirectory($commandPath)) {
                    $paths[$commandPath] = $commandNamespace;
                }
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
