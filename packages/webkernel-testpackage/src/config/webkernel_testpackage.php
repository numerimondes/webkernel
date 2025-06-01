<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WebkernelTestpackage Configuration
    |--------------------------------------------------------------------------
    |
    | A test package to test if it works
    |
    */

    'name' => 'WebkernelTestpackage',
    'version' => '0.0.1',
    'enabled' => env('WEBKERNEL_TESTPACKAGE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Package Settings
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'debug' => env('WEBKERNEL_TESTPACKAGE_DEBUG', false),
        'cache_enabled' => env('WEBKERNEL_TESTPACKAGE_CACHE', true),
        'cache_ttl' => env('WEBKERNEL_TESTPACKAGE_CACHE_TTL', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Settings
    |--------------------------------------------------------------------------
    */

    'database' => [
        'table_prefix' => env('WEBKERNEL_TESTPACKAGE_TABLE_PREFIX', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Settings
    |--------------------------------------------------------------------------
    */

    'assets' => [
        'css_version' => env('WEBKERNEL_TESTPACKAGE_CSS_VERSION', '1.0.0'),
        'js_version' => env('WEBKERNEL_TESTPACKAGE_JS_VERSION', '1.0.0'),
    ],
];
