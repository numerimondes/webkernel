<?php

namespace Webkernel\Aptitudes\Platform\Connector\Services;

use Illuminate\Support\Facades\Log;
use Webkernel\Aptitudes\Platform\Core\Models\LocalLicense;
use Webkernel\Aptitudes\Platform\Core\Services\LicenseCacheService;
use Webkernel\Aptitudes\Platform\Core\Services\LicenseTokenService;

class SyncService
{
  public function __construct(
    private MasterApiClient $apiClient,
    private LicenseTokenService $tokenService,
    private LicenseCacheService $cacheService,
  ) {}

  /**
   * Perform full sync: validate → list → check updates → update cache.
   */
  public function sync(LocalLicense $license): array
  {
    try {
      $token = $license->getDecryptedTokenAttribute();
      if (!$token) {
        throw new \RuntimeException('No valid token available.');
      }

      // Step 1: Validate
      $validation = $this->apiClient->validateLicense($token, $license->domain);
      if ($validation['status'] !== 'active') {
        throw new \RuntimeException('License not active.');
      }

      // Step 2: List modules
      $modules = $this->apiClient->listModules($token);

      // Step 3: Check updates (mock current for MVP)
      $currentModules = []; // Fetch from cache or Arcanes scan
      $updates = $this->apiClient->checkUpdates($token, $currentModules);

      // Update local license and cache
      $license->update([
        'status' => $validation['status'],
        'expires_at' => $validation['expires_at'] ?? null,
        'last_synced_at' => now(),
      ]);
      $this->cacheService->updateCache($license);

      Log::info('Sync completed successfully.', [
        'modules_count' => count($modules),
        'updates_count' => count($updates),
      ]);

      return [
        'success' => true,
        'modules' => $modules,
        'updates' => $updates,
        'synced_at' => now()->toISOString(),
      ];
    } catch (\Exception $e) {
      Log::error('Sync failed: ' . $e->getMessage());
      return [
        'success' => false,
        'error' => $e->getMessage(),
      ];
    }
  }
}
