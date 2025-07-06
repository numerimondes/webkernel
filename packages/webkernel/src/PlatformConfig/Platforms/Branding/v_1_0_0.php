<?php
declare(strict_types=1);

/**
 * Webkernel Platform Version 1.0.0 Configuration
 * 
 * Version-specific configuration for Webkernel v1.0.0
 * 
 * El Moumen Yassine - Numerimondes
 * <yassine@numerimondes.com>
 * www.numerimondes.com
 */

return [
    'version_info' => [
        'version' => '1.0.0',
        'release_date' => '2024-01-15',
        'stability' => 'stable',
        'release_type' => 'major',
        'build_number' => '1000',
        'git_commit' => 'abc123def456',
        'release_notes_url' => 'https://github.com/numerimondes/webkernel/releases/tag/v1.0.0',
    ],
    
    // Version-specific dependencies
    'version_dependencies' => [
        'php' => '>=8.1.0',
        'laravel/framework' => '^10.0',
        'symfony/console' => '^6.0',
        'illuminate/support' => '^10.0',
        'illuminate/container' => '^10.0',
    ],
    
    // Version-specific dev dependencies
    'version_dev_dependencies' => [
        'phpunit/phpunit' => '^10.0',
        'pestphp/pest' => '^2.0',
        'nunomaduro/collision' => '^7.0',
        'spatie/laravel-ignition' => '^2.0',
    ],
    
    // Version-specific autoload configuration
    'version_autoload' => [
        'psr-4' => [
            'Webkernel\\PlatformConfig\\' => 'packages/webkernel/src/PlatformConfig/',
            'Webkernel\\Core\\' => 'packages/webkernel/src/Core/',
            'Webkernel\\Helpers\\' => 'packages/webkernel/src/Helpers/',
        ],
        'files' => [
            'packages/webkernel/src/Core/Helpers/helpers.php',
            'packages/webkernel/src/PlatformConfig/Helpers/PlatformConfigPackagesHelper.php',
            'packages/webkernel/src/PlatformConfig/Helpers/PlatformConfigComposerHelper.php',
            'packages/webkernel/src/PlatformConfig/Helpers/PlatformConfigLoaderHelper.php',
        ],
    ],
    
    // Version-specific configuration overrides
    'version_config' => [
        'cache' => [
            'default_ttl' => 3600,
            'max_ttl' => 86400,
            'cleanup_interval' => 3600,
        ],
        'performance' => [
            'max_execution_time' => 300,
            'memory_limit' => '512M',
            'max_file_size' => '10M',
        ],
        'security' => [
            'hash_algorithm' => 'sha256',
            'encryption_cipher' => 'aes-256-cbc',
            'session_lifetime' => 7200,
        ],
    ],
    
    // Version-specific features
    'version_features' => [
        'composer_integration' => [
            'enabled' => true,
            'auto_generate' => true,
            'validate_dependencies' => true,
        ],
        'module_management' => [
            'enabled' => true,
            'auto_discovery' => true,
            'lazy_loading' => true,
        ],
        'cache_optimization' => [
            'enabled' => true,
            'compression' => true,
            'serialization' => 'json',
        ],
    ],
    
    // Version-specific API configuration
    'version_api' => [
        'version' => '1.0',
        'endpoints' => [
            'modules' => '/api/webkernel/v1/modules',
            'packages' => '/api/webkernel/v1/packages',
            'config' => '/api/webkernel/v1/config',
            'health' => '/api/webkernel/v1/health',
        ],
        'rate_limits' => [
            'default' => 60,
            'authenticated' => 120,
            'admin' => 300,
        ],
    ],
    
    // Version-specific middleware
    'version_middleware' => [
        'api' => [
            'throttle:60,1',
            'auth:api',
        ],
        'web' => [
            'web',
            'csrf',
        ],
    ],
    
    // Version-specific validation rules
    'version_validation' => [
        'module_name' => 'required|string|max:50|alpha_dash',
        'module_version' => 'required|string|regex:/^\d+\.\d+\.\d+$/',
        'module_type' => 'required|in:core,platform,plugin,theme,integration,utility',
    ],
    
    // Version-specific error handling
    'version_errors' => [
        'log_level' => 'error',
        'report_errors' => true,
        'custom_error_pages' => [
            '404' => 'errors.webkernel.404',
            '500' => 'errors.webkernel.500',
        ],
    ],
    
    // Version-specific testing configuration
    'version_testing' => [
        'test_environment' => 'testing',
        'database_connection' => 'sqlite',
        'cache_driver' => 'array',
        'session_driver' => 'array',
    ],
    
    // Compatibility matrix
    'compatibility' => [
        'min_php_version' => '8.1.0',
        'max_php_version' => '8.3.x',
        'laravel_versions' => ['10.x', '11.x'],
        'mysql_versions' => ['5.7', '8.0', '8.1'],
        'redis_versions' => ['6.x', '7.x'],
    ],
    
    // Migration information
    'migration' => [
        'from_version' => '0.0.24',
        'migration_required' => true,
        'migration_script' => 'migrations/v1_0_0_migration.php',
        'backup_recommended' => true,
    ],
];