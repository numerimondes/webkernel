<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;




/**
 * Generate a secure public URL for a private asset with token.
 *
 * @param string $path Relative file path
 * @param int $expirationMinutes Token validity duration in minutes
 * @param bool $forceRefresh Force cache refresh
 * @return string
 */
if (!function_exists('platformAbsoluteUrlAnyPrivatetoPublic')) {
    function platformAbsoluteUrlAnyPrivatetoPublic(string $path, int $expirationMinutes = 30, bool $forceRefresh = false): string
    {
        $cleanPath = ltrim(str_replace('\\', '/', $path), '/');
        $cacheKey = "file_token_by_path:" . md5($cleanPath);

        $cacheIsReady = true;
        try {
            $cacheIsReady = Schema::hasTable('cache');
        } catch (\Exception) {
            $cacheIsReady = false;
        }

        if ($cacheIsReady && !$forceRefresh) {
            $cachedTokenData = Cache::get($cacheKey);
            if ($cachedTokenData && Carbon::now()->lt(Carbon::parse($cachedTokenData['expires_at']))) {
                return url("/assets/{$cachedTokenData['token']}");
            }
        }

        $token = Str::random(32);
        $expiresAt = Carbon::now()->addMinutes($expirationMinutes);

        if ($cacheIsReady) {
            Cache::put("file_token:{$token}", [
                'path' => $cleanPath,
                'expires_at' => $expiresAt->toDateTimeString(),
            ], $expirationMinutes * 60);

            Cache::put($cacheKey, [
                'token' => $token,
                'expires_at' => $expiresAt->toDateTimeString(),
            ], $expirationMinutes * 60);
        }

        return url("/assets/{$token}");
    }
}

/**
 * Flush cached token for asset to force regeneration.
 *
 * @param string $path
 * @return void
 */
if (!function_exists('platformFlushAssetCache')) {
    function platformFlushAssetCache(string $path): void
    {
        $cleanPath = ltrim(str_replace('\\', '/', $path), '/');
        $cacheKey = "file_token_by_path:" . md5($cleanPath);

        $tokenData = Cache::get($cacheKey);
        if ($tokenData) {
            Cache::forget("file_token:{$tokenData['token']}");
            Cache::forget($cacheKey);
        }
    }
}

