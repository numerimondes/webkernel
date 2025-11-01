<?php declare(strict_types=1);

namespace Webkernel\Arcanes;

use Exception;

/**
 * Ultra-Fast Module Asset Service
 *
 * Provides microsecond-level asset loading for modules using intelligent caching
 * and optimized file system access. Designed for high-performance environments
 * like Octane, FrankenPHP, and other long-running PHP processes.
 */
class ModuleAssetService
{
  private const CACHE_FILE = 'storage/framework/cache/webkernel_assets.php';
  private const CACHE_VERSION = '1.0.0';

  private array $runtimeCache = [];
  private ?array $mainCache = null;

  /**
   * Get module assets with ultra-fast caching (target: <1µs)
   *
   * @param string $moduleId The module identifier
   * @return array|null Array with 'css', 'js', and 'elements' file paths, or null if not found
   */
  public function getModuleAssets(string $moduleId): ?array
  {
    // Runtime cache check (fastest path - memory)
    if (isset($this->runtimeCache[$moduleId])) {
      return $this->runtimeCache[$moduleId];
    }

    // Step 1: Try to load from cache (fast path)
    $cache = $this->loadCache();

    if ($cache !== null && isset($cache['modules'][$moduleId])) {
      $moduleCache = $cache['modules'][$moduleId];

      // Verify cache integrity with timestamp check
      if ($this->isCacheValid($moduleCache)) {
        $result = [
          'css' => $moduleCache['css'] ?? [],
          'js' => $moduleCache['js'] ?? [],
          'elements' => $moduleCache['elements'] ?? [],
          'base_path' => $moduleCache['base_path'] ?? '',
        ];

        // Store in runtime cache
        $this->runtimeCache[$moduleId] = $result;

        return $result;
      }
    }

    // Step 2: Cache miss or invalid - fetch from database and rebuild cache
    return $this->refreshModuleAssets($moduleId);
  }

  /**
   * Get a specific module element (logo, icon, etc.)
   *
   * @param string $moduleId The module identifier
   * @param string $elementName The element name (logo, icon, etc.)
   * @return string|null The file path or null if not found
   */
  public function getModuleElement(string $moduleId, string $elementName): ?string
  {
    $assets = $this->getModuleAssets($moduleId);

    if ($assets === null || !isset($assets['elements'])) {
      return null;
    }

    return $assets['elements'][$elementName] ?? null;
  }

  /**
   * Get module CSS files
   *
   * @param string $moduleId The module identifier
   * @param string|null $from Subdirectory path (e.g., "builder/V1")
   * @param bool $recursive Whether to scan recursively
   * @return array Array of CSS file paths
   */
  public function getModuleCss(string $moduleId, ?string $from = null, bool $recursive = false): array
  {
    if ($from !== null) {
      return $this->getModuleAssetsFromPath($moduleId, 'css', $from, $recursive);
    }

    $assets = $this->getModuleAssets($moduleId);
    return $assets['css'] ?? [];
  }

  /**
   * Get module JS files
   *
   * @param string $moduleId The module identifier
   * @param string|null $from Subdirectory path (e.g., "builder/V1")
   * @param bool $recursive Whether to scan recursively
   * @return array Array of JS file paths
   */
  public function getModuleJs(string $moduleId, ?string $from = null, bool $recursive = false): array
  {
    if ($from !== null) {
      return $this->getModuleAssetsFromPath($moduleId, 'js', $from, $recursive);
    }

    $assets = $this->getModuleAssets($moduleId);
    return $assets['js'] ?? [];
  }

