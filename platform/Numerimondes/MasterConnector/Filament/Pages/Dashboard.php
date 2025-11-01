<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Platform\Numerimondes\MasterConnector\Services\LicenseManager;
use Platform\Numerimondes\MasterConnector\Services\ModuleCatalog;
use Platform\Numerimondes\MasterConnector\Models\DownloadLog;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsIconAlias;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
  protected string $view = 'master-connector::filament.pages.master-dashboard';

  public function getTitle(): string|Htmlable
  {
    return 'Master Connector Dashboard';
  }

  /**
   * Get widget data for dashboard
   */
  protected function getViewData(): array
  {
    $licenseManager = app(LicenseManager::class);
    $moduleCatalog = app(ModuleCatalog::class);

    return [
      'licenseStats' => $licenseManager->getStatistics(),
      'moduleStats' => $moduleCatalog->getStatistics(),
      'recentDownloads' => DownloadLog::with(['license', 'module'])
        ->recent(24)
        ->orderByDesc('downloaded_at')
        ->limit(10)
        ->get(),
      'topModules' => $this->getTopModules(),
      'recentActivity' => $this->getRecentActivity(),
    ];
  }

  /**
   * Get top downloaded modules
   */
  private function getTopModules(): array
  {
    return DownloadLog::selectRaw('module_id, COUNT(*) as downloads')
      ->where('success', true)
      ->whereNotNull('module_id')
      ->groupBy('module_id')
      ->orderByDesc('downloads')
      ->limit(5)
      ->with('module')
      ->get()
      ->map(
        fn($log) => [
          'name' => $log->module?->name ?? 'Unknown',
          'downloads' => $log->downloads,
        ],
      )
      ->toArray();
  }

  /**
   * Get recent license activity
   */
  private function getRecentActivity(): array
  {
    return \Platform\Numerimondes\MasterConnector\Models\License::query()
      ->orderByDesc('last_validated_at')
      ->limit(10)
      ->get()
      ->map(
        fn($license) => [
          'domain' => $license->domain,
          'status' => $license->status,
          'last_validated' => $license->last_validated_at?->diffForHumans() ?? 'Never',
        ],
      )
      ->toArray();
  }

  /**
   * Get widgets for dashboard
   */
  public function getWidgets(): array
  {
    return [
        // Add custom widgets here if needed
      ];
  }

  public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
  {
    return static::$navigationIcon ??
      (FilamentIcon::resolve(PanelsIconAlias::PAGES_DASHBOARD_NAVIGATION_ITEM) ??
        (Filament::hasTopNavigation() ? Heroicon::Home : Heroicon::OutlinedHome));
  }
}
