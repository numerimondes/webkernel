<?php
/**
 * PanelsServiceProvider - Centralized Panel Configuration
 * 
 * This service provider implements a pattern to minimize code duplication across Filament panels
 * by providing a BasePanelProvider that extends Filament's PanelProvider with common configurations.
 * 
 * The BasePanelProvider automatically applies:
 * - Common middleware (including CheckUserAccess for access control)
 * - Default branding (colors, favicon, logo height)
 * - Standard pages (Dashboard)
 * - Default widgets (AccountWidget, FilamentInfoWidget)
 * - Common features (SPA mode, database notifications, global search)
 * 
 * Individual panel providers can extend BasePanelProvider and only specify what they need to override,
 * reducing boilerplate code and ensuring consistency across all panels.
 */

namespace Webkernel\ServiceProviders;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Widgets;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Webkernel\Core\Http\Middleware\CheckUserAccess;

abstract class BasePanelProvider extends PanelProvider
{
    abstract public function panel(\Filament\Panel $panel): \Filament\Panel;

    public function register(): void
    {
        Filament::registerPanel(
            $this->panel(Panel::make())
                ->brandLogoHeight('3rem')
                ->favicon(asset('favicon-32x32.png'))
                ->colors([
                    'primary' => Color::Blue,
                    'gray' => Color::Slate,
                ])
                ->spa()
                ->databaseNotifications()
                ->databaseTransactions(false)
                ->middleware([
                    // Liste des middlewares HTTP
                    EncryptCookies::class,
                    AddQueuedCookiesToResponse::class,
                    StartSession::class,
                    AuthenticateSession::class,
                    ShareErrorsFromSession::class,
                    VerifyCsrfToken::class,
                    SubstituteBindings::class,
                    DisableBladeIconComponents::class,
                    DispatchServingFilamentEvent::class,
                    CheckUserAccess::class,
                ])
                ->authMiddleware([
                    Authenticate::class,
                ])
                ->pages([
                    Pages\Dashboard::class,
                ])
                ->widgets([
                    Widgets\AccountWidget::class,
                    Widgets\FilamentInfoWidget::class,
                ])
                ->globalSearchKeyBindings([
                    'ctrl+k'
                ])
        );
    }
}

class PanelsServiceProvider extends ServiceProvider
{
   public function register()
   {
       $this->registerPlatformModulesComponents();
   }

   public function boot()
   {
       $this->configureAllPanels();
   }

   /**
    * Configure tous les panels avec des éléments communs
    */
   protected function configureAllPanels(): void
   {
       $panels = Filament::getPanels();
       
       foreach ($panels as $panel) {
           $this->applyCommonConfiguration($panel);
       }
   }

   /**
    * Applique la configuration commune à un panel
    */
   protected function applyCommonConfiguration($panel): void
   {
       $panelId = $panel->getId();
       
       // Ajouter le middleware CheckUserAccess directement au panel (sauf admin pour éviter les boucles)
       if ($panelId !== 'admin') {
           $currentMiddleware = $panel->getMiddleware();
           if (!in_array(CheckUserAccess::class, $currentMiddleware)) {
               $currentMiddleware[] = CheckUserAccess::class;
               $panel->middleware($currentMiddleware);
           }
       }

       // Configuration via bootUsing pour les autres éléments
       $panel->bootUsing(function ($panelInstance) {
           $panelInstance
               // SPA / Notifications
               ->spa()
               ->databaseNotifications()
               ->databaseTransactions(false)

               // Apparence / UI
               ->colors([
                   // 'primary' => Color::hex('#...')
               ])
               // ->brandLogo(new HtmlString('')) // Optionnel

               // Pages & Resources
               ->pages([
                   // Exemple: Dashboard::class
               ])
               ->resources([
                   // Exemple: UserResource::class
               ])

               // Découverte automatique
               ->discoverClusters(
                   in: base_path(''), // Dossier à scanner
                   for: '' // Namespace
               )
               ->discoverResources(
                   in: base_path(''), 
                   for: ''
               )
               ->discoverPages(
                   in: base_path(''), 
                   for: ''
               )
               ->discoverWidgets(
                   in: base_path(''), 
                   for: ''
               )

               // Widgets manuels
               ->widgets([
                   // Exemple: AccountWidget::class
               ])

               // Middleware
               ->middleware([
                   // Liste des middlewares HTTP
               ])
               ->authMiddleware([
                   // Exemple: Authenticate::class
               ])

               // Autres
               ->globalSearchKeyBindings([
                   // 'ctrl+k'
               ]);
       });
   }

   /**
    * Enregistre les composants découverts dans les panels appropriés
    */
   protected function registerPlatformModulesComponents(): void
   {
       $allComponents = \discover_platform_modules_components('system');
       
       $this->app->resolving('filament', function () use ($allComponents) {
           $panels = Filament::getPanels();
           
           foreach ($panels as $panel) {
               if ($panel->getId() === 'system') {
                   $this->registerComponentsToPanel($panel, $allComponents);
               }
           }
       });
       
       $this->app->booted(function () use ($allComponents) {
           $systemPanel = Filament::getPanel('system');
           if ($systemPanel) {
               $this->registerComponentsToPanel($systemPanel, $allComponents);
           }
       });
   }

   /**
    * Enregistre les composants sur un panel spécifique
    */
   protected function registerComponentsToPanel($panel, array $components): void
   {
       $validResources = [];
       foreach ($components['resources'] as $resourceClass) {
           if (class_exists($resourceClass) && is_subclass_of($resourceClass, Resource::class)) {
               if (method_exists($resourceClass, 'put_in_panel')) {
                   $allowedPanels = $resourceClass::put_in_panel();
                   if (in_array($panel->getId(), $allowedPanels)) {
                       $validResources[] = $resourceClass;
                   }
               } else {
                   $validResources[] = $resourceClass;
               }
           }
       }
       
       if (!empty($validResources)) {
           $panel->resources($validResources);
       }
       
       if (!empty($components['widgets'])) {
           $panel->widgets($components['widgets']);
       }
       
       foreach ($components['clusters'] as $clusterInfo) {
           if (isset($clusterInfo['dir']) && isset($clusterInfo['namespace'])) {
               $panel->discoverClusters(
                   in: $clusterInfo['dir'],
                   for: $clusterInfo['namespace']
               );
           }
       }
   }
}