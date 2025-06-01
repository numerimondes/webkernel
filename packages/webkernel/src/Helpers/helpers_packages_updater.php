<?php

namespace Webkernel\Helpers;

use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| UPDATE PACKAGE AND SUBPACKAGES - helpers_packages_updater.php
|--------------------------------------------------------------------------
*/

if (!function_exists('extract_packages_from_content')) {
    function extract_packages_from_content(string $content): array
    {
        $packages = [];

        if (preg_match("/const\s+WEBKERNEL_PACKAGES\s*=\s*\[(.*?)\];/s", $content, $matches)) {
            $packageDefs = $matches[1];
            $pattern = "/'([^']+)'\s*=>\s*\[\s*'path'\s*=>\s*'([^']+)',\s*'minimum_stable_version_required'\s*=>\s*'([^']+)'(?:,\s*'dependencies'\s*=>\s*\[[^\]]*\])?\s*\]/";

            if (preg_match_all($pattern, $packageDefs, $packageMatches, PREG_SET_ORDER)) {
                foreach ($packageMatches as $match) {
                    $packages[$match[1]] = [
                        'path' => $match[2],
                        'version' => $match[3]
                    ];
                }
            }
        }

        return $packages;
    }
}

if (!function_exists('extract_versions_from_content')) {
    function extract_versions_from_content(string $content): array
    {
        $versions = [];

        $patterns = [
            'WEBKERNEL_VERSION' => "/const\s+WEBKERNEL_VERSION\s*=\s*['\"]([^'\"]+)['\"];/",
            'STABLE_VERSION' => "/const\s+STABLE_VERSION\s*=\s*['\"]([^'\"]+)['\"];/",
            'WEBSITE_BUILDER_VERSION' => "/const\s+WEBSITE_BUILDER_VERSION\s*=\s*['\"]([^'\"]+)['\"];/",
            'VIDEO_TOOLS_VERSION' => "/const\s+VIDEO_TOOLS_VERSION\s*=\s*['\"]([^'\"]+)['\"];/"
        ];

        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $versions[strtolower(str_replace('_VERSION', '', $key))] = $matches[1];
            }
        }

        return $versions;
    }
}

if (!function_exists('get_local_version_from_file')) {
    function get_local_version_from_file(string $filePath, string $packageName = 'webkernel'): ?string
    {
        if (!File::exists($filePath)) {
            return null;
        }

        $content = File::get($filePath);
        $versions = extract_versions_from_content($content);

        return $versions[$packageName] ?? $versions['webkernel'] ?? null;
    }
}

if (!function_exists('compare_versions')) {
    function compare_versions(string $version1, string $version2): int
    {
        return version_compare(ltrim($version1, 'v'), ltrim($version2, 'v'));
    }
}

if (!function_exists('is_update_needed')) {
    function is_update_needed(string $localVersion, string $remoteVersion): bool
    {
        return compare_versions($remoteVersion, $localVersion) === 1;
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }
}

if (!function_exists('get_directory_size')) {
    function get_directory_size(string $directory): int
    {
        if (!File::exists($directory)) {
            return 0;
        }

        $size = 0;

        foreach (File::allFiles($directory) as $file) {
            $size += $file->getSize();
        }

        return $size;
    }
}

if (!function_exists('validate_package_config')) {
    function validate_package_config(array $package): bool
    {
        $required = ['path', 'minimum_stable_version_required'];

        foreach ($required as $key) {
            if (empty($package[$key])) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('get_php_path')) {
    function get_php_path(): string
    {
        $paths = [
            PHP_BINARY,
            '/usr/bin/php',
            '/usr/local/bin/php',
            'php'
        ];

        foreach ($paths as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }

        return 'php';
    }
}
