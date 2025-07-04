<?php

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
 * {{ getPlatformInfo('student.flow', 'name') }}
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

declare(strict_types=1);

if (!function_exists('getWebkernelUltimateBrandingConfig')) {
    function getWebkernelUltimateBrandingConfig(): array
    {
        return [
            'webkernel' => [
                'app_location' => 'Webkernel\\',
                'detection' => [
                    'class_exists' => [],
                    'file_exists' => [],
                    'constant_defined' => ['Application::WEBKERNEL_VERSION'],
                    'default_active' => true,
                ],
                'version_detection' => [
                    'constant' => 'Application::WEBKERNEL_VERSION',
                    'fallback' => '1.0.0',
                ],
                'official_subplatform' => false,
                'subplatforms' => [
                    'student_flow' => [
                        'app_location' => 'StudentFlow\\',
                        'detection' => [
                            'class_exists' => ['StudentFlow\\StudentFlowServiceProvider'],
                            'file_exists' => [],
                            'constant_defined' => ['Application::STUDENT_FLOW_VERSION'],
                            'default_active' => false,
                        ],
                        'version_detection' => [
                            'constant' => 'Application::STUDENT_FLOW_VERSION',
                            'fallback' => '0.9.0',
                        ],
                        'official_subplatform' => true,
                        'versions' => [
                            [
                                'operator' => '<',
                                'version' => '1.0.0',
                                'config' => [
                                    'name' => 'StudentFlow - Gestion Étudiante',
                                    'brandName' => 'StudentFlow',
                                    'description' => 'Plateforme de gestion complète pour étudiants et établissements',
                                    'cssTitle' => 'student-flow-style',
                                    'logos' => [
                                        'default_logo' => '/logos/student-flow-default.png',
                                        'light_logo' => '/logos/student-flow-light.png',
                                        'dark_logo' => '/logos/student-flow-dark.png',
                                        'alt' => 'StudentFlow Logo',
                                    ],
                                    'codename' => 'Academic Stream',
                                    'series' => '24.12',
                                    'checksum' => 'sha256-studentflow-v0',
                                    'metadata' => [
                                        'title' => 'StudentFlow - Gestion Étudiante',
                                        'description' => 'Plateforme moderne de gestion étudiante',
                                        'keywords' => 'étudiants, gestion, académique, formation',
                                        'author' => 'El Moumen Yassine',
                                        'robots' => 'index, follow',
                                    ],
                                    'opengraph' => [
                                        'title' => 'StudentFlow - Gestion Étudiante',
                                        'description' => 'Plateforme moderne de gestion étudiante',
                                        'image' => '/logos/student-flow-og.png',
                                        'type' => 'website',
                                        'url' => '',
                                        'site_name' => 'StudentFlow',
                                    ],
                                    'to_change_in_system_panel' => [
                                        'name' => true,
                                        'brandName' => true,
                                        'description' => true,
                                        'logos' => false,
                                        'cssTitle' => false,
                                        'codename' => false,
                                        'series' => false,
                                        'checksum' => false,
                                        'metadata' => true,
                                        'opengraph' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'ream' => [
                        'app_location' => 'REAM\\',
                        'detection' => [
                            'class_exists' => ['REAM\\REAMServiceProvider'],
                            'file_exists' => [],
                            'constant_defined' => ['Application::REAM_VERSION'],
                            'default_active' => false,
                        ],
                        'version_detection' => [
                            'constant' => 'Application::REAM_VERSION',
                            'fallback' => '0.1.0',
                        ],
                        'official_subplatform' => true,
                        'versions' => [
                            [
                                'operator' => '<',
                                'version' => '1.0.0',
                                'config' => [
                                    'name' => 'REAM - Audit Énergies Renouvelables',
                                    'brandName' => 'REAM',
                                    'description' => 'Gestion des audits d\'énergies renouvelables',
                                    'cssTitle' => 'ream-style',
                                    'logos' => [
                                        'default_logo' => '/packages/webkernel/src/Resources/repo-assets/credits/ream.svg',
                                        'light_logo' => '/packages/webkernel/src/Resources/repo-assets/credits/ream.svg',
                                        'dark_logo' => '/packages/webkernel/src/Resources/repo-assets/credits/ream.svg',
                                        'alt' => 'REAM Logo',
                                    ],
                                    'codename' => 'Green Audit',
                                    'series' => '24.12',
                                    'checksum' => 'sha256-ream-v0',
                                    'metadata' => [
                                        'title' => 'REAM - Audit Énergies Renouvelables',
                                        'description' => 'Plateforme de gestion des audits énergétiques',
                                        'keywords' => 'audit, énergie, renouvelable, environnement',
                                        'author' => 'El Moumen Yassine',
                                        'robots' => 'index, follow',
                                    ],
                                    'opengraph' => [
                                        'title' => 'REAM - Audit Énergies Renouvelables',
                                        'description' => 'Plateforme de gestion des audits énergétiques',
                                        'image' => '/packages/webkernel/src/Resources/repo-assets/credits/ream.svg',
                                        'type' => 'website',
                                        'url' => '',
                                        'site_name' => 'REAM',
                                    ],
                                    'to_change_in_system_panel' => [
                                        'name' => true,
                                        'brandName' => true,
                                        'description' => true,
                                        'logos' => false,
                                        'cssTitle' => false,
                                        'codename' => false,
                                        'series' => false,
                                        'checksum' => false,
                                        'metadata' => true,
                                        'opengraph' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'versions' => [
                    [
                        'operator' => '<',
                        'version' => '1.0.0',
                        'config' => [
                            'name' => 'Webkernel Framework',
                            'brandName' => 'Webkernel',
                            'description' => 'Framework de développement web moderne',
                            'cssTitle' => 'webkernel-style',
                            'logos' => [
                                'default_logo' => '/logos/webkernel-default.png',
                                'light_logo' => '/logos/webkernel-light.png',
                                'dark_logo' => '/logos/webkernel-dark.png',
                                'alt' => 'Webkernel Logo',
                            ],
                            'codename' => 'Core Framework',
                            'series' => '24.12',
                            'checksum' => 'sha256-webkernel-v0',
                            'metadata' => [
                                'title' => 'Webkernel Framework',
                                'description' => 'Framework de développement web moderne',
                                'keywords' => 'framework, web, développement, php',
                                'author' => 'El Moumen Yassine',
                                'robots' => 'index, follow',
                            ],
                            'opengraph' => [
                                'title' => 'Webkernel Framework',
                                'description' => 'Framework de développement web moderne',
                                'image' => '/logos/webkernel-og.png',
                                'type' => 'website',
                                'url' => '',
                                'site_name' => 'Webkernel',
                            ],
                            'to_change_in_system_panel' => [
                                'name' => true,
                                'brandName' => true,
                                'description' => true,
                                'logos' => false,
                                'cssTitle' => false,
                                'codename' => false,
                                'series' => false,
                                'checksum' => false,
                                'metadata' => true,
                                'opengraph' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

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
        
        // First check webkernel subplatforms
        if (isset($config['webkernel']['subplatforms'])) {
            foreach ($config['webkernel']['subplatforms'] as $subplatform => $subConfig) {
                if (platformExists($subplatform)) {
                    return $subplatform;
                }
            }
        }
        
        // Then check main platforms
        $sortedPlatforms = [];
        foreach ($config as $platform => $platformConfig) {
            if ($platformConfig['official_subplatform'] ?? false) {
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
                if (file_exists($file)) {
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
        
        if (!empty($versionDetection['constant']) && defined($versionDetection['constant'])) {
            return constant($versionDetection['constant']);
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

if (!function_exists('getOfficialSubplatforms')) {
    function getOfficialSubplatforms(): array
    {
        $config = getMergedBrandingConfig();
        $officialSubplatforms = [];
        
        // Check main platforms
        foreach ($config as $platform => $platformConfig) {
            if ($platformConfig['official_subplatform'] ?? false) {
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