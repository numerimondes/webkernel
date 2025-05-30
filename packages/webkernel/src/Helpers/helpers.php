<?php

use Illuminate\Support\Facades\Auth;
use Webkernel\Models\Language;
use Webkernel\Models\LanguageTranslation;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Webkernel\Helpers\ResourceLayoutHelper;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;

$helpersPath = __DIR__;
$excludedFile = 'no-file-excluded-for-the-moment.php';

foreach (glob($helpersPath . '/*.php') as $file) {
    if (basename($file) !== $excludedFile) {
        require_once $file;
    }
}

/*
|--------------------------------------------------------------------------
| UTILITY AND SYSTEM HELPERS - CONCERNING SYSTEM UTILITIES - utility_helpers.php
|--------------------------------------------------------------------------
| General utility functions for string generation, authentication checks,
| performance monitoring, caching, and system resource management.
|--------------------------------------------------------------------------
| Credits: El Moumen Yassine - www.numerimondes.com
|
*/

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

if (!function_exists('isHelperLoaded')) {
    function isHelperLoaded() {
        return 'Helper is loaded!';
    }
}

/*
|--------------------------------------------------------------------------
| CACHING AND PERFORMANCE HELPERS - CONCERNING CACHE MANAGEMENT - cache_helpers.php
|--------------------------------------------------------------------------
| Enhanced caching utilities for page caching, performance optimization,
| and cache management with expiration and validation support.
|--------------------------------------------------------------------------
| Credits: El Moumen Yassine - www.numerimondes.com
|
*/

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

/*
|--------------------------------------------------------------------------
| NAVIGATION AND REDIRECTION HELPERS - CONCERNING URL MANAGEMENT - navigation_helpers.php
|--------------------------------------------------------------------------
| Enhanced navigation and redirection utilities with session management,
| one-time redirects, and page history tracking capabilities.
|--------------------------------------------------------------------------
| Credits: El Moumen Yassine - www.numerimondes.com
|
*/

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

/*
|--------------------------------------------------------------------------
| COOKIE AND SESSION HELPERS - CONCERNING SESSION MANAGEMENT - session_helpers.php
|--------------------------------------------------------------------------
| Enhanced cookie and session management utilities with validation,
| acceptance tracking, and secure session handling capabilities.
|--------------------------------------------------------------------------
| Credits: El Moumen Yassine - www.numerimondes.com
|
*/

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

/*
|--------------------------------------------------------------------------
| CONSOLE AND DEBUG HELPERS - CONCERNING DEBUGGING AND LOGGING - debug_helpers.php
|--------------------------------------------------------------------------
| Enhanced debugging utilities with colored console output, message formatting,
| and development environment helpers for better debugging experience.
|--------------------------------------------------------------------------
| Credits: El Moumen Yassine - www.numerimondes.com
|
*/

if (!function_exists('consoleColor')) {
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
}
