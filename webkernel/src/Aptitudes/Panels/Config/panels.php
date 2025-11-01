<?php

/**
 * Purpose: Configuration file for dynamic panel management system
 *
 * This configuration defines how panels are loaded from different sources
 * and provides settings for the dynamic panel registration system.
 *
 * Configuration options:
 * - sources: Define which sources to load panels from
 * - api: API endpoint configuration
 * - validation: Schema validation settings
 * - cache: Caching configuration for performance
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Panel Sources
    |--------------------------------------------------------------------------
    |
    | Configure which sources the PanelsProvider should load panels from.
    | Multiple sources can be enabled simultaneously and will be merged.
    |
    | Available sources: 'database', 'array', 'api'
    |
    */
    'sources' => [
        'database' => null, // Load from apt_panels table
        // 'array' => [
        //     // Array configuration example
        //     [
        //         'id' => 'test',
        //         'path' => 'test',
        //         'methods' => [
        //             'login' => true,
        //             'colors' => [['primary' => 'blue']]
        //         ]
        //     ]
        // ],
        // 'api' => 'https://api.example.com/panels', // API endpoint
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for loading panels from external API endpoints.
    |
    */
    'api' => [
        'endpoint' => env('PANELS_API_ENDPOINT'),
        'timeout' => 30,
        'retry_attempts' => 2,
        'retry_delay' => 1000, // milliseconds
        'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ],
        'auth' => [
            'type' => env('PANELS_API_AUTH_TYPE', 'bearer'), // bearer, basic, none
            'token' => env('PANELS_API_TOKEN'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Schema Validation
    |--------------------------------------------------------------------------
    |
    | Configuration for dynamic schema validation using reflection.
    |
    */
    'validation' => [
        'strict_mode' => env('PANELS_STRICT_VALIDATION', true),
        'cache_schema' => env('PANELS_CACHE_SCHEMA', true),
        'schema_cache_ttl' => 3600, // seconds
        'target_class' => \Filament\Panel::class,
        'excluded_methods' => [
            '__construct',
            '__destruct',
            '__call',
            '__callStatic',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for caching panel configurations and schemas for performance.
    |
    */
    'cache' => [
        'enabled' => env('PANELS_CACHE_ENABLED', true),
        'ttl' => env('PANELS_CACHE_TTL', 3600), // seconds
        'prefix' => 'panels',
        'tags' => ['panels', 'filament'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Panel Settings
    |--------------------------------------------------------------------------
    |
    | Default configuration applied to all panels unless overridden.
    |
    */
    'defaults' => [
        'version' => '4.0',
        'panel_source' => 'database',
        'is_active' => true,
        'is_default' => false,
        'sort_order' => 0,
        'methods' => [
            'login' => true,
            'colors' => [['primary' => 'blue']],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Required Middleware Defaults
    |--------------------------------------------------------------------------
    |
    | These middleware are always included in panels unless explicitly overridden.
    | Modify with caution as removing core middleware may break functionality.
    |
    */
    'required_middleware' => [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Filament\Http\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Filament\Http\Middleware\DisableBladeIconComponents::class,
            \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
        ],
        'auth' => [
            \Filament\Http\Middleware\Authenticate::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging for panel registration and validation processes.
    |
    */
    'logging' => [
        'enabled' => env('PANELS_LOGGING_ENABLED', true),
        'level' => env('PANELS_LOGGING_LEVEL', 'info'),
        'channel' => env('PANELS_LOGGING_CHANNEL', 'single'),
    ],
];
