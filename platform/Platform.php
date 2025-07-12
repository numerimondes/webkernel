<?php

namespace Numerimondes;

class Platform
{
    public const DEFAULT_BRAND_NAME = 'REAM';
    public const DEFAULT_VERSION = '0.1.0';
    public const DEFAULT_DESCRIPTION = 'Gestion des audits d\'énergies renouvelables';
    public const DEFAULT_LOGO_LINK = 'packages/webkernel/src/Resources/repo-assets/credits/ream.svg';

    public function initialize(): void
    {
        setCorePlatformInfos($this->getCorePlatformInfos(), 10);
    }

    public function getCorePlatformInfos(): array
    {
        return [
            'brandName' => static::getHelperValue('getBrandName', self::DEFAULT_BRAND_NAME),
            'version' => static::getHelperValue('getVersion', self::DEFAULT_VERSION),
            'description' => static::getHelperValue('getDescription', self::DEFAULT_DESCRIPTION),
            'logoLink' => static::getHelperValue('getLogoLink', self::DEFAULT_LOGO_LINK),
        ];
    }

    protected static function getHelperValue(string $functionName, $default)
    {
        if (function_exists($functionName)) {
            $value = $functionName();
            if ($value !== null) {
                return $value;
            }
        }
        return $default;
    }
}