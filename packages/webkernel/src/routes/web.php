<?php

use Illuminate\Support\Facades\Route;
use Filament\Notifications\Notification;
use Illuminate\Auth\AuthManager as Auth;
use Illuminate\Auth\Middleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


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


Route::middleware([Authenticate::class])->get('lang/{lang}', function ($lang) {    /*
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



Route::get('/dynamic.css', function () {
    return Response::make(generate_dynamic_css(), 200, [
        'Content-Type' => 'text/css',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->name('dynamic.css');

Route::get('/dynamic.js', function () {
    return Response::make(generate_dynamic_js(), 200, [
        'Content-Type' => 'application/javascript',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->name('dynamic.js');

Route::get('/manifest.json', function () {
    return Response::json(generate_manifest_json());
})->name('manifest.json');

Route::get('/service-worker.js', function () {
    return Response::make(generate_service_worker(), 200, [
        'Content-Type' => 'application/javascript',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->name('service-worker.js');


