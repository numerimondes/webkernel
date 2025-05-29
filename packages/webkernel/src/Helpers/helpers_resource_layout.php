<?php

/*
|--------------------------------------------------------------------------
| RESOURCE LAYOUT SYSTEM HELPERS - CONCERNING FILAMENT RESOURCE LAYOUTS
|--------------------------------------------------------------------------
| Enhanced layout management system for Filament resources with package-based
| auto-detection and convention-over-configuration approach. Supports dynamic
| layout switching and multi-package resource management.
|--------------------------------------------------------------------------
| Credits: El Moumen Yassine - www.numerimondes.com
|
*/

if (!function_exists('webkernel_layout')) {
    /**
     * Set layout for any resource
     *
     * Usage anywhere:
     * webkernel_layout('user', 'tabs');    // UserResource with tabs
     * webkernel_layout('user', 'popup');   // UserResource with popup
     * webkernel_layout('product', 'grid'); // ProductResource with grid
     */
    function webkernel_layout(string $resource, string $layoutKey): void
    {
        session(["webkernel_layout_{$resource}" => $layoutKey]);
    }
}

if (!function_exists('webkernel_detect_package_from_class')) {
    /**
     * Detect package name from class namespace
     */
    function webkernel_detect_package_from_class(?string $className): string
    {
        if (!$className) {
            return 'webkernel';
        }

        // Extract package from namespace (e.g., Solecoles\Filament\Resources\UserResource -> solecoles)
        $parts = explode('\\', $className);
        if (count($parts) > 0) {
            $packageName = strtolower($parts[0]);
            // If it's App namespace, consider it as the main application
            if ($packageName === 'app') {
                return 'webkernel';
            }
            return $packageName;
        }

        return 'webkernel';
    }
}

if (!function_exists('webkernel_detect_resource_name_from_class')) {
    /**
     * Detect resource name from class (user, product, etc.)
     */
    function webkernel_detect_resource_name_from_class(?string $className): string
    {
        if (!$className) {
            return 'user';
        }

        // Extract resource name from class name (e.g., UserResource -> user, ProductResource -> product)
        $parts = explode('\\', $className);
        $className = end($parts);

        // Extract the resource name intelligently
        // For UserResource -> User, ProductResource -> Product, etc.
        if (str_ends_with($className, 'Resource')) {
            $resourceName = substr($className, 0, -8); // Remove 'Resource' suffix
            return strtolower($resourceName);
        }

        // If no 'Resource' suffix, use the full class name
        return strtolower($className);
    }
}

if (!function_exists('webkernel_get_calling_class')) {
    /**
     * Get the calling class automatically
     */
    function webkernel_get_calling_class(): ?string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);

        foreach ($trace as $frame) {
            if (isset($frame['class']) && str_contains($frame['class'], 'Resource')) {
                return $frame['class'];
            }
        }

        return null;
    }
}

if (!function_exists('webkernel_get_calling_class')) {
    /**
     * Get the calling class automatically
     */
    function webkernel_get_calling_class(): ?string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);

        foreach ($trace as $frame) {
            if (isset($frame['class']) && str_contains($frame['class'], 'Resource')) {
                return $frame['class'];
            }
        }

        return null;
    }
}

if (!function_exists('webkernel_get_calling_class')) {
    /**
     * Get the calling class automatically
     */
    function webkernel_get_calling_class(): ?string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);

        foreach ($trace as $frame) {
            if (isset($frame['class']) && str_contains($frame['class'], 'Resource')) {
                return $frame['class'];
            }
        }

        return null;
    }
}

