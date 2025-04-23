<?php

namespace Webkernel\Filament\Pages;

use Illuminate\Contracts\View\View;
use Filament\Pages\Page;

class TestPage extends Page
{
    protected static ?string $title = null;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'webkernel::filament.dev.pages.test-page';
    public function getHeader(): ?View
    {
        return view('webkernel::filament.dev.pages.test-page');
    }

    /**
     * Méthode statique pour définir dynamiquement le titre
     */
    public static function setTitle(): void
    {
        // Définir le titre de manière statique à partir d'une fonction de traduction personnalisée
       static::$title = __('available_languages');
    }

    /**
     * Mount : Appelée à l'initialisation de la page
     */
    public function mount(): void
    {
        // Initialise dynamiquement le titre
        self::setTitle();

        // Exemple de debug (commenté)
        // $languageId = auth()->check()
        //     ? \App\Models\Language::where('code', auth()->user()->user_lang)->value('id')
        //     : null;
        // dd($languageId, 'No user or not authenticated');

        // Si tu veux afficher un composant ou inclure une vue, fais-le dans la vue Blade `filament.pages.test-page`
    }
}
