<?php

use Illuminate\Support\Facades\File;

if (!function_exists('discover_platform_modules_components')) {
    /**
     * Discover all Filament components (resources, pages, widgets, clusters) from platform/Modules/ * /src/Filament/
     *
     * @param string $panelId The ID of the panel to filter registration (e.g., 'system').
     * @return void
     */
    function discover_platform_modules_components(string $panelId): void
    {
        $modulesDir = base_path('platform/Modules');
        if (!File::isDirectory($modulesDir)) {
            return;
        }

        $moduleDirs = File::directories($modulesDir);
        foreach ($moduleDirs as $moduleDir) {
            $filamentDir = $moduleDir . '/src/Filament';
            if (!File::isDirectory($filamentDir)) {
                continue;
            }

            // Convert module path to namespace (e.g., platform/Modules/ream -> PlatformModules\Ream)
            $moduleName = basename($moduleDir);
            $namespacePrefix = 'PlatformModules\\' . str_replace(['-', '_'], '', ucwords($moduleName, '-_'));

            // Discover Resources
            $resourcesDir = $filamentDir . '/Resources';
            if (File::isDirectory($resourcesDir)) {
                \Filament\Facades\Filament::getPanel($panelId)->discoverResources(
                    in: $resourcesDir,
                    for: $namespacePrefix . '\\Filament\\Resources'
                );
            }

            // Discover Pages
            $pagesDir = $filamentDir . '/Resources/Pages';
            if (File::isDirectory($pagesDir)) {
                \Filament\Facades\Filament::getPanel($panelId)->discoverPages(
                    in: $pagesDir,
                    for: $namespacePrefix . '\\Filament\\Resources\\Pages'
                );
            }

            // Discover Widgets
            $widgetsDir = $filamentDir . '/Widgets';
            if (File::isDirectory($widgetsDir)) {
                \Filament\Facades\Filament::getPanel($panelId)->discoverWidgets(
                    in: $widgetsDir,
                    for: $namespacePrefix . '\\Filament\\Widgets'
                );
            }

            // Discover Clusters
            $clustersDir = $filamentDir . '/Clusters';
            if (File::isDirectory($clustersDir)) {
                \Filament\Facades\Filament::getPanel($panelId)->discoverClusters(
                    in: $clustersDir,
                    for: $namespacePrefix . '\\Filament\\Clusters'
                );
            }
        }
    }
}