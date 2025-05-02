<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Webkernel\Providers\WebkernelBladeServiceProvider;
use Webkernel\Providers\WebkernelMigrationServiceProvider;
use Webkernel\Providers\WebkernelViewServiceProvider;
use Webkernel\Providers\WebkernelCommandServiceProvider;
use Webkernel\Providers\WebkernelHelperServiceProvider;
use Webkernel\Providers\WebkernelRouteServiceProvider;
use Webkernel\Providers\WebkernelLivewireServiceProvider;
use Webkernel\Providers\WebkernelUserServiceProvider;
use Webkernel\Providers\WebkernelFactoryServiceProvider;
use Illuminate\Support\Facades\Route;

class WebkernelServiceProvider extends ServiceProvider
{
    /**
     * Register additional services.
     */
    public function register(): void
    {
        $this->app->register(WebkernelBladeServiceProvider::class);
        $this->app->register(WebkernelMigrationServiceProvider::class);
        $this->app->register(WebkernelViewServiceProvider::class);
        $this->app->register(WebkernelCommandServiceProvider::class);
        $this->app->register(WebkernelHelperServiceProvider::class);
        $this->app->register(WebkernelRouteServiceProvider::class);
        $this->app->register(WebkernelLivewireServiceProvider::class);
        $this->app->register(WebkernelPoliciesServiceProvider::class);
        $this->app->register(WebkernelWidgetServiceProvider::class);
    }

    /**
     * Initialize the service provider.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/webkernel.php' => config_path('webkernel.php'),
            //php artisan vendor:publish --provider="Webkernel\Providers\WebkernelServiceProvider" --tag="config"
        ], 'config');
    }
}
