<?php
declare(strict_types=1);

//packages/webkernel/src/Helpers/Platform/VersionManager/helpers.php

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
            try {
                $brandingConfig = function_exists('getWebkernelUltimateBrandingConfig') 
                    ? getWebkernelUltimateBrandingConfig() 
                    : [];
                
                $currentCacheKey = md5(serialize($brandingConfig) . filemtime(__FILE__));
                
                if ($cacheKey !== $currentCacheKey) {
                    $cache = null;
                    $cacheKey = $currentCacheKey;
                }
                
                if ($cache === null) {
                    $cache = buildWebkernelBrandingCache();
                }
            } catch (Exception $e) {
                $cache = [];
            }
        }
        
        return $cache;
    }
}

if (!function_exists('buildWebkernelBrandingCache')) {
    function buildWebkernelBrandingCache(): array
    {
        try {
            $baseConfig = function_exists('getWebkernelUltimateBrandingConfig') 
                ? getWebkernelUltimateBrandingConfig() 
                : [];
            
            $extendedConfig = [];
            
            if (function_exists('config_path')) {
                $configPath = config_path('webkernel-branding.php');
                if (file_exists($configPath)) {
                    $fileConfig = require $configPath;
                    if (is_array($fileConfig)) {
                        $extendedConfig = $fileConfig;
                    }
                }
            }
            
            $mergedConfig = $baseConfig;
            foreach ($extendedConfig as $platform => $config) {
                if (!isOfficialSubplatform($platform)) {
                    $mergedConfig[$platform] = $config;
                }
            }
            
            return processInheritanceInConfig($mergedConfig);
        } catch (Exception $e) {
            return [];
        }
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

if (!function_exists('getMergedBrandingConfig')) {
    function getMergedBrandingConfig(): array
    {
        return getWebkernelBrandingCache();
    }
}

if (!function_exists('isOfficialSubplatform')) {
    function isOfficialSubplatform(string $platform): bool
    {
        try {
            $config = function_exists('getWebkernelUltimateBrandingConfig') 
                ? getWebkernelUltimateBrandingConfig() 
                : [];
            
            if (isset($config['webkernel']['subplatforms'][$platform])) {
                return $config['webkernel']['subplatforms'][$platform]['official_subplatform'] ?? false;
            }
            
            return isset($config[$platform]) && ($config[$platform]['official_subplatform'] ?? false);
        } catch (Exception $e) {
            return false;
        }
    }
}


if (!function_exists('platformExists')) {
    function platformExists(string $platform): bool
    {
        try {
            $config = getMergedBrandingConfig();
            
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
                    try {
                        if (class_exists($class)) {
                            return true;
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
            
            if (!empty($detection['file_exists'])) {
                foreach ($detection['file_exists'] as $file) {
                    try {
                        $fullPath = function_exists('base_path') ? base_path($file) : $file;
                        if (file_exists($fullPath)) {
                            return true;
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
            
            if (!empty($detection['constant_defined'])) {
                foreach ($detection['constant_defined'] as $constant) {
                    try {
                        if (defined($constant)) {
                            return true;
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('getCurrentPlatformVersion')) {
    function getCurrentPlatformVersion(string $platform): string
    {
        try {
            $config = getMergedBrandingConfig();
            
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
                
                // Gestion améliorée des constantes de classe
                if (str_contains($constantName, '::')) {
                    $parts = explode('::', $constantName);
                    $className = $parts[0];
                    $constantName = $parts[1];
                    
                    // Essayer différents emplacements pour trouver la classe
                    $possibleLocations = [
                        $className, // Nom direct
                        $platformConfig['app_location'] . $className, // Avec namespace
                        'App\\' . $className, // Dans App
                        'App\\' . $platformConfig['app_location'] . $className, // App + namespace
                    ];
                    
                    foreach ($possibleLocations as $fullClassName) {
                        try {
                            if (class_exists($fullClassName)) {
                                $reflection = new ReflectionClass($fullClassName);
                                if ($reflection->hasConstant($constantName)) {
                                    return $reflection->getConstant($constantName);
                                }
                            }
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                } else {
                    // Constante globale
                    try {
                        if (defined($constantName)) {
                            return constant($constantName);
                        }
                    } catch (Exception $e) {
                        // Continue
                    }
                }
            }
            
            // Vérifier si on a une version explicite dans le config
            if (isset($platformConfig['current_version'])) {
                return $platformConfig['current_version'];
            }
            
            return $versionDetection['fallback'] ?? '1.0.0';
        } catch (Exception $e) {
            return '1.0.0';
        }
    }
}

if (!function_exists('processLanguageKeys')) {
    function processLanguageKeys(array $config): array
    {
        try {
            foreach ($config as $key => &$value) {
                if (is_array($value)) {
                    $value = processLanguageKeys($value);
                } elseif (is_string($value) && str_starts_with($value, 'platform_')) {
                    // Essayer plusieurs méthodes de traduction
                    $translatedValue = $value;
                    
                    if (function_exists('__')) {
                        $translatedValue = __($value);
                    } elseif (function_exists('trans')) {
                        $translatedValue = trans($value);
                    } elseif (function_exists('lang')) {
                        $translatedValue = lang($value);
                    }
                    
                    // Si la traduction est identique à la clé, utiliser un fallback
                    if ($translatedValue === $value) {
                        $translatedValue = generateFallbackTranslation($value);
                    }
                    
                    $value = $translatedValue;
                }
            }
            
            return $config;
        } catch (Exception $e) {
            return $config;
        }
    }
}

if (!function_exists('generateFallbackTranslation')) {
    function generateFallbackTranslation(string $key): string
    {
        // Extraire le nom de la plateforme et le type de clé
        $parts = explode('_', $key);
        
        if (count($parts) >= 3 && $parts[0] === 'platform') {
            $platform = $parts[1];
            $type = implode('_', array_slice($parts, 2));
            
            // Générer des traductions basiques
            $fallbacks = [
                'brand_name' => ucfirst($platform) . ' Platform',
                'name' => ucfirst($platform),
                'description' => ucfirst($platform) . ' - Advanced Platform Solution',
                'codename' => strtoupper($platform),
                'logo_alt' => ucfirst($platform) . ' Logo',
                'meta_title' => ucfirst($platform) . ' - Platform',
                'meta_description' => ucfirst($platform) . ' platform for advanced solutions',
                'meta_keywords' => $platform . ', platform, solution',
                'og_title' => ucfirst($platform) . ' Platform',
                'og_description' => ucfirst($platform) . ' - Advanced Platform Solution',
                'og_site_name' => ucfirst($platform),
            ];
            
            return $fallbacks[$type] ?? ucfirst(str_replace('_', ' ', $type));
        }
        
        return ucfirst(str_replace(['platform_', '_'], ['', ' '], $key));
    }
}

if (!function_exists('clearWebkernelBrandingCache')) {
    function clearWebkernelBrandingCache(): bool
    {
        try {
            // Approche plus robuste pour vider le cache statique
            $tempFunction = function() {
                static $cache = null;
                static $cacheKey = null;
                $cache = null;
                $cacheKey = null;
                return true;
            };
            
            // Forcer la régénération du cache
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate(__FILE__, true);
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('getVersionDetectionDebug')) {
    function getVersionDetectionDebug(string $platform): array
    {
        try {
            $config = getMergedBrandingConfig();
            
            if (isset($config['webkernel']['subplatforms'][$platform])) {
                $platformConfig = $config['webkernel']['subplatforms'][$platform];
            } elseif (isset($config[$platform])) {
                $platformConfig = $config[$platform];
            } else {
                return ['error' => 'Platform not found'];
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
                'search_attempts' => [],
                'app_location' => $platformConfig['app_location'] ?? '',
            ];
            
            if (str_contains($constantName, '::')) {
                $parts = explode('::', $constantName);
                $className = $parts[0];
                $constantName = $parts[1];
                
                // Essayer différents emplacements
                $possibleLocations = [
                    $className,
                    $platformConfig['app_location'] . $className,
                    'App\\' . $className,
                    'App\\' . $platformConfig['app_location'] . $className,
                ];
                
                foreach ($possibleLocations as $fullClassName) {
                    $attempt = [
                        'class_name' => $fullClassName,
                        'exists' => false,
                        'has_constant' => false,
                        'constant_value' => null,
                    ];
                    
                    try {
                        if (class_exists($fullClassName)) {
                            $attempt['exists'] = true;
                            $debug['class_exists'] = true;
                            $debug['reflection_available'] = true;
                            
                            $reflection = new ReflectionClass($fullClassName);
                            if ($reflection->hasConstant($constantName)) {
                                $attempt['has_constant'] = true;
                                $attempt['constant_value'] = $reflection->getConstant($constantName);
                                $debug['constant_exists'] = true;
                                $debug['constant_value'] = $attempt['constant_value'];
                                break;
                            }
                        }
                    } catch (Exception $e) {
                        $attempt['error'] = $e->getMessage();
                    }
                    
                    $debug['search_attempts'][] = $attempt;
                }
            } elseif ($constantName) {
                try {
                    if (defined($constantName)) {
                        $debug['constant_exists'] = true;
                        $debug['constant_value'] = constant($constantName);
                    }
                } catch (Exception $e) {
                    $debug['error'] = $e->getMessage();
                }
            }
            
            if (!$debug['constant_exists']) {
                $debug['fallback_used'] = true;
                $debug['fallback_value'] = $versionDetection['fallback'] ?? '1.0.0';
            }
            
            return $debug;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}

if (!function_exists('getActivePlatform')) {
    function getActivePlatform(): string
    {
        try {
            $config = getMergedBrandingConfig();
            
            // Priorité spéciale pour REAM
            if (isset($config['webkernel']['subplatforms']['ream'])) {
                try {
                    if (platformExists('ream')) {
                        return 'ream';
                    }
                } catch (Exception $e) {
                    // Continue
                }
            }
            
            // Ensuite, vérifier les autres subplatforms officielles
            if (isset($config['webkernel']['subplatforms'])) {
                foreach ($config['webkernel']['subplatforms'] as $subplatform => $subConfig) {
                    if ($subplatform !== 'ream' && ($subConfig['official_subplatform'] ?? false)) {
                        try {
                            if (platformExists($subplatform)) {
                                return $subplatform;
                            }
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }
                
                // Puis les subplatforms non-officielles
                foreach ($config['webkernel']['subplatforms'] as $subplatform => $subConfig) {
                    if ($subplatform !== 'ream' && !($subConfig['official_subplatform'] ?? false)) {
                        try {
                            if (platformExists($subplatform)) {
                                return $subplatform;
                            }
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }
            }
            
            // Vérifier les plateformes principales
            foreach ($config as $platform => $platformConfig) {
                if ($platform !== 'webkernel' && !isset($platformConfig['subplatforms'])) {
                    try {
                        if (platformExists($platform)) {
                            return $platform;
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
            
            // Fallback vers webkernel
            return 'webkernel';
        } catch (Exception $e) {
            return 'webkernel';
        }
    }
}

if (!function_exists('webkernelCurrentPlatformDebug')) {
    function webkernelCurrentPlatformDebug(): string
    {
        try {
            $activePlatform = getActivePlatform();
            
            if (!platformExists($activePlatform)) {
                return htmlspecialchars(json_encode([
                    'error' => 'Platform does not exist',
                    'platform' => $activePlatform,
                    'message' => 'The active platform detection failed or platform is not properly configured',
                    'available_platforms' => getAvailablePlatforms(),
                ], JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8');
            }
            
            $currentVersion = getCurrentPlatformVersion($activePlatform);
            $platformConfig = getPlatformBrandingConfig($activePlatform);
            $detectionInfo = getPlatformDetectionInfo($activePlatform);
            
            // Analyser les détections réussies
            $successfulDetections = [];
            $failedDetections = [];
            
            if (!empty($detectionInfo['class_exists'])) {
                foreach ($detectionInfo['class_exists'] as $class) {
                    try {
                        if (class_exists($class)) {
                            $successfulDetections[] = "Class exists: {$class}";
                        } else {
                            $failedDetections[] = "Class not found: {$class}";
                        }
                    } catch (Exception $e) {
                        $failedDetections[] = "Class check failed: {$class} - {$e->getMessage()}";
                    }
                }
            }
            
            if (!empty($detectionInfo['file_exists'])) {
                foreach ($detectionInfo['file_exists'] as $file) {
                    try {
                        $fullPath = function_exists('base_path') ? base_path($file) : $file;
                        if (file_exists($fullPath)) {
                            $successfulDetections[] = "File exists: {$file}";
                        } else {
                            $failedDetections[] = "File not found: {$file}";
                        }
                    } catch (Exception $e) {
                        $failedDetections[] = "File check failed: {$file} - {$e->getMessage()}";
                    }
                }
            }
            
            if (!empty($detectionInfo['constant_defined'])) {
                foreach ($detectionInfo['constant_defined'] as $constant) {
                    try {
                        if (defined($constant)) {
                            $successfulDetections[] = "Constant defined: {$constant}";
                        } else {
                            $failedDetections[] = "Constant not defined: {$constant}";
                        }
                    } catch (Exception $e) {
                        $failedDetections[] = "Constant check failed: {$constant} - {$e->getMessage()}";
                    }
                }
            }
            
            if ($detectionInfo['default_active'] ?? false) {
                $successfulDetections[] = "Default active: true";
            }
            
            $versionDetectionDebug = getVersionDetectionDebug($activePlatform);
            $editableFields = getSystemPanelEditableFields($activePlatform);
            $isOfficialSubplatform = isOfficialSubplatform($activePlatform);
            $appLocation = getPlatformLocation($activePlatform);
            
            $debugData = [
                'platform_name' => $activePlatform,
                'platform_status' => [
                    'exists' => true,
                    'is_official_subplatform' => $isOfficialSubplatform,
                    'app_location' => $appLocation ?: 'Not specified',
                    'detection_methods_successful' => $successfulDetections ?: ['No specific detection methods (fallback)'],
                    'detection_methods_failed' => $failedDetections,
                ],
                'version_info' => [
                    'current_version' => $currentVersion,
                    'version_detection_debug' => $versionDetectionDebug,
                ],
                'branding_config' => [
                    'brand_name' => $platformConfig['brandName'] ?? 'Not configured',
                    'css_title' => $platformConfig['cssTitle'] ?? 'Not configured',
                    'has_logos' => !empty($platformConfig['logos']),
                    'has_colors' => !empty($platformConfig['colors']),
                    'has_custom_css' => !empty($platformConfig['customCss']),
                    'total_config_keys' => count($platformConfig),
                    'language_keys_processed' => array_filter($platformConfig, function($value, $key) {
                        return is_string($value) && !str_starts_with($value, 'platform_');
                    }, ARRAY_FILTER_USE_BOTH),
                ],
                'system_panel' => [
                    'editable_fields_count' => count($editableFields),
                    'editable_fields' => $editableFields,
                ],
                'runtime_info' => [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'is_laravel' => function_exists('app'),
                    'cache_active' => !empty(getWebkernelBrandingCache()),
                    'available_platforms' => getAvailablePlatforms(),
                    'available_subplatforms' => getAvailableSubplatforms(),
                ],
            ];
            
            // Recommandations améliorées
            $recommendations = [];
            
            if (empty($successfulDetections)) {
                $recommendations[] = "No detection methods succeeded - platform may be running on fallback";
            }
            
            if (!empty($failedDetections)) {
                $recommendations[] = "Some detection methods failed - check configuration";
            }
            
            if ($versionDetectionDebug['fallback_used'] ?? false) {
                $recommendations[] = "Version detection is using fallback - consider defining version constant in: " . ($versionDetectionDebug['app_location'] ?? 'unknown location');
            }
            
            if (empty($editableFields)) {
                $recommendations[] = "No fields are editable in system panel - consider enabling customization";
            }
            
            if (count($platformConfig) < 5) {
                $recommendations[] = "Platform configuration seems minimal - consider adding more branding elements";
            }
            
            // Vérifier si les clés de langue sont traduites
            $untranslatedKeys = array_filter($platformConfig, function($value) {
                return is_string($value) && str_starts_with($value, 'platform_');
            });
            
            if (!empty($untranslatedKeys)) {
                $recommendations[] = "Some language keys are not translated: " . implode(', ', array_keys($untranslatedKeys));
            }
            
            if (!empty($recommendations)) {
                $debugData['recommendations'] = $recommendations;
            }
            
            return htmlspecialchars(json_encode($debugData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
            
        } catch (Exception $e) {
            return htmlspecialchars(json_encode([
                'error' => 'Debug information unavailable',
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => date('Y-m-d H:i:s'),
            ], JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8');
        }
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

if (!function_exists('webkernelPlatformDebug')) {
    function webkernelPlatformDebug(): string
    {
        try {
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
                    'cache_key' => md5(serialize(function_exists('getWebkernelUltimateBrandingConfig') ? getWebkernelUltimateBrandingConfig() : []) . filemtime(__FILE__)),
                    'file_time' => filemtime(__FILE__),
                    'file_path' => __FILE__,
                ],
            ];
            
            return htmlspecialchars(json_encode($debugData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
        } catch (Exception $e) {
            return htmlspecialchars(json_encode(['error' => 'Debug information unavailable'], JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8');
        }
    }
}

if (!function_exists('getPlatformDetectionDebug')) {
    function getPlatformDetectionDebug(): array
    {
        try {
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
                        
                        if (!empty($subConfig['detection']['class_exists'])) {
                            foreach ($subConfig['detection']['class_exists'] as $class) {
                                try {
                                    $debug[$subplatform]['class_checks'][$class] = class_exists($class);
                                } catch (Exception $e) {
                                    $debug[$subplatform]['class_checks'][$class] = false;
                                }
                            }
                        }
                        
                        if (!empty($subConfig['detection']['file_exists'])) {
                            foreach ($subConfig['detection']['file_exists'] as $file) {
                                try {
                                    $fullPath = function_exists('base_path') ? base_path($file) : $file;
                                    $debug[$subplatform]['file_checks'][$file] = file_exists($fullPath);
                                } catch (Exception $e) {
                                    $debug[$subplatform]['file_checks'][$file] = false;
                                }
                            }
                        }
                        
                        if (!empty($subConfig['detection']['constant_defined'])) {
                            foreach ($subConfig['detection']['constant_defined'] as $constant) {
                                try {
                                    $debug[$subplatform]['constant_checks'][$constant] = defined($constant);
                                } catch (Exception $e) {
                                    $debug[$subplatform]['constant_checks'][$constant] = false;
                                }
                            }
                        }
                    }
                }
            }
            
            return $debug;
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('platformAbsoluteUrlAnyPrivatetoPublic')) {
    function platformAbsoluteUrlAnyPrivatetoPublic(?string $path): string
    {
        try {
            if (!$path) {
                return '';
            }
            
            if (str_starts_with($path, '/')) {
                return function_exists('url') ? url($path) : $path;
            }
            
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            
            return function_exists('asset') ? asset($path) : $path;
        } catch (Exception $e) {
            return $path ?? '';
        }
    }
}

if (!function_exists('getAvailablePlatforms')) {
    function getAvailablePlatforms(): array
    {
        try {
            $config = getMergedBrandingConfig();
            $platforms = array_keys($config);
            
            if (isset($config['webkernel']['subplatforms'])) {
                $platforms = array_merge($platforms, array_keys($config['webkernel']['subplatforms']));
            }
            
            return array_unique($platforms);
        } catch (Exception $e) {
            return ['webkernel'];
        }
    }
}

if (!function_exists('getAvailableSubplatforms')) {
    function getAvailableSubplatforms(): array
    {
        try {
            $config = getMergedBrandingConfig();
            
            if (isset($config['webkernel']['subplatforms'])) {
                return array_keys($config['webkernel']['subplatforms']);
            }
            
            return [];
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('getOfficialSubplatforms')) {
    function getOfficialSubplatforms(): array
    {
        try {
            $config = getMergedBrandingConfig();
            $officialSubplatforms = [];
            
            foreach ($config as $platform => $platformConfig) {
                if (($platformConfig['official_subplatform'] ?? false) && $platform !== 'webkernel') {
                    $officialSubplatforms[] = $platform;
                }
            }
            
            if (isset($config['webkernel']['subplatforms'])) {
                foreach ($config['webkernel']['subplatforms'] as $platform => $platformConfig) {
                    if ($platformConfig['official_subplatform'] ?? false) {
                        $officialSubplatforms[] = $platform;
                    }
                }
            }
            
            return $officialSubplatforms;
        } catch (Exception $e) {
            return [];
        }
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
        try {
            if ($platform === null) {
                $platform = getActivePlatform();
            }
            
            $config = getPlatformBrandingConfig($platform);
            $systemPanelConfig = $config['to_change_in_system_panel'] ?? [];
            
            return array_keys(array_filter($systemPanelConfig, function($value) {
                return $value === true;
            }));
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('getPlatformLocation')) {
    function getPlatformLocation(string $platform): string
    {
        try {
            $config = getMergedBrandingConfig();
            
            if (isset($config['webkernel']['subplatforms'][$platform])) {
                return $config['webkernel']['subplatforms'][$platform]['app_location'] ?? '';
            }
            
            return $config[$platform]['app_location'] ?? '';
        } catch (Exception $e) {
            return '';
        }
    }
}

if (!function_exists('getPlatformDetectionInfo')) {
    function getPlatformDetectionInfo(string $platform): array
    {
        try {
            $config = getMergedBrandingConfig();
            
            if (isset($config['webkernel']['subplatforms'][$platform])) {
                return $config['webkernel']['subplatforms'][$platform]['detection'] ?? [];
            }
            
            return $config[$platform]['detection'] ?? [];
        } catch (Exception $e) {
            return [];
        }
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
