<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Sections;

use Filament\Forms\Components\{TextInput, Toggle};
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;

/**
 * Group Details Section
 *
 * Builds form fields for access control group identification and system settings.
 *
 * @package Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Sections
 */
final class GroupDetails
{
  /**
   * Creates the main section for access control group details
   *
   * @return Section The section for group identification
   */
  public static function main(): Section
  {
    return Section::make('Group Details')
      ->schema([
        TextInput::make('name')
          ->label('Group Name')
          ->required()
          ->maxLength(255)
          ->live(onBlur: true)
          ->afterStateUpdated(function (mixed $state, callable $set, callable $get): void {
            if (empty($get('slug'))) {
              $set('slug', Str::slug((string) $state));
            }
          })
          ->helperText('Descriptive name for this access control group'),

        TextInput::make('slug')
          ->label('Slug')
          ->required()
          ->maxLength(255)
          ->unique(ignoreRecord: true)
          ->alphaDash()
          ->disabled(fn(?object $record): bool => $record?->is_system ?? false)
          ->helperText('Unique identifier for this access control group'),

        TextInput::make('description')
          ->label('Description')
          ->maxLength(150)
          ->helperText('Brief description of this group\'s purpose'),
      ])
      ->columns(3)
      ->columnSpan(9)
      ->compact();
  }

  /**
   * Creates the system configuration toggles section
   *
   * @return Section The section containing system configuration toggles
   */
  public static function systemFlags(): Section
  {
    return Section::make('System Settings')
      ->schema([
        Toggle::make('is_system')
          ->label('System Group')
          ->inline(false)
          ->disabled(fn(?object $record): bool => $record?->is_system ?? false)
          ->helperText('Protected group that cannot be deleted'),

        Toggle::make('is_active')
          ->label('Active')
          ->inline(false)
          ->default(true)
          ->helperText('Enable or disable this group'),
      ])
      ->columns(2)
      ->columnSpan(3)
      ->compact();
  }
}
