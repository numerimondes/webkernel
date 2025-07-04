<?php
declare(strict_types=1);

/**
 * Webkernel Platform Branding System
 * Dynamic branding detection and configuration management with local cache
 * 
 * USAGE EXAMPLES IN BLADE VIEWS:
 * 
 * // Get complete active platform configuration
 * {{ webkernelPlatform() }}
 * 
 * // Get specific values
 * {{ webkernelPlatform('brandName') }}
 * {{ webkernelPlatform('logos.light_logo') }}
 * {{ webkernelPlatform('cssTitle') }}
 * {{ webkernelPlatform('app_location') }}
 * 
 * // Get information from specific platform
 * {{ getPlatformInfo('student_flow', 'name') }}
 * {{ getPlatformInfo('webkernel', 'series') }}
 * 
 * // Check if platform is active
 * @if(isPlatformActive('webkernel'))
 *     <p>Webkernel is active</p>
 * @endif
 * 
 * // Generate absolute URL for asset
 * <img src="{{ platformAbsoluteUrlAnyPrivatetoPublic(webkernelPlatform('logos.light_logo')) }}" 
 *      alt="{{ webkernelPlatform('logos.alt') }}">
 * 
 * // Get available platforms list
 * @foreach(getAvailablePlatforms() as $platform)
 *     <option value="{{ $platform }}">{{ getPlatformInfo($platform, 'brandName') }}</option>
 * @endforeach
 * 
 * // Get current version
 * <p>Version: {{ getCurrentPlatformVersion(getActivePlatform()) }}</p>
 * 
 * // Debug information (now safe for Blade)
 * {!! webkernelPlatformDebug() !!}
 * 
 * El Moumen Yassine - Numerimondes
 * <yassine@numerimondes.com>
 * www.numerimondes.com
 */


if (!function_exists('getWebkernelBrandingCache')) {
    function getWebkernelBrandingCache(): array
    {
        static $cache = null;
        static $cacheKey = null;
        
        if ($cache === null) {
            $currentCacheKey = md5(serialize(getWebkernelUltimateBrandingConfig()) . filemtime(__FILE__));
            
            if ($cacheKey !== $currentCacheKey) {
                $cache = null;
                $cacheKey = $currentCacheKey;
            }
            
            if ($cache === null) {
                $cache = buildWebkernelBrandingCache();
            }
        }
        
        return $cache;
    }
}

if (!function_exists('buildWebkernelBrandingCache')) {
    function buildWebkernelBrandingCache(): array
    {
        $baseConfig = getWebkernelUltimateBrandingConfig();
        $extendedConfig = [];
        
        $configPath = config_path('webkernel-branding.php');
        if (file_exists($configPath)) {
            $fileConfig = require $configPath;
            if (is_array($fileConfig)) {
                $extendedConfig = $fileConfig;
            }
        }
        
        $mergedConfig = $baseConfig;
        foreach ($extendedConfig as $platform => $config) {
            if (!isOfficialSubplatform($platform)) {
                $mergedConfig[$platform] = $config;
            }
        }
        
        return processInheritanceInConfig($mergedConfig);
    }
}

if (!function_exists('processInheritanceInConfig')) {
    function processInheritanceInConfig(array $config): array
    {
        foreach ($config as $platform => &$platformConfig) {
            if (!isset($platformConfig['versions'])) {
                continue;
            }
            
            foreach ($platformConfig['versions'] as &$versionConfig) {
                if (isset($versionConfig['config']['inherits_from'])) {
                    $inheritVersion = $versionConfig['config']['inherits_from'];
                    $baseConfig = findVersionConfig($platformConfig['versions'], $inheritVersion);
                    
                    if ($baseConfig) {
                        $versionConfig['config'] = array_merge($baseConfig, $versionConfig['config']);
                        unset($versionConfig['config']['inherits_from']);
                    }
                }
            }
        }
        
        return $config;
    }
}

if (!function_exists('findVersionConfig')) {
    function findVersionConfig(array $versions, string $targetVersion): ?array
    {
        foreach ($versions as $versionConfig) {
            if ($versionConfig['version'] === $targetVersion) {
                return $versionConfig['config'];
            }
        }
        return null;
    }
}

