<?php

namespace Platform\EnjoyTheWorld\Providers;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class EnjoytheworldAdminPanelProvider extends PanelProvider
{
  public function panel(Panel $panel): Panel
  {
    return withRemoteComponents(
      $panel
        ->id('enjoytheworld-admin')
        ->path('system/enjoytheworld')
        ->colors([
          'primary' => Color::Amber,
        ])

        ->discoverResources(
          in: base_path('platform/EnjoyTheWorld/Filament/Resources'),
          for: 'Platform\EnjoyTheWorld\Filament\Resources',
        )
        ->discoverPages(
          in: base_path('platform/EnjoyTheWorld/Filament/Pages'),
          for: 'Platform\EnjoyTheWorld\Filament\Pages',
        )
        ->discoverWidgets(
          in: base_path('platform/EnjoyTheWorld/Filament/Widgets'),
          for: 'Platform\EnjoyTheWorld\Filament\Widgets',
        )
        ->pages([Dashboard::class])
        ->widgets([AccountWidget::class, FilamentInfoWidget::class])
        ->middleware([
          EncryptCookies::class,
          AddQueuedCookiesToResponse::class,
          StartSession::class,
          AuthenticateSession::class,
          ShareErrorsFromSession::class,
          VerifyCsrfToken::class,
          SubstituteBindings::class,
          DisableBladeIconComponents::class,
          DispatchServingFilamentEvent::class,
        ])
        ->authMiddleware([Authenticate::class]),
    );
  }
}
