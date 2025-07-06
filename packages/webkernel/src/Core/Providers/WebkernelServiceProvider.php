<?php

namespace Webkernel\Core\Providers;

use Illuminate\Support\Facades\File;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\ServiceProvider;

// Sub-providers
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Illuminate\Contracts\Foundation\Application;
use Webkernel\Core\Providers\WebkernelViewServiceProvider;
use Webkernel\Core\Providers\WebkernelBladeServiceProvider;
use Webkernel\Core\Providers\WebkernelRouteServiceProvider;
use Webkernel\Core\Providers\WebkernelConfigServiceProvider;
use Webkernel\Core\Providers\WebkernelWidgetServiceProvider;
use Webkernel\Core\Providers\WebkernelCommandServiceProvider;

// Filament customizations
use Webkernel\Core\Providers\WebkernelLivewireServiceProvider;
use Webkernel\Core\Providers\WebkernelPlatformServiceProvider;
use Webkernel\Core\Providers\WebkernelPoliciesServiceProvider;
use Webkernel\Core\Providers\WebkernelAllPanelsServiceProvider;
use Webkernel\Core\Providers\WebkernelMigrationServiceProvider;
use Webkernel\PlatformConfig\Providers\PlatformConfigServiceProvider;

class WebkernelServiceProvider extends ServiceProvider
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function register(): void
    {
        // Register core Webkernel providers
        $this->app->register(WebkernelBladeServiceProvider::class);
        $this->app->register(WebkernelMigrationServiceProvider::class);
        $this->app->register(WebkernelViewServiceProvider::class);
        $this->app->register(WebkernelCommandServiceProvider::class);
        $this->app->register(WebkernelRouteServiceProvider::class);
        $this->app->register(WebkernelLivewireServiceProvider::class);
        $this->app->register(WebkernelPoliciesServiceProvider::class);
        $this->app->register(WebkernelWidgetServiceProvider::class);
        $this->app->register(WebkernelConfigServiceProvider::class);
        $this->app->register(WebkernelPlatformServiceProvider::class);
        $this->app->register(WebkernelAllPanelsServiceProvider::class);
        $this->app->register(PlatformConfigServiceProvider::class);
        $this->app->register(WebkernelConstantsServiceProvider::class);

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
