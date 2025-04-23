<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;

class WebkernelViewServiceProvider extends ServiceProvider
{
    /**
     * Initialize the service provider.
     */
    public function boot(): void
    {
        $this->loadViewsFromPackage();
    }

    /**
     * Load views from the Webkernel package.
     */
    protected function loadViewsFromPackage(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'webkernel');
        view()->addNamespace('webkernel', base_path('packages/webkernel/src/resources/views'));
    }
}
