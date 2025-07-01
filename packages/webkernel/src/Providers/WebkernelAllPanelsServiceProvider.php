<?php

namespace Webkernel\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use Filament\Enums\ThemeMode;

class WebkernelAllPanelsServiceProvider extends ServiceProvider
{
    public function boot()
{
    // Toujours forcer l'URL sécurisée ici
    $logoUrl = platformAbsoluteUrlAnyPrivatetoPublic(corePlatformInfos('logoLink'));
    $panels = Filament::getPanels();

    foreach ($panels as $panel) {
        $panel->bootUsing(function ($panelInstance) use ($logoUrl) {
            $panelInstance->defaultThemeMode(ThemeMode::Light)->brandLogo($logoUrl)->brandLogoHeight('2.5rem');
        });
    }
}

}
