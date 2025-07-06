<?php

namespace Webkernel\PlatformConfig\Platforms\Modules\ReamMar;

class Module
{
    public static function defaultVersion(): string
    {
        return '1.0.0';
    }

    public static function platformKey(): string
    {
        return 'reammar';
    }

    public static function resolvePreferences(array $subModules): array
    {
        // Logique de résolution entre sous-modules si besoin
        return $subModules;
    }
}
