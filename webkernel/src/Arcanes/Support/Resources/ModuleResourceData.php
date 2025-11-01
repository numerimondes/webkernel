<?php declare(strict_types=1);
namespace Webkernel\Arcanes\Support\Resources;

/**
 * Internal data structure for module resource data
 *
 * @internal
 */
class ModuleResourceData
{
  /**
   * Create a new module resource data instance
   *
   * @param string $moduleName Module display name
   * @param string $moduleId Unique module identifier
   * @param string $moduleNamespace Module namespace for class matching
   * @param array<string, array<int, string>> $resources Resource name to permissions mapping
   */
  public function __construct(
    public readonly string $moduleName,
    public readonly string $moduleId,
    public readonly string $moduleNamespace,
    public array $resources = [],
  ) {}

  /**
   * Add a resource with its permissions
   *
   * @param string $resourceName
   * @param array<int, string> $permissions
   * @return void
   */
  public function addResource(string $resourceName, array $permissions): void
  {
    $this->resources[$resourceName] = $permissions;
  }

  /**
   * Get resource count
   *
   * @return int
   */
  public function getResourceCount(): int
  {
    return count($this->resources);
  }

  /**
   * Check if module has resources
   *
   * @return bool
   */
  public function hasResources(): bool
  {
    return !empty($this->resources);
  }

  /**
   * Sort resources alphabetically by key
   *
   * @return void
   */
  public function sortResources(): void
  {
    ksort($this->resources);
  }
}
