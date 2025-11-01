<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Platform\Numerimondes\MasterConnector\Models\License;
use Platform\Numerimondes\MasterConnector\Models\Module;

class ModuleCatalog
{
  private const CACHE_KEY = 'numerimondes.catalog';
  private const CACHE_TTL = 3600; // 1 hour

  /**
   * Get modules authorized for a license
   */
  public function getAuthorizedModules(License $license): Collection
  {
    return Cache::remember(
      self::CACHE_KEY . ".license.{$license->id}",
      self::CACHE_TTL,
      fn() => $license->modules()->active()->get()->map(fn($module) => $this->formatModuleForApi($module)),
    );
  }

  /**
   * Get all active modules (admin view)
   */
  public function getAllModules(): Collection
  {
    return Cache::remember(
      self::CACHE_KEY . '.all',
      self::CACHE_TTL,
      fn() => Module::active()
        ->orderBy('identifier')
        ->orderByDesc('version')
        ->get()
        ->map(fn($module) => $this->formatModuleForApi($module)),
    );
  }

  /**
   * Get latest version of each module
   */
  public function getLatestModules(): Collection
  {
    return Module::active()
      ->get()
      ->groupBy('identifier')
      ->map(function ($versions) {
        return $versions->sortByDesc(fn($m) => $this->versionToNumber($m->version))->first();
      })
      ->values()
      ->map(fn($module) => $this->formatModuleForApi($module));
  }

  /**
   * Check for updates
   */
  public function checkUpdates(array $currentModules): array
  {
    $updates = [];

    foreach ($currentModules as $current) {
      $latestModule = Module::active()->where('identifier', $current['identifier'])->orderByDesc('version')->first();

      if (!$latestModule) {
        continue;
      }

      if (version_compare($latestModule->version, $current['version'], '>')) {
        $updates[] = [
          'identifier' => $latestModule->identifier,
          'current_version' => $current['version'],
          'new_version' => $latestModule->version,
          'hash' => $latestModule->hash,
          'size' => $latestModule->file_size,
          'formatted_size' => $latestModule->formatted_size,
          'changelog' => $latestModule->getChangelog(),
          'type' => $latestModule->getUpdateType($current['version']),
        ];
      }
    }

    return $updates;
  }

  /**
   * Get checksum for a module
   */
  public function getChecksum(string $identifier, ?string $version = null): ?array
  {
    $query = Module::active()->where('identifier', $identifier);

    if ($version) {
      $query->where('version', $version);
    } else {
      $query->orderByDesc('version');
    }

    $module = $query->first();

    if (!$module) {
      return null;
    }

    return [
      'identifier' => $module->identifier,
      'version' => $module->version,
      'hash' => $module->hash,
    ];
  }

  /**
   * Get module download path
   */
  public function getModuleDownloadPath(int $moduleId): ?string
  {
    $module = Module::active()->find($moduleId);

    if (!$module) {
      return null;
    }

    $disk = Storage::disk(config('master-connector.storage_disk'));

    if (!$disk->exists($module->zip_path)) {
      Log::error('Module ZIP file not found.', [
        'module_id' => $moduleId,
        'path' => $module->zip_path,
      ]);
      return null;
    }

    return $disk->path($module->zip_path);
  }

  /**
   * Invalidate catalog cache
   */
  public function invalidateCache(?int $licenseId = null): void
  {
    if ($licenseId) {
      Cache::forget(self::CACHE_KEY . ".license.{$licenseId}");
    } else {
      Cache::flush(); // Or use tags in production
    }

    Log::info('Catalog cache invalidated.', ['license_id' => $licenseId]);
  }

  /**
   * Format module for API response
   */
  private function formatModuleForApi(Module $module): array
  {
    return [
      'id' => $module->id,
      'identifier' => $module->identifier,
      'name' => $module->name,
      'version' => $module->version,
      'description' => $module->description,
      'size' => $module->file_size,
      'formatted_size' => $module->formatted_size,
      'hash' => $module->hash,
      'changelog' => $module->getChangelog(),
      'dependencies' => $module->getDependencies(),
    ];
  }

  /**
   * Convert semantic version to comparable number
   */
  private function versionToNumber(string $version): int
  {
    $parts = explode('.', $version);
    return (int) (($parts[0] ?? 0) * 10000 + ($parts[1] ?? 0) * 100 + ($parts[2] ?? 0));
  }

  /**
   * Get module statistics
   */
  public function getStatistics(): array
  {
    return [
      'total' => Module::count(),
      'active' => Module::active()->count(),
      'unique_identifiers' => Module::active()->distinct('identifier')->count(),
      'total_size' => Module::active()->sum('file_size'),
    ];
  }
}
