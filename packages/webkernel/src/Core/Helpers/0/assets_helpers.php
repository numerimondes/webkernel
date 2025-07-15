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
        $indexKey = "file_token_index:" . md5($cleanPath);

        $cacheIsReady = true;
        try {
            $cacheIsReady = Schema::hasTable('cache');
        } catch (\Exception) {
            $cacheIsReady = false;
        }

        if ($cacheIsReady && !$forceRefresh) {
            $cachedTokenData = Cache::get($cacheKey);
            if ($cachedTokenData && Carbon::now()->lt(Carbon::parse($cachedTokenData['expires_at']))) {
                // Token existant et encore valide : on le réutilise
                return url("/assets/{$cachedTokenData['token']}");
            }
        }

        // Purge tous les anciens tokens pour ce fichier via l'index
        if ($cacheIsReady) {
            $oldTokens = Cache::get($indexKey, []);
            if (is_array($oldTokens)) {
                foreach ($oldTokens as $oldToken) {
                    Cache::forget("file_token:{$oldToken}");
                }
            }
            // On remplace l'index par le nouveau token
            $token = Str::random(32);
            $expiresAt = Carbon::now()->addMinutes($expirationMinutes);

            Cache::put("file_token:{$token}", [
                'path' => $cleanPath,
                'expires_at' => $expiresAt->toDateTimeString(),
            ], $expirationMinutes * 60);

            Cache::put($cacheKey, [
                'token' => $token,
                'expires_at' => $expiresAt->toDateTimeString(),
            ], $expirationMinutes * 60);

            Cache::put($indexKey, [$token], $expirationMinutes * 60);

            return url("/assets/{$token}");
        }

        // Fallback si cache non prêt
        $token = Str::random(32);
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

