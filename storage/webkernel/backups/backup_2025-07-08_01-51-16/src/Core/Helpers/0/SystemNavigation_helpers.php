<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

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
        return redirect()->route($route)->with($type, $message);
    }
}

if (!function_exists('HelperPageDelayLoad')) {
    function HelperPageDelayLoad($seconds = 3)
    {
        header("Refresh: {$seconds};");
    }
}

if (!function_exists('HelperPageVisited')) {
    function HelperPageVisited($pageName)
    {
        return session()->has("visited_{$pageName}");
    }
}

if (!function_exists('HelperSavePageHistory')) {
    function HelperSavePageHistory($pageName)
    {
        session()->put("visited_{$pageName}", true);
    }
}
