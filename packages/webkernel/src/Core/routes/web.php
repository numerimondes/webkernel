<?php

use Illuminate\Support\Facades\Route;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Response;
use Illuminate\Auth\Middleware\Authenticate;
use Webkernel\Filament\Pages\Settings;
use Webkernel\Filament\Pages\BaseSettingsPage;
use Webkernel\Core\Http\Controllers\PlatformUpdateController;

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

Route::middleware([Authenticate::class])->get('lang/{lang}', function ($lang) {
    if (auth()->check()) {
        auth()->user()->update(['user_lang' => $lang]);
    }

    session(['locale' => $lang]);
    cookie()->queue(cookie('locale', $lang, 60 * 24 * 365));
    return redirect()->back();
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

/*
|--------------------------------------------------------------------------
| Webkernel Platform Update Routes
|--------------------------------------------------------------------------
|
| These routes provide API endpoints for platform updates and management.
| All routes are prefixed with 'webkernel' and require authentication.
|
*/

Route::prefix('webkernel/updates')->middleware(['auth'])->group(function () {
    
    // Check for updates
    Route::get('/check', [PlatformUpdateController::class, 'checkForUpdates'])
        ->name('webkernel.updates.check');
    
    // Get current status
    Route::get('/status', [PlatformUpdateController::class, 'getStatus'])
        ->name('webkernel.updates.status');
    
    // Perform update
    Route::post('/perform', [PlatformUpdateController::class, 'performUpdate'])
        ->name('webkernel.updates.perform');
    
    // Auto-update (rolling release)
    Route::post('/auto', [PlatformUpdateController::class, 'autoUpdate'])
        ->name('webkernel.updates.auto');
    
    // Get available backups
    Route::get('/backups', [PlatformUpdateController::class, 'getBackups'])
        ->name('webkernel.updates.backups');
    
    // Restore from backup
    Route::post('/restore', [PlatformUpdateController::class, 'restoreFromBackup'])
        ->name('webkernel.updates.restore');
    
    // Clear version cache
    Route::post('/clear-cache', [PlatformUpdateController::class, 'clearCache'])
        ->name('webkernel.updates.clear-cache');
});

// Public health check endpoint (no auth required)
Route::get('webkernel/health', function () {
    return response()->json([
        'status' => 'healthy',
        'version' => defined('WEBKERNEL_VERSION') ? WEBKERNEL_VERSION : 'unknown',
        'timestamp' => now()->toISOString()
    ]);
})->name('webkernel.health');


