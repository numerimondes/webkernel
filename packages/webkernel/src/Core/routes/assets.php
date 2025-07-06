<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/**
 * Route to serve private file via token (URL: /assets/{token}).
 */
Route::get('/assets/{token}', function (string $token) {
    $fileInfo = Cache::get("file_token:{$token}");
    if (!$fileInfo) {
        abort(404, 'File not found or expired');
    }

    if (now()->gt(Carbon::parse($fileInfo['expires_at']))) {
        Cache::forget("file_token:{$token}");
        abort(404, 'File expired');
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

    abort(404, 'File not found');
})->name('private-asset')->where('token', '[a-zA-Z0-9]{32}');


/**
 * Generate clean asset URL with hash + extension.
 */
if (!function_exists('platformCleanAssetUrl')) {
    function platformCleanAssetUrl(string $path, int $expirationMinutes = 30): string
    {
        $hash = substr(md5($path . floor(time() / ($expirationMinutes * 60))), 0, 16);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        Cache::put("asset_hash:{$hash}", [
            'path' => $path,
            'expires_at' => now()->addMinutes($expirationMinutes)
        ], $expirationMinutes * 60);

        return url("/assets/{$hash}" . ($extension ? ".{$extension}" : ''));
    }
}

Route::get('/assets/{hash}{extension?}', function (string $hash, string $extension = '') {
    $extension = ltrim($extension, '.');
    $fileInfo = Cache::get("asset_hash:{$hash}");

    if (!$fileInfo) {
        abort(404, 'Asset not found or expired');
    }

    if (now()->gt(Carbon::parse($fileInfo['expires_at']))) {
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
