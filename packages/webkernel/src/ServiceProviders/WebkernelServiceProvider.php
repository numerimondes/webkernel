<?php

namespace Webkernel\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Webkernel\Core\Services\ModuleDetectionService;
use Webkernel\Core\Services\PanelsAccessManager;
use Webkernel\Core\Services\PanelRoutingService;
use Webkernel\Core\Http\Middleware\PanelAccessMiddleware;

class WebkernelServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Services singleton
        $this->app->singleton(ModuleDetectionService::class);
        $this->app->singleton(PanelsAccessManager::class);
        $this->app->singleton(PanelRoutingService::class);
        
        // Configuration
        $this->mergeConfigFrom(__DIR__ . '/../Core/config/webkernel.php', 'webkernel');
    }

    public function boot()
    {
        // Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Core/database/migrations');
        
        // Routes
        $this->loadRoutesFrom(__DIR__ . '/../Core/routes/web.php');
        
        // Vues
        $this->loadViewsFrom(__DIR__ . '/../Core/resources/views', 'webkernel');
        
        // Middleware
        $this->app['router']->aliasMiddleware('webkernel.panel', PanelAccessMiddleware::class);
        
        // Helpers
        $this->loadHelpers();
        
        // Hooks Filament
        $this->registerFilamentHooks();
        
        // Publier les assets
        $this->publishes([
            __DIR__ . '/../Core/config/webkernel.php' => config_path('webkernel.php'),
        ], 'webkernel-config');
        
        $this->publishes([
            __DIR__ . '/../Core/resources/views' => resource_path('views/vendor/webkernel'),
        ], 'webkernel-views');
    }

    private function loadHelpers(): void
    {
        $helpersPath = __DIR__ . '/../Settings/Helpers/WebkernelPlatformDataHelper.php';
        if (file_exists($helpersPath)) {
            require_once $helpersPath;
        }
    }

    private function registerFilamentHooks(): void
    {
        // Hook pour le sélecteur de modules dans le menu utilisateur
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_PROFILE_AFTER,
            fn() => $this->renderModuleSelector()
        );
        
        // Hook pour les informations de contexte
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_START,
            fn() => $this->renderContextInfo()
        );
    }

    private function renderModuleSelector(): string
    {
        if (!auth()->check()) {
            return '';
        }

        $moduleService = app(ModuleDetectionService::class);
        $panelsManager = app(PanelsAccessManager::class);
        
        $user = auth()->user();
        $currentContext = request()->get('context') ?? 
                         WebkernelPlatformData_get('current_context', $user->id);
        
        $availableModules = $moduleService->getAvailableModules();
        $userPanels = $panelsManager->getUserPanels($user->id, $currentContext);
        
        // Filtrer les modules accessibles
        $accessibleModules = [];
        foreach ($availableModules as $moduleKey => $moduleData) {
            $modulePanels = $moduleData['panels'] ?? [];
            
            $hasAccess = false;
            foreach ($modulePanels as $panelId) {
                if ($panelsManager->userCanAccessPanel($user->id, $panelId, $currentContext)) {
                    $hasAccess = true;
                    break;
                }
            }
            
            if ($hasAccess) {
                $accessibleModules[$moduleKey] = $moduleData;
            }
        }
        
        // Déterminer le module actuel
        $currentPanel = request()->route('panel') ?? 
                       WebkernelPlatformData_get('last_selected_panel', $user->id, $currentContext);
        
        $currentModule = null;
        foreach ($accessibleModules as $moduleKey => $moduleData) {
            if (in_array($currentPanel, $moduleData['panels'] ?? [])) {
                $currentModule = $moduleKey;
                break;
            }
        }
        
        return view('webkernel::components.webkernel.ui.molecules.multi-modules-selector', [
            'modules' => $accessibleModules,
            'currentModule' => $currentModule,
            'currentContext' => $currentContext,
            'hasMultipleModules' => count($accessibleModules) > 1
        ])->render();
    }

    private function renderContextInfo(): string
    {
        if (!auth()->check()) {
            return '';
        }

        $user = auth()->user();
        $currentContext = request()->get('context') ?? 
                         WebkernelPlatformData_get('current_context', $user->id);
        
        $contextInfo = [
            'user_id' => $user->id,
            'context' => $currentContext,
            'panel' => request()->route('panel'),
            'csrf_token' => csrf_token()
        ];

        return sprintf(
            '<script>window.webkernelContext = %s;</script>',
            json_encode($contextInfo)
        );
    }
} 