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
use Illuminate\Support\ServiceProvider;
use Webkernel\Core\Http\Middleware\CheckUserAccess;

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