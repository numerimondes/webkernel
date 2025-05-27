<?php

//Customize how different some interface components behave and appear across the platform.

use Illuminate\Support\Facades\Auth;
use Webkernel\Models\Language;
use Webkernel\Models\LanguageTranslation;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\HtmlString;
/*
|--------------------------------------------------------------------------
| Translation Helper lang('') @Numerimondes Web Kernel
|--------------------------------------------------------------------------
| Author : El Moumen Yassine   ➜   www.numerimondes.com
|--------------------------------------------------------------------------
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

        if (is_null($key)) {
            return $key;
        }

        if (!is_array($replace)) {
            $replace = [];
        }

        // Get user language code
        $userLangCode = 'en';
        if (Auth::check() && Auth::user()->user_lang) {
            $userLangCode = Auth::user()->user_lang;
        }

        // Search in ALL packages/*/src/lang directories FIRST
        $packageDirs = glob(base_path('packages/*/src/lang'));

        foreach ($packageDirs as $packageDir) {
            $filePath = $packageDir . '/' . $userLangCode . '/translations.php';

            // Debug: Vérifier si le fichier existe
            if (file_exists($filePath)) {
                try {
                    $translations = include $filePath;

                    // Vérifier si $translations est un array valide
                    if (is_array($translations)) {
                        // Structure flexible: chercher dans plusieurs niveaux
                        $translationValue = null;

                        // Essayer différentes structures possibles
                        if (isset($translations['actions'][$key]['label'])) {
                            $translationValue = $translations['actions'][$key]['label'];
                        } elseif (isset($translations['actions'][$key])) {
                            $translationValue = $translations['actions'][$key];
                        } elseif (isset($translations[$key]['label'])) {
                            $translationValue = $translations[$key]['label'];
                        } elseif (isset($translations[$key])) {
                            $translationValue = $translations[$key];
                        }

                        if ($translationValue) {
                            $message = $translationValue;
                            foreach ($replace as $search => $replaceValue) {
                                $message = str_replace(':' . $search, $replaceValue, $message);
                            }
                            // CORRECTION: Retourner du HTML non-échappé si contient des balises HTML
                            return (strpos($message, '<') !== false) ? new HtmlString($message) : $message;
                        }
                    }
                } catch (\Exception $e) {
                    // Log l'erreur pour debug
                    error_log("Erreur lors du chargement du fichier de traduction: " . $filePath . " - " . $e->getMessage());
                    continue;
                }
            }
        }

        // Fallback: essayer avec la langue par défaut si différente
        if ($userLangCode !== 'en') {
            foreach ($packageDirs as $packageDir) {
                $filePath = $packageDir . '/en/translations.php';

                if (file_exists($filePath)) {
                    try {
                        $translations = include $filePath;

                        if (is_array($translations)) {
                            $translationValue = null;

                            if (isset($translations['actions'][$key]['label'])) {
                                $translationValue = $translations['actions'][$key]['label'];
                            } elseif (isset($translations['actions'][$key])) {
                                $translationValue = $translations['actions'][$key];
                            } elseif (isset($translations[$key]['label'])) {
                                $translationValue = $translations[$key]['label'];
                            } elseif (isset($translations[$key])) {
                                $translationValue = $translations[$key];
                            }

                            if ($translationValue) {
                                $message = $translationValue;
                                foreach ($replace as $search => $replaceValue) {
                                    $message = str_replace(':' . $search, $replaceValue, $message);
                                }
                                // CORRECTION: Retourner du HTML non-échappé si contient des balises HTML
                                return (strpos($message, '<') !== false) ? new HtmlString($message) : $message;
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        // Search in app/lang as second option
        $appLangPath = app_path('lang/' . $userLangCode . '/translations.php');
        if (file_exists($appLangPath)) {
            try {
                $translations = include $appLangPath;
                if (is_array($translations) && isset($translations['actions'][$key]['label'])) {
                    $message = $translations['actions'][$key]['label'];
                    foreach ($replace as $search => $replaceValue) {
                        $message = str_replace(':' . $search, $replaceValue, $message);
                    }

                    // CORRECTION: Gérer le HTML et les clés de traduction
                    $containsHtml = strpos($message, '<') !== false;

                    if (function_exists('shouldShowTranslationKey') && shouldShowTranslationKey($key)) {
                        $wrappedMessage = "<span class='translatable' data-trans-key=\"{$key}\" title=\"{$key}\">{$message}</span>";
                        return new HtmlString($wrappedMessage);
                    }

                    return $containsHtml ? new HtmlString($message) : $message;
                }
            } catch (\Exception $e) {
                // Continue to database search
            }
        }

        // Search in database as fallback
        $userLangId = 1; // Default
        if (Auth::check()) {
            if (class_exists('Webkernel\Models\Language')) {
                $userLangId = \Webkernel\Models\Language::where('code', Auth::user()->user_lang)->value('id') ?? 1;
            }
        }

        if (class_exists('Webkernel\Models\LanguageTranslation')) {
            try {
                $translations = \Webkernel\Models\LanguageTranslation::getTranslationsForKey($key);
                $translation = $translations->firstWhere('lang', $userLangId);

                if ($translation) {
                    $message = $translation->translation;
                    foreach ($replace as $search => $replaceValue) {
                        $message = str_replace(':' . $search, $replaceValue, $message);
                    }

                    // CORRECTION: Gérer le HTML et les clés de traduction
                    $containsHtml = strpos($message, '<') !== false;

                    if (function_exists('shouldShowTranslationKey') && shouldShowTranslationKey($key)) {
                        $wrappedMessage = "<span class='translatable' data-trans-key=\"{$key}\" title=\"{$key}\">{$message}</span>";
                        return new HtmlString($wrappedMessage);
                    }

                    return $containsHtml ? new HtmlString($message) : $message;
                }
            } catch (\Exception $e) {
                // Continue to final fallback
            }
        }

        // Final fallback to Laravel's built-in translation
        $fallback = __($key, $replace, $locale);
        return (is_string($fallback) && strpos($fallback, '<') !== false) ? new HtmlString($fallback) : $fallback;
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
