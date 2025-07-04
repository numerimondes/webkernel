<?php

use Illuminate\Support\Facades\File;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Filament\Clusters\Cluster;

if (!function_exists('discover_platform_modules_components')) {
    /**
     * Découvre dynamiquement toutes les classes Filament des modules Numerimondes\Modules\*,
     * en se basant sur la config PSR-4 du composer.json racine.
     * Retourne un tableau associatif avec ressources, widgets, clusters.
     *
     * @param string $panelId
     * @return array<string, array<int, string|array>>
     */
    function discover_platform_modules_components(string $panelId): array
    {
        static $discoveredComponents = [];
        
        // Cache pour éviter la redécouverte multiple
        if (isset($discoveredComponents[$panelId])) {
            return $discoveredComponents[$panelId];
        }
        
        $result = [
            'resources' => [],
            'widgets' => [],
            'clusters' => [],
        ];

        $composerJsonPath = base_path('composer.json');
        if (!File::exists($composerJsonPath)) {
            return $discoveredComponents[$panelId] = $result;
        }

        $composerJson = json_decode(File::get($composerJsonPath), true);
        if (!isset($composerJson['autoload']['psr-4'])) {
            return $discoveredComponents[$panelId] = $result;
        }

        $psr4 = $composerJson['autoload']['psr-4'];
        
        foreach ($psr4 as $namespaceRoot => $pathRelative) {
            if (!str_starts_with($namespaceRoot, 'Numerimondes\\Modules\\')) {
                continue;
            }

            $srcPath = base_path(rtrim($pathRelative, '/'));
            if (!File::isDirectory($srcPath)) {
                continue;
            }

            // Découverte des Resources
            $result['resources'] = array_merge(
                $result['resources'], 
                discoverFilamentComponents($srcPath, $namespaceRoot, 'Resources', Resource::class, $panelId)
            );

            // Découverte des Widgets
            $result['widgets'] = array_merge(
                $result['widgets'], 
                discoverFilamentComponents($srcPath, $namespaceRoot, 'Widgets', Widget::class, $panelId)
            );

            // Découverte des Clusters
            $clustersDir = $srcPath . '/Filament/Clusters';
            if (File::isDirectory($clustersDir)) {
                $result['clusters'][] = [
                    'dir' => $clustersDir,
                    'namespace' => $namespaceRoot . 'Filament\\Clusters',
                ];
            }
        }

        return $discoveredComponents[$panelId] = $result;
    }
}

if (!function_exists('discoverFilamentComponents')) {
    /**
     * Découvre les composants Filament d'un type spécifique
     *
     * @param string $srcPath
     * @param string $namespaceRoot
     * @param string $componentType
     * @param string $baseClass
     * @param string $panelId
     * @return array<int, string>
     */
    function discoverFilamentComponents(string $srcPath, string $namespaceRoot, string $componentType, string $baseClass, string $panelId): array
    {
        $components = [];
        $componentDir = $srcPath . '/Filament/' . $componentType;
        
        if (!File::isDirectory($componentDir)) {
            return $components;
        }

        $files = File::allFiles($componentDir);
        
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relativeFilePath = str_replace($srcPath . '/', '', $file->getPathname());
            $relativeClassPath = substr($relativeFilePath, 0, -4);
            $classNamespace = str_replace('/', '\\', $relativeClassPath);
            $fullClassName = $namespaceRoot . $classNamespace;

            // Vérifications de base
            if (!class_exists($fullClassName)) {
                continue;
            }

            if (!is_subclass_of($fullClassName, $baseClass)) {
                continue;
            }

            // Vérification pour les classes abstraites
            $reflection = new ReflectionClass($fullClassName);
            if ($reflection->isAbstract()) {
                continue;
            }

            // Vérification spécifique pour les ressources avec put_in_panel
            if ($baseClass === Resource::class && method_exists($fullClassName, 'put_in_panel')) {
                $allowedPanels = $fullClassName::put_in_panel();
                if (!in_array($panelId, $allowedPanels) && !in_array(system_panel_id(), $allowedPanels)) {
                    continue;
                }
            }

            // Vérification pour la méthode isDiscovered (si elle existe)
            if (method_exists($fullClassName, 'isDiscovered') && !$fullClassName::isDiscovered()) {
                continue;
            }

            $components[] = $fullClassName;
        }

        return $components;
    }
}

if (!function_exists('system_panel_id')) {
    /**
     * Retourne l'ID du panel système
     *
     * @return string
     */
    function system_panel_id(): string
    {
        return 'system';
    }
}