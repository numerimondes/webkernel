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
 * 
 * // Get information from specific platform
 * {{ getPlatformInfo('solecoles', 'name') }}
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
                'versions' => [
                    [
                        'operator' => '<',
                        'version' => '1.0.0',
                        'config' => [
                            'name' => 'branding_big_baby_name',
                            'brandName' => 'branding_big_baby_brand_name',
                            'description' => 'branding_big_baby_description',
                            'cssTitle' => 'big-baby-style',
                            'logos' => [
                                'default_logo' => '/logos/big-baby-default.png',
                                'light_logo' => '/logos/big-baby-light.png',
                                'dark_logo' => '/logos/big-baby-dark.png',
                                'alt' => 'branding_big_baby_logo_alt',
                            ],
                            'codename' => 'branding_big_baby_codename',
                            'series' => '24.12',
                            'checksum' => 'sha256-checksum-v0',
                            'metadata' => [
                                'title' => 'branding_big_baby_meta_title',
                                'description' => 'branding_big_baby_meta_description',
                                'keywords' => 'branding_big_baby_meta_keywords',
                                'author' => 'El Moumen Yassine',
                                'robots' => 'index, follow',
                            ],
                            'opengraph' => [
                                'title' => 'branding_big_baby_og_title',
                                'description' => 'branding_big_baby_og_description',
                                'image' => '/logos/big-baby-og.png',
                                'type' => 'website',
                                'url' => '',
                                'site_name' => 'branding_big_baby_site_name',
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
                    [
                        'operator' => '>=',
                        'version' => '1.0.0',
                        'config' => [
                            'name' => 'branding_dynamic_dolphin_name',
                            'brandName' => 'branding_dynamic_dolphin_brand_name',
                            'description' => 'branding_dynamic_dolphin_description',
                            'cssTitle' => 'dynamic-dolphin-style',
                            'inherits_from' => '1.0.0',
                            'logos' => [
                                'default_logo' => '/logos/dynamic-dolphin-default.png',
                                'light_logo' => '/logos/dynamic-dolphin-light.png',
                                'dark_logo' => '/logos/dynamic-dolphin-dark.png',
                                'alt' => 'branding_dynamic_dolphin_logo_alt',
                            ],
                            'codename' => 'branding_dynamic_dolphin_codename',
                            'series' => '25.07',
                            'checksum' => 'sha256-checksum-v1',
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
                    [
                        'operator' => '>=',
                        'version' => '2.0.0',
                        'config' => [
                            'name' => 'branding_graceful_giraffe_name',
                            'brandName' => 'branding_graceful_giraffe_brand_name',
                            'description' => 'branding_graceful_giraffe_description',
                            'cssTitle' => 'graceful-giraffe-style',
                            'inherits_from' => '1.0.0',
                            'logos' => [
                                'default_logo' => '/logos/graceful-giraffe-default.png',
                                'light_logo' => '/logos/graceful-giraffe-light.png',
                                'dark_logo' => '/logos/graceful-giraffe-dark.png',
                                'alt' => 'branding_graceful_giraffe_logo_alt',
                            ],
                            'codename' => 'branding_graceful_giraffe_codename',
                            'series' => '26.04',
                            'checksum' => 'sha256-checksum-v2',
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
            'solecoles' => [
                'app_location' => 'app/Models/School',
                'detection' => [
                    'class_exists' => ['App\Models\School'],
                    'file_exists' => [app_path('Models/School.php')],
                    'constant_defined' => ['Application::SOLECOLES_VERSION'],
                    'default_active' => false,
                ],
                'version_detection' => [
                    'constant' => 'Application::SOLECOLES_VERSION',
                    'fallback' => '1.2.0',
                ],
                'official_subplatform' => true,
                'versions' => [
                    [
                        'operator' => '<',
                        'version' => '1.0.0',
                        'config' => [
                            'name' => 'branding_school_starter_name',
                            'brandName' => 'branding_school_starter_brand_name',
                            'description' => 'branding_school_starter_description',
                            'cssTitle' => 'school-starter-style',
                            'logos' => [
                                'default_logo' => '/logos/school-starter-default.png',
                                'light_logo' => '/logos/school-starter-light.png',
                                'dark_logo' => '/logos/school-starter-dark.png',
                                'alt' => 'branding_school_starter_logo_alt',
                            ],
                            'codename' => 'branding_school_starter_codename',
                            'series' => '24.09',
                            'checksum' => 'sha256-checksum-school-v0',
                            'metadata' => [
                                'title' => 'branding_school_starter_meta_title',
                                'description' => 'branding_school_starter_meta_description',
                                'keywords' => 'branding_school_starter_meta_keywords',
                                'author' => 'El Moumen Yassine',
                                'robots' => 'index, follow',
                            ],
                            'opengraph' => [
                                'title' => 'branding_school_starter_og_title',
                                'description' => 'branding_school_starter_og_description',
                                'image' => '/logos/school-starter-og.png',
                                'type' => 'website',
                                'url' => '',
                                'site_name' => 'branding_school_starter_site_name',
                            ],
                        ],
                    ],
                    [
                        'operator' => '>=',
                        'version' => '1.0.0',
                        'config' => [
                            'name' => 'branding_education_eagle_name',
                            'brandName' => 'branding_education_eagle_brand_name',
                            'description' => 'branding_education_eagle_description',
                            'cssTitle' => 'education-eagle-style',
                            'inherits_from' => '1.0.0',
                            'logos' => [
                                'default_logo' => '/logos/education-eagle-default.png',
                                'light_logo' => '/logos/education-eagle-light.png',
                                'dark_logo' => '/logos/education-eagle-dark.png',
                                'alt' => 'branding_education_eagle_logo_alt',
                            ],
                            'codename' => 'branding_education_eagle_codename',
                            'series' => '25.01',
                            'checksum' => 'sha256-checksum-school-v1',
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
        return isset($config[$platform]) && ($config[$platform]['official_subplatform'] ?? false);
    }
}

if (!function_exists('getActivePlatform')) {
    function getActivePlatform(): string
    {
        $config = getMergedBrandingConfig();
        
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
        
        return isset($config['webkernel']) ? 'webkernel' : array_key_first($config);
    }
}

if (!function_exists('platformExists')) {
    function platformExists(string $platform): bool
    {
        $config = getMergedBrandingConfig();
        
        if (!isset($config[$platform])) {
            return false;
        }
        
        $platformConfig = $config[$platform];
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
        
        if (!isset($config[$platform])) {
            return '1.0.0';
        }
        
        $platformConfig = $config[$platform];
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
        
        if (!isset($config[$platform])) {
            return [];
        }
        
        $platformConfig = $config[$platform];
        $currentVersion = getCurrentPlatformVersion($platform);
        
        if (!isset($platformConfig['versions'])) {
            return [];
        }
        
        foreach ($platformConfig['versions'] as $versionConfig) {
            if (compareVersionWithOperator(
                $currentVersion,
                $versionConfig['operator'],
                $versionConfig['version']
            )) {
                return $versionConfig['config'];
            }
        }
        
        return [];
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
            'official_subplatforms' => getOfficialSubplatforms(),
            'config' => $config,
            'detection_info' => getPlatformDetectionInfo($activePlatform),
            'editable_fields' => getSystemPanelEditableFields(),
            'cache_info' => [
                'cache_key' => md5(serialize(getWebkernelUltimateBrandingConfig()) . filemtime(__FILE__)),
                'file_time' => filemtime(__FILE__),
            ],
        ];

        return '<pre>' . json_encode($debugData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>';
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
        return array_keys($config);
    }
}

if (!function_exists('getOfficialSubplatforms')) {
    function getOfficialSubplatforms(): array
    {
        $config = getMergedBrandingConfig();
        $officialSubplatforms = [];
        
        foreach ($config as $platform => $platformConfig) {
            if ($platformConfig['official_subplatform'] ?? false) {
                $officialSubplatforms[] = $platform;
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
        return $config[$platform]['app_location'] ?? '';
    }
}

if (!function_exists('getPlatformDetectionInfo')) {
    function getPlatformDetectionInfo(string $platform): array
    {
        $config = getMergedBrandingConfig();
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