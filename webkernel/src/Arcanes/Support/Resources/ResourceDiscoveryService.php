<?php declare(strict_types=1);
namespace Webkernel\Arcanes\Support\Resources;

use Webkernel\Arcanes\Support\Resources\{
  ModuleResourceData,
  PermissionExtractor,
  ResourceCacheManager,
  ResourceDiscovery,
  ResourceMatcher,
  ResourceNameFormatter,
};

/**
 * Main service for resource discovery and matching
 * Orchestrates all discovery components with caching support
 */
class ResourceDiscoveryService
{
  private ResourceDiscovery $resourceDiscovery;
  private ResourceMatcher $resourceMatcher;
  private PermissionExtractor $permissionExtractor;
  private ResourceCacheManager $cacheManager;
  private bool $enableLogging;

  /**
   * Create a new resource discovery service
   *
   * @param bool $enableLogging Enable debug logging
   * @param int $cacheDuration Cache duration in seconds (0 to disable)
   */
  public function __construct(bool $enableLogging = false, int $cacheDuration = 3600)
  {
    $this->enableLogging = $enableLogging;
    $this->resourceDiscovery = new ResourceDiscovery($enableLogging);
    $this->resourceMatcher = new ResourceMatcher($enableLogging);
    $this->permissionExtractor = new PermissionExtractor($enableLogging);
    $this->cacheManager = new ResourceCacheManager($cacheDuration);
  }

  /**
   * Discover all resources grouped by modules
   *
   * @return array<string, ModuleResourceData>
   */
  public function discoverResourcesByModule(): array
  {
    return $this->cacheManager->remember(fn(): array => $this->performDiscovery());
  }

  /**
   * Perform the actual discovery process
   *
   * @return array<string, ModuleResourceData>
   */
  private function performDiscovery(): array
  {
    $modules = $this->loadModules();
    $resources = $this->resourceDiscovery->discoverResourcesWithMetadata();

    $matchResult = $this->resourceMatcher->matchResourcesToModules($resources, $modules);

    /** @var array<string, ModuleResourceData> $moduleResources */
    $moduleResources = $matchResult['matched'];

    /** @var array<int, string> $unmatchedClasses */
    $unmatchedClasses = $matchResult['unmatched'];

    $this->populateResourcePermissions($moduleResources, $resources);

    if (!empty($unmatchedClasses)) {
      $otherModule = $this->createOtherResourcesModule($unmatchedClasses, $resources);
      if ($otherModule->hasResources()) {
        $moduleResources['Other'] = $otherModule;
      }
    }

    foreach ($moduleResources as $moduleData) {
      $moduleData->sortResources();
    }

    return $moduleResources;
  }

  /**
   * Load modules from QueryModules
   *
   * @return array<int, array<string, mixed>>
   */
  private function loadModules(): array
  {
    return \Webkernel\Arcanes\QueryModules::make()
      ->select(['namespace', 'name', 'id'])
      ->unique()
      ->get();
  }

  /**
   * Populate permissions for all resources in matched modules
   *
   * @param array<string, ModuleResourceData> $moduleResources
   * @param array<string, array<string, mixed>> $resources
   * @return void
   */
  private function populateResourcePermissions(array $moduleResources, array $resources): void
  {
    foreach ($moduleResources as $moduleData) {
      foreach ($resources as $resourceClass => $metadata) {
        if (!str_contains($resourceClass, $moduleData->moduleNamespace)) {
          continue;
        }

        $resourceName = $this->extractResourceName($resourceClass);
        $permissions = $this->permissionExtractor->extractPermissions($resourceClass);

        if (!empty($permissions)) {
          $moduleData->addResource($resourceName, $permissions);
        }
      }
    }
  }

  /**
   * Create fallback module for unmatched resources
   *
   * @param array<int, string> $unmatchedClasses
   * @param array<string, array<string, mixed>> $resources
   * @return ModuleResourceData
   */
  private function createOtherResourcesModule(array $unmatchedClasses, array $resources): ModuleResourceData
  {
    $otherModule = new ModuleResourceData(
      moduleName: 'Other Resources',
      moduleId: 'other',
      moduleNamespace: 'Other',
      resources: [],
    );

    foreach ($unmatchedClasses as $resourceClass) {
      if (!isset($resources[$resourceClass])) {
        continue;
      }

      $resourceName = $this->extractResourceName($resourceClass);
      $permissions = $this->permissionExtractor->extractPermissions($resourceClass);

      if (!empty($permissions)) {
        $otherModule->addResource($resourceName, $permissions);
      }
    }

    return $otherModule;
  }

  /**
   * Extract resource name from class name
   *
   * @param string $resourceClass
   * @return string
   */
  private function extractResourceName(string $resourceClass): string
  {
    return ResourceNameFormatter::extractResourceName($resourceClass);
  }

  /**
   * Clear discovery cache
   *
   * @return void
   */
  public function clearCache(): void
  {
    $this->cacheManager->clear();
  }

  /**
   * Get cache statistics
   *
   * @return array<string, mixed>
   */
  public function getCacheStats(): array
  {
    return $this->cacheManager->getStats();
  }

  /**
   * Get combined logs from all components
   *
   * @return array<string, mixed>
   */
  public function getAllLogs(): array
  {
    if (!$this->enableLogging) {
      return [];
    }

    return [
      'discovery' => $this->resourceDiscovery->getLog(),
      'matching' => $this->resourceMatcher->getLog(),
      'permissions' => $this->permissionExtractor->getLog(),
    ];
  }

  /**
   * Get total resource count
   *
   * @param array<string, ModuleResourceData> $moduleResources
   * @return int
   */
  public function getTotalResourceCount(array $moduleResources): int
  {
    $total = 0;

    foreach ($moduleResources as $moduleData) {
      $total += $moduleData->getResourceCount();
    }

    return $total;
  }

  /**
   * Get all privileges for badge count and synchronization
   *
   * @param array<string, ModuleResourceData> $moduleResources
   * @return array<int, array<string, string>>
   */
  public function extractPrivileges(array $moduleResources): array
  {
    $privileges = [];

    foreach ($moduleResources as $moduleData) {
      if (!$moduleData->hasResources()) {
        continue;
      }

      foreach ($moduleData->resources as $resourceName => $permissions) {
        $resourceNameStr = (string) $resourceName;

        foreach ($permissions as $permission) {
          $permissionStr = (string) $permission;

          $privileges[] = [
            'key' => ResourceNameFormatter::generatePrivilegeKey($resourceNameStr, $permissionStr),
            'description' => ResourceNameFormatter::generatePrivilegeDescription(
              $resourceNameStr,
              $permissionStr,
              $moduleData->moduleName,
            ),
          ];
        }
      }
    }

    return $privileges;
  }
}
