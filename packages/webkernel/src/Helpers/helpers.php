<?php

use Illuminate\Support\Facades\Auth;
use Webkernel\Models\Language;
use Webkernel\Models\LanguageTranslation;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

/*
|--------------------------------------------------------------------------
| Enhanced Translation Helper lang('') @Numerimondes Web Kernel
|--------------------------------------------------------------------------
| Author : El Moumen Yassine   ➜   www.numerimondes.com
|--------------------------------------------------------------------------
| Enhanced version with improved robustness and future-proofing
| Requires only two tables for all custom translations:
| - webkernel_lang (Model: Language)
| - webkernel_lang_words (Model: LanguageTranslation)
|--------------------------------------------------------------------------
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
                ['actions', $key, 'label'],
                ['actions', $key],
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

/*
|--------------------------------------------------------------------------
| Legacy compatibility functions (deprecated but maintained)
|--------------------------------------------------------------------------
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

/*|
  |FIN DU BLOCK DE LANG() - Enhanced Version
 *|

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




/*
|--------------------------------------------------------------------------
| Current User Timezone Helper @Numerimondes Web Kernel
|--------------------------------------------------------------------------
|
| Returns the timezone of the authenticated user.
| Falls back to the default app timezone if not logged in.
|
*/

// In myhelper.php
function isHelperLoaded() {
    return 'Helper is loaded!';
}

// packages/webkernel/src/Tools/helpers.php

if (! function_exists('webkernel_include')) {

     /**
     * Inclut une vue du module Webkernel.
     *
     * @return string
     */
    function webkernel_include(string $path, ?string $alias = null): string
    {
        $view = 'webkernel::' . $path;

        if (!view()->exists($view)) {
            // Commentaire dynamique pour IDE ou débogage
            return "<!-- Webkernel view not found: {$view} at " . now() . " -->"; // Ajout de l'heure actuelle pour un suivi précis.
        }

        return view($view, ['__alias' => $alias])->render();
    }
}

if (!function_exists('redirect_once')) {
    function redirect_once($url, $key = null)
    {
        $key = $key ?: 'redirect_once_' . md5($url);

        if (!Session::has($key)) {
            Session::put($key, true);
            return Redirect::to($url);
        }

        return null;
    }
}



