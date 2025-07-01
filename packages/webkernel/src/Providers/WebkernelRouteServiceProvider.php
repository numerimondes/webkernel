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
    public function loadRoutes(): void
    {
        // Load core routes
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        // Load ALL route files from webkernel package
        $this->loadWebkernelRoutes();

        // Load routes from platform and packages
        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $this->loadRoutesFromPath($path . '/routes');
        }
    }

    /**
     * Load all route files from webkernel package
     */
    protected function loadWebkernelRoutes(): void
    {
        $webkernelRoutesPath = base_path('packages/webkernel/src/routes');
        $this->loadRoutesFromPath($webkernelRoutesPath);
    }

    /**
     * Load all route files from a given path
     */
    protected function loadRoutesFromPath(string $routesPath): void
    {
        if (!File::isDirectory($routesPath)) {
            return;
        }

        $routeFiles = File::glob($routesPath . '/*.php');

        foreach ($routeFiles as $routeFile) {
            $fileName = basename($routeFile, '.php');

            // Déterminer le middleware selon le nom du fichier
            $middleware = $this->getMiddlewareForRouteFile($fileName);

            if ($middleware) {
                Route::middleware($middleware)->group($routeFile);
            } else {
                // Pas de middleware spécifique, charger directement
                Route::group([], $routeFile);
            }
        }
    }

    /**
     * Determine middleware based on route file name
     */
    protected function getMiddlewareForRouteFile(string $fileName): ?string
    {
        return match($fileName) {
            'web' => 'web',
            'api' => 'api',
            'assets' => 'web', // Les assets utilisent le middleware web
            'console' => null, // Les routes console n'ont pas de middleware HTTP
            default => 'web', // Par défaut, utiliser le middleware web
        };
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
                if (
                    str_starts_with($path, 'platform/') ||
                    (str_starts_with($path, 'packages/') && $path !== 'packages/webkernel/src/')
                ) {
                    $namespaces[rtrim($namespace, '\\') . '\\'] = base_path($path);
                }
            }
        }

        return $namespaces;
    }
}
