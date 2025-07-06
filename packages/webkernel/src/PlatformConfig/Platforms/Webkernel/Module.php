<?php
declare(strict_types=1);

/**
 * Webkernel Platform Module Configuration
 * 
 * Main platform configuration file for Webkernel
 * 
 * El Moumen Yassine - Numerimondes
 * <yassine@numerimondes.com>
 * www.numerimondes.com
 */

return [
    'name' => 'Webkernel',
    'slug' => 'webkernel',
    'type' => 'platform',
    'version' => '1.0.0',
    'description' => 'Core Webkernel Platform - Main framework and utilities',
    'author' => 'El Moumen Yassine',
    'email' => 'yassine@numerimondes.com',
    'company' => 'Numerimondes',
    'website' => 'https://www.numerimondes.com',
    'license' => 'MPL-2.0',
    'status' => 'active',
    'priority' => 1,
    'bootstrap' => true,
    'autoload' => true,
    
    // Platform dependencies
    'dependencies' => [
        'php' => '>=8.1',
        'laravel/framework' => '^10.0',
    ],
    
    // Platform capabilities
    'capabilities' => [
        'module_management',
        'platform_config',
        'cache_management',
        'version_control',
        'composer_integration',
    ],
    
    // Configuration paths
    'paths' => [
        'config' => 'packages/webkernel/src/PlatformConfig',
        'helpers' => 'packages/webkernel/src/PlatformConfig/Helpers',
        'providers' => 'packages/webkernel/src/PlatformConfig/Providers',
        'modules' => 'packages/webkernel/src/PlatformConfig/Platforms/Modules',
    ],
    
    // Service providers to register
    'providers' => [
        'Webkernel\PlatformConfig\Providers\WebkernelServiceProvider',
        'Webkernel\PlatformConfig\Providers\PlatformConfigServiceProvider',
    ],
    
    // Artisan commands
    'commands' => [
        'webkernel:update',
        'webkernel:platform-update',
        'webkernel:module-list',
        'webkernel:composer-generate',
    ],
    
    // Configuration files to publish
    'config_files' => [
        'webkernel.php',
        'platform.php',
    ],
    
    // Environment support
    'environments' => [
        'local' => true,
        'development' => true,
        'staging' => true,
        'production' => true,
    ],
    
    // Cache settings
    'cache' => [
        'enabled' => true,
        'ttl' => [
            'packages' => 3600,
            'modules' => 1800,
            'versions' => 7200,
        ],
        'key_prefix' => 'webkernel_',
    ],
    
    // API endpoints
    'api' => [
        'enabled' => true,
        'version' => 'v1',
        'prefix' => 'api/webkernel',
    ],
    
    // Security settings
    'security' => [
        'csrf_protection' => true,
        'rate_limiting' => true,
        'authentication_required' => false,
    ],
    
    // Logging configuration
    'logging' => [
        'enabled' => true,
        'level' => 'info',
        'channels' => ['daily', 'slack'],
    ],
    
    // Feature flags
    'features' => [
        'auto_update' => env('WEBKERNEL_AUTO_UPDATE', false),
        'debug_mode' => env('WEBKERNEL_DEBUG', false),
        'performance_monitoring' => env('WEBKERNEL_PERFORMANCE_MONITORING', true),
    ],
    
    // Repository configuration
    'repository' => [
        'type' => 'git',
        'url' => 'https://github.com/numerimondes/webkernel',
        'branch' => 'main',
        'remote_config_path' => 'packages/webkernel/platformConfig/webkernel.php',
    ],
];