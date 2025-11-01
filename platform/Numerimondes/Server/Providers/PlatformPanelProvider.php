<?php
declare(strict_types=1);

namespace Platform\Numerimondes\Server\Providers;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Http\Middleware\Authenticate;
use Platform\Numerimondes\Server\Models\Organization;
use Platform\Numerimondes\Server\Filament\Resources\Software\SoftwareResource;
use Platform\Numerimondes\Server\Filament\Pages\Tenancy\RegisterOrganization;
use Platform\Numerimondes\Server\Filament\Pages\Tenancy\EditOrganizationProfile;
use Webkernel\Aptitudes\Multitenancy\TenantResolver;
use Platform\MasterConnector\Filament\Pages\Dashboard as MasterConnectorDashboard;

class PlatformPanelProvider extends PanelProvider
{
  /**
   * Configure panel
   *
   * @param Panel $panel
   * @return Panel
   */
  public function panel(Panel $panel): Panel
  {
    return withRemoteComponents(
      $panel
        ->id('workspace')
        ->path('workspace')
        ->tenant(Organization::class, slugAttribute: 'slug')
        ->tenantRegistration(RegisterOrganization::class)
        ->tenantProfile(EditOrganizationProfile::class)
        ->login()
        ->registration()
        ->colors(['primary' => Color::Blue])
        //->resources([SoftwareResource::class])
        ->pages([Dashboard::class])
        ->widgets([AccountWidget::class])
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
