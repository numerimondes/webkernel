<?php

use Illuminate\Support\Facades\Auth;
use Webkernel\Models\Language;
use Webkernel\Models\LanguageTranslation;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

/*
|--------------------------------------------------------------------------
| ENHANCED TRANSLATION SYSTEM HELPERS - CONCERNING MULTILINGUAL SUPPORT - translation_helpers.php
|--------------------------------------------------------------------------
| Enhanced translation system with comprehensive fallback strategy, caching,
| and multi-source support. Supports database, file-based, and package-based
| translations with priority management and error handling.
|--------------------------------------------------------------------------
| Credits: El Moumen Yassine - www.numerimondes.com
|
*/

if (!function_exists('lang')) {
    function lang($key = null, $replace = [], $locale = null)
    {
        static $doc_description = 'Returns the translated string for a given key using the current user\'s language, falling back to the default language if not available.';
        static $doc_usage = 'lang("payment_notice", ["name" => "Yassine", "amount" => "99 MAD", "date" => "17 April 2025"]);';
        static $doc_output = '"Hello, Yassine! Your payment of 99 MAD is due on 17 April 2025."';
        static $doc_basedonfunction = 'trans()';
        static $doc_relatedfile = 'Observers/LanguageTranslationObserver.php';

        // Early returns for invalid inputs
        if (is_null($key) || !is_string($key) || empty(trim($key))) {
            return $key;
        }

        // Normalize parameters
        $replace = is_array($replace) ? $replace : [];
        $userLangCode = getUserLanguageCode($locale);

        // Try to get cached translation first
        $cacheKey = "translation.{$userLangCode}.{$key}." . md5(serialize($replace));
        if (config('translations.cache_enabled', false)) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        // Get translation using enhanced priority-based approach
        $translation = getTranslationWithFallback($key, $userLangCode, $replace);

        // Cache the result if caching is enabled
        if (config('translations.cache_enabled', false) && $translation !== null) {
            Cache::put($cacheKey, $translation, config('translations.cache_ttl', 3600));
        }

        return $translation;
    }
}

if (!function_exists('getUserLanguageCode')) {
    /**
     * Get the user's language code with fallbacks.
     *
     * @param string|null $locale Override locale
     * @return string Language code
     */
    function getUserLanguageCode($locale = null)
    {
        if ($locale) {
            return validateLanguageCode($locale);
        }

        // Try authenticated user's language
        if (Auth::check() && Auth::user()->user_lang) {
            return validateLanguageCode(Auth::user()->user_lang);
        }

        // Try session language
        if (Session::has('user_lang')) {
            return validateLanguageCode(Session::get('user_lang'));
        }

        // Try app locale
        $appLocale = app()->getLocale();
        if ($appLocale) {
            return validateLanguageCode($appLocale);
        }

        // Final fallback
        return config('app.locale', 'en');
    }
}

if (!function_exists('validateLanguageCode')) {
    /**
     * Validate and normalize language code.
     *
     * @param string $langCode Language code to validate
     * @return string Validated language code
     */
    function validateLanguageCode($langCode)
    {
        if (!is_string($langCode)) {
            return 'en';
        }

        // Clean and normalize
        $langCode = strtolower(trim($langCode));

        // Handle common variations
        $normalizations = [
            'pt_br' => 'pt-BR',
            'pt_pt' => 'pt-PT',
            'zh_cn' => 'zh-CN',
            'zh_tw' => 'zh-TW',
        ];

        $langCode = $normalizations[$langCode] ?? $langCode;

        // Validate format (basic check)
        if (preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $langCode)) {
            return $langCode;
        }

        return 'en';
    }
}

if (!function_exists('getTranslationWithFallback')) {
    /**
     * Get translation with comprehensive fallback strategy.
     *
     * @param string $key Translation key
     * @param string $langCode Language code
     * @param array $replace Replacement parameters
     * @return HtmlString|string|null
     */
    function getTranslationWithFallback($key, $langCode, $replace = [])
    {
        // Primary language attempt
        $translation = getTranslationFromSources($key, $langCode, $replace);
        if ($translation !== null) {
            return $translation;
        }

        // Fallback to English if not already English
        if ($langCode !== 'en') {
            $translation = getTranslationFromSources($key, 'en', $replace);
            if ($translation !== null) {
                return $translation;
            }
        }

        // Fallback to base language (without region)
        if (strpos($langCode, '-') !== false) {
            $baseLang = explode('-', $langCode)[0];
            $translation = getTranslationFromSources($key, $baseLang, $replace);
            if ($translation !== null) {
                return $translation;
            }
        }

        // Laravel's built-in translation as final fallback
        try {
            $fallback = __($key, $replace, $langCode);
            if ($fallback !== $key) {
                return formatTranslationOutput($fallback, $key);
            }
        } catch (Exception $e) {
            error_log("Laravel translation fallback failed for key {$key}: " . $e->getMessage());
        }

        // Return the key itself as last resort
        return $key;
    }
}

