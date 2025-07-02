<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Webkernel\Providers\WebkernelRouteServiceProvider;
use Numerimondes\Platform;

class WebkernelPlatformServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        if (file_exists(base_path('platform/Platform.php')) && class_exists(Platform::class)) {
            $platform = new Platform();
            $platform->initialize();
        }

        $routeProvider = new WebkernelRouteServiceProvider($this->app);
        $routeProvider->loadRoutes();
    }
}
