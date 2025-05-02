<?php

namespace Webkernel\Filament\Widgets;

use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class AccountWidget extends ImmediateLoadWidget
{
    // This is critical for Livewire to find the component
    protected static string $view = 'webkernel::widgets.account-widget';

    // Force immediate loading
    protected static bool $isLazy = false;

    // Don't wait for deferred loading
    public $deferLoading = false;

    // Set widget configuration
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

    // Optional method if you need to determine if this widget should be rendered
    public static function canView(): bool
    {
        return Auth::check();
    }

    // Disable real-time polling
    protected function getPollingInterval(): ?string
    {
        return null;
    }
}
