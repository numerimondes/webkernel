<?php

namespace Webkernel\Providers\Filament;

//Filament
use Webkernel\Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
//Illuminate
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
//Webkernel
use Filament\Resources\Resource;
use Webkernel\Http\Middleware\SetLang;
use Webkernel\Models\CorePlatformSettings;
use Webkernel\Filament\Pages\Auth\EditProfile;


class SystemPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
       // $svgPath = __DIR__.'/../../resources/branding/webkernel.svg';
//
       // $svgContent = File::get($svgPath) . "<br>";
//
       // $svgContent = preg_replace(
       //     '/<svg([^>]*)>/',
       //     '<svg$1 style="height: 35px;  width: auto;">',
       //     $svgContent
       // );
//
       // $svgContent .= '<br>';

        return $panel
            ->default()
            ->id('system')
            ->path('system')
            ->login()
            ->registration()
            /**
             * End of custom Fortify parameters
             */
         //   ->brandLogo(new HtmlString($svgContent))
            //->topNavigation()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->profile(EditProfile::class, false)
            ->spa()
            //->unsavedChangesAlerts()
            ->databaseNotifications()
            ->databaseTransactions(false)
            ->discoverClusters(in: base_path('packages/webkernel/src/Filament/Clusters'), for: 'Webkernel\\Filament\\Clusters')
            ->colors([
                'primary' => Color::hex('#3276c3'),
            ])
            /**
             * End of custom Fortify parameters
             */
            // Découvrir automatiquement les ressources à partir du dossier spécifié
            // Découverte pour Webkernel
            ->discoverResources(in: base_path('packages/webkernel/src/Filament/Resources'), for: 'Webkernel\\Filament\\Resources')
            ->discoverPages(in: base_path('packages/webkernel/src/Filament/Pages'), for: 'Webkernel\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            //->resources([
            // \Webkernel\Filament\Resources\UserResource::class,
            // \Webkernel\Filament\Resources\LanguageResource::class,
            //])
            // Découvrir automatiquement les widgets à partir du dossier spécifié
            ->discoverWidgets(in: app_path('packages/webkernel/src/Filament/Widgets'), for: 'Webkernel\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