if (!function_exists('clearWebkernelBrandingCache')) {
    function clearWebkernelBrandingCache(): void
    {
        if (function_exists('getWebkernelBrandingCache')) {
            $reflection = new ReflectionFunction('getWebkernelBrandingCache');
            $staticVars = $reflection->getStaticVariables();
            
            if (isset($staticVars['cache'])) {
                $staticVars['cache'] = null;
                $staticVars['cacheKey'] = null;
            }
        }
    }
}

if (!function_exists('getMergedBrandingConfig')) {
    function getMergedBrandingConfig(): array
    {
        return getWebkernelBrandingCache();
    }
}

if (!function_exists('isOfficialSubplatform')) {
    function isOfficialSubplatform(string $platform): bool
    {
        $config = getWebkernelUltimateBrandingConfig();
        
        // Check if it's a direct subplatform
        if (isset($config['webkernel']['subplatforms'][$platform])) {
            return $config['webkernel']['subplatforms'][$platform]['official_subplatform'] ?? false;
        }
        
        // Check if it's a main platform
        return isset($config[$platform]) && ($config[$platform]['official_subplatform'] ?? false);
    }
}

if (!function_exists('getActivePlatform')) {
    function getActivePlatform(): string
    {
        $config = getMergedBrandingConfig();
        
        // First check webkernel subplatforms in priority order
        if (isset($config['webkernel']['subplatforms'])) {
            // Check REAM first since it's more specific
            if (isset($config['webkernel']['subplatforms']['ream']) && platformExists('ream')) {
                return 'ream';
            }
            
            // Then check other subplatforms
            foreach ($config['webkernel']['subplatforms'] as $subplatform => $subConfig) {
                if ($subplatform !== 'ream' && platformExists($subplatform)) {
                    return $subplatform;
                }
            }
        }
        
        // Then check main platforms
        $sortedPlatforms = [];
        foreach ($config as $platform => $platformConfig) {
            if (isset($platformConfig['official_subplatform']) && $platformConfig['official_subplatform']) {
                array_unshift($sortedPlatforms, $platform);
            } else {
                $sortedPlatforms[] = $platform;
            }
        }
        
        foreach ($sortedPlatforms as $platform) {
            if (platformExists($platform)) {
                return $platform;
            }
        }
        
        return 'webkernel';
    }
}

if (!function_exists('platformExists')) {
    function platformExists(string $platform): bool
    {
        $config = getMergedBrandingConfig();
        
        // Check if it's a subplatform
        if (isset($config['webkernel']['subplatforms'][$platform])) {
            $platformConfig = $config['webkernel']['subplatforms'][$platform];
        } elseif (isset($config[$platform])) {
            $platformConfig = $config[$platform];
        } else {
            return false;
        }
        
        $detection = $platformConfig['detection'] ?? [];
        
        if ($detection['default_active'] ?? false) {
            return true;
        }
        
        if (!empty($detection['class_exists'])) {
            foreach ($detection['class_exists'] as $class) {
                if (class_exists($class)) {
                    return true;
                }
            }
        }
        
        if (!empty($detection['file_exists'])) {
            foreach ($detection['file_exists'] as $file) {
                if (file_exists(base_path($file))) {
                    return true;
                }
            }
        }
        
        if (!empty($detection['constant_defined'])) {
            foreach ($detection['constant_defined'] as $constant) {
                if (defined($constant)) {
                    return true;
                }
            }
        }
        
        return false;
    }
}

if (!function_exists('getCurrentPlatformVersion')) {
    function getCurrentPlatformVersion(string $platform): string
    {
        $config = getMergedBrandingConfig();
        
        // Check if it's a subplatform
        if (isset($config['webkernel']['subplatforms'][$platform])) {
            $platformConfig = $config['webkernel']['subplatforms'][$platform];
        } elseif (isset($config[$platform])) {
            $platformConfig = $config[$platform];
        } else {
            return '1.0.0';
        }
        
        $versionDetection = $platformConfig['version_detection'] ?? [];
        
        if (!empty($versionDetection['constant'])) {
            $constantName = $versionDetection['constant'];
            
            // Handle Application::CONSTANT format
            if (str_contains($constantName, '::')) {
                $parts = explode('::', $constantName);
                $className = $parts[0];
                $constantName = $parts[1];
                
                if (class_exists($className)) {
                    $reflection = new ReflectionClass($className);
                    if ($reflection->hasConstant($constantName)) {
                        return $reflection->getConstant($constantName);
                    }
                }
            } elseif (defined($constantName)) {
                return constant($constantName);
            }
        }
        
        if (isset($platformConfig['current_version'])) {
            return $platformConfig['current_version'];
        }
        
        return $versionDetection['fallback'] ?? '1.0.0';
    }
}

