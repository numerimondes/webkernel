<?php

/*
|--------------------------------------------------------------------------
| Webkernel Configuration File
|--------------------------------------------------------------------------
|
| Author: El Moumen Yassine
| Email: yassine@numerimondes.com
| Phone/WhatsApp: +212 6 20 99 06 92
| Website: www.numerimondes.com
|
| License: Mozilla Public License (MPL)
|
| This configuration file contains all the settings for the Webkernel
| system including translation engines, widgets, updates, and future
| AI-powered features.
|
| Webkernel is a comprehensive Laravel framework extension that provides
| robust translation management, widget systems, and advanced features
| for modern web applications.
|
*/

return [
    /*
    |--------------------------------------------------------------------------
    | Webkernel Repository & System Information
    |--------------------------------------------------------------------------
    |
    | Basic information about the Webkernel system, repository, and
    | update management configuration.
    |
    */

    'system' => [
        'name' => 'Webkernel',
        'version' => env('WEBKERNEL_VERSION', '1.0.0'),
        'author' => 'El Moumen Yassine',
        'email' => 'yassine@numerimondes.com',
        'website' => 'https://www.numerimondes.com',
        'license' => 'Mozilla Public License (MPL)',
        'repository' => 'https://github.com/numerimondes/webkernel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Webkernel Updates Configuration
    |--------------------------------------------------------------------------
    |
    | This section contains the configuration for Webkernel updates and
    | repository management.
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

    /*
    |--------------------------------------------------------------------------
    | Translation Helper Configuration (for lang() function)
    |--------------------------------------------------------------------------
    |
    | Configuration used by the enhanced lang() helper function.
    | These settings control how translations are loaded and cached.
    |
    */

    'translations' => [
        /*
        |--------------------------------------------------------------------------
        | Translation Source Priority
        |--------------------------------------------------------------------------
        |
        | Define the order in which translation sources should be checked.
        | Options: 'database', 'app', 'webkernel', 'other_packages'
        |
        */
        'priority' => [
            'database',        // Database translations (highest priority)
            'app',            // app/lang files
            'webkernel',      // packages/webkernel/src/lang files
            'other_packages', // Other package translation files
        ],

        /*
        |--------------------------------------------------------------------------
        | Translation Caching
        |--------------------------------------------------------------------------
        |
        | Enable or disable caching for translation lookups to improve performance.
        |
        */
        'cache_enabled' => env('TRANSLATION_CACHE_ENABLED', true),
        'cache_ttl' => env('TRANSLATION_CACHE_TTL', 3600), // 1 hour in seconds

        /*
        |--------------------------------------------------------------------------
        | Translation Debug Mode
        |--------------------------------------------------------------------------
        |
        | When enabled, translations will be wrapped with debug information.
        |
        */
        'debug_mode' => env('TRANSLATION_DEBUG_MODE', false),
        'show_keys' => env('TRANSLATION_SHOW_KEYS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Engine Configuration
    |--------------------------------------------------------------------------
    |
    | Configure translation engines and their priorities for the webkernel
    | translation system. AI engines will be used in future versions.
    |
    */

    'translation' => [

        /*
        |--------------------------------------------------------------------------
        | Engine Priority
        |--------------------------------------------------------------------------
        |
        | Define the order in which translation engines should be tried.
        | Available engines: google, bing, yandex, deepl (via translate-shell)
        | AI engines: openai, claude, gemini, local_llama (future implementation)
        |
        */

        'engine_priority' => [
            'google',
            'bing',
            'yandex',
            // 'deepl',  // Requires API key

            /**
             * Future AI engines
             * Not yet implemented)
             * 'openai',
             * 'claude',
             * 'gemini',
             * 'local_llama',
             */
        ],

        /*
        |--------------------------------------------------------------------------
        | Translation Quality Settings
        |--------------------------------------------------------------------------
        |
        | Configure quality assessment and scoring for translations.
        |
        */

        'quality' => [
            'enable_multi_engine_comparison' => true,
            'minimum_score_threshold' => 50,
            'prefer_ai_engines' => false, // Will be enabled in future versions
            'fallback_to_traditional' => true,
            'cache_results' => true,
            'cache_ttl' => 3600, // 1 hour
        ],

        /*
        |--------------------------------------------------------------------------
        | Scoring Weights
        |--------------------------------------------------------------------------
        |
        | Adjust the importance of different quality factors.
        |
        */

        'scoring_weights' => [
            'length_similarity' => 20,
            'word_uniqueness' => 15,
            'meaningful_content' => 15,
            'proper_capitalization' => 10,
            'engine_preference' => 5,
            'error_penalties' => -5,
            'original_text_penalty' => -30,
        ],

        /*
        |--------------------------------------------------------------------------
        | Language-Specific Settings
        |--------------------------------------------------------------------------
        |
        | Special configurations for specific languages.
        |
        */

        'language_settings' => [
            'rtl_languages' => ['ar', 'fa', 'he', 'ku', 'ckb'],
            'special_handling' => [
                'zh' => ['prefer_traditional' => false],
                'pt' => ['default_variant' => 'pt-PT'],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | AI Translation Engines (Future Implementation)
        |--------------------------------------------------------------------------
        |
        | Configuration for AI-based translation engines.
        | These will be implemented in future versions.
        |
        */

        'ai_engines' => [

            'openai' => [
                'enabled' => false,
                'type' => 'remote',
                'api_key' => env('OPENAI_API_KEY'),
                'model' => 'gpt-4',
                'base_url' => 'https://api.openai.com/v1',
                'max_tokens' => 1000,
                'temperature' => 0.1,
                'system_prompt' => 'You are a professional translator. Translate the given text accurately while preserving meaning, tone, and context.',
            ],

            'claude' => [
                'enabled' => false,
                'type' => 'remote',
                'api_key' => env('ANTHROPIC_API_KEY'),
                'model' => 'claude-3-sonnet-20240229',
                'base_url' => 'https://api.anthropic.com/v1',
                'max_tokens' => 1000,
                'system_prompt' => 'You are an expert translator. Provide accurate translations that maintain the original meaning and style.',
            ],

            'gemini' => [
                'enabled' => false,
                'type' => 'remote',
                'api_key' => env('GOOGLE_AI_API_KEY'),
                'model' => 'gemini-pro',
                'base_url' => 'https://generativelanguage.googleapis.com/v1',
                'safety_settings' => [
                    'block_none' => true,
                ],
                'system_prompt' => 'Translate the following text accurately while preserving context and meaning.',
            ],

            'local_llama' => [
                'enabled' => false,
                'type' => 'local',
                'model_path' => env('LOCAL_LLAMA_MODEL_PATH', '/models/llama-2-7b-chat.gguf'),
                'host' => env('LOCAL_LLAMA_HOST', '127.0.0.1'),
                'port' => env('LOCAL_LLAMA_PORT', 8080),
                'context_length' => 2048,
                'temperature' => 0.1,
                'system_prompt' => 'You are a translation assistant. Translate accurately and concisely.',
            ],

            'ollama' => [
                'enabled' => false,
                'type' => 'local',
                'host' => env('OLLAMA_HOST', '127.0.0.1'),
                'port' => env('OLLAMA_PORT', 11434),
                'model' => env('OLLAMA_MODEL', 'llama2'),
                'temperature' => 0.1,
                'system_prompt' => 'Translate the text while maintaining accuracy and context.',
            ],
        ],
    ],

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
    | Future AI Features (Placeholder Configuration)
    |--------------------------------------------------------------------------
    |
    | These settings are prepared for future AI-enhanced features.
    |
    */

    'ai_features' => [
        'context_aware_translation' => false,
        'cultural_adaptation' => false,
        'sentiment_preservation' => false,
        'technical_term_handling' => false,
        'batch_optimization' => false,
    ],
];
