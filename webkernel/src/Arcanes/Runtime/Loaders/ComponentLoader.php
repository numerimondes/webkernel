<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Runtime\Loaders;

use Webkernel\Arcanes\Runtime\WebkernelManager;
use Webkernel\Arcanes\Support\Base\ArcanesLoader;

/**
 * Loads views, translations, migrations, and Blade components
 * Optimized for sub-millisecond performance
 */
class ComponentLoader implements ArcanesLoader
{
  private $view;
  private $translator;
  private $migrator;
  private $blade;

  public function __construct(private WebkernelManager $manager)
  {
    // Cache app services for faster access
    $this->view = app('view');
    $this->translator = app('translator');
    $this->migrator = app('migrator');
    $this->blade = app('blade.compiler');
  }

  /**
   * Load all module components
   *
   * @return void
   */
  public function load(): void
  {
    $modules = $this->manager->getModules();

    foreach ($modules as $moduleId => $module) {
      $moduleInstance = $this->manager->getModule($moduleId);

      if (!$moduleInstance) {
        continue;
      }

      $this->loadModuleComponents($moduleInstance, $module);
    }
  }

  /**
   * Load components for a specific module
   *
   * @param \Webkernel\Arcanes\WebkernelApp $module Module instance
   * @param array $moduleData Module data
   * @return void
   */
  private function loadModuleComponents($module, array $moduleData): void
  {
    $config = $module->getModuleConfig();

    // Load views
    $viewsPath = $module->getViewsPath();
    if (is_dir($viewsPath)) {
      $viewNamespace = $config->viewNamespace ?: strtolower($config->id);
      $this->view->addNamespace($viewNamespace, $viewsPath);
      $this->registerIndexComponents($viewNamespace, $viewsPath);
    }

    // Load translations
    $langPath = $module->getLangPath();
    if (is_dir($langPath)) {
      $this->translator->addNamespace(strtolower($config->id), $langPath);
    }

    // Load migrations
    $migrationsPath = $module->getMigrationsPath();
    if (is_dir($migrationsPath)) {
      $this->migrator->path($migrationsPath);
    }
  }

  /**
   * Register Blade components with index file support
   *
   * @param string $namespace View namespace
   * @param string $viewsPath Views directory path
   * @return void
   */
  private function registerIndexComponents(string $namespace, string $viewsPath): void
  {
    $directories = glob($viewsPath . '/*', GLOB_ONLYDIR);

    if (!is_array($directories)) {
      return;
    }

    foreach ($directories as $directory) {
      $indexFile = $directory . '/index.blade.php';

      if (file_exists($indexFile)) {
        $componentName = basename($directory);
        $this->blade->component($namespace . '::' . $componentName . '.index', $namespace . '::' . $componentName);
      }
    }
  }
}
