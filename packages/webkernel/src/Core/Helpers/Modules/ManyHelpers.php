<?php

if (!function_exists('getCurrentApplication')) {
    /**
     * Retourne la config du module courant selon le contexte d'appel (namespace, panel, etc.)
     * @param string $key
     * @return string|null
     */
    function getCurrentApplication(string $key = 'name'): ?string
    {
        // 1. Analyse du backtrace pour trouver le namespace ou le chemin du fichier appelant
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $callerFile = null;
        $callerNamespace = null;
        foreach ($trace as $frame) {
            if (isset($frame['file'])) {
                $callerFile = $frame['file'];
                if (preg_match('#/Modules/([^/]+)/#', $callerFile, $matches)) {
                    $callerNamespace = $matches[1];
                    break;
                }
            }
            if (isset($frame['class'])) {
                if (preg_match('/Modules\\\\([^\\\\]+)/', $frame['class'], $matches)) {
                    $callerNamespace = $matches[1];
                    break;
                }
            }
        }

        // 2. Si on a trouvé un module, chercher sa config
        if ($callerNamespace) {
            $modulesBase = base_path("packages/webkernel/src/Applications/platform/Modules/{$callerNamespace}/");
            if (is_dir($modulesBase)) {
                $subModules = scandir($modulesBase);
                foreach ($subModules as $subModule) {
                    if ($subModule === '.' || $subModule === '..') continue;
                    $subModulePath = $modulesBase . $subModule . '/';
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

        // 3. Fallback ultime : Webkernel
        $webkernelConfig = base_path('packages/webkernel/src/Applications/Webkernel/Core/v_1_0_0/config.php');
        if (file_exists($webkernelConfig)) {
            $config = require $webkernelConfig;
            if (is_array($config) && isset($config[$key])) {
                return $config[$key];
            }
        }

        // 4. Valeurs par défaut sûres pour éviter les erreurs fatales
        $defaults = [
            'name' => 'Webkernel',
            'logo' => 'packages/webkernel/src/Core/Resources/repo-assets/credits/numerimondes.png',
            'url'  => 'https://webkernel.numerimondes.com',
        ];
        return $defaults[$key] ?? '';
    }
}
