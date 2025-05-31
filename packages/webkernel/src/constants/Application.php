<?php

namespace Webkernel\constants;

use Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication
{
    /**
     * The Webkernel version.
     *
     * @var string
     */
    const WEBKERNEL_VERSION = '0.0.12';

    /**
     * The Webkernel stable version.
     *
     * @var string
     */
    const STABLE_VERSION = '0.0.12';

    /**
     * Additional packages to update when updating webkernel
     * Comma-separated list of package names
     * Example: const ADDITIONAL_PACKAGES = 'webkernel-blog,webkernel-commerce,webkernel-crm';
     * @var string
     */
    const ADDITIONAL_PACKAGES = '';

    /**
     * Get the current package name
     *
     * @return string
     */
    public static function getPackageName()
    {
        return 'webkernel';
    }
}

