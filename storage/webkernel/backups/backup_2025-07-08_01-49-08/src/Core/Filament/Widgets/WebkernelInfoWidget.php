<?php

namespace Webkernel\Core\Filament\Widgets;

use Filament\Widgets\Widget;

class WebkernelInfoWidget extends Widget
{
    protected string $view = 'webkernel::widgets.webkernel-info-widget';

    // Force immediate loading
    protected static bool $isLazy = false;

    // Don't wait for deferred loading
    public $deferLoading = false;

}
