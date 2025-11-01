<?php
declare(strict_types=1);
return [
    /*
    |--------------------------------------------------------------------------
    | Module Discovery Configuration
    |--------------------------------------------------------------------------
    */
    'discovery' => [
        'paths' => [
            base_path('app/Modules'),
            base_path('modules'),
            base_path('webkernel/src/Aptitudes'),
        ],

        'exclude_patterns' => [
            '/vendor/composer/',
            '/vendor/bin/',
            '/node_modules/',
            '/storage/',
            '/bootstrap/',
            '/database/migrations/',
            '/database/factories/',
            '/database/seeders/',
            '/tests/',
            '/Http/Controllers/',
            '/Http/Middleware/',
            '/Http/Requests/',
            '/Console/',
            '/Exceptions/',
            '/Jobs/',
            '/Mail/',
            '/Notifications/',
            '/Policies/',
            '/Rules/',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Ultra Performance Settings
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'cache_enabled' => env('WEBKERNEL_CACHE', true),
        'lazy_loading' => env('WEBKERNEL_LAZY', true),
        'opcache_optimization' => env('WEBKERNEL_OPCACHE', true),
        'instant_reload' => env('WEBKERNEL_INSTANT_RELOAD', true),
        'ultra_cache' => env('WEBKERNEL_ULTRA_CACHE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Settings
    |--------------------------------------------------------------------------
    */
    'development' => [
        'debug' => env('WEBKERNEL_DEBUG', false),
        'auto_refresh_on_config_change' => env('WEBKERNEL_AUTO_REFRESH', true),
    ],
];
