<?php

namespace Webkernel\Core\Providers;

//Filament
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
//Illuminate
use Webkernel\Core\Filament\Pages\Dashboard;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Webkernel\Core\Filament\Pages\Auth\EditProfile;
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

use WEBKERNEL__LANGUAGE__MIDDLEWARE as SetLang;


use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\File;
use Filament\Resources\Resource;
use Filament\Pages;
use Filament\Widgets;
use Filament\Facades\Filament;


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
            ->discoverClusters(in: base_path('packages/webkernel/src/Core/Filament/Clusters'), for: 'Webkernel\\Core\\Filament\\Clusters')
            ->colors([
                'primary' => Color::hex('#3276c3')
            ])

            ->discoverResources( base_path('packages/webkernel/src/Core/Filament/Resources'), for: 'Webkernel\\Core\\Filament\\Resources')
            ->discoverPages( base_path('packages/webkernel/src/Core/Filament/Pages'), for: 'Webkernel\\Core\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])

            ->resources([])
            ->discoverWidgets(in: base_path('packages/webkernel/src/Core/Filament/Widgets'), for: 'Webkernel\\Core\\Filament\\Widgets')
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
        public static function webkernelPanelInfo(): array
        {
            $panel = Filament::getPanel('system');
            
            return [
                'id'           => $panel->getId(),
                'path'         => $panel->getPath(),
                'icon'         => 'heroicon-o-rectangle-stack',
                'description'  => 'Module System pour la gestion administrative',
                'url'          => $panel->getUrl(),
                'fontfamily'   => $panel->getFontFamily(),
                'fontprovider' => $panel->getFontProvider(),
                'restricted'   => true,
            ];
        }
    }