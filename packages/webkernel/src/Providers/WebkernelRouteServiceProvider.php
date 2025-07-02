<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class WebkernelRouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutes();
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')->group(base_path('routes/web.php'));

        $this->loadWebkernelRoutes();

        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $this->loadRoutesFromPath($path . '/routes');
        }
    }

    protected function loadWebkernelRoutes(): void
    {
        $webkernelRoutesPath = base_path('packages/webkernel/src/routes');
        $this->loadRoutesFromPath($webkernelRoutesPath);
    }

    protected function loadRoutesFromPath(string $routesPath): void
    {
        if (!File::isDirectory($routesPath)) {
            return;
        }

        $routeFiles = File::glob($routesPath . '/*.php');

        foreach ($routeFiles as $routeFile) {
            $fileName = basename($routeFile, '.php');
            $middleware = $this->getMiddlewareForRouteFile($fileName);

            if ($middleware) {
                Route::middleware($middleware)->group($routeFile);
            } else {
                Route::group([], $routeFile);
            }
        }
    }

    protected function getMiddlewareForRouteFile(string $fileName): ?string
    {
        return match ($fileName) {
            'web' => 'web',
            'api' => 'api',
            'assets' => 'web',
            'console' => null,
            default => 'web',
        };
    }

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
