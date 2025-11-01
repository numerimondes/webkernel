<?php declare(strict_types=1);

namespace Webkernel\Arcanes\Support\Resources;

/**
 * Manages caching for resource discovery
 * Provides centralized cache control with tagging support
 */
class ResourceCacheManager
{
  private const CACHE_KEY = 'filament.discovered.resources';
  private const CACHE_TAG = 'filament_resources';
  private int $defaultDuration;

  /**
   * Create a new cache manager
   *
   * @param int $defaultDuration Default cache duration in seconds
   */
  public function __construct(int $defaultDuration = 3600)
  {
    $this->defaultDuration = $defaultDuration;
  }

  /**
   * Get cached resources or execute callback
   *
   * @param callable $callback
   * @param int|null $duration Override default duration
   * @return array<string, ModuleResourceData>
   */
  public function remember(callable $callback, ?int $duration = null): array
  {
    $duration = $duration ?? $this->defaultDuration;

    if ($duration <= 0) {
      return $callback();
    }

    if ($this->supportsTags()) {
      return cache()
        ->tags([self::CACHE_TAG])
        ->remember(self::CACHE_KEY, $duration, $callback);
    }

    return cache()->remember(self::CACHE_KEY, $duration, $callback);
  }

  /**
   * Clear resource cache
   *
   * @return void
   */
  public function clear(): void
  {
    if ($this->supportsTags()) {
      cache()
        ->tags([self::CACHE_TAG])
        ->flush();
    } else {
      cache()->forget(self::CACHE_KEY);
    }
  }

  /**
   * Check if cached data exists
   *
   * @return bool
   */
  public function has(): bool
  {
    if ($this->supportsTags()) {
      return cache()
        ->tags([self::CACHE_TAG])
        ->has(self::CACHE_KEY);
    }

    return cache()->has(self::CACHE_KEY);
  }

  /**
   * Get cached data without fallback
   *
   * @return array<string, ModuleResourceData>|null
   */
  public function get(): ?array
  {
    if ($this->supportsTags()) {
      return cache()
        ->tags([self::CACHE_TAG])
        ->get(self::CACHE_KEY);
    }

    return cache()->get(self::CACHE_KEY);
  }

  /**
   * Set cached data manually
   *
   * @param array<string, ModuleResourceData> $data
   * @param int|null $duration
   * @return void
   */
  public function put(array $data, ?int $duration = null): void
  {
    $duration = $duration ?? $this->defaultDuration;

    if ($this->supportsTags()) {
      cache()
        ->tags([self::CACHE_TAG])
        ->put(self::CACHE_KEY, $data, $duration);
    } else {
      cache()->put(self::CACHE_KEY, $data, $duration);
    }
  }

  /**
   * Check if cache driver supports tagging
   *
   * @return bool
   */
  private function supportsTags(): bool
  {
    try {
      $driver = config('cache.default');
      $taggableDrivers = ['redis', 'memcached', 'dynamodb', 'octane'];

      return in_array($driver, $taggableDrivers, true);
    } catch (\Exception $e) {
      return false;
    }
  }

  /**
   * Get cache statistics
   *
   * @return array<string, mixed>
   */
  public function getStats(): array
  {
    return [
      'cache_key' => self::CACHE_KEY,
      'cache_tag' => self::CACHE_TAG,
      'default_duration' => $this->defaultDuration,
      'has_cache' => $this->has(),
      'supports_tags' => $this->supportsTags(),
      'driver' => config('cache.default'),
    ];
  }
}