if (!function_exists('webkernel_get_layout')) {
    /**
     * Get layout class using convention: packages/{package}/src/Layouts/{Resource}/{Layout}Layout.php
     */
    function webkernel_get_layout(string $resource, ?string $package = null, ?string $resourceClass = null): ?string
    {
        $layoutName = 'default';

        // Auto-detect package if not provided
        if (!$package) {
            $package = webkernel_detect_package_from_class($resourceClass);
        }

        // Auto-detect resource name if not provided
        if (!$resource || $resource === 'user') {
            $resource = webkernel_detect_resource_name_from_class($resourceClass);
        }

        // Get layout name from resource class property
        if ($resourceClass && class_exists($resourceClass)) {
            $reflection = new \ReflectionClass($resourceClass);
            if ($reflection->hasProperty('webkernel_layout')) {
                $property = $reflection->getProperty('webkernel_layout');
                if ($property->isStatic()) {
                    $layoutName = $resourceClass::$webkernel_layout;
                }
            }
        }

        // Build layout class using convention
        // packages/{package}/src/Layouts/{Resource}/{Layout}Layout.php
        $layoutClass = ucfirst($package) . '\\Layouts\\' . ucfirst($resource) . '\\' . ucfirst($layoutName) . 'Layout';

        // Try to load the class file manually if not autoloaded
        $layoutFile = "packages/{$package}/src/Layouts/" . ucfirst($resource) . "/" . ucfirst($layoutName) . "Layout.php";
        if (file_exists($layoutFile) && !class_exists($layoutClass)) {
            require_once $layoutFile;
        }

        // Return class if it exists
        if (class_exists($layoutClass)) {
            return $layoutClass;
        }

        // Fallback to webkernel package if different package
        if ($package !== 'webkernel') {
            $fallbackClass = 'Webkernel\\Layouts\\' . ucfirst($resource) . '\\' . ucfirst($layoutName) . 'Layout';
            $fallbackFile = "packages/webkernel/src/Layouts/" . ucfirst($resource) . "/" . ucfirst($layoutName) . "Layout.php";
            if (file_exists($fallbackFile) && !class_exists($fallbackClass)) {
                require_once $fallbackFile;
            }
            if (class_exists($fallbackClass)) {
                return $fallbackClass;
            }
        }

        return null;
    }
}

if (!function_exists('webkernel_form')) {
    /**
     * Get form layout for resource
     */
    function webkernel_form($form, ?string $resourceClass = null)
    {
        // Auto-detect calling class if not provided
        if (!$resourceClass) {
            $resourceClass = webkernel_get_calling_class();
        }

        // Auto-detect everything from the calling class
        $package = webkernel_detect_package_from_class($resourceClass);
        $resource = webkernel_detect_resource_name_from_class($resourceClass);

        $layoutClass = webkernel_get_layout($resource, $package, $resourceClass);

        if ($layoutClass && class_exists($layoutClass) && method_exists($layoutClass, 'form')) {
            return $layoutClass::form($form);
        }

        return $form;
    }
}

if (!function_exists('webkernel_table')) {
    /**
     * Get table layout for resource
     */
    function webkernel_table($table, ?string $resourceClass = null)
    {
        // Auto-detect calling class if not provided
        if (!$resourceClass) {
            $resourceClass = webkernel_get_calling_class();
        }

        // Auto-detect everything from the calling class
        $package = webkernel_detect_package_from_class($resourceClass);
        $resource = webkernel_detect_resource_name_from_class($resourceClass);

        $layoutClass = webkernel_get_layout($resource, $package, $resourceClass);

        if ($layoutClass && class_exists($layoutClass) && method_exists($layoutClass, 'table')) {
            return $layoutClass::table($table);
        }

        return $table;
    }
}

if (!function_exists('webkernel_pages')) {
    /**
     * Get pages configuration for resource - AUTO-DETECTS calling class
     */
    function webkernel_pages(?string $resourceClass = null): array
    {
        // Auto-detect calling class if not provided
        if (!$resourceClass) {
            $resourceClass = webkernel_get_calling_class();
        }

        if (!$resourceClass) {
            return [];
        }

        $package = webkernel_detect_package_from_class($resourceClass);
        $resource = webkernel_detect_resource_name_from_class($resourceClass);

        $layoutClass = webkernel_get_layout($resource, $package, $resourceClass);

        if ($layoutClass && class_exists($layoutClass) && method_exists($layoutClass, 'pages')) {
            return $layoutClass::pages();
        }

        return [];
    }
}

if (!function_exists('webkernel_available_layouts')) {
    /**
     * Get available layouts for a resource
     */
    function webkernel_available_layouts(string $resource, string $package = 'webkernel'): array
    {
        return config("webkernel.layouts.{$package}.{$resource}", []);
    }
}

if (!function_exists('webkernel_change_layout_dynamically')) {
    /**
     * Change layout dynamically via user settings
     */
    function webkernel_change_layout_dynamically(string $resource, string $layoutKey, string $package = 'webkernel'): bool
    {
        $layouts = webkernel_available_layouts($resource, $package);

        if (isset($layouts[$layoutKey])) {
            // Save in session for immediate effect
            session(["webkernel_layout_{$resource}" => $layoutKey]);

            // TODO: Save in user preferences table for persistence
            // This could be implemented later when user preferences system is ready

            return true;
        }

        return false;
    }
}
