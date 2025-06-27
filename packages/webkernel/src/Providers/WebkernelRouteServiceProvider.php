<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

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
     * Load routes from the Webkernel package and platform/packages.
     */
    protected function loadRoutes(): void
    {
        // Load core routes
        Route::middleware('web')
             ->group(base_path('routes/web.php'));

        Route::middleware('web')
             ->group(base_path('packages/webkernel/src/routes/web.php'));

        // Load routes from platform and packages
        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $routePath = $path . '/routes/web.php';
            if (File::exists($routePath)) {
                Route::middleware('web')->group($routePath);
            }
        }
    }

    /**
     * Get PSR-4 namespaces from composer.json
     *
     * @return array
     */
    protected function getAutoloadNamespaces(): array
    {
        $composerJson = json_decode(File::get(base_path('composer.json')), true);
        $namespaces = [];

        if (isset($composerJson['autoload']['psr-4'])) {
            foreach ($composerJson['autoload']['psr-4'] as $namespace => $path) {
                if (str_starts_with($path, 'platform/') || (str_starts_with($path, 'packages/') && $path !== 'packages/webkernel/src/')) {
                    $namespaces[rtrim($namespace, '\\') . '\\'] = base_path($path);
                }
            }
        }

        return $namespaces;
    }
}
