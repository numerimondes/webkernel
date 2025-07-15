<?php

if (!function_exists('getCurrentApplication')) {
    /**
     * Get the current application name (marketing name if available), 
     * or a specific key from the module config if $key est fourni.
     *
     * @param string $key
     * @return string|null
     */
    function getCurrentApplication(string $key = 'name'): ?string
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
