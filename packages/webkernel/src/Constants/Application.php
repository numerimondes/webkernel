<?php
namespace Webkernel\Constants;

use Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication
{
    const WEBKERNEL_VERSION = '0.0.19';
    const STABLE_VERSION = '0.0.19';

    const WEBKERNEL_PACKAGES = [
        'webkernel' => [
            'path' => 'packages/webkernel',
            'minimum_stable_version_required' => self::WEBKERNEL_VERSION,
            'dependencies' => []
        ]
    ];


    const BUSINESS_APPLICATIONS = [];

    /**
     * Get the main package name
     */
    public static function getPackageName(): string
    {
        return 'webkernel';
    }

    /**
     * Get all webkernel packages configuration
     */
    public static function getWebkernelPackages(): array
    {
        return self::WEBKERNEL_PACKAGES;
    }

    /**
     * Get business applications configuration
     */
    public static function getBusinessApplications(): array
    {
        return self::BUSINESS_APPLICATIONS;
    }

    /**
     * Get current webkernel version
     */
    public static function getVersion(): string
    {
        return self::WEBKERNEL_VERSION;
    }

    /**
     * Get stable version
     */
    public static function getStableVersion(): string
    {
        return self::STABLE_VERSION;
    }

    /**
     * Check if a package exists in configuration
     */
    public static function hasPackage(string $packageName): bool
    {
        return isset(self::WEBKERNEL_PACKAGES[$packageName]);
    }

    /**
     * Get specific package configuration
     */
    public static function getPackage(string $packageName): ?array
    {
        return self::WEBKERNEL_PACKAGES[$packageName] ?? null;
    }

    /**
     * Get package version
     */
    public static function getPackageVersion(string $packageName): ?string
    {
        $package = self::getPackage($packageName);
        return $package['minimum_stable_version_required'] ?? null;
    }
}
