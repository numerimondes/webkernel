<?php

namespace Webkernel\Filament\Widgets;

use Filament\Widgets\Widget;

class WebkernelInfoWidget extends Widget
{
    protected static string $view = 'webkernel::widgets.webkernel-info-widget';

    // Force immediate loading
    protected static bool $isLazy = false;

    // Don't wait for deferred loading
    public $deferLoading = false;

}
