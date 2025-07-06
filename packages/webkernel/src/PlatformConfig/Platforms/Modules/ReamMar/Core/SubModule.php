<?php

namespace Webkernel\PlatformConfig\Platforms\Modules\ReamMar\Core;

class SubModule
{
    public static function getIdentifier(): string
    {
        return 'reammar_core';
    }

    public static function defaultVersion(): string
    {
        return '1.0.0';
    }
}
