<?php
declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Webkernel I18n Configuration
    |--------------------------------------------------------------------------
    |
    | Ultra-optimized I18n system configuration for microsecond performance
    | Compatible with Octane/Swoole/FrankenPHP
    |
    */

    'group' => 'webkernel-config',

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configure caching behavior for maximum performance
    |
    */
    'cache' => [
        'ttl' => 3600, // 1 hour
        'memory_limit' => 10000, // Maximum items in memory cache
        'warm_on_boot' => true,
        'clear_on_deploy' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Microsecond performance optimizations
    |
    */
    'performance' => [
        'enable_memory_cache' => true,
        'preload_common_keys' => true,
        'batch_database_queries' => true,
        'lazy_load_modules' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation System Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the translation automation system
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
    | Module Language Paths
    |--------------------------------------------------------------------------
    |
    | These paths are automatically discovered via QueryModules but can be
    | manually overridden for performance optimization
    |
    */
    'module_paths' => [
        // Auto-discovered via QueryModules::make()->select(['langpath'])...
        // Manual override example:
        // '/path/to/webkernel/src/Aptitudes/Base/Lang',
        // '/path/to/app/Modules/Lang',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | Configure fallback behavior when translations are missing
    |
    */
    'fallback' => [
        'locale' => 'en',
        'show_key_on_missing' => true,
        'log_missing_translations' => false,
        'auto_create_missing' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for database-stored translations
    |
    */
    'database' => [
        'enabled' => true,
        'table' => 'translations',
        'cache_queries' => true,
        'batch_size' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Development & Debugging
    |--------------------------------------------------------------------------
    |
    | Settings for development and debugging
    |
    */
    'debug' => [
        'log_performance' => false,
        'track_usage' => false,
        'show_source' => false, // Show which source provided the translation
        'validate_placeholders' => true,
    ],
];
