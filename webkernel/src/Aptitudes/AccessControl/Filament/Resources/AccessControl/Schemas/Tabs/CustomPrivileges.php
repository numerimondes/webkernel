<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Tabs;

use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Concerns\InteractsWithTabs;
/**
 * Custom Privileges Tab
 */
class CustomPrivileges
{
  use InteractsWithTabs;

  /**
   * Get the tab
   */
  public static function getTab(): Tab
  {
    return Tab::make('custom_privileges')
      ->label('Custom')
      ->badge(count(static::getPrivileges()))
      ->schema([
        Section::make('Custom Privileges')
          ->description('Manually defined custom permissions')
          ->headerActions([
            Action::make('add_custom_privilege')
              ->label('Add Custom')
              ->icon('heroicon-o-plus')
              ->action(function () {
                // Open modal to add custom privilege
              })
              ->tooltip('Create new custom privilege'),
          ])
          ->schema([static::getSelectAllToggle('custom'), ...static::getCheckboxListGrid('custom')])
          ->collapsible()
          ->columnSpanFull(),
      ])
      ->extraAttributes([
        'data-tab-key' => 'custom_privileges',
      ]);
  }

  /**
   * Get separator image configuration for custom privileges tab
   *
   * @return array<string, mixed>
   */
  public static function getSeparatorConfig(): array
  {
    return [
      'path' => public_path('images/assets/svg/custom_separator.png'),
      'width' => 350,
      'height' => 'auto',
    ];
  }

  /**
   * Get custom privileges
   *
   * @return array<int, array<string, string>>
   */
  public static function getPrivileges(): array
  {
    return [
      ['key' => 'custom.export.data', 'description' => 'Export Data'],
      ['key' => 'custom.import.data', 'description' => 'Import Data'],
      ['key' => 'custom.bulk.actions', 'description' => 'Execute Bulk Actions'],
      ['key' => 'custom.advanced.search', 'description' => 'Use Advanced Search'],
    ];
  }
}
