<?php

namespace Webkernel\Core\Filament\Pages;

use Illuminate\Contracts\View\View;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentIcon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;
use Webkernel\Core\Filament\Widgets\AccountWidget;
use Webkernel\Core\Filament\Widgets\WebkernelInfoWidget;
use BackedEnum;

class TestPage extends Page
{

protected static ?string $navigationParentItem = 'Products';

public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
{

        return 'heroicon-o-document-text';
    }

    protected string $view = 'webkernel::filament.pages.test-page';

    /**
     * Méthode statique pour définir dynamiquement le titre
     */
    public static function setTitle(): void
    {
       static::$title = __('available_languages');
    }

    /**
     * Mount : Appelée à l'initialisation de la page
     */
    public function mount(): void
    {
        // Initialise dynamiquement le titre
        self::setTitle();
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    public function getColumns(): int | string | array
    {
        return 2;
    }
}
