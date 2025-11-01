<?php

declare(strict_types=1);

if (!function_exists('CurrentUserTimezone')) {
    /**
     * Get the current user's timezone display name.
     */
    function CurrentUserTimezone(): string
    {
        return \Webkernel\Aptitudes\Users\Models\UserExtensions\UserPreference::getCurrentUserTimezoneDisplay();
    }
}

if (!function_exists('CurrentUserTimezoneName')) {
    /**
     * Get the current user's timezone name (for JavaScript).
     */
    function CurrentUserTimezoneName(): string
    {
        return \Webkernel\Aptitudes\Users\Models\UserExtensions\UserPreference::getCurrentUserTimezone();
    }
}
