<?php

namespace Webkernel\Core\Providers;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Webkernel\Core\Filament\Pages\Dashboard;
use Webkernel\Core\Filament\Pages\Auth\EditProfile;
use Webkernel\Core\Filament\Pages\ErrorPage;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

use WEBKERNEL__LANGUAGE__MIDDLEWARE as SetLang;

class SystemPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('system')
            ->path('system')
            ->login()
            ->registration()
            ->brandLogo(platformAbsoluteUrlAnyPrivatetoPublic(getCurrentApplication('logo')))
            ->brandLogoHeight('2.5rem')
            ->databaseNotifications()

            ->profile(EditProfile::class, false)
            
            // Configuration spécifique au panel system
            ->colors([
                'primary' => Color::hex('#3276c3')
            ])
            
            // Pages et ressources spécifiques
            ->pages([
                Dashboard::class,
            ])
            ->resources([])
            
            // Découverte automatique
            ->discoverClusters(in: base_path('packages/webkernel/src/Core/Filament/Clusters'), for: 'Webkernel\\Core\\Filament\\Clusters')
            ->discoverResources(base_path('packages/webkernel/src/Core/Filament/Resources'), for: 'Webkernel\\Core\\Filament\\Resources')
            ->discoverPages(base_path('packages/webkernel/src/Core/Filament/Pages'), for: 'Webkernel\\Core\\Filament\\Pages')
            ->discoverWidgets(in: base_path('packages/webkernel/src/Core/Filament/Widgets'), for: 'Webkernel\\Core\\Filament\\Widgets')
            
            // Widgets
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            
            // Middleware de base (CheckUserAccess sera ajouté automatiquement par PanelsServiceProvider)
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
                SetLang::class,
                \Webkernel\Core\Http\Middleware\CheckUserAccess::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public static function webkernelPanelInfo(): array
    {
        $panel = \Filament\Facades\Filament::getPanel('system');
        
        return [
            'id'           => $panel->getId(),
            'path'         => $panel->getPath(),
            'icon'         => 'heroicon-o-cog',
            'description'  => 'Module System pour la gestion administrative',
            'url'          => $panel->getUrl(),
            'fontfamily'   => $panel->getFontFamily(),
            'fontprovider' => $panel->getFontProvider(),
            'restricted'   => true,
        ];
    }
}