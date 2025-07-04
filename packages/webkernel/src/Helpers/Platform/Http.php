<?php
// packages/webkernel/src/Helpers/helpers_platformHttp.php

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;



if (!function_exists('platformAbsoluteUrl')) {
    /**
     * Return absolute URL from a given path and optional base URL.
     * Does NOT query DB. Pure string manipulation.
     *
     * @param string $path Relative or absolute URL/path
     * @param string|null $baseUrl Base URL (e.g. https://example.com). Defaults to current HTTP host.
     * @return string Absolute URL
     */
    function platformAbsoluteUrl(string $path, ?string $baseUrl = null): string
    {
        // If already absolute URL, return as-is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Get base URL if not provided
        if ($baseUrl === null) {
            if (function_exists('request') && app()->has('request')) {
                try {
                    $baseUrl = request()->getSchemeAndHttpHost();
                } catch (\Exception $e) {
                    $baseUrl = config('app.url', 'http://localhost');
                }
            } else {
                $baseUrl = config('app.url', 'http://localhost');
            }
        }

        // Ensure base URL doesn't end with slash
        $baseUrl = rtrim($baseUrl, '/');

        // Handle absolute paths (starting with /)
        if (str_starts_with($path, '/')) {
            return $baseUrl . $path;
        }

        // Handle relative paths
        return $baseUrl . '/' . ltrim($path, '/');
    }
}

if (!function_exists('platformAbsoluteIP4')) {
    /**
     * Return absolute IPv4 address from given input.
     * If input is already an IPv4, return it.
     * If input is IPv6, try to map it to IPv4.
     * Else returns empty string.
     *
     * @param string $ip
     * @return string IPv4 address or empty string
     */
    function platformAbsoluteIP4(string $ip): string
    {
        // Clean input
        $ip = trim($ip);

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $ip;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // Try IPv6 to IPv4 mapping ::ffff:192.168.1.1
            if (preg_match('/^::ffff:(\d+\.\d+\.\d+\.\d+)$/i', $ip, $matches)) {
                return $matches[1];
            }
        }

        return '';
    }
}

if (!function_exists('platformAbsoluteIP6')) {
    /**
     * Return absolute IPv6 address from given input.
     * If input is IPv6, return it.
     * If input is IPv4, convert to IPv6 mapped address.
     * Else returns empty string.
     *
     * @param string $ip
     * @return string IPv6 address or empty string
     */
    function platformAbsoluteIP6(string $ip): string
    {
        // Clean input
        $ip = trim($ip);

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return $ip;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return '::ffff:' . $ip;
        }

        return '';
    }
}

if (!function_exists('platformValidateUrl')) {
    /**
     * Validate if URL is accessible without making HTTP request
     * Just validates URL format and basic checks
     *
     * @param string $url
     * @return bool
     */
    function platformValidateUrl(string $url): bool
    {
        if (empty($url) || !is_string($url)) {
            return false;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Parse URL components
        $parsed = parse_url($url);
        if (!$parsed || !isset($parsed['scheme'], $parsed['host'])) {
            return false;
        }

        // Check allowed schemes
        $allowedSchemes = ['http', 'https', 'ftp', 'ftps'];
        if (!in_array(strtolower($parsed['scheme']), $allowedSchemes)) {
            return false;
        }

        return true;
    }
}
