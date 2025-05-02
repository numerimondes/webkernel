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
        // Get all widget classes in the Widgets directory
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

        // Load custom view components
        $this->loadViewComponentsFrom(
            base_path('packages/webkernel/src/View/Components'),
            'webkernel'
        );

        // Publish widget assets for immediate loading
        $this->publishes([
            base_path('packages/webkernel/src/resources/views/widgets') => resource_path('views/vendor/webkernel/widgets'),
        ], 'webkernel-widget-views');
    }

    /**
     * Discover all widget classes in the Widgets directory
     *
     * @return array
     */
    protected function discoverWidgets(): array
    {
        $widgetPath = base_path('packages/webkernel/src/Filament/Widgets');
        $namespace = 'Webkernel\\Filament\\Widgets';
        $widgets = [];

        // Check if the directory exists
        if (!File::isDirectory($widgetPath)) {
            return $widgets;
        }

        // Get all PHP files in the widgets directory
        $files = File::files($widgetPath);

        foreach ($files as $file) {
            // Get the filename without extension
            $className = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            // Build the fully qualified class name
            $class = $namespace . '\\' . $className;

            // Check if the class exists and is a Widget
            if (class_exists($class)) {
                $reflection = new ReflectionClass($class);
                if (!$reflection->isAbstract() && $reflection->isSubclassOf(Widget::class)) {
                    $widgets[] = $class;
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
        // Extract class name from the fully qualified class name
        $reflection = new ReflectionClass($widgetClass);
        $className = $reflection->getShortName();

        // Convert class name to kebab case for Livewire component name
        $alias = preg_replace('/([a-z])([A-Z])/', '$1-$2', $className);
        $alias = strtolower($alias);

        // Return the full Livewire component name
        return 'webkernel.filament.widgets.' . $alias;
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
            $namespace = 'Webkernel\\View\\Components\\' . $className;

            if (class_exists($namespace)) {
                $this->app->make('blade.compiler')->component($namespace, null, $namespace);
            }
        }
    }
}
