<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Widget Settings
    |--------------------------------------------------------------------------
    |
    | Here you can configure settings for your Webkernel widgets.
    |
    */

    'widgets' => [
        // Force all widgets to load immediately by default
        'immediate_loading' => true,

        // Define widgets that should be exempt from immediate loading
        'exempt_from_immediate_loading' => [
            // Example: 'Webkernel\Filament\Widgets\DataIntensiveWidget',
        ],

        // Disable real-time polling for all widgets by default
        'disable_polling' => true,
    ],



    /*
    |--------------------------------------------------------------------------
    | Webkernel Updates Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Webkernel updates.
    |
    */

    'updates' => [

        /*
        |--------------------------------------------------------------------------
        | Auto-Update Preference
        |--------------------------------------------------------------------------
        |
        | Determines the auto-update behavior. Options: 'stable', 'development', 'nothing'.
        | 'stable': Automatically update to stable versions.
        | 'development': Automatically update to the latest version, including development versions.
        | 'nothing': Do not automatically update.
        |
        */
        'auto_update_preference' => env('WEBKERNEL_AUTO_UPDATE_PREFERENCE', 'nothing'),

        /*
        |--------------------------------------------------------------------------
        | GitHub Repository
        |--------------------------------------------------------------------------
        |
        | The GitHub repository to check for updates.
        |
        */
        'github_repo' => env('WEBKERNEL_GITHUB_REPO', 'numerimondes/webkernel'),

        /*
        |--------------------------------------------------------------------------
        | GitHub Branch
        |--------------------------------------------------------------------------
        |
        | The branch to check for updates. Default is 'main'.
        |
        */
        'github_branch' => env('WEBKERNEL_GITHUB_BRANCH', 'main'),

        /*
        |--------------------------------------------------------------------------
        | Packages Path
        |--------------------------------------------------------------------------
        |
        | The path where packages are stored. Default is 'packages'.
        |
        */
        'packages_path' => env('WEBKERNEL_PACKAGES_PATH', 'packages'),
    ],

];
