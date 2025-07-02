<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

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
     * Load views from the Webkernel package and platform/packages.
     */
    protected function loadViewsFromPackage(): void
    {
        // Load Webkernel views
        $this->loadViewsFrom(base_path('packages/webkernel/src/Resources/Views'), 'webkernel');
        view()->addNamespace('webkernel', base_path('packages/webkernel/src/Resources/Views'));

        // Load views from platform and other packages
        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $viewPath = $path . '/Resources/Views';
            if (File::isDirectory($viewPath)) {
                $viewNamespace = strtolower(str_replace('\\', '.', $namespace));
                $this->loadViewsFrom($viewPath, $viewNamespace);
                view()->addNamespace($viewNamespace, $viewPath);
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
