<?php
namespace Webkernel\Providers;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\ServiceProvider;
use Filament\Enums\ThemeMode;

class WebkernelAllPanelsServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Enregistrement précoce des composants avant le boot de Filament
        $this->registerPlatformModulesComponents();
    }

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

    /**
     * Enregistre les composants découverts dans les panels appropriés
     */
    protected function registerPlatformModulesComponents(): void
    {
        // Découvrir tous les composants une seule fois
        $allComponents = discover_platform_modules_components('system');
        
        // Hook into Filament panel configuration avant le boot
        $this->app->resolving('filament', function () use ($allComponents) {
            $panels = Filament::getPanels();
            
            foreach ($panels as $panel) {
                if ($panel->getId() === 'system') {
                    $this->registerComponentsToPanel($panel, $allComponents);
                }
            }
        });
        
        // Alternative: Hook directement sur la création des panels
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
        // Filtrer et enregistrer les ressources
        $validResources = [];
        foreach ($components['resources'] as $resourceClass) {
            if (class_exists($resourceClass) && is_subclass_of($resourceClass, Resource::class)) {
                // Vérifier si la ressource doit être ajoutée à ce panel
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
        
        // Enregistrer les ressources
        if (!empty($validResources)) {
            $panel->resources($validResources);
        }
        
        // Enregistrer les widgets
        if (!empty($components['widgets'])) {
            $panel->widgets($components['widgets']);
        }
        
        // Enregistrer les clusters
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