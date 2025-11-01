<?php declare(strict_types=1);
namespace Webkernel\Arcanes\Runtime;

use Illuminate\Foundation\Application;
use Exception;
use Webkernel\Arcanes\WebkernelApp;

/**
 * Central manager for module registry and lifecycle
 * Optimized for sub-millisecond performance
 */
class WebkernelManager
{
  private array $moduleRegistry = [];
  private array $instantiatedModules = [];
  private bool $isBootstrapped = false;

  public function __construct(private Application $app, private ModuleScanner $scanner, private CacheManager $cache) {}

  /**
   * Initialize module discovery and registration
   *
   * @return void
   */
  public function initialize(): void
  {
    if ($this->isBootstrapped) {
      return;
    }

    $startTime = hrtime(true);

    if ($this->cache->loadFromCache($this->moduleRegistry)) {
      $this->logPerformance('Cache Load', $startTime, count($this->moduleRegistry));
      $this->isBootstrapped = true;
      return;
    }

    $this->moduleRegistry = $this->scanner->scan();
    $this->cache->saveToCache($this->moduleRegistry);

    $this->logPerformance('Fresh Scan', $startTime, count($this->moduleRegistry));
    $this->isBootstrapped = true;
  }

  /**
   * Boot a specific module by ID
   *
   * @param string $id Module identifier
   * @return WebkernelApp|null
   */
  public function bootModule(string $id): ?WebkernelApp
  {
    if (isset($this->instantiatedModules[$id])) {
      return $this->instantiatedModules[$id];
    }

    $data = $this->moduleRegistry[$id] ?? null;
    if (!$data || !isset($data['class'])) {
      return null;
    }

    try {
      if (!class_exists($data['class'])) {
        return null;
      }

      $module = new ($data['class'])($this->app);
      $module->register();
      $module->bootIfNeeded();

      $this->instantiatedModules[$id] = $module;
      $this->moduleRegistry[$id]['instantiated'] = true;

      return $module;
    } catch (Exception $e) {
      if (config('webkernel-arcanes.development.debug', false)) {
        error_log("Failed to boot module {$id}: " . $e->getMessage());
      }
      return null;
    }
  }

  /**
   * Get module instance by ID
   *
   * @param string $id Module identifier
   * @return WebkernelApp|null
   */
  public function getModule(string $id): ?WebkernelApp
  {
    return $this->instantiatedModules[$id] ?? $this->bootModule($id);
  }

  /**
   * Get all registered modules data
   *
   * @return array<string, array>
   */
  public function getModules(): array
  {
    return $this->moduleRegistry;
  }

  /**
   * Check if a module exists in the registry
   *
   * @param string $id Module identifier
   * @return bool
   */
  public function hasModule(string $id): bool
  {
    return isset($this->moduleRegistry[$id]);
  }

  /**
   * Refresh module discovery and clear all caches
   *
   * @return void
   */
  public function refreshDiscovery(): void
  {
    $this->cache->clearCache();
    $this->moduleRegistry = [];
    $this->instantiatedModules = [];
    $this->isBootstrapped = false;
    $this->initialize();
  }

  /**
   * Log performance metrics including total request time
   *
   * @param string $operation Operation name
   * @param int $startTime Start time in nanoseconds
   * @param int $moduleCount Number of modules processed
   * @return void
   */
  private function logPerformance(string $operation, int $startTime, int $moduleCount): void
  {
    $operationDurationMs = (hrtime(true) - $startTime) / 1_000_000;

    // Retrieve application timings (same source as /timing route)
    $applicationTimings = $this->app->has('app.timings') ? $this->app->get('app.timings') : [];

    // Calculate total request time since LARAVEL_START (same as /timing route)
    $totalRequestTimeMs = defined('LARAVEL_START') ? round((microtime(true) - LARAVEL_START) * 1000, 2) : 0;

    // Calculate booting time if available (same as /timing route)
    $bootingTimeMs = null;
    if (isset($applicationTimings['booting_start']) && isset($applicationTimings['booting_end'])) {
      $bootingTimeMs = round(($applicationTimings['booting_end'] - $applicationTimings['booting_start']) * 1000, 2);
    }

    // Calculate percentage of total request time
    $percentageOfRequest = $totalRequestTimeMs > 0 ? round(($operationDurationMs / $totalRequestTimeMs) * 100) : 0;

    // Format: Webkernel Cache Load: (12.04ms, 20 modules) for a request that took 94.72ms to load (13%)
    $logMessage = sprintf(
      'Webkernel %s: (%.2fms, %d modules) for a request that took %.2fms to load (%d%%)',
      $operation,
      $operationDurationMs,
      $moduleCount,
      $totalRequestTimeMs,
      $percentageOfRequest,
    );

    // Add booting time if available
    if ($bootingTimeMs !== null) {
      $logMessage .= sprintf(' | Boot: %.2fms', $bootingTimeMs);
    }

    error_log($logMessage);
  }
}
