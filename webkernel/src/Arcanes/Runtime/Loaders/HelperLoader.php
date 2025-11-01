<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Runtime\Loaders;

use Webkernel\Arcanes\Runtime\WebkernelManager;
use Webkernel\Arcanes\Support\Base\ArcanesLoader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Loads helper files from all registered modules
 * Optimized for sub-millisecond performance
 */
class HelperLoader implements ArcanesLoader
{
  private array $processedPaths = [];

  public function __construct(private WebkernelManager $manager) {}

  /**
   * Load all helper files from registered modules
   *
   * @return void
   */
  public function load(): void
  {
    $modules = $this->manager->getModules();

    // Pre-filter modules with helpers
    $modulesWithHelpers = [];
    foreach ($modules as $module) {
      $path = $module['helpersPath'] ?? null;
      if ($path && is_dir($path) && !in_array($path, $this->processedPaths, true)) {
        $modulesWithHelpers[] = $path;
        $this->processedPaths[] = $path;
      }
    }

    // Load helpers
    foreach ($modulesWithHelpers as $path) {
      $this->loadHelpersFromPath($path);
    }
  }

  /**
   * Load all PHP files from a directory recursively
   *
   * @param string $path Directory path
   * @return void
   */
  private function loadHelpersFromPath(string $path): void
  {
    $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::LEAVES_ONLY,
    );

    // Batch load files for better performance
    foreach ($iterator as $file) {
      if ($file->isFile() && $file->getExtension() === 'php') {
        require_once $file->getPathname();
      }
    }
  }
}
