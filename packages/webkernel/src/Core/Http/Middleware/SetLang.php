<?php

namespace Webkernel\Core\Http\Middleware;

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
    | To invoque this class use 
    | WEBKERNEL__LANGUAGE__MIDDLEWARE 
    | WEBKERNEL__LANGUAGE__MIDDLEWARE__CLASS_ESCAPED 
    |
    | This method is responsible for setting the language of the application.
    | It checks if the user is authenticated and applies their preferred language.
    | If not authenticated, it uses the language stored in the session or defaults to 'en'.
    |
    */

    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', 'en');  

        if (auth()->check()) {

            $userLang = auth()->user()->user_lang ?? $locale;
            app()->setLocale($userLang); 
        } else {

            app()->setLocale($locale);
        }

        return $next($request);
    }
}

