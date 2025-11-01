<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Runtime;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * Scans filesystem for WebkernelApp modules
 * Optimized for sub-millisecond performance
 */
class ModuleScanner
{
  private static ?array $configCache = null;

  public function __construct(private CacheManager $cache, private PathResolver $pathResolver) {}

  /**
   * Scan configured paths for modules
   *
   * @return array<string, array>
   */
  public function scan(): array
  {
    // Cache config to avoid repeated calls
    if (self::$configCache === null) {
      self::$configCache = config('webkernel-arcanes.discovery', []);
    }

    $paths = self::$configCache['paths'] ?? [base_path('app')];
    $excludePatterns = self::$configCache['exclude_patterns'] ?? [];

    $modules = [];

    foreach ($paths as $path) {
      if (is_dir($path)) {
        $modules = array_merge($modules, $this->scanDirectory($path, $excludePatterns));
      }
    }

    return $modules;
  }

  /**
   * Scan a directory for module classes
   *
   * @param string $path Directory path
   * @param array<string> $excludePatterns Patterns to exclude
   * @return array<string, array>
   */
  private function scanDirectory(string $path, array $excludePatterns): array
  {
    $modules = [];

    $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator(
        $path,
        RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS,
      ),
      RecursiveIteratorIterator::LEAVES_ONLY,
    );

    $phpFiles = new RegexIterator($iterator, '/\.php$/');

    foreach ($phpFiles as $file) {
      $filePath = $file->getPathname();

      // Fast exclusion check
      if ($excludePatterns && $this->isExcluded($filePath, $excludePatterns)) {
        continue;
      }

      $moduleData = $this->extractModuleData($filePath);
      if ($moduleData) {
        $modules[$moduleData['id']] = $moduleData;
      }
    }

    return $modules;
  }

  /**
   * Check if a file path should be excluded (optimized)
   *
   * @param string $filePath File path to check
   * @param array<string> $excludePatterns Exclusion patterns
   * @return bool
   */
  private function isExcluded(string $filePath, array $excludePatterns): bool
  {
    foreach ($excludePatterns as $pattern) {
      if (str_contains($filePath, $pattern)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Extract module data from a PHP file (optimized)
   *
   * @param string $filePath Path to PHP file
   * @return array|null Module data or null if not a valid module
   */
  private function extractModuleData(string $filePath): ?array
  {
    $content = file_get_contents($filePath);

    // Fast rejection: check for WebkernelApp first
    if (!str_contains($content, 'WebkernelApp')) {
      return null;
    }

    // More precise check
    if (!preg_match('/class\s+(\w+)\s+extends\s+.*WebkernelApp/m', $content)) {
      return null;
    }

    // Extract namespace and class name in one pass
    $namespace = null;
    $className = null;

    if (preg_match('/^\s*namespace\s+([^;]+);/m', $content, $nsMatch)) {
      $namespace = trim($nsMatch[1]);
    }
    if (preg_match('/class\s+(\w+)\s+extends\s+.*WebkernelApp/m', $content, $classMatch)) {
      $className = trim($classMatch[1]);
    }

    if (!$namespace || !$className) {
      return null;
    }

    $fullClassName = $namespace . '\\' . $className;
    $basePath = dirname($filePath);

    try {
      // Instantiate module to get config
      $module = new $fullClassName(app());
      $config = $module->getModuleConfig();

      // Precompile into opcache
      if (function_exists('opcache_compile_file')) {
        opcache_compile_file($filePath);
      }

      // Build module data array
      return [
        'id' => $config->id,
        'name' => $config->name,
        'description' => $config->description,
        'version' => $config->version,
        'class' => $fullClassName,
        'namespace' => $namespace,
        'viewNamespace' => $config->viewNamespace ?: strtolower($config->id),
        'instantiated' => false,
        'path' => $filePath,
        'basePath' => $basePath,
        'viewsPath' => $this->checkPath($module->getViewsPath()),
        'langPath' => $this->checkPath($module->getLangPath()),
        'helpersPath' => $this->checkPath($module->getHelpersPath()),
        'consolePath' => $this->checkPath($module->getConsolePath()),
        'commandsPath' => $this->checkPath($module->getCommandsPath()),
        'routesPath' => $this->checkPath($module->getRoutesPath()),
        'migrationsPath' => $this->checkPath($module->getMigrationsPath()),
        'configPath' => $this->checkPath($module->getConfigPath()),
      ];
    } catch (\Throwable) {
      return null;
    }
  }

  /**
   * Check if path exists and return it or null
   *
   * @param string $path Path to check
   * @return string|null
   */
  private function checkPath(string $path): ?string
  {
    return is_dir($path) ? $path : null;
  }
}
