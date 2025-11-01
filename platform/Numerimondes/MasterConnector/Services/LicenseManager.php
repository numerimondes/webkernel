<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webkernel\Aptitudes\Platform\Core\Services\LicenseTokenService;
use Platform\Numerimondes\MasterConnector\Models\License;
use Platform\Numerimondes\MasterConnector\Models\Module;

class LicenseManager
{
  public function __construct(private LicenseTokenService $tokenService) {}

  /**
   * Create a new license with token generation
   */
  public function createLicense(
    string $domain,
    array $moduleIds = [],
    ?\DateTimeInterface $expiresAt = null,
    ?int $organizationId = null,
    array $metadata = [],
  ): array {
    return DB::transaction(function () use ($domain, $moduleIds, $expiresAt, $organizationId, $metadata) {
      // Generate token
      $token = $this->tokenService->generateToken();
      $tokenHash = hash('sha256', $token);

      // Create license
      $license = License::create([
        'token_hash' => $tokenHash,
        'domain' => $domain,
        'status' => 'active',
        'expires_at' => $expiresAt,
        'organization_id' => $organizationId,
        'metadata' => $metadata,
      ]);

      // Attach modules
      if (!empty($moduleIds)) {
        $this->attachModules($license, $moduleIds);
      }

      Log::info('License created.', [
        'license_id' => $license->id,
        'domain' => $domain,
        'modules_count' => count($moduleIds),
      ]);

      // Return token (one-time display)
      return [
        'license' => $license,
        'token' => $token, // IMPORTANT: Only shown once
      ];
    });
  }

  /**
   * Validate a license by token and domain
   */
  public function validateLicense(string $token, string $domain, string $ipAddress): array
  {
    $tokenHash = hash('sha256', $token);

    $license = License::where('token_hash', $tokenHash)->where('domain', $domain)->first();

    if (!$license) {
      Log::warning('License validation failed: not found.', [
        'domain' => $domain,
        'ip' => $ipAddress,
        'token_hash_partial' => substr($tokenHash, 0, 8),
      ]);

      return [
        'valid' => false,
        'error' => 'Invalid license or domain mismatch.',
      ];
    }

    if (!$license->isValid()) {
      Log::warning('License validation failed: invalid status.', [
        'license_id' => $license->id,
        'status' => $license->status,
        'expires_at' => $license->expires_at?->toISOString(),
      ]);

      return [
        'valid' => false,
        'error' => 'License is ' . $license->status . '.',
      ];
    }

    // Update validation timestamp
    $license->touchValidation($ipAddress);

    // Get authorized modules
    $modules = $license
      ->modules()
      ->active()
      ->get()
      ->map(function ($module) {
        return [
          'id' => $module->id,
          'identifier' => $module->identifier,
          'name' => $module->name,
          'version' => $module->version,
        ];
      });

    return [
      'valid' => true,
      'license_id' => $license->id,
      'expires_at' => $license->expires_at?->toISOString(),
      'status' => $license->status,
      'modules' => $modules->toArray(),
    ];
  }

  /**
   * Attach modules to a license
   */
  public function attachModules(License $license, array $moduleIds): void
  {
    $validModules = Module::active()->whereIn('id', $moduleIds)->pluck('id');

    $license->modules()->syncWithoutDetaching(
      $validModules
        ->mapWithKeys(
          fn($id) => [
            $id => ['granted_at' => now()],
          ],
        )
        ->toArray(),
    );

    Log::info('Modules attached to license.', [
      'license_id' => $license->id,
      'modules_count' => $validModules->count(),
    ]);
  }

  /**
   * Revoke modules from a license
   */
  public function revokeModules(License $license, array $moduleIds): void
  {
    $license
      ->allModules()
      ->wherePivotIn('module_id', $moduleIds)
      ->wherePivotNull('revoked_at')
      ->each(function ($module) {
        $module->pivot->revoke();
      });

    Log::info('Modules revoked from license.', [
      'license_id' => $license->id,
      'modules_count' => count($moduleIds),
    ]);
  }

  /**
   * Revoke a license entirely
   */
  public function revokeLicense(License $license, string $reason = null): void
  {
    $license->revoke($reason);

    // Optionally revoke all module access
    $license->allModules()->wherePivotNull('revoked_at')->each(fn($module) => $module->pivot->revoke());

    Log::warning('License fully revoked.', [
      'license_id' => $license->id,
      'reason' => $reason,
    ]);
  }

  /**
   * Extend license expiration
   */
  public function extendLicense(License $license, \DateTimeInterface $newExpiresAt): void
  {
    $license->update(['expires_at' => $newExpiresAt]);

    Log::info('License extended.', [
      'license_id' => $license->id,
      'new_expires_at' => $newExpiresAt->format('Y-m-d'),
    ]);
  }

  /**
   * Get license statistics
   */
  public function getStatistics(): array
  {
    return [
      'total' => License::count(),
      'active' => License::active()->count(),
      'expired' => License::expired()->count(),
      'revoked' => License::revoked()->count(),
    ];
  }
}
