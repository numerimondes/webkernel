<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;

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

            $basePath = base_path('packages/webkernel/src/Console');
            $baseNamespace = 'Webkernel\\Console\\';

            // Commands in Console/Commands
            $mainCommands = collect(glob("{$basePath}/Commands/*.php"))
                ->map(fn($file) => $baseNamespace . 'Commands\\' . basename($file, '.php'))
                ->filter(fn($class) => class_exists($class) && is_subclass_of($class, \Illuminate\Console\Command::class));

            // Commands in Console/Install
            $installCommands = collect(glob("{$basePath}/Install/*.php"))
                ->map(fn($file) => $baseNamespace . 'Install\\' . basename($file, '.php'))
                ->filter(fn($class) => class_exists($class) && is_subclass_of($class, \Illuminate\Console\Command::class));

            // Commands in Console/Package
            $packageCommands = collect(glob("{$basePath}/Package/*.php"))
                ->map(fn($file) => $baseNamespace . 'Package\\' . basename($file, '.php'))
                ->filter(fn($class) => class_exists($class) && is_subclass_of($class, \Illuminate\Console\Command::class));

            // Merge and register all found command classes
            $allCommands = $mainCommands
                ->merge($installCommands)
                ->merge($packageCommands)
                ->toArray();

            $this->commands($allCommands);
        }
    }
}
