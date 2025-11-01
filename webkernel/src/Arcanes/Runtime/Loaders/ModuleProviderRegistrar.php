<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Runtime\Loaders;

use Webkernel\Arcanes\Runtime\WebkernelManager;
use Webkernel\Arcanes\Support\Base\ArcanesLoader;
use Illuminate\Contracts\Foundation\Application;
use Throwable;

/**
 * Registers service providers declared by modules during application booting
 *
 * This loader is called early in the boot process to ensure module providers
 * are registered before other bootstrapping occurs
 */
class ModuleProviderRegistrar implements ArcanesLoader
{
  public function __construct(private Application $app, private WebkernelManager $manager) {}

  /**
   * Load and register all module providers
   *
   * @return void
   */
  public function load(): void
  {
    foreach ($this->manager->getModules() as $moduleId => $module) {
      if (!isset($module['class']) || !class_exists($module['class'])) {
        continue;
      }

      $this->registerModuleProviders($module['class'], $moduleId);
    }
  }

  /**
   * Register providers for a specific module
   *
   * @param string $moduleClass Module class name
   * @param string $moduleId Module identifier for logging
   * @return void
   */
  private function registerModuleProviders(string $moduleClass, string $moduleId): void
  {
    try {
      $instance = new $moduleClass($this->app);
      $providers = $instance->providers();

      foreach ($providers as $provider) {
        if (is_string($provider) && class_exists($provider)) {
          $this->app->register($provider);
        }
      }
    } catch (Throwable $e) {
      if (config('webkernel-arcanes.development.debug', false)) {
        error_log("Failed to register providers for module {$moduleId}: " . $e->getMessage());
      }
    }
  }
}
