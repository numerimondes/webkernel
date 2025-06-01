<?php

namespace WebkernelTestpackage\Constants;

class Application
{
    public const NAME = 'WebkernelTestpackage';
    public const VERSION = '0.0.1';
    public const PACKAGE_NAME = 'webkernel-testpackage';
    public const DESCRIPTION = 'A test package to test if it works';
    public const CONFIG_PREFIX = 'webkernel_testpackage';
    public const TRANSLATION_NAMESPACE = 'webkernel-testpackage';

    public static function getVersion(): string
    {
        return self::VERSION;
    }

    public static function getName(): string
    {
        return self::NAME;
    }

    public static function getPackageName(): string
    {
        return self::PACKAGE_NAME;
    }

    public static function getDescription(): string
    {
        return self::DESCRIPTION;
    }

    public static function getConfigPrefix(): string
    {
        return self::CONFIG_PREFIX;
    }

    public static function getTranslationNamespace(): string
    {
        return self::TRANSLATION_NAMESPACE;
    }
}
