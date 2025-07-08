<?php

namespace Webkernel\Constants\ServiceProviders;

use Illuminate\Support\Facades\File;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\ServiceProvider;

// Sub-providers
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Illuminate\Contracts\Foundation\Application;
use Webkernel\Constants\ServiceProviders\ViewServiceProvider;
use Webkernel\Constants\ServiceProviders\BladeServiceProvider;
use Webkernel\Constants\ServiceProviders\RouteServiceProvider;
use Webkernel\Constants\ServiceProviders\ConfigServiceProvider;
use Webkernel\Constants\ServiceProviders\WidgetServiceProvider;
use Webkernel\Constants\ServiceProviders\CommandServiceProvider;

// Filament customizations
use Webkernel\Constants\ServiceProviders\LivewireServiceProvider;
use Webkernel\Constants\ServiceProviders\PoliciesServiceProvider;
use Webkernel\Constants\ServiceProviders\PanelsServiceProvider;
use Webkernel\Constants\ServiceProviders\MigrationServiceProvider;
use Webkernel\Constants\ServiceProviders\RenderHooksServiceProvider;

// Webkernel
use Webkernel\Core\Providers\SystemPanelProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function register(): void
    {
        // Register core Webkernel providers
        $this->app->register(BladeServiceProvider::class);
        $this->app->register(MigrationServiceProvider::class);
        $this->app->register(ViewServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(LivewireServiceProvider::class);
        $this->app->register(PoliciesServiceProvider::class);
        $this->app->register(WidgetServiceProvider::class);
        $this->app->register(ConfigServiceProvider::class);
        $this->app->register(PanelsServiceProvider::class);
        $this->app->register(SystemPanelProvider::class);
        $this->app->register(RenderHooksServiceProvider::class);

        // Register providers from platform and packages
        $this->registerPlatformProviders();
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/webkernel.php' => config_path('webkernel.php'),
        ], 'config');

        Fieldset::configureUsing(fn (Fieldset $fieldset) => $fieldset->columnSpanFull());
        Grid::configureUsing(fn (Grid $grid) => $grid->columnSpanFull());
        Section::configureUsing(fn (Section $section) => $section->columnSpanFull());
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
