<?php

namespace Webkernel\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class WebkernelAllPanelsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $logoUrl = platformAbsoluteUrlAnyPrivatetoPublic(corePlatformInfos('logoLink'));

        $panels = Filament::getPanels();

        foreach ($panels as $panel) {
            $panel->bootUsing(function ($panelInstance) use ($logoUrl) {
                $panelInstance->brandLogo($logoUrl);
            });
        }
    }
}
