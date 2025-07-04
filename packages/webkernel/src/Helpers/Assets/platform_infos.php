<?php

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
