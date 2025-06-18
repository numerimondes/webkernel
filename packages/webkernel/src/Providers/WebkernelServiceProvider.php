<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

// Sub-providers
use Webkernel\Providers\WebkernelBladeServiceProvider;
use Webkernel\Providers\WebkernelMigrationServiceProvider;
use Webkernel\Providers\WebkernelViewServiceProvider;
use Webkernel\Providers\WebkernelCommandServiceProvider;
use Webkernel\Providers\WebkernelHelperServiceProvider;
use Webkernel\Providers\WebkernelRouteServiceProvider;
use Webkernel\Providers\WebkernelLivewireServiceProvider;
use Webkernel\Providers\WebkernelConfigServiceProvider;
use Webkernel\Providers\WebkernelUserServiceProvider;
use Webkernel\Providers\WebkernelFactoryServiceProvider;
use Webkernel\Providers\WebkernelPoliciesServiceProvider;
use Webkernel\Providers\WebkernelWidgetServiceProvider;

// Filament customizations
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;

class WebkernelServiceProvider extends ServiceProvider
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function register(): void
    {
        $this->app->register(WebkernelBladeServiceProvider::class);
        $this->app->register(WebkernelMigrationServiceProvider::class);
        $this->app->register(WebkernelViewServiceProvider::class);
        $this->app->register(WebkernelCommandServiceProvider::class);
        $this->app->register(WebkernelRouteServiceProvider::class);
        $this->app->register(WebkernelLivewireServiceProvider::class);
        $this->app->register(WebkernelPoliciesServiceProvider::class);
        $this->app->register(WebkernelWidgetServiceProvider::class);
        $this->app->register(WebkernelConfigServiceProvider::class);
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
}