if (!function_exists('compareVersionWithOperator')) {
    function compareVersionWithOperator(string $currentVersion, string $operator, string $compareVersion): bool
    {
        return version_compare(
            ltrim($currentVersion, 'v'),
            ltrim($compareVersion, 'v'),
            $operator
        );
    }
}

if (!function_exists('getPlatformBrandingConfig')) {
    function getPlatformBrandingConfig(string $platform): array
    {
        $config = getMergedBrandingConfig();
        
        // Check if it's a subplatform
        if (isset($config['webkernel']['subplatforms'][$platform])) {
            $platformConfig = $config['webkernel']['subplatforms'][$platform];
        } elseif (isset($config[$platform])) {
            $platformConfig = $config[$platform];
        } else {
            return [];
        }
        
        $currentVersion = getCurrentPlatformVersion($platform);
        $result = [];
        
        // Add base platform info
        $result['app_location'] = $platformConfig['app_location'] ?? '';
        $result['official_subplatform'] = $platformConfig['official_subplatform'] ?? false;
        $result['current_version'] = $currentVersion;
        
        // Get version-specific config
        if (isset($platformConfig['versions'])) {
            foreach ($platformConfig['versions'] as $versionConfig) {
                if (compareVersionWithOperator(
                    $currentVersion,
                    $versionConfig['operator'],
                    $versionConfig['version']
                )) {
                    $result = array_merge($result, $versionConfig['config']);
                    break;
                }
            }
        }
        
        // Process language keys
        $result = processLanguageKeys($result);
        
        return $result;
    }
}

if (!function_exists('processLanguageKeys')) {
    function processLanguageKeys(array $config): array
    {
        foreach ($config as $key => &$value) {
            if (is_array($value)) {
                $value = processLanguageKeys($value);
            } elseif (is_string($value) && str_starts_with($value, 'platform_')) {
                if (function_exists('lang')) {
                    $value = lang($value);
                }
            }
        }
        
        return $config;
    }
}

if (!function_exists('webkernelPlatform')) {
    function webkernelPlatform(?string $key = null): mixed
    {
        $activePlatform = getActivePlatform();
        $config = getPlatformBrandingConfig($activePlatform);
        
        if ($key === null) {
            return json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }
        
        if (str_contains($key, '.')) {
            $keys = explode('.', $key);
            $value = $config;
            
            foreach ($keys as $subKey) {
                if (!is_array($value) || !isset($value[$subKey])) {
                    return '';
                }
                $value = $value[$subKey];
            }
            
            return is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_SLASHES);
        }
        
        $result = $config[$key] ?? '';
        return is_string($result) ? $result : json_encode($result, JSON_UNESCAPED_SLASHES);
    }
}

if (!function_exists('webkernelPlatfom')) {
    function webkernelPlatfom(?string $key = null): mixed
    {
        return webkernelPlatform($key);
    }
}

