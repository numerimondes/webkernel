<?php

namespace Webkernel\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Panel;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentIcon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;
use Webkernel\Filament\Widgets\AccountWidget;
use Webkernel\Filament\Widgets\WebkernelInfoWidget;
use BackedEnum;

class Dashboard extends BaseDashboard
{
    // Vue personnalisée
    protected string $view = 'webkernel::filament.pages.dashboard';

public static function getNavigationIcon(): BackedEnum | string | null
    {
        return 'heroicon-o-home';
    }

    // Label du menu - CORRIGÉ: doit être ?string selon l'erreur
    protected static ?string $navigationLabel = null;

    // Chemin de route
    protected static string $routePath = '/';

    // Ordre dans le menu
    protected static ?int $navigationSort = -2;

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel
            ?? static::$title
            ?? __('filament-panels::pages/dashboard.title');
    }

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    /**
     * Liste des widgets affichés.
     *
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            WebkernelInfoWidget::class,
        ];
    }

    /**
     * Widgets visibles après filtrage.
     *
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getVisibleWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getWidgets());
    }

    /**
     * Nombre de colonnes sur le dashboard.
     */
    public function getColumns(): int
    {
        return 2;
    }

    /**
     * Titre affiché dans la page.
     */
    public function getTitle(): string | Htmlable
    {
        return static::$title ?? __('filament-panels::pages/dashboard.title');
    }
}
