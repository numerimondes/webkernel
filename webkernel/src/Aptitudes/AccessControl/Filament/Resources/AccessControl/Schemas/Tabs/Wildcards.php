<?php declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Tabs;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

/**
 * Wildcard Patterns Tab
 */
class Wildcards
{
  /**
   * Get the tab
   */
  public static function getTab(): Tab
  {
    return Tab::make('wildcard_patterns')
      ->label('Wildcard Patterns')
      ->schema([
        Section::make('Pattern Configuration')
          ->description('Create wildcard patterns to grant multiple privileges at once')
          ->schema([
            Toggle::make('use_pattern')
              ->label('Use Wildcard Pattern')
              ->helperText('Enable to create a pattern instead of individual privileges')
              ->live()
              ->columnSpanFull(),

            TextInput::make('pattern')
              ->label('Pattern')
              ->placeholder('panel.*.access or resource.*.viewAny')
              ->helperText('Use * as wildcard. Examples: panel.*.access, resource.User.*')
              ->visible(fn(Get $get): bool => (bool) $get('use_pattern'))
              ->live()
              ->afterStateUpdated(function (?string $state, Set $set): void {
                if ($state !== null) {
                  static::validateAndPreviewPattern($state, $set);
                }
              })
              ->columnSpanFull(),

            Select::make('common_patterns')
              ->label('Common Patterns')
              ->options(static::getCommonPatterns())
              ->visible(fn(Get $get): bool => (bool) $get('use_pattern'))
              ->live()
              ->afterStateUpdated(function (?string $state, Set $set): void {
                if ($state) {
                  $set('pattern', $state);
                }
              })
              ->placeholder('Select a common pattern')
              ->columnSpanFull(),

            Textarea::make('pattern_description')
              ->label('Pattern Description')
              ->placeholder('Describe what this pattern grants access to...')
              ->visible(fn(Get $get): bool => (bool) $get('use_pattern'))
              ->rows(2)
              ->columnSpanFull(),
          ])
          ->columnSpanFull(),
      ])
      ->extraAttributes([
        'data-tab-key' => 'wildcard_patterns',
      ]);
  }

  /**
   * Get separator image configuration for wildcard patterns tab
   *
   * @return array<string, mixed>
   */
  public static function getSeparatorConfig(): array
  {
    return [
      'path' => public_path('images/assets/svg/wildcard_separator.png'),
      'width' => 350,
      'height' => 'auto',
    ];
  }

  /**
   * Get common patterns
   *
   * @return array<string, string>
   */
  public static function getCommonPatterns(): array
  {
    return [
      'panel.*.access' => 'All Panels Access',
      'panel.*.manage' => 'All Panels Manage',
      'resource.*.viewAny' => 'View Any All Resources',
      'resource.*.view' => 'View All Resources',
      'resource.*.create' => 'Create All Resources',
      'resource.*.update' => 'Update All Resources',
      'resource.*.delete' => 'Delete All Resources',
      'resource.*.restore' => 'Restore All Resources',
      'page.*.access' => 'Access All Pages',
      'page.*.edit' => 'Edit All Pages',
      'widget.*.view' => 'View All Widgets',
      'custom.*' => 'All Custom Privileges',
    ];
  }

  /**
   * Validate and preview pattern
   *
   * @param string $pattern
   * @param Set $set
   * @return void
   */
  protected static function validateAndPreviewPattern(string $pattern, Set $set): void
  {
    if (empty($pattern)) {
      return;
    }

    if (!preg_match('/^[\w*.]+$/', $pattern)) {
      return;
    }
  }
}