if (!function_exists('getTranslationFromSources')) {
    /**
     * Get translation from all configured sources in priority order.
     *
     * @param string $key Translation key
     * @param string $langCode Language code
     * @param array $replace Replacement parameters
     * @return HtmlString|string|null
     */
    function getTranslationFromSources($key, $langCode, $replace = [])
    {
        $sources = getTranslationSourcesConfig();

        foreach ($sources as $source) {
            try {
                $translation = getTranslationFromSource($source, $key, $langCode);

                if ($translation !== null) {
                    return processTranslation($translation, $key, $replace);
                }
            } catch (Exception $e) {
                error_log("Translation source '{$source}' failed for key {$key}: " . $e->getMessage());
                continue;
            }
        }

        return null;
    }
}

if (!function_exists('getTranslationSourcesConfig')) {
    /**
     * Get prioritized translation sources from configuration.
     *
     * @return array
     */
    function getTranslationSourcesConfig()
    {
        $default = ['database', 'app', 'webkernel', 'other_packages'];
        $configured = config('translations.priority', $default);

        // Ensure all valid sources are included
        $valid = ['database', 'app', 'webkernel', 'other_packages'];
        $filtered = array_intersect($configured, $valid);

        // Add missing sources at the end
        return array_unique(array_merge($filtered, $default));
    }
}

if (!function_exists('getTranslationFromSource')) {
    /**
     * Get translation from a specific source.
     *
     * @param string $source Source type
     * @param string $key Translation key
     * @param string $langCode Language code
     * @return string|null
     */
    function getTranslationFromSource($source, $key, $langCode)
    {
        switch ($source) {
            case 'database':
                return getTranslationFromDatabase($key, $langCode);

            case 'app':
                return getTranslationFromFiles(app_path("lang/{$langCode}/translations.php"), $key);

            case 'webkernel':
                return getTranslationFromFiles(base_path("packages/webkernel/src/lang/{$langCode}/translations.php"), $key);

            case 'other_packages':
                return getTranslationFromOtherPackages($key, $langCode);

            default:
                return null;
        }
    }
}

