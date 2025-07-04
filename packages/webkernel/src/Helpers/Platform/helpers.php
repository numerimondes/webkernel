<?php

if (!function_exists('appIsWebkernelSubPlatform')) {
    function appIsWebkernelSubPlatform(): bool
    {
        return !empty(glob(base_path('platform/*')));
    }
}