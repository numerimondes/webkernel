<?php

if (!function_exists('HelperRandomString')) {
    function HelperRandomString($length = 16)
    {
        if ($length <= 0 || $length % 2 !== 0) {
            throw new InvalidArgumentException('Length must be a positive even number.');
        }
        return bin2hex(random_bytes($length / 2));
    }
}

if (!function_exists('HelperIsLoggedIn')) {
    function HelperIsLoggedIn()
    {
        return auth()->check();
    }
}

if (!function_exists('HelperPageLoadTime')) {
    function HelperPageLoadTime(): string
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
        $duration = microtime(true) - $startTime;

        if ($duration < 0.001) return round($duration * 1_000_000, 2) . ' μs';
        if ($duration < 1)     return round($duration * 1000, 2) . ' ms';
        if ($duration < 60)    return round($duration, 2) . ' s';

        $minutes = floor($duration / 60);
        $seconds = round($duration % 60, 2);
        return $minutes . ' min ' . $seconds . ' s';
    }
}

if (!function_exists('HelperMemoryUsage')) {
    function HelperMemoryUsage()
    {
        $bytes = memory_get_usage(true);
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 2) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 2) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }
}

if (!function_exists('HelperExecutionTime')) {
    function HelperExecutionTime($startTime)
    {
        return number_format(microtime(true) - $startTime, 4);
    }
}

if (!function_exists('HelperTotalRequests')) {
    function HelperTotalRequests()
    {
        $requests = session('total_requests', 0);
        session(['total_requests' => ++$requests]);
        return $requests;
    }
}

if (!function_exists('isHelperLoaded')) {
    function isHelperLoaded() {
        return 'Helper is loaded!';
    }
}
