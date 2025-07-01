<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Webkernel\Providers\WebkernelRouteServiceProvider;

class WebkernelPlatformServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $routeProvider = new WebkernelRouteServiceProvider($this->app);
        $routeProvider->loadRoutes();

        $this->app->booted(function () {
            $platformFile = base_path('platform/platform.php');
            if (file_exists($platformFile)) {
                require $platformFile;
            }
        });
    }
}
