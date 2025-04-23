<?php

namespace Webkernel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
class SetLang
{
    /*
    |--------------------------------------------------------------------------
    | Handle the incoming request
    |--------------------------------------------------------------------------
    |
    | This method is responsible for setting the language of the application.
    | It checks if the user is authenticated and applies their preferred language.
    | If not authenticated, it uses the language stored in the session or defaults to 'en'.
    |
    */

    public function handle(Request $request, Closure $next)
    {
        // Check if a language is set in the session or use the default language 'en'
        $locale = session('locale', 'en');  // 'en' is the default language

        // If the user is authenticated, use their preferred language, otherwise use the session's language
        if (auth()->check()) {
            // Retrieve the user's preferred language or fallback to the session language
            $userLang = auth()->user()->user_lang ?? $locale;
            app()->setLocale($userLang); // Set the application locale
        } else {
            // If the user is not authenticated, use the language from the session
            app()->setLocale($locale); // Set the application locale
        }

        // Proceed with the next request
        return $next($request);
    }
}

