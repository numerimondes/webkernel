<?php

use Illuminate\Support\Facades\Cookie;

if (!function_exists('HelperHasAcceptedCookies')) {
    function HelperHasAcceptedCookies()
    {
        return Cookie::has('cookie_accepted');
    }
}
