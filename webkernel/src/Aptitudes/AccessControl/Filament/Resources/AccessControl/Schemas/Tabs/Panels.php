<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Tabs;

use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Concerns\InteractsWithTabs;

/**
 * Discovered Panels Tab
 */
class Panels
{
  use InteractsWithTabs;

  /**
   * Get the tab
   */
  public static function getTab(): Tab
  {
    return Tab::make('discovered_panels')
      ->label('Panels')
      ->badge(count(static::getPrivileges()))
      ->schema([
        Section::make('Filament Panels')
          ->description('Permissions discovered from Filament panels')
          ->headerActions([
            Action::make('sync_panels')
              ->label('Sync Panels')
              ->icon('heroicon-o-arrow-path')
              ->action(function () {
                // Trigger panel discovery
              })
              ->tooltip('Refresh discovered panels'),
          ])
          ->schema([static::getSelectAllToggle('panels'), ...static::getCheckboxListGrid('panels')])
          ->collapsible()
          ->columnSpanFull(),
      ])
      ->extraAttributes([
        'data-tab-key' => 'discovered_panels',
      ]);
  }

  /**
   * Get separator image configuration for panels tab
   *
   * @return array<string, mixed>
   */
  public static function getSeparatorConfig(): array
  {
    return [
      'path' => public_path('images/assets/svg/panels_separator.png'),
      'width' => 350,
      'height' => 'auto',
    ];
  }

  /**
   * Get panel privileges
   *
   * @return array<int, array<string, string>>
   */
  public static function getPrivileges(): array
  {
    return [
      ['key' => 'panel.admin.access', 'description' => 'Access Admin Panel'],
      ['key' => 'panel.admin.view_dashboard', 'description' => 'View Dashboard'],
      ['key' => 'panel.admin.manage_users', 'description' => 'Manage Users'],
      ['key' => 'panel.app.access', 'description' => 'Access App Panel'],
      ['key' => 'panel.app.view_analytics', 'description' => 'View Analytics'],
    ];
  }
}
