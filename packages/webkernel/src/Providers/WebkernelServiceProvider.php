<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

use Webkernel\Providers\WebkernelBladeServiceProvider;
use Webkernel\Providers\WebkernelMigrationServiceProvider;
use Webkernel\Providers\WebkernelViewServiceProvider;
use Webkernel\Providers\WebkernelCommandServiceProvider;
use Webkernel\Providers\WebkernelHelperServiceProvider;
use Webkernel\Providers\WebkernelRouteServiceProvider;
use Webkernel\Providers\WebkernelLivewireServiceProvider;
use Webkernel\Providers\CommandProtectionServiceProvider;
use Webkernel\Providers\WebkernelUserServiceProvider;
use Webkernel\Providers\WebkernelFactoryServiceProvider;
use Webkernel\Providers\WebkernelPoliciesServiceProvider;
use Webkernel\Providers\WebkernelWidgetServiceProvider;

use Webkernel\Traits\Configurable as GlobalWebkernelConfigurable;

class WebkernelServiceProvider extends ServiceProvider
{
    use GlobalWebkernelConfigurable;

    /**
     * WebkernelServiceProvider constructor.
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->initializeConfig(); // from GlobalWebkernelConfigurable
    }

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
        $this->app->register(CommandProtectionServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     * To publish php artisan vendor:publish --provider="Webkernel\Providers\WebkernelServiceProvider" --tag="config"
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/webkernel.php' => config_path('webkernel.php'),
        ], 'config');

    }
}
