<?php

use Carbon\Carbon;
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


/*
|--------------------------------------------------------------------------
| USER TIMEZONE AND DATE HELPERS - CONCERNING DATE/TIME MANAGEMENT - datetime_helpers.php
|--------------------------------------------------------------------------
| Enhanced date and time management helpers with timezone support,
| user preferences, and comprehensive date formatting utilities.
|--------------------------------------------------------------------------
| Credits: El Moumen Yassine - www.numerimondes.com
|
*/

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
            return Carbon::parse($date)->format($format);
        } catch (Exception $e) {
            return 'Invalid Date'; // or return another default value
        }
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
