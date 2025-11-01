<?php
declare(strict_types=1);

namespace Webkernel\Arcanes;

use Webkernel\Arcanes\Runtime\{ModuleConfig, ModuleBuilder, PathResolver};
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;
use ReflectionClass;

/**
 * Abstract base class for all Webkernel modules
 *
 * This class provides a standardized structure for modules including
 * configuration, path resolution, and component registration.
 */
abstract class WebkernelApp extends ServiceProvider
{
  private ?ModuleConfig $moduleConfig = null;
  private ?string $basePath = null;
  private ?PathResolver $pathResolver = null;
  private bool $isBooted = false;

  public function __construct(Application $app)
  {
    parent::__construct($app);
    $this->basePath = $this->resolveBasePath();
    $this->pathResolver = new PathResolver();
  }

  /**
   * Configure module metadata and settings
   * This method must be implemented by each module
   *
   * @return void
   */
  abstract public function configureModule(): void;

  /**
   * Register module services
   *
   * @return void
   */
  public function register(): void
  {
    parent::register();
  }

  /**
   * Bootstrap module services
   *
   * @return void
   */
  public function boot(): void
  {
    if (method_exists($this, 'autoRegisterComponents')) {
      $this->autoRegisterComponents();
    }
  }

  /**
   * Get service providers declared by this module
   *
   * @return array<int, string>
   */
  public function providers(): array
  {
    return $this->getModuleConfig()->providers;
  }

  /**
   * Get module builder instance
   *
   * @return ModuleBuilder
   */
  protected function module(): ModuleBuilder
  {
    return new ModuleBuilder();
  }

  /**
   * Set module configuration
   *
   * @param ModuleConfig $config Module configuration
   * @return void
   */
  protected function setModuleConfig(ModuleConfig $config): void
  {
    $this->moduleConfig = $config;
  }

  /**
   * Get module configuration
   *
   * @return ModuleConfig
   */
  public function getModuleConfig(): ModuleConfig
  {
    if ($this->moduleConfig === null) {
      $this->configureModule();
    }
    return $this->moduleConfig;
  }

  /**
   * Get module base path
   *
   * @return string
   */
  public function getBasePath(): string
  {
    return $this->basePath;
  }

  /**
   * Get language files path
   *
   * @param string $subPath Optional sub-path
   * @return string
   */
  public function getLangPath(string $subPath = ''): string
  {
    return $this->pathResolver->resolve($this->basePath, 'Lang', $subPath);
  }

  /**
   * Get views path
   *
   * @param string $subPath Optional sub-path
   * @return string
   */
  public function getViewsPath(string $subPath = ''): string
  {
    return $this->pathResolver->resolve($this->basePath, 'Resources/Views', $subPath);
  }

  /**
   * Get routes path
   *
   * @param string $subPath Optional sub-path
   * @return string
   */
  public function getRoutesPath(string $subPath = ''): string
  {
    return $this->pathResolver->resolve($this->basePath, 'Routes', $subPath);
  }

  /**
   * Get migrations path
   *
   * @param string $subPath Optional sub-path
   * @return string
   */
  public function getMigrationsPath(string $subPath = ''): string
  {
    return $this->pathResolver->resolve($this->basePath, 'Database/Migrations', $subPath);
  }

  /**
   * Get config path
   *
   * @param string $subPath Optional sub-path
   * @return string
   */
  public function getConfigPath(string $subPath = ''): string
  {
    return $this->pathResolver->resolve($this->basePath, 'config', $subPath);
  }

  /**
   * Get controllers path
   *
   * @param string $subPath Optional sub-path
   * @return string
   */
  public function getControllersPath(string $subPath = ''): string
  {
    return $this->pathResolver->resolve($this->basePath, 'Http/Controllers', $subPath);
  }

  /**
   * Get middleware path
   *
   * @param string $subPath Optional sub-path
   * @return string
   */
  public function getMiddlewarePath(string $subPath = ''): string
  {
    return $this->pathResolver->resolve($this->basePath, 'Http/Middleware', $subPath);
  }

  /**
   * Get services path
   *
   * @param string $subPath Optional sub-path
   * @return string
   */
  public function getServicesPath(string $subPath = ''): string
  {
    return $this->pathResolver->resolve($this->basePath, 'Services', $subPath);
  }

  /**
   * Get console path
   *
   * @param string $subPath Optional sub-path
   * @return string
   */
  public function getConsolePath(string $subPath = ''): string
  {
    return $this->pathResolver->resolve($this->basePath, 'Console', $subPath);
  }

  /**
   * Get commands path
   *
   * @param string $subPath Optional sub-path
   * @return string
   */
  public function getCommandsPath(string $subPath = ''): string
  {
    return $this->pathResolver->resolve($this->basePath, 'Commands', $subPath);
  }

  /**
   * Get helpers path
   *
   * @param string $subPath Optional sub-path
   * @return string
   */
  public function getHelpersPath(string $subPath = ''): string
  {
    return $this->pathResolver->resolve($this->basePath, 'Helpers', $subPath);
  }

  /**
   * Boot module if not already booted
   *
   * @return void
   */
  public function bootIfNeeded(): void
  {
    if ($this->isBooted) {
      return;
    }

    $this->boot();
    $this->isBooted = true;
  }

  /**
   * Auto-register module components
   * This method is called automatically during boot
   *
   * @return void
   */
  protected function autoRegisterComponents(): void
  {
    // Components are now loaded by ComponentLoader
    // This method is kept for backward compatibility
  }

  /**
   * Resolve module base path from class location
   *
   * @return string
   */
  private function resolveBasePath(): string
  {
    return dirname(new ReflectionClass(static::class)->getFileName());
  }

  /**
   * Get all widgets registered by this module
   * Override this method in modules to provide widget registry
   *
   * @return array<int, string>
   */
  public function getRegisteredWidgets(): array
  {
    return [];
  }
}
