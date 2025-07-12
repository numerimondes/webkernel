<?php

namespace Webkernel\ServiceProviders;

use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\File;
use Filament\Schemas\Components\Grid;

// Sub-providers
use Illuminate\Support\ServiceProvider;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Foundation\Application;
use Webkernel\Core\Services\PanelRoutingService;
use Webkernel\Core\Services\PanelsAccessManager;
use Webkernel\Core\Providers\SystemPanelProvider;
use Webkernel\Core\Services\ModuleDetectionService;
use Webkernel\ServiceProviders\AuthServiceProvider;

// Filament customizations
use Webkernel\ServiceProviders\ViewServiceProvider;
use Webkernel\ServiceProviders\BladeServiceProvider;
use Webkernel\ServiceProviders\RouteServiceProvider;
use Webkernel\ServiceProviders\ConfigServiceProvider;
use Webkernel\ServiceProviders\PanelsServiceProvider;

// Webkernel
use Webkernel\ServiceProviders\WidgetServiceProvider;
use Webkernel\ServiceProviders\CommandServiceProvider;
use Webkernel\ServiceProviders\LivewireServiceProvider;
use Webkernel\ServiceProviders\PoliciesServiceProvider;
use Webkernel\ServiceProviders\MigrationServiceProvider;
use Webkernel\ServiceProviders\WebkernelServiceProvider;
use Webkernel\ServiceProviders\RenderHooksServiceProvider;
use Numerimondes\Modules\ReamMar\Core\Providers\Filament\MarPanelProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

        public function register(): void
    {
        $this->registerIfExists([
            BladeServiceProvider::class,
            MigrationServiceProvider::class,
            ViewServiceProvider::class,
            CommandServiceProvider::class,
            RouteServiceProvider::class,
            LivewireServiceProvider::class,
            PoliciesServiceProvider::class,
            WidgetServiceProvider::class,
            ConfigServiceProvider::class,
            PanelsServiceProvider::class,
            SystemPanelProvider::class,
            RenderHooksServiceProvider::class,
            WebkernelServiceProvider::class,
            MarPanelProvider::class,
        ]);

        $this->registerPlatformProviders();
    }

    protected function registerIfExists(array $providers): void
    {
        foreach ($providers as $provider) {
            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }
    
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../Core/config/webkernel.php' => config_path('webkernel.php'),
        ], 'config');

        Fieldset::configureUsing(fn (Fieldset $fieldset) => $fieldset->columnSpanFull());
        Grid::configureUsing(fn (Grid $grid) => $grid->columnSpanFull());
        Section::configureUsing(fn (Section $section) => $section->columnSpanFull());

        // Charger les migrations du module
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Enregistrer le hook pour le sélecteur de modules
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_PROFILE_AFTER,
            fn() => $this->renderModuleSelector()
        );
    }

    /**
     * Register providers from platform and packages
     */
    protected function registerPlatformProviders(): void
    {
        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $providerPath = $path . '/Providers';
            if (File::isDirectory($providerPath)) {
                $files = File::files($providerPath);
                foreach ($files as $file) {
                    $className = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    $providerClass = $namespace . 'Providers\\' . $className;
                    if (class_exists($providerClass) && is_subclass_of($providerClass, ServiceProvider::class)) {
                        $this->app->register($providerClass);
                    }
                }
            }
        }
    }

    /**
     * Get PSR-4 namespaces from composer.json
     *
     * @return array
     */
    protected function getAutoloadNamespaces(): array
    {
        $composerJson = json_decode(File::get(base_path('composer.json')), true);
        $namespaces = [];

        if (isset($composerJson['autoload']['psr-4'])) {
            foreach ($composerJson['autoload']['psr-4'] as $namespace => $path) {
                if (str_starts_with($path, 'platform/') || (str_starts_with($path, 'packages/') && $path !== 'packages/webkernel/src/')) {
                    $namespaces[rtrim($namespace, '\\') . '\\'] = base_path($path);
                }
            }
        }

        return $namespaces;
    }

    private function renderModuleSelector(): string
    {
        if (!auth()->check()) {
            return '';
        }
        
        $moduleService = app(ModuleDetectionService::class);
        $panelsManager = app(PanelsAccessManager::class);
        
        $availableModules = $moduleService->getAvailableModules();
        $userPanels = $panelsManager->getUserPanels(auth()->id());
        
        return view('webkernel::components.webkernel.ui.molecules.multi-modules-selector', [
            'modules' => $availableModules,
            'userPanels' => $userPanels,
            'currentPanel' => request()->route('panel') ?? 'system'
        ])->render();
    }
}
