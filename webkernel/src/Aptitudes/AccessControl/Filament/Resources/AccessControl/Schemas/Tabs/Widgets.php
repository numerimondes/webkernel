<?php declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Tabs;

use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Concerns\InteractsWithTabs;

/**
 * Discovered Widgets Tab
 */
class Widgets
{
  use InteractsWithTabs;

  /**
   * Get the tab
   */
  public static function getTab(): Tab
  {
    return Tab::make('discovered_widgets')
      ->label('Widgets')
      ->badge(count(static::getPrivileges()))
      ->schema([
        Section::make('Filament Widgets')
          ->description('Permissions discovered from Filament widgets')
          ->headerActions([
            Action::make('sync_widgets')
              ->label('Sync Widgets')
              ->icon('heroicon-o-arrow-path')
              ->action(function () {
                // Trigger widget discovery
              })
              ->tooltip('Refresh discovered widgets'),
          ])
          ->schema([static::getSelectAllToggle('widgets'), ...static::getCheckboxListGrid('widgets')])
          ->collapsible()
          ->columnSpanFull(),
      ])
      ->extraAttributes([
        'data-tab-key' => 'discovered_widgets',
      ]);
  }

  /**
   * Get separator image configuration for widgets tab
   *
   * @return array<string, mixed>
   */
  public static function getSeparatorConfig(): array
  {
    return [
      'path' => public_path('images/assets/svg/widgets_separator.png'),
      'width' => 350,
      'height' => 'auto',
    ];
  }

  /**
   * Get widget privileges
   *
   * @return array<int, array<string, string>>
   */
  public static function getPrivileges(): array
  {
    return [
      ['key' => 'widget.stats.view', 'description' => 'View Statistics Widget'],
      ['key' => 'widget.chart.view', 'description' => 'View Chart Widget'],
      ['key' => 'widget.users.view', 'description' => 'View Users Widget'],
      ['key' => 'widget.recent_activity.view', 'description' => 'View Recent Activity Widget'],
    ];
  }
}
