<?php
declare(strict_types=1);

namespace Webkernel;

use Illuminate\Contracts\Foundation\Application as Illuminate;

/**
 * Main application class for Webkernel framework
 */
class Webkernel
{
  private string $version = '2.5.0';
  private string $minPhpVersion = '8.2.0';

  private Illuminate $app;
  private bool $initialized = false;
  private bool $maintenanceMode = false;

  /**
   * Create a new Webkernel application instance
   *
   * @param Illuminate $app The Laravel application instance
   */
  public function __construct(Illuminate $app)
  {
    $this->app = $app;
    $this->checkPhpVersion();
  }

  /**
   * Bootstrap the application and initialize all services
   *
   * @return void
   */
  public function bootstrap(): void
  {
    if ($this->initialized) {
      return;
    }

    $this->initialized = true;
  }

  /**
   * Check if current PHP version meets minimum requirements
   *
   * @return void
   * @throws \RuntimeException If PHP version is insufficient
   */
  private function checkPhpVersion(): void
  {
    if (version_compare(phpversion(), $this->minPhpVersion, '<')) {
      throw new \RuntimeException("Requires PHP {$this->minPhpVersion}, current: " . phpversion());
    }
  }

  /**
   * Check if the application has been initialized
   *
   * @return bool
   */
  public function isInitialized(): bool
  {
    return $this->initialized;
  }

  /**
   * Check if the application is in maintenance mode
   *
   * @return bool
   */
  public function isInMaintenance(): bool
  {
    return $this->maintenanceMode;
  }

  /**
   * Get the current application version
   *
   * @return string
   */
  public function version(): string
  {
    return $this->version;
  }

  /**
   * Get the underlying Laravel application instance
   *
   * @return Illuminate
   */
  public function getLaravel(): Illuminate
  {
    return $this->app;
  }
}