if (!function_exists('webkernelPlatformDebug')) {
    function webkernelPlatformDebug(): string
    {
        $activePlatform = getActivePlatform();
        $config = getPlatformBrandingConfig($activePlatform);
        
        $debugData = [
            'active_platform' => $activePlatform,
            'current_version' => getCurrentPlatformVersion($activePlatform),
            'platform_exists' => platformExists($activePlatform),
            'available_platforms' => getAvailablePlatforms(),
            'available_subplatforms' => getAvailableSubplatforms(),
            'official_subplatforms' => getOfficialSubplatforms(),
            'config' => $config,
            'detection_info' => getPlatformDetectionInfo($activePlatform),
            'editable_fields' => getSystemPanelEditableFields(),
            'version_detection_debug' => getVersionDetectionDebug($activePlatform),
            'platform_detection_debug' => getPlatformDetectionDebug(),
            'cache_info' => [
                'cache_key' => md5(serialize(getWebkernelUltimateBrandingConfig()) . filemtime(__FILE__)),
                'file_time' => filemtime(__FILE__),
                'file_path' => __FILE__,
            ],
        ];

        return '<pre style="background: #f5f5f5; padding: 15px; border: 1px solid #ddd; border-radius: 5px; overflow-x: auto;">' . 
               htmlspecialchars(json_encode($debugData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') . 
               '</pre>';
    }
}

if (!function_exists('getVersionDetectionDebug')) {
    function getVersionDetectionDebug(string $platform): array
    {
        $config = getMergedBrandingConfig();
        
        if (isset($config['webkernel']['subplatforms'][$platform])) {
            $platformConfig = $config['webkernel']['subplatforms'][$platform];
        } elseif (isset($config[$platform])) {
            $platformConfig = $config[$platform];
        } else {
            return [];
        }
        
        $versionDetection = $platformConfig['version_detection'] ?? [];
        $constantName = $versionDetection['constant'] ?? '';
        
        $debug = [
            'constant_name' => $constantName,
            'constant_exists' => false,
            'constant_value' => null,
            'reflection_available' => false,
            'class_exists' => false,
            'fallback_used' => false,
        ];
        
        if (str_contains($constantName, '::')) {
            $parts = explode('::', $constantName);
            $className = $parts[0];
            $constantName = $parts[1];
            
            $debug['class_exists'] = class_exists($className);
            
            if ($debug['class_exists']) {
                $debug['reflection_available'] = true;
                $reflection = new ReflectionClass($className);
                $debug['constant_exists'] = $reflection->hasConstant($constantName);
                
                if ($debug['constant_exists']) {
                    $debug['constant_value'] = $reflection->getConstant($constantName);
                }
            }
        } elseif (defined($constantName)) {
            $debug['constant_exists'] = true;
            $debug['constant_value'] = constant($constantName);
        }
        
        if (!$debug['constant_exists']) {
            $debug['fallback_used'] = true;
        }
        
        return $debug;
    }
}

if (!function_exists('getPlatformDetectionDebug')) {
    function getPlatformDetectionDebug(): array
    {
        $config = getMergedBrandingConfig();
        $debug = [];
        
        foreach ($config as $platform => $platformConfig) {
            if (isset($platformConfig['subplatforms'])) {
                foreach ($platformConfig['subplatforms'] as $subplatform => $subConfig) {
                    $debug[$subplatform] = [
                        'is_subplatform' => true,
                        'exists' => platformExists($subplatform),
                        'detection' => $subConfig['detection'] ?? [],
                        'class_checks' => [],
                        'file_checks' => [],
                        'constant_checks' => [],
                    ];
                    
                    // Check classes
                    if (!empty($subConfig['detection']['class_exists'])) {
                        foreach ($subConfig['detection']['class_exists'] as $class) {
                            $debug[$subplatform]['class_checks'][$class] = class_exists($class);
                        }
                    }
                    
                    // Check files
                    if (!empty($subConfig['detection']['file_exists'])) {
                        foreach ($subConfig['detection']['file_exists'] as $file) {
                            $debug[$subplatform]['file_checks'][$file] = file_exists(base_path($file));
                        }
                    }
                    
                    // Check constants
                    if (!empty($subConfig['detection']['constant_defined'])) {
                        foreach ($subConfig['detection']['constant_defined'] as $constant) {
                            $debug[$subplatform]['constant_checks'][$constant] = defined($constant);
                        }
                    }
                }
            }
        }
        
        return $debug;
    }
}

if (!function_exists('platformAbsoluteUrlAnyPrivatetoPublic')) {
    function platformAbsoluteUrlAnyPrivatetoPublic(?string $path): string
    {
        if (!$path) {
            return '';
        }
        
        if (str_starts_with($path, '/')) {
            return url($path);
        }
        
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        return asset($path);
    }
}

if (!function_exists('getAvailablePlatforms')) {
    function getAvailablePlatforms(): array
    {
        $config = getMergedBrandingConfig();
        $platforms = array_keys($config);
        
        // Add subplatforms
        if (isset($config['webkernel']['subplatforms'])) {
            $platforms = array_merge($platforms, array_keys($config['webkernel']['subplatforms']));
        }
        
        return array_unique($platforms);
    }
}

//FROM HERE CORRECT IC PLEASE

if (!function_exists('getAvailableSubplatforms')) {
    function getAvailableSubplatforms(): array
    {
        $config = getMergedBrandingConfig();
        
        if (isset($config['webkernel']['subplatforms'])) {
            return array_keys($config['webkernel']['subplatforms']);
        }
        
        return [];
    }
}

//FROM HERE CORRECT IC PLEASE

// Fix the duplicate function definition issue
if (!function_exists('getOfficialSubplatforms')) {
    function getOfficialSubplatforms(): array
    {
        $config = getMergedBrandingConfig();
        $officialSubplatforms = [];
        
        // Check main platforms
        foreach ($config as $platform => $platformConfig) {
            if (($platformConfig['official_subplatform'] ?? false) && $platform !== 'webkernel') {
                $officialSubplatforms[] = $platform;
            }
        }
        
        // Check subplatforms
        if (isset($config['webkernel']['subplatforms'])) {
            foreach ($config['webkernel']['subplatforms'] as $platform => $platformConfig) {
                if ($platformConfig['official_subplatform'] ?? false) {
                    $officialSubplatforms[] = $platform;
                }
            }
        }
        
        return $officialSubplatforms;
    }
}

if (!function_exists('getPlatformInfo')) {
    function getPlatformInfo(string $platform, ?string $key = null): mixed
    {
        $config = getPlatformBrandingConfig($platform);
        
        if ($key === null) {
            return json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }
        
        if (str_contains($key, '.')) {
            $keys = explode('.', $key);
            $value = $config;
            
            foreach ($keys as $subKey) {
                if (!is_array($value) || !isset($value[$subKey])) {
                    return '';
                }
                $value = $value[$subKey];
            }
            
            return is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_SLASHES);
        }
        
        $result = $config[$key] ?? '';
        return is_string($result) ? $result : json_encode($result, JSON_UNESCAPED_SLASHES);
    }
}

if (!function_exists('isPlatformActive')) {
    function isPlatformActive(string $platform): bool
    {
        return getActivePlatform() === $platform;
    }
}

if (!function_exists('canChangeInSystemPanel')) {
    function canChangeInSystemPanel(string $key, ?string $platform = null): bool
    {
        if ($platform === null) {
            $platform = getActivePlatform();
        }
        
        $config = getPlatformBrandingConfig($platform);
        $systemPanelConfig = $config['to_change_in_system_panel'] ?? [];
        
        return $systemPanelConfig[$key] ?? false;
    }
}

if (!function_exists('getSystemPanelEditableFields')) {
    function getSystemPanelEditableFields(?string $platform = null): array
    {
        if ($platform === null) {
            $platform = getActivePlatform();
        }
        
        $config = getPlatformBrandingConfig($platform);
        $systemPanelConfig = $config['to_change_in_system_panel'] ?? [];
        
        return array_keys(array_filter($systemPanelConfig, function($value) {
            return $value === true;
        }));
    }
}

if (!function_exists('getPlatformLocation')) {
    function getPlatformLocation(string $platform): string
    {
        $config = getMergedBrandingConfig();
        
        // Check if it's a subplatform
        if (isset($config['webkernel']['subplatforms'][$platform])) {
            return $config['webkernel']['subplatforms'][$platform]['app_location'] ?? '';
        }
        
        return $config[$platform]['app_location'] ?? '';
    }
}

if (!function_exists('getPlatformDetectionInfo')) {
    function getPlatformDetectionInfo(string $platform): array
    {
        $config = getMergedBrandingConfig();
        
        // Check if it's a subplatform
        if (isset($config['webkernel']['subplatforms'][$platform])) {
            return $config['webkernel']['subplatforms'][$platform]['detection'] ?? [];
        }
        
        return $config[$platform]['detection'] ?? [];
    }
}

if (!function_exists('validatePlatformConfig')) {
    function validatePlatformConfig(array $config): bool
    {
        $requiredKeys = ['detection', 'version_detection', 'official_subplatform', 'versions', 'app_location'];
        
        foreach ($requiredKeys as $key) {
            if (!isset($config[$key])) {
                return false;
            }
        }
        
        $detectionKeys = ['class_exists', 'file_exists', 'constant_defined', 'default_active'];
        foreach ($detectionKeys as $key) {
            if (!isset($config['detection'][$key])) {
                return false;
            }
        }
        
        $versionDetectionKeys = ['constant', 'fallback'];
        foreach ($versionDetectionKeys as $key) {
            if (!isset($config['version_detection'][$key])) {
                return false;
            }
        }
        
        return true;
    }
}