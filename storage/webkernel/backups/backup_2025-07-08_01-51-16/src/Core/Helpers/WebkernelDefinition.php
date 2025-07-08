<?php
// packages/webkernel/src/Core/Helpers/helpers.php

if (!function_exists('platform_constant')) {
    /**
     * Get a platform constant value with optional default
     */
    function platform_constant(string $name, mixed $default = null): mixed
    {
        return defined($name) ? constant($name) : $default;
    }
}

if (!function_exists('platform_version')) {
    /**
     * Get the version of a platform module
     */
    function platform_version(string $module, string $subModule = 'Core'): ?string
    {
        $constantName = strtoupper("PLATFORM_{$module}_{$subModule}_VERSION");
        return defined($constantName) ? constant($constantName) : null;
    }
}

if (!function_exists('platform_enabled')) {
    /**
     * Check if a platform module is enabled
     */
    function platform_enabled(string $module, string $subModule = 'Core'): bool
    {
        $constantName = strtoupper("PLATFORM_{$module}_{$subModule}_ENABLED");
        return defined($constantName) ? (bool)constant($constantName) : false;
    }
}

if (!function_exists('platform_config')) {
    /**
     * Get platform configuration value
     */
    function platform_config(string $key, mixed $default = null): mixed
    {
        $constantName = strtoupper("PLATFORM_CONFIG_{$key}");
        return defined($constantName) ? constant($constantName) : $default;
    }
}

if (!function_exists('webkernel_version')) {
    /**
     * Get Webkernel version
     */
    function webkernel_version(): string
    {
        return defined('WEBKERNEL_VERSION') ? WEBKERNEL_VERSION : '1.0.0';
    }
}

if (!function_exists('is_debug_mode')) {
    /**
     * Check if debug mode is enabled
     */
    function is_debug_mode(): bool
    {
        return defined('DEBUG_MODE') ? (bool)DEBUG_MODE : false;
    }
}

if (!function_exists('get_tenant_constant')) {
    /**
     * Get tenant-specific constant value
     */
    function get_tenant_constant(string $name, mixed $default = null): mixed
    {
        $tenantConstant = "TENANT_{$name}";
        if (defined($tenantConstant)) {
            return constant($tenantConstant);
        }
        return platform_constant($name, $default);
    }
}

if (!function_exists('feature_enabled')) {
    /**
     * Check if a feature flag is enabled
     */
    function feature_enabled(string $feature): bool
    {
        $constantName = strtoupper("FEATURE_{$feature}_ENABLED");
        return defined($constantName) ? (bool)constant($constantName) : false;
    }
}

if (!function_exists('get_api_endpoint')) {
    /**
     * Get API endpoint for a module
     */
    function get_api_endpoint(string $module, string $subModule = 'Api'): ?string
    {
        $constantName = strtoupper("API_{$module}_{$subModule}_ENDPOINT");
        return defined($constantName) ? constant($constantName) : null;
    }
}

if (!function_exists('get_rate_limit')) {
    /**
     * Get rate limit for a module
     */
    function get_rate_limit(string $module, string $subModule = 'Api'): int
    {
        $constantName = strtoupper("API_{$module}_{$subModule}_RATE_LIMIT");
        return defined($constantName) ? (int)constant($constantName) : 100;
    }
}