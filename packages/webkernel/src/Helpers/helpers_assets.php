<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

/**
 * Génère une URL sécurisée vers un asset privé avec token.
 * Utilise le cache pour éviter de régénérer un token à chaque appel.
 *
 * @param string $path Chemin relatif vers le fichier (ex: 'packages/.../logo.svg')
 * @param int $expirationMinutes Durée de validité du token
 * @param bool $forceRefresh Forcer le rafraîchissement du token et cache
 * @return string URL publique sécurisée vers le fichier
 */
if (!function_exists('platformAbsoluteUrlAnyPrivatetoPublic')) {
    function platformAbsoluteUrlAnyPrivatetoPublic(string $path, int $expirationMinutes = 30, bool $forceRefresh = false): string
    {
        $cleanPath = ltrim(str_replace('\\', '/', $path), '/');
        $cacheKey = "file_token_by_path:" . md5($cleanPath);

        $cacheIsReady = true;
        try {
            $cacheIsReady = Schema::hasTable('cache');
        } catch (\Exception $e) {
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
 * Supprime le cache lié à un asset (flush) pour forcer la régénération du token.
 *
 * @param string $path Chemin relatif vers le fichier
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
 * Retourne une info de configuration plateforme (brandName, logoLink, etc.)
 * Si setCorePlatformInfos a été appelé, retourne l’info prioritaire,
 * sinon retourne les defaults ou fallback.
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
            'logoLink' => 'packages/webkernel/src/resources/repo-assets/credits/numerimondes.png',
        ];

        $fallbacks = [
            'brandName' => 'Numerimondes Platform',
            'logoLink' => 'packages/webkernel/src/resources/repo-assets/credits/numerimondes.png',
        ];

        if (!empty($GLOBALS['__corePlatformInfos']['infos'][$key])) {
            return $GLOBALS['__corePlatformInfos']['infos'][$key];
        }

        if (empty(glob(base_path('platform/*')))) {
            return $defaults[$key] ?? null;
        }

        return $fallbacks[$key] ?? null;
    }
}

/**
 * Définit les infos plateforme avec priorité.
 * Si 'logoLink' est défini, le transforme en URL sécurisée.
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
            if (isset($infos['logoLink'])) {
                // we do nothing
            }
            $data['infos'] = array_merge($data['infos'], $infos);
            $data['priority'] = $priority;
        }
        $GLOBALS['__corePlatformInfos'] = $data;
    }
}
