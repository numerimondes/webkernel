<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Filament\Widgets\Widget;
use ReflectionClass;

class WebkernelWidgetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Get all widget classes in the Widgets directories
        $widgetClasses = $this->discoverWidgets();

        if (!empty($widgetClasses)) {
            // Register widgets with Filament
            Filament::registerWidgets($widgetClasses);

            // Register each widget with Livewire
            foreach ($widgetClasses as $widgetClass) {
                $alias = $this->getWidgetAlias($widgetClass);
                Livewire::component($alias, $widgetClass);

                // Configure widget for immediate loading
                if (method_exists($widgetClass, 'isLazy')) {
                    $widgetClass::isLazy(false);
                }
            }
        }

        // Load custom view components from all packages and platform
        $viewComponentPaths = $this->getViewComponentPaths();
        foreach ($viewComponentPaths as $path => $namespace) {
            $this->loadViewComponentsFrom($path, $namespace);
        }

        // Publish widget assets for immediate loading
        $this->publishes([
            base_path('packages/webkernel/src/resources/views/widgets') => resource_path('views/vendor/webkernel/widgets'),
        ], 'webkernel-widget-views');
    }

    /**
     * Discover all widget classes in the Widgets directories
     *
     * @return array
     */
    protected function discoverWidgets(): array
    {
        $widgets = [];
        $widgetPaths = $this->getWidgetPaths();

        foreach ($widgetPaths as $path => $namespace) {
            if (!File::isDirectory($path)) {
                continue;
            }

            $files = File::files($path);
            foreach ($files as $file) {
                $className = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $class = $namespace . '\\' . $className;

                if (class_exists($class)) {
                    $reflection = new ReflectionClass($class);
                    if (!$reflection->isAbstract() && $reflection->isSubclassOf(Widget::class)) {
                        $widgets[] = $class;
                    }
                }
            }
        }

        return $widgets;
    }

    /**
     * Generate a Livewire component alias from widget class
     *
     * @param string $widgetClass
     * @return string
     */
    protected function getWidgetAlias(string $widgetClass): string
    {
        $reflection = new ReflectionClass($widgetClass);
        $className = $reflection->getShortName();
        $alias = preg_replace('/([a-z])([A-Z])/', '$1-$2', $className);
        $alias = strtolower($alias);

        // Use namespace prefix for alias to avoid conflicts
        $namespace = $reflection->getNamespaceName();
        $prefix = str_replace('\\', '.', strtolower(str_replace('Filament\\Widgets', '', $namespace)));
        return trim($prefix, '.') . '.filament.widgets.' . $alias;
    }

    /**
     * Load view components from the specified path.
     *
     * @param string $path
     * @param string $namespace
     * @return void
     */
    protected function loadViewComponentsFrom(string $path, string $namespace): void
    {
        if (!File::isDirectory($path)) {
            return;
        }

        $files = File::allFiles($path);

        foreach ($files as $file) {
            $className = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $class = $namespace . '\\' . $className;

            if (class_exists($class)) {
                $this->app->make('blade.compiler')->component($class, null, $namespace);
            }
        }
    }

    /**
     * Get widget paths and their corresponding namespaces
     *
     * @return array
     */
    protected function getWidgetPaths(): array
    {
        $paths = [
            base_path('packages/webkernel/src/Filament/Widgets') => 'Webkernel\\Filament\\Widgets',
        ];

        // Add platform and package paths
        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $widgetPath = $path . '/Filament/Widgets';
            if (File::isDirectory($widgetPath)) {
                $paths[$widgetPath] = $namespace . 'Filament\\Widgets';
            }
        }

        return $paths;
    }

    /**
     * Get view component paths and their corresponding namespaces
     *
     * @return array
     */
    protected function getViewComponentPaths(): array
    {
        $paths = [
            base_path('packages/webkernel/src/View/Components') => 'Webkernel\\View\\Components',
        ];

        // Add platform and package paths
        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $componentPath = $path . '/View/Components';
            if (File::isDirectory($componentPath)) {
                $paths[$componentPath] = $namespace . 'View\\Components';
            }
        }

        return $paths;
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
                // Include paths under platform/ and packages/ (excluding webkernel)
                if (str_starts_with($path, 'platform/') || (str_starts_with($path, 'packages/') && $path !== 'packages/webkernel/src/')) {
                    $namespaces[rtrim($namespace, '\\') . '\\'] = base_path($path);
                }
            }
        }

        return $namespaces;
    }
}
