<?php

namespace Webkernel\Aptitudes\Platform\Core\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Webkernel\Aptitudes\Platform\Core\Models\LocalLicense;

class LicenseCacheService
{
  private const CACHE_KEY = 'numerimondes.license_info';
  private const CACHE_TTL = 3600; // 1 hour
  private const STALE_WARNING_DAYS = 7;

  /**
   * Update the license cache from the local model.
   *
   * @param LocalLicense $license
   * @return void
   */
  public function updateCache(LocalLicense $license): void
  {
    $data = [
      'token_encrypted' => $license->token_encrypted,
      'domain' => $license->domain,
      'last_synced_at' => $license->last_synced_at?->toISOString(),
      'expires_at' => $license->expires_at?->toISOString(),
      'status' => $license->status,
      'synced_at' => now()->toISOString(),
    ];

    Cache::put(self::CACHE_KEY, $data, self::CACHE_TTL);
    Log::info('License cache updated.', ['synced_at' => $data['synced_at']]);
  }

  /**
   * Get cached license info, or null if expired/stale.
   *
   * @return array|null
   */
  public function getCache(): ?array
  {
    $cached = Cache::get(self::CACHE_KEY);

    if (!$cached) {
      return null;
    }

    // Check for staleness
    $syncedAt = $cached['synced_at'] ?? null;
    if ($syncedAt && now()->diffInDays($syncedAt) > self::STALE_WARNING_DAYS) {
      Log::warning('License cache is stale (>7 days without sync).');
      // Optionally emit event for UI warning
    }

    return $cached;
  }

  /**
   * Invalidate the cache.
   *
   * @return void
   */
  public function invalidateCache(): void
  {
    Cache::forget(self::CACHE_KEY);
    Log::info('License cache invalidated.');
  }

  /**
   * Check if cache is valid for degraded operation.
   *
   * @return bool
   */
  public function isCacheValid(): bool
  {
    $cache = $this->getCache();
    return $cache !== null && ($cache['status'] ?? '') === 'active';
  }
}
