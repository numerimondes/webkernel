<?php
namespace Numerimondes\Modules\ReamMar\Core\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Webkernel\Core\Filament\Widgets\WebkernelInfoWidget;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class MarPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->login()
            ->registration()

            ->brandLogo(platformAbsoluteUrlAnyPrivatetoPublic(getCurrentApplication('logo')))
            ->brandLogoHeight('2.5rem')
            ->spa()
            ->id('mar')
            ->path('mar')
            
            // Configuration spécifique au panel mar
            ->colors([
                'primary' => Color::Amber,
            ])
            ->databaseNotifications()

            // Pages et ressources spécifiques
            ->pages([
                Dashboard::class,
            ])
            ->resources([
                
            ])
            
            // Découverte automatique
            ->discoverResources(in: base_path('platform/Modules/ReamMar/Core/Filament/Resources'), for: 'Numerimondes\Modules\ReamMar\Core\Filament\Resources')
            ->discoverPages(in: base_path('platform/Modules/ReamMar/Core/Filament/Pages'), for: 'Numerimondes\Modules\ReamMar\Core\Filament\Pages')
            ->discoverWidgets(in: base_path('platform/Modules/ReamMar/Core/Filament/Widgets'), for: 'Numerimondes\Modules\ReamMar\Core\Filament\Widgets')

            // Widgets
            ->widgets([
                    AccountWidget::class,
                    WebkernelInfoWidget::class,
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
                \Webkernel\Core\Http\Middleware\CheckUserAccess::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public static function webkernelPanelInfo(): array
    {
        $panel = Filament::getPanel('mar');
        
        return [
            'id'           => $panel->getId(),
            'path'         => $panel->getPath(),
            'icon'         => 'heroicon-o-rectangle-stack',
            'description'  => lang('Module MAR pour la gestion des clients'),
            'url'          => $panel->getUrl(), 
            'restricted'   => true, 
            'fontfamily'   => $panel->getFontFamily(),
            'fontprovider' => $panel->getFontProvider(),
        ];
    }
}