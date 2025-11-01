<?php declare(strict_types=1);

namespace Webkernel\Arcanes\Support\Resources;

use Filament\Facades\Filament;
use ReflectionClass;

/**
 * Discovers Filament resources from all panels
 * Centralized resource discovery logic
 */
class ResourceDiscovery
{
  private array $discoveryLog = [];
  private bool $enableLogging;

  /**
   * Create a new resource discovery instance
   *
   * @param bool $enableLogging Enable debug logging
   */
  public function __construct(bool $enableLogging = false)
  {
    $this->enableLogging = $enableLogging;
  }

  /**
   * Discover all Filament resources from all panels
   *
   * @return array<int, string> List of resource class names
   */
  public function discoverAllResources(): array
  {
    $allResources = [];
    $panels = Filament::getPanels();

    $this->log('panels_discovery', [
      'count' => count($panels),
      'panel_ids' => array_keys($panels),
    ]);

    foreach ($panels as $panelId => $panel) {
      $panelResources = $panel->getResources();

      $this->log("panel_{$panelId}", [
        'count' => count($panelResources),
        'resources' => $panelResources,
      ]);

      foreach ($panelResources as $resourceClass) {
        if (!in_array($resourceClass, $allResources, true)) {
          $allResources[] = $resourceClass;
        }
      }
    }

    $this->log('discovery_complete', [
      'total_resources' => count($allResources),
      'resources' => $allResources,
    ]);

    return $allResources;
  }

  /**
   * Discover resources with detailed metadata
   *
   * @return array<string, array<string, mixed>> Resource class to metadata mapping
   */
  public function discoverResourcesWithMetadata(): array
  {
    $resources = [];
    $allResourceClasses = $this->discoverAllResources();

    foreach ($allResourceClasses as $resourceClass) {
      if (!class_exists($resourceClass)) {
        continue;
      }

      $reflection = new ReflectionClass($resourceClass);
      $metadata = [
        'class' => $resourceClass,
        'namespace' => $reflection->getNamespaceName(),
        'short_name' => $reflection->getShortName(),
        'model' => null,
      ];

      if (method_exists($resourceClass, 'getModel')) {
        try {
          $metadata['model'] = $resourceClass::getModel();
        } catch (\Exception $e) {
          $metadata['model_error'] = $e->getMessage();
        }
      }

      $resources[$resourceClass] = $metadata;
    }

    return $resources;
  }

  /**
   * Get discovery log
   *
   * @return array<string, mixed>
   */
  public function getLog(): array
  {
    return $this->discoveryLog;
  }

  /**
   * Log discovery information
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

    $this->discoveryLog[$key] = $data;
  }
}
