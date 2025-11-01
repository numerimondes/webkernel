<?php
declare(strict_types=1);

use Webkernel\Aptitudes\I18n\I18nBase as OptimizedI18n;
use Illuminate\Support\HtmlString;

/**
 * Global I18n helper functions - Ultra-optimized for microsecond performance
 * Compatible with Octane/Swoole/FrankenPHP
 */

if (!function_exists('lang')) {
    /**
     * Get the translation for a given key or return current locale
     *
     * @param string|null $key Translation key
     * @param array $replace Replacement parameters
     * @param string|null $locale Target locale
     * @return string
     */
    function lang(?string $key = null, array $replace = [], ?string $locale = null): string
    {
        if ($key === null) {
            return app()->getLocale();
        }

        return OptimizedI18n::instance()->translate($key, $replace, $locale);
    }
}


if (!function_exists('langHtml')) {
    /**
     * Get HTML-safe translation
     *
     * @param string $key Translation key
     * @param array $replace Replacement parameters
     * @param string|null $locale Target locale
     * @return HtmlString
     */
    function langHtml(string $key, array $replace = [], ?string $locale = null): HtmlString
    {
        return new HtmlString(lang($key, $replace, $locale));
    }
}

if (!function_exists('getAvailableLocales')) {
    /**
     * Get all available locales
     *
     * @return array
     */
    function getAvailableLocales(): array
    {
        return OptimizedI18n::instance()->getAvailableLocales();
    }
}

if (!function_exists('clearI18nCache')) {
    /**
     * Clear all I18n caches
     *
     * @return void
     */
    function clearI18nCache(): void
    {
        OptimizedI18n::instance()->clearCache();
    }
}

if (!function_exists('warmI18nCache')) {
    /**
     * Warm I18n cache for all locales
     *
     * @return void
     */
    function warmI18nCache(): void
    {
        OptimizedI18n::instance()->warmCache();
    }
}

if (!function_exists('langBulk')) {
    /**
     * Get multiple translations at once for better performance
     *
     * @param array $keys Array of translation keys
     * @param array $replace Replacement parameters
     * @param string|null $locale Target locale
     * @return array
     */
    function langBulk(array $keys, array $replace = [], ?string $locale = null): array
    {
        $results = [];
        $i18n = OptimizedI18n::instance();

        foreach ($keys as $key) {
            $results[$key] = $i18n->translate($key, $replace, $locale);
        }

        return $results;
    }
}

if (!function_exists('langExists')) {
    /**
     * Check if a translation key exists
     *
     * @param string $key Translation key
     * @param string|null $locale Target locale
     * @return bool
     */
    function langExists(string $key, ?string $locale = null): bool
    {
        $translation = lang($key, [], $locale);
        return $translation !== $key;
    }
}

if (!function_exists('langChoice')) {
    /**
     * Get translation with pluralization support
     *
     * @param string $key Translation key
     * @param int $number Number for pluralization
     * @param array $replace Replacement parameters
     * @param string|null $locale Target locale
     * @return string
     */
    function langChoice(string $key, int $number, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        // Add number to replacements
        $replace['count'] = $number;

        // Try pluralized key first
        $pluralKey = $number === 1 ? "{$key}.singular" : "{$key}.plural";
        $translation = lang($pluralKey, $replace, $locale);

        if ($translation !== $pluralKey) {
            return $translation;
        }

        // Fallback to base key
        return lang($key, $replace, $locale);
    }
}

if (!function_exists('langFallback')) {
    /**
     * Get translation with multiple fallback keys
     *
     * @param array $keys Array of keys to try in order
     * @param array $replace Replacement parameters
     * @param string|null $locale Target locale
     * @return string
     */
    function langFallback(array $keys, array $replace = [], ?string $locale = null): string
    {
        foreach ($keys as $key) {
            $translation = lang($key, $replace, $locale);
            if ($translation !== $key) {
                return $translation;
            }
        }

        // Return first key if no translation found
        return $keys[0] ?? '';
    }
}

if (!function_exists('isRtlLocale')) {
    /**
     * Check if locale requires RTL text direction
     *
     * @param string|null $locale Locale to check (current if null)
     * @return bool
     */
    function isRtlLocale(?string $locale = null): bool
    {
        $locale = $locale ?: app()->getLocale();

        $rtlLocales = [
            'ar', 'fa', 'he', 'ur', 'ps', 'ckb', 'ku', 'sd', 'dv'
        ];

        return in_array($locale, $rtlLocales, true);
    }
}

if (!function_exists('getTextDirection')) {
    /**
     * Get text direction for locale
     *
     * @param string|null $locale Locale to check (current if null)
     * @return string 'rtl' or 'ltr'
     */
    function getTextDirection(?string $locale = null): string
    {
        return isRtlLocale($locale) ? 'rtl' : 'ltr';
    }
}

if (!function_exists('validateLanguageCode')) {
    /**
     * Validate and normalize language code
     *
     * @param string $langCode Language code to validate
     * @return string Normalized language code
     */
    function validateLanguageCode(string $langCode): string
    {
        $langCode = str_replace('_', '-', strtolower(trim($langCode)));

        // Normalization map
        $normalizations = [
            'pt-br' => 'pt-BR',
            'pt-pt' => 'pt-PT',
            'zh-cn' => 'zh-CN',
            'zh-tw' => 'zh-TW',
            'en-us' => 'en-US',
            'en-gb' => 'en-GB',
            'fr-ca' => 'fr-CA',
            'es-mx' => 'es-MX',
            'de-de' => 'de-DE',
            'it-it' => 'it-IT',
            'ja-jp' => 'ja-JP',
            'ko-kr' => 'ko-KR',
        ];

        return $normalizations[$langCode] ?? $langCode;
    }
}

if (!function_exists('langStats')) {
    /**
     * Get I18n system statistics
     *
     * @return array
     */
    function langStats(): array
    {
        return OptimizedI18n::instance()->getStats();
    }
}

if (!function_exists('langDebug')) {
    /**
     * Debug translation key resolution
     *
     * @param string $key Translation key
     * @param string|null $locale Target locale
     * @return array Debug information
     */
    function langDebug(string $key, ?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $translation = lang($key, [], $locale);

        return [
            'key' => $key,
            'locale' => $locale,
            'translation' => $translation,
            'exists' => $translation !== $key,
            'stats' => langStats(),
        ];
    }
}
