<?php
declare(strict_types=1);

namespace Webkernel\Arcanes;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Foundation\Application;
use Webkernel\Arcanes\Support\RemoteComponents\RCServiceProvider;
use Webkernel\Arcanes\Runtime\{WebkernelManager, ModuleBootstrapper, CacheManager, ModuleScanner, PathResolver};
use Webkernel\Arcanes\Runtime\Loaders\{
  HelperLoader,
  RouteLoader,
  PolicyLoader,
  ProviderLoader,
  CommandLoader,
  ComponentLoader,
  ModuleProviderRegistrar,
};

/**
 * Service provider for Webkernel Arcanes module system
 *
 * This provider is responsible ONLY for registering services into the container.
 * All bootstrapping logic is delegated to specialized classes.
 */
class ArcancesServiceProvider extends LaravelServiceProvider
{
  /**
   * Register application services
   *
   * @return void
   */
  public function register(): void
  {
    $this->registerCoreServices();
    $this->registerLoaders();
    $this->registerRemoteComponents();
    $this->scheduleEarlyBootstrap();
    $this->scheduleProviderRegistration();
    $this->app->register(CommandLoader::class);
  }

  /**
   * Bootstrap application services
   *
   * @return void
   */
  public function boot(): void
  {
    $this->publishConfiguration();
    $this->bootstrapModules();
    $this->registerMakeModuleCommand();
  }

  /**
   * Register core services into the container
   *
   * @return void
   */
  private function registerCoreServices(): void
  {
    $this->app->singleton(CacheManager::class, function (Application $app) {
      return new CacheManager(storage_path('framework/cache/webkernel_ultra.php'));
    });

    $this->app->singleton(PathResolver::class, function (Application $app) {
      return new PathResolver();
    });

    $this->app->singleton(ModuleScanner::class, function (Application $app) {
      return new ModuleScanner($app->make(CacheManager::class), $app->make(PathResolver::class));
    });

    $this->app->singleton(WebkernelManager::class, function (Application $app) {
      return new WebkernelManager($app, $app->make(ModuleScanner::class), $app->make(CacheManager::class));
    });

    $this->app->singleton(ModuleBootstrapper::class, function (Application $app) {
      return new ModuleBootstrapper($app, $app->make(WebkernelManager::class));
    });

    $this->app->singleton(LicenseService::class, function ($app) {
      return new LicenseService();
    });
  }

  /**
   * Register loader services
   *
   * @return void
   */
  private function registerLoaders(): void
  {
    $this->app->singleton(HelperLoader::class, function (Application $app) {
      return new HelperLoader($app->make(WebkernelManager::class));
    });

    $this->app->singleton(RouteLoader::class, function (Application $app) {
      return new RouteLoader($app->make(WebkernelManager::class));
    });

    $this->app->singleton(PolicyLoader::class, function (Application $app) {
      return new PolicyLoader($app, $app->make(WebkernelManager::class));
    });

    $this->app->singleton(ProviderLoader::class, function (Application $app) {
      return new ProviderLoader($app, $app->make(WebkernelManager::class));
    });

    $this->app->singleton(ModuleProviderRegistrar::class, function (Application $app) {
      return new ModuleProviderRegistrar($app, $app->make(WebkernelManager::class));
    });

    $this->app->singleton(CommandLoader::class, function (Application $app) {
      return new CommandLoader($app->make(WebkernelManager::class));
    });

    $this->app->singleton(ComponentLoader::class, function (Application $app) {
      return new ComponentLoader($app->make(WebkernelManager::class));
    });
  }

  /**
   * Register remote components service provider
   *
   * @return void
   */
  private function registerRemoteComponents(): void
  {
    $this->app->register(RCServiceProvider::class);
  }

  /**
   * Schedule early bootstrap after WebkernelManager is resolved
   *
   * This ensures modules are discovered and helpers are loaded immediately
   *
   * @return void
   */
  private function scheduleEarlyBootstrap(): void
  {
    $this->app->afterResolving(WebkernelManager::class, function (WebkernelManager $manager) {
      $manager->initialize();
      $this->app->make(HelperLoader::class)->load();
    });
  }

  /**
   * Schedule provider registration during application booting
   *
   * This happens before boot() is called on service providers
   *
   * @return void
   */
  private function scheduleProviderRegistration(): void
  {
    $this->app->booting(function () {
      $loader = $this->app->make(ModuleProviderRegistrar::class);
      $loader->load();
    });
  }

  /**
   * Publish configuration files
   *
   * @return void
   */
  private function publishConfiguration(): void
  {
    if (method_exists($this, 'publishes')) {
      $this->publishes(
        [
          __DIR__ . '/config/webkernel-arcanes.php' => config_path('webkernel-arcanes.php'),
        ],
        'webkernel-config',
      );
    }
  }

  /**
   * Bootstrap all modules
   *
   * @return void
   */
  private function bootstrapModules(): void
  {
    $bootstrapper = $this->app->make(ModuleBootstrapper::class);
    $bootstrapper->bootstrap();
  }

  /**
   * Register make:module command if running in console
   *
   * @return void
   */
  private function registerMakeModuleCommand(): void
  {
    if ($this->app->runningInConsole()) {
      $this->commands([\Webkernel\Arcanes\Commands\MakeModuleCommand::class]);
    }
  }

  /**
   * Get service providers declared by this provider
   *
   * @return array<int, string>
   */
  public function providers(): array
  {
    return [];
  }
}
