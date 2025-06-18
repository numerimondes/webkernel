<?php

namespace Webkernel\Providers;

use Blade;
use Illuminate\Support\ServiceProvider;

class WebkernelBladeServiceProvider extends ServiceProvider
{
    /**
     * Register additional services.
     */
    public function register(): void
    {
        // Register services if necessary
    }

    /**
     * Initialize the service provider.
     */
    public function boot(): void
    {
        $this->registerBladeDirectives();
    }

    /**
     * Register custom Blade directives.
     */
    protected function registerBladeDirectives(): void
    {
        Blade::directive('webkernel', fn($expression) => "<?php echo webkernel_include({$expression}); ?>");
    }
}
