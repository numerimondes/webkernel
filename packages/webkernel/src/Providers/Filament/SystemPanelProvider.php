<?php

namespace Webkernel\Providers\Filament;

//Filament
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\File;
use Webkernel\Http\Middleware\SetLang;
//Illuminate
use Webkernel\Filament\Pages\Dashboard;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Webkernel\Filament\Pages\Auth\EditProfile;
use Webkernel\Filament\Pages\PlatformSettings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
//Webkernel
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;


class SystemPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // $svgPath = __DIR__.'/../../resources/branding/webkernel.svg';
        // $svgContent = File::get($svgPath) . "<br>";
        // $svgContent = preg_replace(
        //     '/<svg([^>]*)>/',
        //     '<svg$1 style="height: 35px;  width: auto;">',
        //     $svgContent
        // );
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

            //->brandLogo(new HtmlString($svgContent))
            //->topNavigation()

            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->profile(EditProfile::class, false)
            ->spa()
            //->unsavedChangesAlerts()
            ->databaseNotifications()
            ->databaseTransactions(false)
            ->discoverClusters(in: base_path('packages/webkernel/src/Filament/Clusters'), for: 'Webkernel\\Filament\\Clusters')
            ->colors([
                'primary' => Color::hex('#3276c3')
            ])

            ->discoverResources(in: base_path('packages/webkernel/src/Filament/Resources'), for: 'Webkernel\\Filament\\Resources')
            ->discoverPages(in: base_path('packages/webkernel/src/Filament/Pages'), for: 'Webkernel\\Filament\\Pages')
            ->pages([
            ])

            ->resources([])
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
