<?php

namespace Webkernel\Aptitudes\Base\Providers;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Enums\GlobalSearchPosition;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Webkernel\Aptitudes\Users\Filament\Auth\RegisterOwner;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Support\Enums\Platform;
use Filament\Pages\Dashboard;

class SystemPanelProvider extends PanelProvider
{
  public function boot(): void {}

  public function panel(Panel $panel): Panel
  {
    return withRemoteComponents(
      $panel
        ->id('system')
        ->path('system')
        ->navigationItems([...ComingNavigation::navigationItems()])
        ->navigationGroups(['Administration', 'Platform Tools', 'System Management'])
        ->brandLogo(
          'https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/brands/numerimondes/identity/logos/v2/logo_entier_v3.png',
        )
        ->darkModeBrandLogo(
          'https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/brands/numerimondes/identity/logos/v2/numerimondes-white.png',
        )
        ->brandLogoHeight('2.5rem')
        ->brandName('Numerimondes')
        ->sidebarCollapsibleOnDesktop()
        ->maxContentWidth(Width::ScreenTwoExtraLarge)
        ->strictAuthorization()
        ->topbar(false)
        ->globalSearch(position: GlobalSearchPosition::Sidebar)
        ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
        ->globalSearchFieldSuffix(
          fn(): ?string => match (Platform::detect()) {
            Platform::Windows, Platform::Linux => 'CTRL+K',
            Platform::Mac => '⌘K',
            default => null,
          },
        )
        ->spa()
        ->login()
        ->passwordReset()
        ->emailVerification()
        ->emailChangeVerification()
        ->loginRouteSlug('login')
        ->registrationRouteSlug('register')
        ->databaseNotifications()
        ->passwordResetRoutePrefix('password-reset')
        ->passwordResetRequestRouteSlug('request')
        ->passwordResetRouteSlug('reset')
        ->emailVerificationRoutePrefix('email-verification')
        ->emailVerificationPromptRouteSlug('prompt')
        ->emailVerificationRouteSlug('verify')
        ->emailChangeVerificationRoutePrefix('email-change-verification')
        ->emailChangeVerificationRouteSlug('verify')
        ->registration(RegisterOwner::class)
        ->profile(\Webkernel\Aptitudes\Users\Filament\Auth\EditProfile::class, false)
        ->colors([
          'primary' => Color::Blue,
        ])
        ->pages([Dashboard::class])
        ->widgets([AccountWidget::class, FilamentInfoWidget::class])
        ->resources([])
        ->plugins([])
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
          \Webkernel\Aptitudes\I18n\Http\Middleware\LanguageResolutionMiddleware::class,
        ])
        ->authMiddleware([Authenticate::class]),
    );
  }
}
