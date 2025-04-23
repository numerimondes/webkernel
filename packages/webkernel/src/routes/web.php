<?php

use Illuminate\Support\Facades\Route;
use Filament\Notifications\Notification;
use Illuminate\Auth\AuthManager as Auth;
use Illuminate\Auth\Middleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;

Route::get('/', function () {
    return redirect_once('/system') ?? view('welcome');
});

Route::get('notif', function () {
    $recipient = auth()->user();

    if ($recipient) {
        Notification::make()
            ->title('Test Notification')
            ->body('Ceci est un test de notification.')
            ->sendToDatabase($recipient);
    }

    return 'Notification envoyée.';
});


Route::middleware(['auth'])->get('lang/{lang}', function ($lang) {
    /*
    |--------------------------------------------------------------------------
    | Change Language Route
    |--------------------------------------------------------------------------
    |
    | This route is responsible for changing the language of the application.
    | If the user is authenticated, their language preference is updated in the database.
    | The selected language is then stored in the session and optionally in a cookie.
    |
    */

 //   dd(auth());

    // If the user is authenticated, update their language preference in the database
    if (auth()->check()) {
        auth()->user()->update(['user_lang' => $lang]);
        // Update the user's language in the database
    }

    // Store the selected language in the session
    session(['locale' => $lang]); // Set the language in the session

    // Optionally: Make the language persistent via a cookie
    cookie()->queue(cookie('locale', $lang, 60 * 24 * 365));  // Set a cookie for the language, expires in 1 year

    // Redirect the user back to the previous page
    return redirect()->back(); // Redirect to the previous page
});
