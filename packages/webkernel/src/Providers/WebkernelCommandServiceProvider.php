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
            $commandClasses = collect(glob("{$commandPath}/*.php"))
                ->map(fn($file) => $commandNamespace . basename($file, '.php'))
                ->filter(fn($class) => class_exists($class) && is_subclass_of($class, \Illuminate\Console\Command::class))
                ->toArray();

            $this->commands($commandClasses);
        }
    }
}
