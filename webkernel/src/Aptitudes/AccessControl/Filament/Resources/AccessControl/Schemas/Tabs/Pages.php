<?php declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Tabs;

use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Concerns\InteractsWithTabs;

/**
 * Discovered Pages Tab
 */
class Pages
{
  use InteractsWithTabs;

  /**
   * Get the tab
   */
  public static function getTab(): Tab
  {
    return Tab::make('discovered_pages')
      ->label('Pages')
      ->badge(count(static::getPrivileges()))
      ->schema([
        Section::make('Filament Pages')
          ->description('Permissions discovered from Filament pages')
          ->headerActions([
            Action::make('sync_pages')
              ->label('Sync Pages')
              ->icon('heroicon-o-arrow-path')
              ->action(function () {
                // Trigger page discovery
              })
              ->tooltip('Refresh discovered pages'),
          ])
          ->schema([static::getSelectAllToggle('pages'), ...static::getCheckboxListGrid('pages')])
          ->collapsible()
          ->columnSpanFull(),
      ])
      ->extraAttributes([
        'data-tab-key' => 'discovered_pages',
      ]);
  }

  /**
   * Get separator image configuration for pages tab
   *
   * @return array<string, mixed>
   */
  public static function getSeparatorConfig(): array
  {
    return [
      'path' => public_path('images/assets/svg/pages_separator.png'),
      'width' => 350,
      'height' => 'auto',
    ];
  }

  /**
   * Get page privileges
   *
   * @return array<int, array<string, string>>
   */
  public static function getPrivileges(): array
  {
    return [
      ['key' => 'page.settings.access', 'description' => 'Access Settings Page'],
      ['key' => 'page.settings.edit', 'description' => 'Edit Settings'],
      ['key' => 'page.report.view', 'description' => 'View Reports Page'],
      ['key' => 'page.profile.edit', 'description' => 'Edit Profile Page'],
    ];
  }
}