if (!function_exists('getTranslationFromDatabase')) {
    /**
     * Get translation from database.
     *
     * @param string $key Translation key
     * @param string $langCode Language code
     * @return string|null
     */
    function getTranslationFromDatabase($key, $langCode)
    {
        if (!class_exists('Webkernel\Models\LanguageTranslation')) {
            return null;
        }

        try {
            $userLangId = getUserLanguageId($langCode);
            $translations = LanguageTranslation::getTranslationsForKey($key);
            $translation = $translations->firstWhere('lang', $userLangId);

            return $translation ? decodeTranslation($translation->translation) : null;
        } catch (Exception $e) {
            error_log("Database translation lookup failed for key {$key}: " . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('getUserLanguageId')) {
    /**
     * Get user language ID from language code.
     *
     * @param string $langCode Language code
     * @return int Language ID
     */
    function getUserLanguageId($langCode)
    {
        static $cache = [];

        if (isset($cache[$langCode])) {
            return $cache[$langCode];
        }

        if (class_exists('Webkernel\Models\Language')) {
            try {
                $id = Language::where('code', $langCode)->value('id') ?? 1;
                $cache[$langCode] = $id;
                return $id;
            } catch (Exception $e) {
                error_log("Language ID lookup failed for {$langCode}: " . $e->getMessage());
            }
        }

        return 1; // Default fallback
    }
}

if (!function_exists('getTranslationFromFiles')) {
    /**
     * Get translation from file-based sources.
     *
     * @param string $filePath Path to translation file
     * @param string $key Translation key
     * @return string|null
     */
    function getTranslationFromFiles($filePath, $key)
    {
        if (!file_exists($filePath)) {
            return null;
        }

        try {
            // Check file syntax before including
            if (!isValidPhpFile($filePath)) {
                error_log("Invalid PHP syntax in translation file: {$filePath}");
                return null;
            }

            $translations = include $filePath;

            if (!is_array($translations)) {
                return null;
            }

            // Try multiple possible structures
            $patterns = [
                ['lang_ref', $key, 'label'],
                ['lang_ref', $key],
                [$key, 'label'],
                [$key]
            ];

            foreach ($patterns as $pattern) {
                $value = $translations;
                foreach ($pattern as $segment) {
                    if (!isset($value[$segment])) {
                        $value = null;
                        break;
                    }
                    $value = $value[$segment];
                }

                if ($value !== null) {
                    return decodeTranslation($value);
                }
            }

            return null;
        } catch (Exception $e) {
            error_log("File translation lookup failed for {$filePath}: " . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('getTranslationFromOtherPackages')) {
    /**
     * Get translation from other package files.
     *
     * @param string $key Translation key
     * @param string $langCode Language code
     * @return string|null
     */
    function getTranslationFromOtherPackages($key, $langCode)
    {
        $packageDirs = glob(base_path('packages/*/src/lang'));

        foreach ($packageDirs as $packageDir) {
            // Skip webkernel as it's handled separately
            if (strpos($packageDir, 'webkernel') !== false) {
                continue;
            }

            $filePath = $packageDir . '/' . $langCode . '/translations.php';
            $translation = getTranslationFromFiles($filePath, $key);

            if ($translation !== null) {
                return $translation;
            }
        }

        return null;
    }
}

if (!function_exists('isValidPhpFile')) {
    /**
     * Check if PHP file has valid syntax.
     *
     * @param string $filePath Path to PHP file
     * @return bool
     */
    function isValidPhpFile($filePath)
    {
        static $cache = [];

        if (isset($cache[$filePath])) {
            return $cache[$filePath];
        }

        // Quick syntax check using php -l
        $output = shell_exec("php -l " . escapeshellarg($filePath) . " 2>&1");
        $isValid = $output && strpos($output, 'No syntax errors') !== false;

        $cache[$filePath] = $isValid;
        return $isValid;
    }
}

if (!function_exists('processTranslation')) {
    /**
     * Process translation with replacements and formatting.
     *
     * @param string $translation Raw translation
     * @param string $key Translation key
     * @param array $replace Replacement parameters
     * @return HtmlString|string
     */
    function processTranslation($translation, $key, $replace = [])
    {
        // Apply replacements
        foreach ($replace as $search => $replaceValue) {
            $translation = str_replace(':' . $search, $replaceValue, $translation);
        }

        return formatTranslationOutput($translation, $key);
    }
}

if (!function_exists('formatTranslationOutput')) {
    /**
     * Format translation output with proper HTML handling.
     *
     * @param string $translation Processed translation
     * @param string $key Translation key
     * @return HtmlString|string
     */
    function formatTranslationOutput($translation, $key)
    {
        $containsHtml = strpos($translation, '<') !== false;

        // Add debugging wrapper if enabled
        if (function_exists('shouldShowTranslationKey') && shouldShowTranslationKey($key)) {
            $wrappedTranslation = "<span class='translatable' data-trans-key=\"{$key}\" title=\"{$key}\">{$translation}</span>";
            return new HtmlString($wrappedTranslation);
        }

        return $containsHtml ? new HtmlString($translation) : $translation;
    }
}

if (!function_exists('decodeTranslation')) {
    /**
     * Enhanced decode function with better error handling.
     *
     * @param string $translation Translation to decode
     * @return string Decoded translation
     */
    function decodeTranslation($translation)
    {
        if (!is_string($translation)) {
            return (string) $translation;
        }

        if (strpos($translation, 'base64:') === 0) {
            try {
                $decoded = base64_decode(substr($translation, 7), true);
                return $decoded !== false ? $decoded : $translation;
            } catch (Exception $e) {
                error_log("Base64 decode failed for translation: " . $e->getMessage());
                return $translation;
            }
        }

        return $translation;
    }
}

if (!function_exists('clearTranslationCache')) {
    /**
     * Clear translation cache.
     *
     * @param string|null $pattern Optional pattern to clear specific keys
     * @return bool
     */
    function clearTranslationCache($pattern = null)
    {
        if (!config('translations.cache_enabled', false)) {
            return true;
        }

        try {
            if ($pattern) {
                // For cache implementations that support pattern clearing
                if (method_exists(Cache::getStore(), 'flush')) {
                    Cache::flush();
                }
            } else {
                Cache::flush();
            }
            return true;
        } catch (Exception $e) {
            error_log("Translation cache clear failed: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('shouldShowTranslationKey')) {
    function shouldShowTranslationKey($key = null): bool
    {
        // Smart logic placeholder – to be extended later
        if (!Auth::check()) return false;

        $user = Auth::user();

        // Example: allow only superadmins and when special session is active
        return $user->is_admin
            && session()->has('can_edit_translations'); // or any dynamic condition
    }
}

if (!function_exists('lang_i')) {
 function lang_i($key = null, $replace = [], $locale = null)
 {
     static $doc_description = 'Returns the translation wrapped in single quotes, useful for inline display in forms and other components.';
     static $doc_usage = 'lang_i("hello");';
     static $doc_output = '\'Hello\'';
     static $doc_basedonfunction = '';
     static $doc_relatedfile = '';
     $translation = lang($key, $replace, $locale);
     return "'{$translation}'";
 }
}

/*
|--------------------------------------------------------------------------
| LEGACY COMPATIBILITY HELPERS - CONCERNING BACKWARD COMPATIBILITY - legacy_helpers.php
|--------------------------------------------------------------------------
| Legacy compatibility functions for maintaining backward compatibility
| with older versions of the system. These functions are deprecated but
| maintained for transition periods.
|--------------------------------------------------------------------------
| Credits: El Moumen Yassine - www.numerimondes.com
|
*/

if (!function_exists('getTranslationSources')) {
    /**
     * @deprecated Use getTranslationSourcesConfig() instead
     */
    function getTranslationSources()
    {
        return getTranslationSourcesConfig();
    }
}

if (!function_exists('priorityTranslation')) {
    /**
     * @deprecated Use getTranslationWithFallback() instead
     */
    function priorityTranslation($key, $langCode, $replace = [])
    {
        return getTranslationWithFallback($key, $langCode, $replace);
    }
}
