<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Runtime;

/**
 * Manages ultra-fast caching with config-aware invalidation
 * Optimized for sub-millisecond performance
 */
class CacheManager
{
  private static ?array $configHashCache = null;
  private static ?array $directoryHashesCache = null;

  public function __construct(private string $cachePath) {}

  /**
   * Load module registry from cache
   *
   * @param array $moduleRegistry Reference to populate
   * @return bool True if cache was loaded successfully
   */
  public function loadFromCache(array &$moduleRegistry): bool
  {
    if (!file_exists($this->cachePath)) {
      return false;
    }

    // Use opcache if available for faster loading
    $cache = include $this->cachePath;

    if (!is_array($cache)) {
      return false;
    }

    if (!$this->isCacheValid($cache)) {
      return false;
    }

    $moduleRegistry = $cache['modules'];
    return true;
  }

  /**
   * Save module registry to cache
   *
   * @param array $moduleRegistry Module data to cache
   * @return void
   */
  public function saveToCache(array $moduleRegistry): void
  {
    $cacheDir = dirname($this->cachePath);
    if (!is_dir($cacheDir)) {
      mkdir($cacheDir, 0755, true);
    }

    $configHash = $this->getConfigHash();
    $directoryHashes = $this->getDirectoryHashes();

    $cache = [
      'timestamp' => time(),
      'config_hash' => $configHash,
      'hashes' => $directoryHashes,
      'modules' => $moduleRegistry,
    ];

    // Use var_export for opcache optimization
    $cacheContent = "<?php\nreturn " . var_export($cache, true) . ';';
    file_put_contents($this->cachePath, $cacheContent, LOCK_EX);

    if (function_exists('opcache_compile_file')) {
      opcache_compile_file($this->cachePath);
    }
  }

  /**
   * Clear all caches
   *
   * @return void
   */
  public function clearCache(): void
  {
    if (file_exists($this->cachePath)) {
      unlink($this->cachePath);

      if (function_exists('opcache_invalidate')) {
        opcache_invalidate($this->cachePath, true);
      }
    }

    // Clear static caches
    self::$configHashCache = null;
    self::$directoryHashesCache = null;
  }

  /**
   * Check if cache is valid
   *
   * @param array $cache Cache data
   * @return bool
   */
  private function isCacheValid(array $cache): bool
  {
    // Fast path: check config hash first (cheapest operation)
    $configHash = $this->getConfigHash();
    if (($cache['config_hash'] ?? '') !== $configHash) {
      return false;
    }

    $config = config('webkernel-arcanes.discovery', []);
    $paths = $config['paths'] ?? [base_path('app')];

    // Use cached hashes for comparison
    $cachedHashes = $cache['hashes'] ?? [];

    foreach ($paths as $path) {
      if (!is_dir($path)) {
        continue;
      }

      $cachedHash = $cachedHashes[$path] ?? null;
      if ($cachedHash === null) {
        return false;
      }

      $currentHash = $this->getDirectoryHash($path);
      if ($currentHash !== $cachedHash) {
        return false;
      }
    }

    return true;
  }

  /**
   * Get configuration file hash (cached)
   *
   * @return string
   */
  private function getConfigHash(): string
  {
    if (self::$configHashCache !== null) {
      return self::$configHashCache['hash'];
    }

    $configFile = base_path('config/webkernel-arcanes.php');
    if (!file_exists($configFile)) {
      self::$configHashCache = ['hash' => ''];
      return '';
    }

    // Use clearstatcache for accurate file info
    clearstatcache(true, $configFile);
    $hash = md5(filemtime($configFile) . filesize($configFile));

    self::$configHashCache = ['hash' => $hash];
    return $hash;
  }

  /**
   * Get directory hashes for all configured paths
   *
   * @return array<string, string>
   */
  private function getDirectoryHashes(): array
  {
    if (self::$directoryHashesCache !== null) {
      return self::$directoryHashesCache;
    }

    $config = config('webkernel-arcanes.discovery', []);
    $paths = $config['paths'] ?? [base_path('app')];

    $hashes = [];
    foreach ($paths as $path) {
      if (is_dir($path)) {
        $hashes[$path] = $this->getDirectoryHash($path);
      }
    }

    self::$directoryHashesCache = $hashes;
    return $hashes;
  }

  /**
   * Calculate hash for a directory (optimized)
   *
   * @param string $path Directory path
   * @return string
   */
  private function getDirectoryHash(string $path): string
  {
    $hash = '';

    // Use SPL for faster iteration
    $iterator = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator(
        $path,
        \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::FOLLOW_SYMLINKS,
      ),
      \RecursiveIteratorIterator::LEAVES_ONLY,
    );

    // Pre-allocate array for better memory performance
    $files = [];
    foreach ($iterator as $file) {
      if ($file->isFile() && $file->getExtension() === 'php') {
        // Store pathname and mtime together to avoid second stat call
        $files[] = $file->getPathname() . ':' . $file->getMTime();
      }
    }

    // Sort for consistent hashing
    sort($files);
    $hash = implode('|', $files);

    return md5($hash);
  }
}
