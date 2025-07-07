<?php
/**
 * Auto-generated autoload stubs - DO NOT EDIT
 * Generated: 2025-07-07 17:06:44
 * Total aliases: 3
 * Total escaped: 3
 */

// Class aliases for *_CLASS_ALIAS_SIMPLE constants
if (!class_exists('WEBKERNEL_LANGUAGE_MODEL') && !interface_exists('WEBKERNEL_LANGUAGE_MODEL') && !trait_exists('WEBKERNEL_LANGUAGE_MODEL')) {
    if (class_exists('Webkernel\Core\Models\Language') || interface_exists('Webkernel\Core\Models\Language') || trait_exists('Webkernel\Core\Models\Language')) {
        class_alias('Webkernel\Core\Models\Language', 'WEBKERNEL_LANGUAGE_MODEL');
    }
}
if (!class_exists('WEBKERNEL_LANGUAGE_TRANSLATION_MODEL') && !interface_exists('WEBKERNEL_LANGUAGE_TRANSLATION_MODEL') && !trait_exists('WEBKERNEL_LANGUAGE_TRANSLATION_MODEL')) {
    if (class_exists('Webkernel\Core\Models\LanguageTranslation') || interface_exists('Webkernel\Core\Models\LanguageTranslation') || trait_exists('Webkernel\Core\Models\LanguageTranslation')) {
        class_alias('Webkernel\Core\Models\LanguageTranslation', 'WEBKERNEL_LANGUAGE_TRANSLATION_MODEL');
    }
}
if (!class_exists('WEBKERNEL_LANGUAGE_MIDDLEWARE') && !interface_exists('WEBKERNEL_LANGUAGE_MIDDLEWARE') && !trait_exists('WEBKERNEL_LANGUAGE_MIDDLEWARE')) {
    if (class_exists('Webkernel\Core\Http\Middleware\SetLang') || interface_exists('Webkernel\Core\Http\Middleware\SetLang') || trait_exists('Webkernel\Core\Http\Middleware\SetLang')) {
        class_alias('Webkernel\Core\Http\Middleware\SetLang', 'WEBKERNEL_LANGUAGE_MIDDLEWARE');
    }
}

// Escaped class references for *_CLASS_ESCAPED constants
// WEBKERNEL_LANGUAGE_MODEL_CLASS_ESCAPED = 'Webkernel\\Core\\Models\\Language'
// WEBKERNEL_LANGUAGE_TRANSLATION_MODEL_CLASS_ESCAPED = 'Webkernel\\Core\\Models\\LanguageTranslation'
// WEBKERNEL_LANGUAGE_MIDDLEWARE_CLASS_ESCAPED = 'Webkernel\\Core\\Http\\Middleware\\SetLang'

