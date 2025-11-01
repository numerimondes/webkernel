<?php

declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Filament\Resources\Providers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProviderForm
{
  /**
   * Configure provider form schema
   *
   * @param Schema $schema
   * @return Schema
   */
  public static function configure(Schema $schema): Schema
  {
    return $schema->schema(self::getSchema());
  }

  /**
   * Get form schema components
   *
   * @return array<int, \Filament\Forms\Components\Component>
   */
  public static function getSchema(): array
  {
    return [
      Select::make('user_id')
        ->label(__('Associated User'))
        ->relationship('user', 'name')
        ->searchable()
        ->preload()
        ->required()
        ->helperText(__('Select the user account for this provider')),

      TextInput::make('company_name')
        ->label(__('Company Name'))
        ->required()
        ->maxLength(255)
        ->helperText(__('Full legal company name')),

      TextInput::make('phone')->label(__('Phone Number'))->tel()->maxLength(20)->helperText(__('Contact phone number')),

      TextInput::make('website')
        ->label(__('Website URL'))
        ->url()
        ->maxLength(255)
        ->helperText(__('Company website (https://example.com)')),

      Toggle::make('is_active')
        ->label(__('Active Status'))
        ->default(true)
        ->helperText(__('Enable or disable this provider')),
    ];
  }
}
