<?php
use Illuminate\Support\Facades\Route;
use Webkernel\Arcanes\QueryModules;
use Illuminate\Support\Facades\Cache;
use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

//Route::get('/', fn() => view('base::base'))->name('base.welcome');

// Route::fallback(function (): void {
//   $referer = request()->header('referer');
//   $currentUrl = request()->fullUrl();
//
//   error_response()
//     ->code(404)
//     ->errorCode('FALLBACK_ROUTE')
//     ->message('You requested a link that does not exist.')
//     ->source('Router Fallback')
//     ->originalUrl($currentUrl)
//     ->previousUrl($referer)
//     ->showBackButton(true)
//     ->showReloadButton(true)
//     ->showHomeButton(true)
//     ->redirect();
// });
