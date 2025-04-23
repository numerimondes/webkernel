<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class WebkernelRouteServiceProvider extends ServiceProvider
{
    /**
     * Initialize the service provider.
     */
    public function boot(): void
    {
        $this->loadRoutes();
    }

    /**
     * Load routes from the Webkernel package.
     */
    protected function loadRoutes(): void
    {
        Route::middleware('web')
             ->group(base_path('routes/web.php'));

        Route::middleware('web')
             ->group(base_path('packages/webkernel/src/routes/web.php'));
    }
}
