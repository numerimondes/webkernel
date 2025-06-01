<?php

namespace WebkernelTestpackage\Providers;

use Illuminate\Support\ServiceProvider;
use WebkernelTestpackage\Constants\Application;

class WebkernelTestpackageServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/webkernel_testpackage.php',
            Application::CONFIG_PREFIX
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerViews();
        $this->registerTranslations();
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerCommands();
    }

    /**
     * Register publishing resources.
     */
    private function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/webkernel_testpackage.php' => config_path('webkernel_testpackage.php'),
            ], 'webkernel-testpackage-config');

            $this->publishes([
                __DIR__ . '/../public' => public_path('vendor/webkernel-testpackage'),
            ], 'webkernel-testpackage-assets');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/webkernel-testpackage'),
            ], 'webkernel-testpackage-views');
        }
    }

    /**
     * Register views.
     */
    private function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', Application::TRANSLATION_NAMESPACE);
    }

    /**
     * Register translations.
     */
    private function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', Application::TRANSLATION_NAMESPACE);
    }

    /**
     * Register routes.
     */
    private function registerRoutes(): void
    {
        if (file_exists(__DIR__ . '/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        if (file_exists(__DIR__ . '/../routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        }
    }

    /**
     * Register migrations.
     */
    private function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Register commands.
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            // Register your commands here
        }
    }
}
