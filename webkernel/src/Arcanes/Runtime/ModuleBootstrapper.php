<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Runtime;

use Illuminate\Foundation\Application;
use Webkernel\Arcanes\Runtime\Loaders\{RouteLoader, PolicyLoader, ComponentLoader};

/**
 * Handles the bootstrapping of all modules and their components
 */
class ModuleBootstrapper
{
  public function __construct(private Application $app, private WebkernelManager $manager) {}

  /**
   * Bootstrap all modules and load their components
   *
   * @return void
   */
  public function bootstrap(): void
  {
    $this->bootAllModules();
    $this->loadLateComponents();
  }

  /**
   * Load components that can be loaded after modules are booted
   *
   * @return void
   */
  private function loadLateComponents(): void
  {
    $this->app->make(PolicyLoader::class)->load();
    $this->app->make(RouteLoader::class)->load();
    $this->app->make(ComponentLoader::class)->load();
  }

  /**
   * Boot all registered modules
   *
   * @return void
   */
  private function bootAllModules(): void
  {
    foreach ($this->manager->getModules() as $id => $data) {
      $this->manager->bootModule($id);
    }
  }
}
