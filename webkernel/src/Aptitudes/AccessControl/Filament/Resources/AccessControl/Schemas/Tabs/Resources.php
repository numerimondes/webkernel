<?php declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Tabs;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Str;
use Webkernel\Arcanes\Queries\{ModuleResourceData, ResourceDiscoveryService};
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Concerns\InteractsWithTabs;

/**
 * Discovered Resources Tab
 *
 * Manages the discovery and display of Filament resources grouped by modules
 * with their associated permissions for access control management.
 *
 * This class is frontend-focused and delegates all discovery logic to
 * the centralized ResourceDiscoveryService.
 */
class Resources
{
  use InteractsWithTabs;

  /**
   * Enable debug mode to display diagnostic information
   */
  private const DEBUG_MODE_ENABLED = false;

  /**
   * Cache duration in seconds
   */
  private const CACHE_DURATION = 3600;

  /**
   * Cached module resources data
   *
   * @var array<string, ModuleResourceData>
   */
  private static array $cachedModuleResources = [];

  /**
   * Total count of discovered resources
   *
   * @var int
   */
  private static int $totalResourceCount = 0;

  /**
   * Resource discovery service instance
   *
   * @var ResourceDiscoveryService|null
   */
  private static ?ResourceDiscoveryService $discoveryService = null;

  /**
   * Get the discovered resources tab configuration
   *
   * @return Tab
   */
  public static function getTab(): Tab
  {
    static::loadModuleResources();
    $moduleSections = static::buildModuleSections();

    $schema = [
      Section::make('Filament Resources')
        ->description('Permissions discovered from resources grouped by modules')
        ->headerActions([
          Action::make('sync_resources')
            ->label('Sync Resources')
            ->icon('heroicon-o-arrow-path')
            ->action(function (): void {
              static::clearCache();
            })
            ->tooltip('Refresh discovered resources'),
        ])
        ->schema([Grid::make(2)->schema($moduleSections)])
        ->columnSpanFull(),
    ];

    if (self::DEBUG_MODE_ENABLED) {
      $schema[] = static::buildDebugSection();
    }

    return Tab::make('discovered_resources')
      ->label('Resources')
      ->badge(static::$totalResourceCount)
      ->schema($schema)
      ->extraAttributes([
        'data-tab-key' => 'discovered_resources',
      ]);
  }

  /**
   * Get or create discovery service instance
   *
   * @return ResourceDiscoveryService
   */
  private static function getDiscoveryService(): ResourceDiscoveryService
  {
    if (static::$discoveryService === null) {
      static::$discoveryService = new ResourceDiscoveryService(
        enableLogging: self::DEBUG_MODE_ENABLED,
        cacheDuration: self::CACHE_DURATION,
      );
    }

    return static::$discoveryService;
  }

  /**
   * Load module resources from cache or discover them
   *
   * @return void
   */
  private static function loadModuleResources(): void
  {
    if (!empty(static::$cachedModuleResources)) {
      return;
    }

    $service = static::getDiscoveryService();
    static::$cachedModuleResources = $service->discoverResourcesByModule();
    static::$totalResourceCount = $service->getTotalResourceCount(static::$cachedModuleResources);
  }

  /**
   * Clear all caches
   *
   * @return void
   */
  private static function clearCache(): void
  {
    static::getDiscoveryService()->clearCache();
    static::$cachedModuleResources = [];
    static::$totalResourceCount = 0;
    static::$discoveryService = null;
  }

  /**
   * Build debug information section
   *
   * @return Section
   */
  private static function buildDebugSection(): Section
  {
    $service = static::getDiscoveryService();
    $logs = $service->getAllLogs();

    $debugContent = '';

    foreach ($logs as $category => $categoryLogs) {
      $debugContent .= sprintf("## %s\n\n", strtoupper($category));

      foreach ($categoryLogs as $key => $data) {
        $debugContent .= sprintf("### %s\n", str_replace('_', ' ', $key));
        $debugContent .= "```json\n";
        $debugContent .= json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $debugContent .= "\n```\n\n";
      }
    }

    return Section::make('Debug Information')
      ->description('Diagnostic data for resource discovery')
      ->schema([TextEntry::make('debug_output')->markdown()->state($debugContent)->columnSpanFull()])
      ->collapsible()
      ->collapsed(true);
  }

  /**
   * Build sections grouped by modules
   *
   * @return array<int, Section>
   */
  protected static function buildModuleSections(): array
  {
    if (empty(static::$cachedModuleResources)) {
      return [];
    }

    $sections = [];

    foreach (static::$cachedModuleResources as $moduleData) {
      if (!($moduleData instanceof ModuleResourceData)) {
        continue;
      }

      if (!$moduleData->hasResources()) {
        continue;
      }

      $resourceSections = static::buildResourceSectionsForModule($moduleData);

      $sections[] = Section::make($moduleData->moduleName)
        ->description("Resources from {$moduleData->moduleName} module")
        ->schema($resourceSections)
        ->collapsible()
        ->collapsed(false);
    }

    return $sections;
  }

  /**
   * Build resource sections for a specific module
   *
   * @param ModuleResourceData $moduleData
   * @return array<int, Section>
   */
  private static function buildResourceSectionsForModule(ModuleResourceData $moduleData): array
  {
    $resourceSections = [];

    foreach ($moduleData->resources as $resourceName => $permissions) {
      $resourceNameStr = (string) $resourceName;
      $resourceTitle = Str::title(str_replace('_', ' ', $resourceNameStr));

      $resourceSections[] = Section::make($resourceTitle)
        ->schema([static::getSelectAllToggle($resourceNameStr), ...static::getCheckboxListGrid($resourceNameStr)])
        ->collapsible()
        ->collapsed(true);
    }

    return $resourceSections;
  }

  /**
   * Get all privileges for badge count and synchronization
   *
   * @return array<int, array<string, string>>
   */
  public static function getPrivileges(): array
  {
    static::loadModuleResources();

    $service = static::getDiscoveryService();
    return $service->extractPrivileges(static::$cachedModuleResources);
  }

  /**
   * Get separator image configuration for resources tab
   *
   * @return array<string, mixed>
   */
  public static function getSeparatorConfig(): array
  {
    return [
      'path' => public_path('images/assets/svg/mobile-tablet_1760638936.svg'),
      'width' => 350,
      'height' => 'auto',
    ];
  }
}
