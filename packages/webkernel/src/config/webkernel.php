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
        | User Model Extensions Configuration
        |--------------------------------------------------------------------------
        |
        | Configuration for User model extensions across different packages.
        | Only one package can be active per application to avoid conflicts.
        |
        */

        'user_extensions' => [
            /*
            |--------------------------------------------------------------------------
            | Active User Extension Package
            |--------------------------------------------------------------------------
            |
            | Determines which package is active for extending the User model.
            | Only one extension can be active per application.
            |
            */
            'active_package' => 'webkernel', // 'webkernel' | 'solecoles' | 'autre_package'

            /*
            |--------------------------------------------------------------------------
            | User Model Extensions per Package
            |--------------------------------------------------------------------------
            |
            | Configuration of User model extensions for each package.
            | Each package can define its own traits, relations, etc.
            |
            */
            'extensions' => [
                'webkernel' => [
                    'trait' => \Webkernel\Traits\UserExtensions::class,
                    'description' => 'Base webkernel user extensions with username, mobile, language, etc.',
                ],

                // School application

                // 'solecoles' => [
                //     'trait' => \Solecoles\Traits\UserExtensions::class,
                //     'description' => 'School-specific user extensions with student_id, grades, enrollment, etc.',
                //     'inherits' => 'webkernel', // Inherits webkernel functionalities
                // ],

                // Other packages...

                // 'autre_package' => [
                //     'trait' => \AutrePackage\Traits\UserExtensions::class,
                //     'description' => 'Custom user extensions for specific application needs.',
                //     'inherits' => 'webkernel',
                // ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Migration Priority
            |--------------------------------------------------------------------------
            |
            | Determines in which order migrations should be executed.
            | Priority packages apply their migrations first.
            |
            */
            'migration_priority' => [
                'webkernel',
                'autre_package',
                // Other packages...
            ],

            /*
            |--------------------------------------------------------------------------
            | Conflict Resolution
            |--------------------------------------------------------------------------
            |
            | How to resolve conflicts between packages.
            |
            */
            'conflict_resolution' => [
                'fillable_merge_strategy' => 'merge', // 'merge' | 'override' | 'ignore'
                'trait_merge_strategy' => 'stack',    // 'stack' | 'override' | 'ignore'
                'cast_merge_strategy' => 'merge',     // 'merge' | 'override' | 'ignore'
            ],
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
        | Word Replacement System
        |--------------------------------------------------------------------------
        |
        | Enable or disable the interactive word replacement prompts during translation.
        | When enabled, users will be asked to provide word substitutions for better
        | translation accuracy. Set to false to skip all replacement prompts.
        |
        */
        'word_replacement_enabled' => false,

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

        'engines' => [
            'bing',
            'google',
            'yandex',
            // 'deepl',  // Requires API key
            // Future AI engines (not yet implemented):
            // 'openai',
            // 'claude',
            // 'gemini',
            // 'local_llama',
        ],

        /*
        |--------------------------------------------------------------------------
        | Complete Language Mapping for TranslationHub
        |--------------------------------------------------------------------------
        |
        | Maps Laravel locales to translate-shell language codes for all 53 supported languages.
        |
        */

        'languages' => [
            // Priority languages first
            'en' => 'en', 'ar' => 'ar', 'fr' => 'fr',
            // Extended language support
            'az' => 'az', 'bg' => 'bg', 'bn' => 'bn', 'ha' => 'ha', 'ca' => 'ca',
            'ckb' => 'ku', 'cs' => 'cs', 'da' => 'da', 'de' => 'de', 'el' => 'el',
            'es' => 'es', 'fa' => 'fa', 'fi' => 'fi', 'he' => 'he', 'hi' => 'hi',
            'hr' => 'hr', 'hu' => 'hu', 'hy' => 'hy', 'id' => 'id', 'it' => 'it', 'ja' => 'ja',
            'ka' => 'ka', 'km' => 'km', 'ko' => 'ko', 'ku' => 'ku', 'lt' => 'lt', 'lv' => 'lv',
            'mk' => 'mk', 'ml' => 'ml', 'mn' => 'mn', 'ms' => 'ms', 'my' => 'my', 'ne' => 'ne',
            'nl' => 'nl', 'no' => 'no', 'pa' => 'pa', 'pl' => 'pl', 'ps' => 'ps', 'pt' => 'pt',
            'ro' => 'ro', 'ru' => 'ru', 'si' => 'si', 'sk' => 'sk', 'sl' => 'sl', 'so' => 'so',
            'sq' => 'sq', 'sr' => 'sr', 'sv' => 'sv', 'sw' => 'sw', 'ta' => 'ta', 'th' => 'th',
            'tr' => 'tr', 'uk' => 'uk', 'ur' => 'ur', 'uz' => 'uz', 'vi' => 'vi',
            'zh' => 'zh', 'zh_CN' => 'zh-cn', 'zh_TW' => 'zh-tw'
        ],

        /*
        |--------------------------------------------------------------------------
        | RTL Language Support
        |--------------------------------------------------------------------------
        |
        | Languages that require right-to-left text direction.
        |
        */

        'rtl_languages' => ['ar', 'fa', 'he', 'ur', 'ps', 'ckb', 'ku'],

        /*
        |--------------------------------------------------------------------------
        | Language Display Names (Dynamic)
        |--------------------------------------------------------------------------
        |
        | Human-readable names for all languages. This is used dynamically
        | in translation files instead of hardcoded values.
        |
        */

        'language_names' => [
            'en' => 'English', 'ar' => 'Arabic', 'fr' => 'French', 'es' => 'Spanish',
            'de' => 'German', 'it' => 'Italian', 'pt' => 'Portuguese', 'ru' => 'Russian',
            'zh' => 'Chinese', 'ja' => 'Japanese', 'ko' => 'Korean', 'hi' => 'Hindi',
            'az' => 'Azerbaijani', 'bg' => 'Bulgarian', 'bn' => 'Bengali', 'ha' => 'Hausa',
            'ca' => 'Catalan', 'ckb' => 'Kurdish (Sorani)', 'cs' => 'Czech', 'da' => 'Danish',
            'el' => 'Greek', 'fa' => 'Persian', 'fi' => 'Finnish', 'he' => 'Hebrew',
            'hr' => 'Croatian', 'hu' => 'Hungarian', 'hy' => 'Armenian', 'id' => 'Indonesian',
            'ka' => 'Georgian', 'km' => 'Khmer', 'ku' => 'Kurdish', 'lt' => 'Lithuanian',
            'lv' => 'Latvian', 'mk' => 'Macedonian', 'ml' => 'Malayalam', 'mn' => 'Mongolian',
            'ms' => 'Malay', 'my' => 'Myanmar', 'ne' => 'Nepali', 'nl' => 'Dutch',
            'no' => 'Norwegian', 'pa' => 'Punjabi', 'pl' => 'Polish', 'ps' => 'Pashto',
            'ro' => 'Romanian', 'si' => 'Sinhala', 'sk' => 'Slovak', 'sl' => 'Slovenian',
            'so' => 'Somali', 'sq' => 'Albanian', 'sr' => 'Serbian', 'sv' => 'Swedish',
            'sw' => 'Swahili', 'ta' => 'Tamil', 'th' => 'Thai', 'tr' => 'Turkish',
            'uk' => 'Ukrainian', 'ur' => 'Urdu', 'uz' => 'Uzbek', 'vi' => 'Vietnamese',
            'zh_CN' => 'Chinese (Simplified)', 'zh_TW' => 'Chinese (Traditional)'
        ],

        /*
        |--------------------------------------------------------------------------
        | Native Language Names (Dynamic)
        |--------------------------------------------------------------------------
        |
        | Names of languages in their native script/language.
        | Used for language_destination field in translation files.
        |
        */

        'native_names' => [
            'ar' => 'العربية', 'fr' => 'Français', 'es' => 'Español', 'de' => 'Deutsch',
            'it' => 'Italiano', 'pt' => 'Português', 'ru' => 'Русский', 'zh' => '中文',
            'ja' => '日本語', 'ko' => '한국어', 'hi' => 'हिन्दी', 'az' => 'Azərbaycan dili',
            'bg' => 'Български', 'bn' => 'বাংলা', 'ca' => 'Català', 'cs' => 'Čeština',
            'da' => 'Dansk', 'el' => 'Ελληνικά', 'fa' => 'فارسی', 'fi' => 'Suomi',
            'he' => 'עברית', 'hr' => 'Hrvatski', 'hu' => 'Magyar', 'hy' => 'Հայերեն',
            'id' => 'Bahasa Indonesia', 'ka' => 'ქართული', 'km' => 'ខ្មែរ', 'ku' => 'کوردی',
            'lt' => 'Lietuvių', 'lv' => 'Latviešu', 'mk' => 'Македонски', 'ml' => 'മലയാളം',
            'mn' => 'Монгол', 'ms' => 'Bahasa Melayu', 'my' => 'မြန်မာ', 'ne' => 'नेपाली',
            'nl' => 'Nederlands', 'no' => 'Norsk', 'pa' => 'ਪੰਜਾਬੀ', 'pl' => 'Polski',
            'ps' => 'پښتو', 'ro' => 'Română', 'si' => 'සිංහල', 'sk' => 'Slovenčina',
            'sl' => 'Slovenščina', 'so' => 'Soomaali', 'sq' => 'Shqip', 'sr' => 'Српски',
            'sv' => 'Svenska', 'sw' => 'Kiswahili', 'ta' => 'தமிழ்', 'th' => 'ไทย',
            'tr' => 'Türkçe', 'uk' => 'Українська', 'ur' => 'اردو', 'uz' => 'Oʻzbekcha',
            'vi' => 'Tiếng Việt', 'zh_CN' => '简体中文', 'zh_TW' => '繁體中文'
        ],

        /*
        |--------------------------------------------------------------------------
        | Protection System Configuration
        |--------------------------------------------------------------------------
        |
        | Settings for translation protection and backup management.
        |
        */

        'protection' => [
            'auto_backup' => true,
            'retain_backups' => 30,
            'protected_source' => true,
            'timestamp_migration' => true,
            'override_confirmation' => true
        ],

        /*
        |--------------------------------------------------------------------------
        | Output Format Configuration
        |--------------------------------------------------------------------------
        |
        | Configure how TranslationHub outputs information (console, future API, DB).
        |
        */

        'output' => [
            'format' => 'console', // console, json, api, database
            'detail_level' => 'normal', // minimal, normal, verbose
            'colors' => true,
            'progress_indicators' => true,
            'error_logging' => true,
            'success_confirmations' => true
        ],

        /*
        |--------------------------------------------------------------------------
        | Priority Languages for Detailed Tickets
        |--------------------------------------------------------------------------
        |
        | Languages that should show detailed translation tickets in the summary.
        | These languages will display complete translation information.
        |
        */

        'priority_ticket_languages' => ['en', 'ar', 'fr'],

        /*
        |--------------------------------------------------------------------------
        | Word Substitution System
        |--------------------------------------------------------------------------
        |
        | Replace technical terms before translation for better accuracy.
        |
        */

        'word_substitutions' => [
            'system' => 'application',
            'admin' => 'administrator',
            'config' => 'configuration',
            'auth' => 'authentication',
            'API' => 'programming interface',
            'URL' => 'web address',
            'UI' => 'user interface'
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

    /*
    |--------------------------------------------------------------------------
    | Resource Layouts (Convention-based)
    |--------------------------------------------------------------------------
    |
    | Layouts are now automatically discovered using convention:
    | packages/{package}/src/Layouts/{Resource}/{Layout}Layout.php
    |
    | Usage in Resource classes:
    | public static string $webkernel_layout = 'avatar';
    |
    | This will automatically load:
    | Webkernel\Layouts\User\AvatarLayout (for webkernel package)
    | Solecoles\Layouts\User\AvatarLayout (for solecoles package)
    |
    | Available layouts:
    | - default: DefaultLayout
    | - tabs: TabsLayout
    | - popup: PopupLayout
    | - avatar: AvatarLayout (with 3-part structure)
    |
    */
];
