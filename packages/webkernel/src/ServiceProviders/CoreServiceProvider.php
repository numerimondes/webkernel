<?php
namespace Webkernel\ServiceProviders;

use Illuminate\Support\Facades\File;
use Filament\Schemas\Components\Grid;
// Sub-providers
use Illuminate\Support\ServiceProvider;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Illuminate\Contracts\Foundation\Application;
use Webkernel\ServiceProviders\ViewServiceProvider;
use Webkernel\ServiceProviders\BladeServiceProvider;
use Webkernel\ServiceProviders\RouteServiceProvider;
use Webkernel\ServiceProviders\ConfigServiceProvider;
use Webkernel\ServiceProviders\PanelsServiceProvider;
use Webkernel\ServiceProviders\WidgetServiceProvider;
// Webkernel
use Webkernel\ServiceProviders\CommandServiceProvider;
use Webkernel\ServiceProviders\LivewireServiceProvider;
use Webkernel\ServiceProviders\PoliciesServiceProvider;
use Webkernel\ServiceProviders\ExceptionServiceProvider;
use Webkernel\ServiceProviders\MigrationServiceProvider;
use Webkernel\ServiceProviders\WebkernelServiceProvider;
use Webkernel\ServiceProviders\MiddlewareServiceProvider;
use Webkernel\ServiceProviders\RenderHooksServiceProvider;
use Numerimondes\Modules\ReamMar\Core\Providers\Filament\MarPanelProvider;
use Webkernel\Core\Providers\SystemPanelProvider;// Filament customizations

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
            MiddlewareServiceProvider::class,
            ExceptionServiceProvider::class,
        ]);

        $this->registerPlatformProviders();
    }

    protected function registerIfExists(array $providers): void
    {
        foreach ($providers as $provider) {
            try {
                if (class_exists($provider)) {
                    $this->app->register($provider);
                }
            } catch (\Error $e) {
                // Ignorer les erreurs de classe non trouvée (include/require failed)
                continue;
            } catch (\Exception $e) {
                // Ignorer les autres exceptions potentielles
                continue;
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

}