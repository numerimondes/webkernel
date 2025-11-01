<?php declare(strict_types=1);

namespace Webkernel\Arcanes\Support\Resources;
/**
 * Resource to module matching engine
 * Handles matching of resources to modules with fallback support
 */
class ResourceMatcher
{
  private NamespaceMatcher $namespaceMatcher;
  private array $matchLog = [];
  private bool $enableLogging;

  /**
   * Create a new resource matcher
   *
   * @param bool $enableLogging Enable debug logging
   */
  public function __construct(bool $enableLogging = false)
  {
    $this->namespaceMatcher = new NamespaceMatcher();
    $this->enableLogging = $enableLogging;
  }

  /**
   * Match resources to modules
   *
   * @param array<string, array<string, mixed>> $resources Resource metadata
   * @param array<int, array<string, mixed>> $modules Module data
   * @return array<string, ModuleResourceData> Module namespace to ModuleResourceData mapping
   */
  public function matchResourcesToModules(array $resources, array $modules): array
  {
    $moduleResources = [];
    $matchedResources = [];

    foreach ($resources as $resourceClass => $metadata) {
      $resourceNamespace = $metadata['namespace'];
      $matched = false;

      foreach ($modules as $module) {
        $moduleNamespace = $module['namespace'] ?? '';
        $moduleName = $module['name'] ?? '';
        $moduleId = $module['id'] ?? '';

        if (empty($moduleNamespace) || empty($moduleName)) {
          continue;
        }

        if ($this->namespaceMatcher->matches($resourceClass, $resourceNamespace, $moduleNamespace)) {
          if (!isset($moduleResources[$moduleNamespace])) {
            $moduleResources[$moduleNamespace] = new ModuleResourceData(
              moduleName: $moduleName,
              moduleId: $moduleId,
              moduleNamespace: $moduleNamespace,
              resources: [],
            );
          }

          $matchedResources[] = $resourceClass;
          $matched = true;

          $this->log("matched_{$resourceClass}", [
            'resource' => $resourceClass,
            'module' => $moduleName,
            'strategies' => $this->namespaceMatcher->getMatchDetails(
              $resourceClass,
              $resourceNamespace,
              $moduleNamespace,
            ),
          ]);

          break;
        }
      }

      if (!$matched) {
        $this->log("unmatched_{$resourceClass}", [
          'resource' => $resourceClass,
          'namespace' => $resourceNamespace,
        ]);
      }
    }

    return [
      'matched' => $moduleResources,
      'matched_classes' => $matchedResources,
      'unmatched' => array_diff(array_keys($resources), $matchedResources),
    ];
  }

  /**
   * Get match log
   *
   * @return array<string, mixed>
   */
  public function getLog(): array
  {
    return $this->matchLog;
  }

  /**
   * Log match information
   *
   * @param string $key
   * @param mixed $data
   * @return void
   */
  private function log(string $key, mixed $data): void
  {
    if (!$this->enableLogging) {
      return;
    }

    $this->matchLog[$key] = $data;
  }
}
