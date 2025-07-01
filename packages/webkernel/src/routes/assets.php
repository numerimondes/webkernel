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


// Route propre pour servir les fichiers
Route::get('/assets/{token}', function (string $token, Request $request) {
    // Récupérer les infos du token depuis le cache
    $fileInfo = Cache::get("file_token:{$token}");

    if (!$fileInfo) {
        abort(404, 'File not found or expired');
    }

    // Vérifier l'expiration
    if (now()->gt($fileInfo['expires_at'])) {
        Cache::forget("file_token:{$token}");
        abort(404, 'File expired');
    }

    $path = $fileInfo['path'];

    // Vérification de sécurité
    if (str_contains($path, '..') || str_contains($path, '\\')) {
        abort(403, 'Invalid path');
    }

    // Essayer d'abord via Storage
    if (Storage::disk('local')->exists($path)) {
    $response = Storage::disk('local')->response($path);
    $response->headers->set('Cache-Control', 'public, max-age=3600');
    return $response;
    }

    // Ensuite via le système de fichiers
    $fullPath = base_path($path);
    if (File::exists($fullPath) && File::isFile($fullPath)) {
        $mimeType = File::mimeType($fullPath);
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    abort(404, 'File not found');

})->name('private-asset')->where('token', '[a-zA-Z0-9]{32}');

// Alternative avec hash du nom de fichier (encore plus propre)
if (!function_exists('platformCleanAssetUrl')) {
    function platformCleanAssetUrl(string $path, int $expirationMinutes = 30): string
    {
        // Créer un hash basé sur le chemin + timestamp
        $hash = substr(md5($path . floor(time() / ($expirationMinutes * 60))), 0, 16);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        // Stocker le mapping dans le cache
        Cache::put("asset_hash:{$hash}", [
            'path' => $path,
            'expires_at' => now()->addMinutes($expirationMinutes)
        ], $expirationMinutes * 60);

        // URL encore plus propre avec extension
        return url("/assets/{$hash}" . ($extension ? ".{$extension}" : ''));
    }
}

// Route alternative avec extension dans l'URL
Route::get('/assets/{hash}{extension?}', function (string $hash, string $extension = '', Request $request) {
    // Nettoyer l'extension
    $extension = ltrim($extension, '.');

    $fileInfo = Cache::get("asset_hash:{$hash}");

    if (!$fileInfo) {
        abort(404, 'Asset not found or expired');
    }

    if (now()->gt($fileInfo['expires_at'])) {
        Cache::forget("asset_hash:{$hash}");
        abort(404, 'Asset expired');
    }

    $path = $fileInfo['path'];

    if (str_contains($path, '..') || str_contains($path, '\\')) {
        abort(403, 'Invalid path');
    }

    if (Storage::disk('local')->exists($path)) {
        $response = Storage::disk('local')->response($path);
        $response->headers->set('Cache-Control', 'public, max-age=3600');
        return $response;
    }

    $fullPath = base_path($path);
    if (File::exists($fullPath) && File::isFile($fullPath)) {
        return response()->file($fullPath, [
            'Content-Type' => File::mimeType($fullPath),
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    abort(404, 'Asset not found');

})->name('clean-asset')
  ->where('hash', '[a-f0-9]{16}')
  ->where('extension', '\.[a-zA-Z0-9]+');