if (!function_exists('CurrentUserTimezone')) {
    function CurrentUserTimezone()
    {
        static $doc_description = 'Returns the timezone of the authenticated user, or default timezone if not logged in.';
        static $doc_usage = 'CurrentUserTimezone();';
        static $doc_output = '"Europe/Paris" // Or config("app.timezone") if not logged in';
        static $doc_basedonfunction = '';

        return Auth::check()
            ? Auth::user()->timezone
            : config('app.timezone');
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

if (!function_exists('HelperCurrentYear')) {
    function HelperCurrentYear()
    {
        static $doc_description = 'Returns the current year (e.g., 2025).';
        static $doc_usage = 'currentyear();';
        static $doc_output = '2025';
        static $doc_basedonfunction = '';
        static $doc_relatedfile = '';

        return date('Y');
    }
}

if (!function_exists('HelperCurrentMonth')) {
    function HelperCurrentMonth()
    {
        static $doc_description = 'Returns the full name of the current month (e.g., "April").';
        static $doc_usage = 'currentmonth();';
        static $doc_output = '"April"';
        static $doc_basedonfunction = '';
        static $doc_relatedfile = '';

        return date('F');
    }
}

if (!function_exists('HelperCurrentDay')) {
    function HelperCurrentDay()
    {
        static $doc_description = 'Returns the full name of the current day of the week (e.g., "Monday").';
        static $doc_usage = 'HelperCurrentDay();';
        static $doc_output = '"Monday"';
        static $doc_basedonfunction = '';
        static $doc_relatedfile = '';

        return date('l');
    }
}

if (!function_exists('HelperFormattedDate')) {
    function HelperFormattedDate($date, $format = 'd/m/Y')
    {
        static $doc_description = 'Formats a given date into a specified format (default: "d/m/Y").';
        static $doc_usage = 'HelperFormattedDate("2025-04-17");';
        static $doc_output = '"17/04/2025"';
        static $doc_basedonfunction = 'Carbon::parse()';
        static $doc_relatedfile = '';

        // Check if date is valid before formatting
        if (!$date) {
            return 'Invalid Date'; // You could change this to 'N/A' or other values as needed
        }

        try {
            return \Carbon\Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return 'Invalid Date'; // or return another default value
        }
    }
}


if (!function_exists('HelperRandomString')) {
    function HelperRandomString($length = 16)
    {
        static $doc_description = 'Generates a random string of the specified length (default: 16).';
        static $doc_usage = 'HelperRandomString(8);';
        static $doc_output = '"f4a3d2b1" // Output will vary each time';
        static $doc_basedonfunction = 'random_bytes()';
        static $doc_relatedfile = '';

        // Ensure the length is a positive even number
        if ($length <= 0 || $length % 2 !== 0) {
            throw new InvalidArgumentException('Length must be a positive even number.');
        }

        return bin2hex(random_bytes($length / 2));
    }
}

if (!function_exists('HelperIsWeekend')) {
    function HelperIsWeekend($date = null)
    {
        static $doc_description = 'Checks if the provided date is a weekend (Saturday or Sunday).';
        static $doc_usage = 'isWeekend("2025-04-19");';
        static $doc_output = 'true';
        static $doc_basedonfunction = 'date()';
        static $doc_relatedfile = '';

        if ($date === null) {
            $date = date('Y-m-d');
        }

        return (date('N', strtotime($date)) >= 6);
    }
}

if (!function_exists('HelperIsLoggedIn')) {
    function HelperIsLoggedIn()
    {
        static $doc_description = 'Checks if the user is logged in.';
        static $doc_usage = 'isLoggedIn();';
        static $doc_output = 'true // if user is authenticated';
        static $doc_basedonfunction = 'auth()->check()';
        static $doc_relatedfile = '';

        return auth()->check();
    }
}

if (!function_exists('HelperDateToday')) {
    function HelperDateToday($format = 'Y-m-d')
    {
        static $doc_description = 'Returns today\'s date in the specified format (default: "Y-m-d").';
        static $doc_usage = 'datetoday("d-m-Y");';
        static $doc_output = '"17-04-2025"';
        static $doc_basedonfunction = 'date()';
        static $doc_relatedfile = '';

        return date($format);
    }
}

if (!function_exists('HelperPageLoadTime')) {
    /**
     * Returns the page load time with appropriate unit (μs, ms, s, min).
     *
     * @return string
     */
    function HelperPageLoadTime(): string
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
        $duration = microtime(true) - $startTime;

        if ($duration < 0.001) {
            return round($duration * 1_000_000, 2) . ' μs';
        }
        elseif ($duration < 1) {
            return round($duration * 1000, 2) . ' ms';
        }
        elseif ($duration < 60) {
            return round($duration, 2) . ' s';
        }
        else {
            $minutes = floor($duration / 60);
            $seconds = round($duration % 60, 2);
            return $minutes . ' min ' . $seconds . ' s';
        }
    }
}


if (!function_exists('HelperCachePage')) {
    function HelperCachePage($key, $content, $minutes = 60)
    {
        static $doc_description = 'Caches a page with a key and content.';
        static $doc_usage = 'cachePage("home_page_cache", $pageContent);';
        static $doc_output = 'true // If the page is successfully cached';
        static $doc_basedonfunction = 'Cache::put()';
        static $doc_relatedfile = '';

        Cache::put($key, $content, $minutes);
        return true;
    }
}

if (!function_exists('HelperExecutionTime')) {
    function HelperExecutionTime($startTime)
    {
        static $doc_description = 'Measures the execution time of a page from a starting point.';
        static $doc_usage = 'executionTime($startTime);';
        static $doc_output = '"0.4523"';
        static $doc_basedonfunction = 'microtime()';
        static $doc_relatedfile = '';

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        return number_format($executionTime, 4);
    }
}

if (!function_exists('HelperRedirectWithMessage')) {
    function HelperRedirectWithMessage($route, $message, $type = 'success')
    {
        static $doc_description = 'Redirects to a page with a flash message.';
        static $doc_usage = 'redirectWithMessage("home", "Page loaded successfully!");';
        static $doc_output = 'Redirect with flash message';
        static $doc_basedonfunction = 'redirect()->route()';
        static $doc_relatedfile = '';

        return redirect()->route($route)->with($type, $message);
    }
}

if (!function_exists('HelperHasAcceptedCookies')) {
    function HelperHasAcceptedCookies()
    {
        static $doc_description = 'Checks if the user has accepted cookies.';
        static $doc_usage = 'hasAcceptedCookies();';
        static $doc_output = 'true // If cookies have been accepted, false otherwise';
        static $doc_basedonfunction = 'Cookie::has()';
        static $doc_relatedfile = '';

        return Cookie::has('cookie_accepted');
    }
}

if (!function_exists('HelperPageDelayLoad')) {
    function HelperPageDelayLoad($seconds = 3)
    {
        static $doc_description = 'Reloads a page after a specific delay.';
        static $doc_usage = 'pageDelayLoad(5);';
        static $doc_output = 'Redirect after delay';
        static $doc_basedonfunction = 'header()';
        static $doc_relatedfile = '';

        header("Refresh: {$seconds};");
    }
}

if (!function_exists('HelperPageVisited')) {
    function HelperPageVisited($pageName)
    {
        static $doc_description = 'Checks if a specific page has been visited by the user.';
        static $doc_usage = 'pageVisited("home");';
        static $doc_output = 'true // If the page has been visited, false otherwise';
        static $doc_basedonfunction = 'session()';
        static $doc_relatedfile = '';

        return session()->has("visited_{$pageName}");
    }
}

if (!function_exists('HelperSavePageHistory')) {
    function HelperSavePageHistory($pageName)
    {
        static $doc_description = 'Saves a page in the user\'s history.';
        static $doc_usage = 'savePageHistory("home");';
        static $doc_output = 'true // Page saved in the history';
        static $doc_basedonfunction = 'session()';
        static $doc_relatedfile = '';

        session()->put("visited_{$pageName}", true);
    }
}


if (!function_exists('HelperServerTime')) {
    function HelperServerTime()
    {
        static $doc_description = 'Returns the current server time in a readable format.';
        static $doc_usage = 'servertime();';
        static $doc_output = '2025-04-17 14:23:45'; // Example output
        static $doc_basedonfunction = '';

        return now()->toDateTimeString(); // Using Carbon for easy handling of dates/times
    }
}

if (!function_exists('HelperMemoryUsage')) {
    function HelperMemoryUsage()
    {
        // Get the memory usage in bytes
        $memory_usage = memory_get_usage(true);

        // Determine the appropriate unit
        if ($memory_usage < 1024) {
            // Less than 1 KB, return in bytes
            return $memory_usage . ' B'; // Bytes
        } elseif ($memory_usage < 1024 * 1024) {
            // Less than 1 MB, return in kilobytes
            return round($memory_usage / 1024, 2) . ' KB'; // Kilobytes
        } elseif ($memory_usage < 1024 * 1024 * 1024) {
            // Less than 1 GB, return in megabytes
            return round($memory_usage / 1024 / 1024, 2) . ' MB'; // Megabytes
        } else {
            // 1 GB or more, return in gigabytes
            return round($memory_usage / 1024 / 1024 / 1024, 2) . ' GB'; // Gigabytes
        }
    }
}

if (!function_exists('HelperTotalRequests')) {
    function HelperTotalRequests()
    {
        static $doc_description = 'Returns the total number of requests handled by the application during the session.';
        static $doc_usage = 'totalrequests();';
        static $doc_output = '1200'; // Example output
        static $doc_basedonfunction = '';

        // Track the number of requests using a session variable
        $requests = session('total_requests', 0);
        session(['total_requests' => ++$requests]); // Increment on each request

        return $requests;
    }
}

// ConsoleColor helper function to handle colored messages with different types (info, error, warn)
function consoleColor($message, $type = 'info')
{
    // Define color codes for each type
    $colors = [
        'info'  => "\033[32m", // Green
        'error' => "\033[31m", // Red
        'warn'  => "\033[33m", // Yellow
    ];

    // Default to green (info) if an invalid type is passed
    $color = $colors[$type] ?? $colors['info'];

    // Output the message with the chosen color
    echo $color . $message . "\033[0m\n"; // Reset color after message
}

if (!function_exists('customizable_render_hook_view')) {
    function customizable_render_hook_view(string $view, array $data = []): \Illuminate\Contracts\View\View
    {
        // Convert name to Laravel path
        // Ex: webkernel::components.webkernel.ui.atoms.search-hide => components.webkernel.ui.atoms.search-hide
        $customView = str_replace('webkernel::', '', $view);

        if (view()->exists($customView)) {
            return view($customView, $data);
        }

        return view($view, $data); // fallback to the original package view
    }
}
