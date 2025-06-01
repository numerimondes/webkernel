<?php

use WebkernelTestpackage\Constants\Application;

if (!function_exists('webkernel_testpackage_version')) {
    /**
     * Get the version of WebkernelTestpackage package
     */
    function webkernel_testpackage_version(): string
    {
        return Application::getVersion();
    }
}

if (!function_exists('webkernel_testpackage_config')) {
    /**
     * Get package configuration value
     */
    function webkernel_testpackage_config(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return config(Application::CONFIG_PREFIX);
        }

        return config(Application::CONFIG_PREFIX . '.' . $key, $default);
    }
}

if (!function_exists('webkernel_testpackage_enabled')) {
    /**
     * Check if the package is enabled
     */
    function webkernel_testpackage_enabled(): bool
    {
        return webkernel_testpackage_config('enabled', true);
    }
}

if (!function_exists('webkernel_testpackage_route')) {
    /**
     * Generate route URL for the package
     */
    function webkernel_testpackage_route(string $name, array $parameters = []): string
    {
        return route(Application::CONFIG_PREFIX . '.' . $name, $parameters);
    }
}
