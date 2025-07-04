<?php

use Illuminate\Support\Facades\Cache;

if (!function_exists('HelperCachePage')) {
    function HelperCachePage($key, $content, $minutes = 60)
    {
        Cache::put($key, $content, $minutes);
        return true;
    }
}
