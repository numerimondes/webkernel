<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Webkernel\Helpers\WebkernelHelpers;

class WebkernelHelperServiceProvider extends ServiceProvider
{
    /**
     * Initialize the service provider.
     */
    public function boot(): void
    {
        $this->loadHelpers();
    }

    /**
     * Load Webkernel helpers.
     */
    protected function loadHelpers(): void
    {
        WebkernelHelpers::loadHelpers();
    }
}
