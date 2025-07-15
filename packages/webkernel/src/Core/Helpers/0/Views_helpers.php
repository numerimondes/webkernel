<?php

use Illuminate\Support\Facades\Auth;
use Webkernel\Core\Models\Language;
use Webkernel\Core\Models\LanguageTranslation;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Webkernel\Core\Helpers\ResourceLayoutHelper;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Webkernel\Applications\ApplicationContext;



/*
|--------------------------------------------------------------------------
| VIEW AND RENDERING HELPERS - CONCERNING VIEW RENDERING AND INCLUDES - view_helpers.php
|--------------------------------------------------------------------------
| Enhanced view rendering helpers for Webkernel modules with fallback
| support, custom view resolution, and hook-based rendering system.
|--------------------------------------------------------------------------
| Credits: El Moumen Yassine - www.numerimondes.com
|
*/

if (!function_exists('webkernel_include')) {
     /**
     * Inclut une vue du module Webkernel.
     *
     * @return string
     */
    function webkernel_include(string $path, ?string $alias = null): string
    {
        $view = 'webkernel::' . $path;

        if (!view()->exists($view)) {
            // Commentaire dynamique avec dateTime pour IDE ou débogage
            return "<!-- Webkernel view not found: {$view} at " . now() . " -->"; 
        }

        return view($view, ['__alias' => $alias])->render();
    }
}

if (!function_exists('safe_render_hook_view')) {
    function safe_render_hook_view(string $viewName, array $data = []): string
    {
        try {
            // Essayer d'abord la vue exacte
            if (view()->exists($viewName)) {
                return view($viewName, $data)->render();
            }

            // Si c'est une vue webkernel, essayer sans le préfixe
            if (str_starts_with($viewName, 'webkernel::')) {
            $customView = str_replace('webkernel::', '', $viewName);
            if (view()->exists($customView)) {
                return view($customView, $data)->render();
                }
            }

            // Essayer avec le chemin complet
            $fullPath = base_path('packages/webkernel/src/Core/Resources/Views/' . str_replace('webkernel::', '', $viewName) . '.blade.php');
            if (file_exists($fullPath)) {
                return view($viewName, $data)->render();
            }

            Log::warning("RenderHook view not found: {$viewName}");
            return '<!-- RenderHook view not found: ' . $viewName . ' -->';

        } catch (Throwable $e) {
            Log::error("Error rendering renderHook view '{$viewName}': " . $e->getMessage());
            return '<!-- Error rendering view: ' . $e->getMessage() . ' -->';
        }
    }
}

if (!function_exists('getCurrentApplication')) {
    /**
     * Get the current application name (marketing name if available), or a specific key from the module config if $key est fourni.
     *
     * @param string $key
     * @return string|null
     */
    function getCurrentApplication(string $key = 'brandName'): ?string
    {
        $modulesBase = base_path("packages/webkernel/src/Applications/platform/Modules/");
        
        if (is_dir($modulesBase)) {
            $modules = scandir($modulesBase);
            foreach ($modules as $module) {
                if ($module === '.' || $module === '..') continue;
                
                $modulePath = $modulesBase . $module . '/';
                if (is_dir($modulePath)) {
                    $subModules = scandir($modulePath);
                    foreach ($subModules as $subModule) {
                        if ($subModule === '.' || $subModule === '..') continue;
                        
                        $subModulePath = $modulePath . $subModule . '/';
                        if (is_dir($subModulePath)) {
                            $versions = scandir($subModulePath);
                            foreach ($versions as $version) {
                                if ($version === '.' || $version === '..') continue;
                                if (strpos($version, 'v_') === 0) {
                                    $configPath = $subModulePath . $version . '/config.php';
                                    if (file_exists($configPath)) {
                                        $config = require $configPath;
                                        if (is_array($config) && isset($config[$key])) {
                                            return $config[$key];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return null;
    }
}
