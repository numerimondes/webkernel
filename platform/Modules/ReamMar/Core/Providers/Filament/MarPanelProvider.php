<?php
namespace Numerimondes\Modules\ReamMar\Core\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
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
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\ClientResource;
use Filament\Facades\Filament;

class MarPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('mar')
            ->path('mar')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: base_path('platform/Modules/ReamMar/Core/Filament/Resources'), for: 'Numerimondes\Filament\Mar\Resources')
            ->discoverPages(in: base_path('platform/Modules/ReamMar/Core/Filament/Pages'), for: 'Numerimondes\Filament\Mar\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->resources([ClientResource::class,])
            ->discoverWidgets(in: base_path('platform/Modules/ReamMar/Core/Filament/Widgets'), for: 'Numerimondes\Filament\Mar\Widgets')
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