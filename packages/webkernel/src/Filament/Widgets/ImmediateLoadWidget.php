<?php

namespace Webkernel\Filament\Widgets;

use Filament\Widgets\Widget;

/**
 * Base widget class that ensures immediate loading
 */
abstract class ImmediateLoadWidget extends Widget
{
    /**
     * Disable lazy loading by default
     *
     * @var bool
     */
    protected static bool $isLazy = false;

    /**
     * Configure widget to load immediately
     *
     * @var bool
     */
    public static function isLazy(bool $condition = false): bool
    {
        static::$isLazy = $condition;

        return static::$isLazy;
    }

    /**
     * Skip poll for real-time updates by default
     *
     * @var bool|null
     */
    protected static ?bool $shouldPoll = false;
}
