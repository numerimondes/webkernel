<?php

namespace Webkernel\Filament\Pages;

use Illuminate\Contracts\View\View;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentIcon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;
use Webkernel\Filament\Widgets\AccountWidget;
use Webkernel\Filament\Widgets\WebkernelInfoWidget;

class TestPage extends Page
{
    protected static ?string $title = null;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'webkernel::filament.pages.test-page';

    public function getHeader(): ?View
    {
        return view('webkernel::filament.pages.test-page');
    }

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