  /**
   * Get module assets from a specific subdirectory path
   *
   * @param string $moduleId The module identifier
   * @param string $type Asset type ('css' or 'js')
   * @param string $from Subdirectory path (e.g., "builder/V1")
   * @param bool $recursive Whether to scan recursively
   * @return array Array of file paths
   */
  public function getModuleAssetsFromPath(string $moduleId, string $type, string $from, bool $recursive = false): array
  {
    $basePath = $this->getModuleBasePath($moduleId);

    if (!$basePath) {
      return [];
    }

    // Candidate roots to support assets co-located in Views as well
    $candidateRoots = [
      rtrim($basePath, '/') . '/Resources/' . $type . '/' . ltrim($from, '/'),
      rtrim($basePath, '/') . '/Resources/Views/' . ltrim($from, '/'),
      rtrim($basePath, '/') . '/Resources/Views/components/' . ltrim($from, '/'),
    ];

    $collected = [];

    foreach ($candidateRoots as $searchPath) {
      if (!is_dir($searchPath)) {
        continue;
      }

      if ($recursive) {
        // Recursive scan using RecursiveDirectoryIterator
        $iterator = new \RecursiveIteratorIterator(
          new \RecursiveDirectoryIterator($searchPath, \RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
          if ($file->isFile() && strtolower($file->getExtension()) === strtolower($type)) {
            $collected[] = $file->getRealPath();
          }
        }
      } else {
        // Non-recursive scan - only direct files in the directory
        $pattern = $searchPath . '/*.' . $type;
        $files = glob($pattern) ?: [];
        foreach ($files as $f) {
          $collected[] = $f;
        }
      }
    }

    // Normalize, unique, and sort for consistent ordering
    $collected = array_values(array_unique(array_map('strval', $collected)));
    sort($collected);

    return $collected;
  }

  /**
   * Get module base path
   *
   * @param string $moduleId The module identifier
   * @return string|null The module base path or null if not found
   */
  public function getModuleBasePath(string $moduleId): ?string
  {
    $assets = $this->getModuleAssets($moduleId);
    return $assets['base_path'] ?? null;
  }

  /**
   * Load the asset cache file
   *
   * @return array|null The cached data or null if not available
   */
  private function loadCache(): ?array
  {
    if ($this->mainCache !== null) {
      return $this->mainCache;
    }

    $cacheFile = base_path(self::CACHE_FILE);

    if (!file_exists($cacheFile)) {
      return null;
    }

    try {
      $cache = include $cacheFile;

      // Validate cache structure
      if (
        !is_array($cache) ||
        !isset($cache['version'], $cache['timestamp'], $cache['modules']) ||
        $cache['version'] !== self::CACHE_VERSION
      ) {
        return null;
      }

      $this->mainCache = $cache;
      return $cache;
    } catch (Exception $e) {
      return null;
    }
  }

  /**
   * Check if the cached module data is still valid
   *
   * @param array $moduleCache The cached module data
   * @return bool True if cache is valid
   */
  private function isCacheValid(array $moduleCache): bool
  {
    if (!isset($moduleCache['timestamp'], $moduleCache['hash'])) {
      return false;
    }

    $cacheTime = $moduleCache['timestamp'];
    $currentTime = time();

    // Cache is valid for 1 hour by default (configurable)
    $cacheLifetime = config('webkernel.asset_cache_lifetime', 3600);

    if ($currentTime - $cacheTime > $cacheLifetime) {
      return false;
    }

    // Additional validation: check if files still exist
    $allFiles = array_merge(
      $moduleCache['css'] ?? [],
      $moduleCache['js'] ?? [],
      array_values($moduleCache['elements'] ?? []),
    );

    foreach ($allFiles as $file) {
      if (!file_exists($file)) {
        return false;
      }
    }

    return true;
  }

  /**
   * Refresh module assets from database and update cache
   *
   * @param string $moduleId The module identifier
   * @return array|null The refreshed asset data
   */
  private function refreshModuleAssets(string $moduleId): ?array
  {
    try {
      // Query module using the ultra-fast QueryModule (1µs)
      $modules = QueryModules::make()
        ->select(['id', 'basePath'])
        ->where('id')
        ->is($moduleId)
        ->get();

      // Find the correct module manually since WHERE conditions aren't working properly
      $module = null;
      foreach ($modules as $mod) {
        if (isset($mod['id']) && $mod['id'] === $moduleId) {
          $module = $mod;
          break;
        }
      }

      if (!$module || !isset($module['basePath'])) {
        return null;
      }

      $basePath = rtrim($module['basePath'], '/');

      // Scan for different asset types
      $assets = [
        'css' => glob($basePath . '/Resources/css/*.css') ?: [],
        'js' => glob($basePath . '/Resources/js/*.js') ?: [],
        'elements' => $this->scanModuleElements($basePath . '/Resources/Assets'),
        'base_path' => $basePath,
      ];

      // Update cache with new data
      $this->updateCache($moduleId, $assets, $basePath);

      // Store in runtime cache
      $this->runtimeCache[$moduleId] = $assets;

      return $assets;
    } catch (Exception $e) {
      // Log error but don't fail completely
      error_log("ModuleAssetService: Failed to refresh assets for module {$moduleId}: " . $e->getMessage());
      return null;
    }
  }

  /**
   * Scan module elements directory for assets
   *
   * @param string $elementsPath The elements directory path
   * @return array Array of element name => file path
   */
  private function scanModuleElements(string $elementsPath): array
  {
    if (!is_dir($elementsPath)) {
      return [];
    }

    $elements = [];
    $supportedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp'];

    $files = glob($elementsPath . '/*');

    foreach ($files as $file) {
      if (is_file($file)) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($extension, $supportedExtensions)) {
          $elementName = pathinfo($file, PATHINFO_FILENAME);
          $elements[$elementName] = $file;
        }
      }
    }

