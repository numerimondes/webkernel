<?php

namespace Webkernel\Aptitudes\Base\Filament\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use Filament\View\PanelsIconAlias;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class WelcomeSystemDashboard extends Page
{

    //protected static string $layout = 'filament-panels::components.layout.base';
    protected string $view = 'base::filament.pages.welcome-system-dashboard';
    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve(PanelsIconAlias::PAGES_DASHBOARD_NAVIGATION_ITEM)
            ?? (Filament::hasTopNavigation() ? Heroicon::Home : Heroicon::OutlinedHome);
    }

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return view('base::blank');
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ??
            static::$title ??
            __('filament-panels::pages/dashboard.title');
    }
}
