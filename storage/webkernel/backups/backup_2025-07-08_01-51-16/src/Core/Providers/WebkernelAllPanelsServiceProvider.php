<?php
namespace Webkernel\Core\Providers;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\ServiceProvider;
use Filament\Enums\ThemeMode;

class WebkernelAllPanelsServiceProvider extends ServiceProvider
{
   public function register()
   {
       $this->registerPlatformModulesComponents();
   }
//
   public function boot()
   {
       $logoUrl = platformAbsoluteUrlAnyPrivatetoPublic(corePlatformInfos('logoLink'));
       
       $panels = Filament::getPanels();
       
       foreach ($panels as $panel) {
           $panel->bootUsing(function ($panelInstance) use ($logoUrl) {
               $panelInstance->defaultThemeMode(ThemeMode::Light)
                   ->brandLogo($logoUrl)
                   ->brandLogoHeight('2.5rem');
           });
       }
   }
//
   /**
    * Enregistre les composants découverts dans les panels appropriés
    */
   protected function registerPlatformModulesComponents(): void
   {
       $allComponents = discover_platform_modules_components('system');
       
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
//
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