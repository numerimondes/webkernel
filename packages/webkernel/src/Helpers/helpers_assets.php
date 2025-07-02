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

/**
 * Return platform info for given key, fallback on defaults or fallback values.
 *
 * @param string $key
 * @return mixed|null
 */
if (!function_exists('corePlatformInfos')) {
    function corePlatformInfos(string $key): mixed
    {
        $defaults = [
            'brandName' => 'Webkernel',
            'cssTitle' => 'Webkernel - by Numerimondes',
            'description' => 'A production-ready Laravel foundation that transforms development workflow from day one',
            'logoLink' => 'packages/webkernel/src/Resources/repo-assets/credits/numerimondes.png',
        ];

        $fallbacks = [
            'brandName' => 'Numerimondes Platform',
            'logoLink' => 'packages/webkernel/src/Resources/repo-assets/credits/numerimondes.png',
        ];

        if (
            isset($GLOBALS['__corePlatformInfos']['infos']) &&
            is_array($GLOBALS['__corePlatformInfos']['infos']) &&
            array_key_exists($key, $GLOBALS['__corePlatformInfos']['infos'])
        ) {
            return $GLOBALS['__corePlatformInfos']['infos'][$key];
        }

        if (empty(glob(base_path('platform/*')))) {
            return $defaults[$key] ?? null;
        }

        return $fallbacks[$key] ?? null;
    }
}

/**
 * Set platform info with priority.
 *
 * @param array $infos
 * @param int $priority
 * @return void
 */
if (!function_exists('setCorePlatformInfos')) {
    function setCorePlatformInfos(array $infos, int $priority = 0): void
    {
        static $data = ['infos' => [], 'priority' => -INF];
        if ($priority > $data['priority']) {
            $data['infos'] = array_merge($data['infos'], $infos);
            $data['priority'] = $priority;
        }
        $GLOBALS['__corePlatformInfos'] = $data;
    }
}
