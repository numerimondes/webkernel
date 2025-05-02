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

            $commandNamespace = 'Webkernel\\Commands\\';
            $commandPath = base_path('packages/webkernel/src/Commands');

            // Main commands in the Webkernel/Commands directory
            $mainCommands = collect(glob("{$commandPath}/*.php"))
                ->map(fn($file) => $commandNamespace . basename($file, '.php'))
                ->filter(fn($class) => class_exists($class) && is_subclass_of($class, \Illuminate\Console\Command::class));

            // Subcommands located in the composer/PostCreateProjectCmd directory
            $subCommandPath = "{$commandPath}/composer/PostCreateProjectCmd";
            $subCommandNamespace = $commandNamespace . 'composer\\PostCreateProjectCmd\\';

            $subCommands = collect(glob("{$subCommandPath}/*.php"))
                ->map(fn($file) => $subCommandNamespace . basename($file, '.php'))
                ->filter(fn($class) => class_exists($class) && is_subclass_of($class, \Illuminate\Console\Command::class));

            // Commands related to PackageManager
            $packageManagerCommandPath = "{$commandPath}/PackageManager";
            $packageManagerNamespace = $commandNamespace . 'PackageManager\\';

            $packageManagerCommands = collect(glob("{$packageManagerCommandPath}/*.php"))
                ->map(fn($file) => $packageManagerNamespace . basename($file, '.php'))
                ->filter(fn($class) => class_exists($class) && is_subclass_of($class, \Illuminate\Console\Command::class));

            // Merge all commands
            $allCommands = $mainCommands->merge($subCommands)->merge($packageManagerCommands)->toArray();

            // Register all commands
            $this->commands($allCommands);
        }
    }
}