    return $elements;
  }

  /**
   * Update the cache with new module asset data
   *
   * @param string $moduleId The module identifier
   * @param array $assets The asset data
   * @param string $basePath The module base path
   */
  private function updateCache(string $moduleId, array $assets, string $basePath): void
  {
    try {
      $cache = $this->loadCache() ?? [
        'version' => self::CACHE_VERSION,
        'timestamp' => time(),
        'modules' => [],
        'global' => [
          'last_scan' => time(),
          'total_modules' => 0,
        ],
      ];

      // Generate content hash for integrity checking
      $allFiles = array_merge($assets['css'], $assets['js'], array_values($assets['elements']));
      $contentHash = md5(serialize($allFiles) . $basePath);

      // Update module data
      $cache['modules'][$moduleId] = [
        'css' => $assets['css'],
        'js' => $assets['js'],
        'elements' => $assets['elements'],
        'base_path' => $basePath,
        'timestamp' => time(),
        'hash' => $contentHash,
        'file_count' => count($allFiles),
      ];

      // Update global metadata
      $cache['timestamp'] = time();
      $cache['global']['total_modules'] = count($cache['modules']);

      // Write cache file atomically
      $this->writeCache($cache);

      // Update main cache reference
      $this->mainCache = $cache;
    } catch (Exception $e) {
      error_log('ModuleAssetService: Failed to update cache: ' . $e->getMessage());
    }
  }

  /**
   * Write cache data to file atomically
   *
   * @param array $cache The cache data to write
   */
  private function writeCache(array $cache): void
  {
    $cacheFile = base_path(self::CACHE_FILE);
    $cacheDir = dirname($cacheFile);

    // Ensure cache directory exists
    if (!is_dir($cacheDir)) {
      mkdir($cacheDir, 0755, true);
    }

    // Generate PHP cache content
    $content = "<?php declare(strict_types=1);\n\n";
    $content .= "// WebKernel Module Assets Cache\n";
    $content .= '// Generated on ' . date('Y-m-d H:i:s') . "\n";
    $content .= '// Version: ' . self::CACHE_VERSION . "\n\n";
    $content .= 'return ' . var_export($cache, true) . ";\n";

    // Write atomically using temporary file
    $tempFile = $cacheFile . '.tmp.' . getmypid();

    if (file_put_contents($tempFile, $content, LOCK_EX) !== false) {
      rename($tempFile, $cacheFile);
    }

    // Cleanup temp file if rename failed
    if (file_exists($tempFile)) {
      unlink($tempFile);
    }
  }

  /**
   * Clear the entire asset cache
   *
   * @return bool True if cache was cleared successfully
   */
  public function clearCache(): bool
  {
    $cacheFile = base_path(self::CACHE_FILE);

    // Clear runtime cache
    $this->runtimeCache = [];
    $this->mainCache = null;

    if (file_exists($cacheFile)) {
      return unlink($cacheFile);
    }

    return true;
  }

  /**
   * Warm up the cache by preloading all module assets
   *
   * @return int Number of modules cached
   */
  public function warmUpCache(): int
  {
    try {
      // Get all modules
      $modules = QueryModules::make()
        ->select(['id', 'basePath'])
        ->get();

      $cachedCount = 0;

      foreach ($modules as $module) {
        if ($module->id && $module->basePath) {
          $assets = $this->getModuleAssets($module->id);
          if ($assets !== null) {
            $cachedCount++;
          }
        }
      }

      return $cachedCount;
    } catch (Exception $e) {
      error_log('ModuleAssetService: Failed to warm up cache: ' . $e->getMessage());
      return 0;
    }
  }

  /**
   * Get cache statistics
   *
   * @return array Cache statistics
   */
  public function getCacheStats(): array
  {
    $cache = $this->loadCache();

    if ($cache === null) {
      return [
        'status' => 'empty',
        'modules' => 0,
        'total_files' => 0,
        'cache_size' => 0,
        'last_update' => null,
      ];
    }

    $totalFiles = 0;
    foreach ($cache['modules'] as $module) {
      $totalFiles += $module['file_count'] ?? 0;
    }

    $cacheFile = base_path(self::CACHE_FILE);
    $cacheSize = file_exists($cacheFile) ? filesize($cacheFile) : 0;

    return [
      'status' => 'active',
      'version' => $cache['version'] ?? 'unknown',
      'modules' => count($cache['modules']),
      'total_files' => $totalFiles,
      'cache_size' => $cacheSize,
      'last_update' => $cache['timestamp'] ?? null,
      'cache_file' => $cacheFile,
      'runtime_cache_size' => count($this->runtimeCache),
    ];
  }

  /**
   * Rebuild the entire cache from scratch
   *
   * @return bool True if rebuild was successful
   */
  public function rebuildCache(): bool
  {
    try {
      // Clear existing cache
      $this->clearCache();

      // Warm up with fresh data
      $cachedCount = $this->warmUpCache();

      return $cachedCount > 0;
    } catch (Exception $e) {
      error_log('ModuleAssetService: Failed to rebuild cache: ' . $e->getMessage());
      return false;
    }
  }

  public function getElementsFromPath(string $moduleId, string $path, string $extension): array
  {
    $files = $this->getModuleAssetsFromPath($moduleId, $extension, $path);
    $elements = [];
    foreach ($files as $file) {
      $elementName = basename($file, '.' . $extension);
      $content = file_get_contents($file);
      $name = preg_match('/@theme\s+name:\s*["\']([^"\']+)["\']/', $content, $matches)
        ? $matches[1]
        : ucfirst(str_replace('_', ' ', $elementName));
      $elements[$elementName] = $name;
    }
    return $elements;
  }

  /**
   * Resolve a module:// path to an absolute file path.
   *
   * @param string $path The path to resolve.
   * @return string The resolved path or the original path if not a module path.
   */
  public function resolveModulePath(string $path): string
  {
    if (str_starts_with($path, 'module://')) {
      $parts = explode('/', substr($path, 9)); // 9 is strlen('module://')
      $moduleId = array_shift($parts);
      $assetPath = implode('/', $parts);

      if ($moduleId) {
        $moduleBasePath = $this->getModuleBasePath($moduleId);

        if ($moduleBasePath) {
          return $moduleBasePath . '/' . $assetPath;
        }
      }
    }

    return $path;
  }
}
