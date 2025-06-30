<?php

namespace Webkernel\Filament\Widgets;

use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class AccountWidget extends ImmediateLoadWidget
{
    // Doit être non statique comme dans la classe parente
    protected string $view = 'webkernel::widgets.account-widget';

    // Ne pas différer le chargement
    public $deferLoading = false;

    // Configuration de la largeur du widget
    protected int | string | array $columnSpan = 1;

    public function getUserName(): string
    {
        return Auth::user()->name ?? 'Guest';
    }

    public function getUserEmail(): string
    {
        return Auth::user()->email ?? '';
    }

    public function getAvatarUrl(): ?string
    {
        return Auth::user()->avatar_url ?? null;
    }

    // Méthode statique obligatoire (héritée statique)
    public static function canView(): bool
    {
        return Auth::check();
    }

    // Désactive le polling temps réel
    protected function getPollingInterval(): ?string
    {
        return null;
    }
}
