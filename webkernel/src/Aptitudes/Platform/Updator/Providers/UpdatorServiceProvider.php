<?php

namespace Webkernel\Aptitudes\Platform\Updator\Providers;

use Illuminate\Support\ServiceProvider;
use Webkernel\Aptitudes\Platform\Updator\Console\InstallModuleCommand;
use Webkernel\Aptitudes\Platform\Updator\Console\ListModulesCommand;
use Webkernel\Aptitudes\Platform\Updator\Console\SyncCommand;
use Webkernel\Aptitudes\Platform\Updator\Console\UpdateModuleCommand;
use Webkernel\Aptitudes\Platform\Updator\Services\BackupService;
use Webkernel\Aptitudes\Platform\Updator\Services\ModuleExtractor;
use Webkernel\Aptitudes\Platform\Updator\Services\ModuleInstaller;
use Webkernel\Aptitudes\Platform\Updator\Services\ModuleUpdater;
use Webkernel\Aptitudes\Platform\Updator\Services\MigrationRunner;
use Webkernel\Aptitudes\Platform\Updator\Services\OverrideManager;
use Webkernel\Aptitudes\Platform\Updator\Services\ProgressReporter;

class UpdatorServiceProvider extends ServiceProvider
{
  public function register(): void
  {
    $this->app->singleton(ModuleExtractor::class);
    $this->app->singleton(MigrationRunner::class);
    $this->app->singleton(BackupService::class);
    $this->app->singleton(OverrideManager::class);
    $this->app->singleton(ProgressReporter::class);
    $this->app->singleton(ModuleInstaller::class);
    $this->app->singleton(ModuleUpdater::class);
  }

  public function boot(): void {}
}
