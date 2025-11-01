<?php

namespace Platform\Numerimondes\Server\Filament\Resources\Software\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SoftwareForm
{
  public static function configure(Schema $schema): Schema
  {
    return $schema->components([
      Section::make('General')
        ->columnSpanFull()
        ->schema([
          Grid::make(5)->schema([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')
              ->required()
              ->unique(table: 'softwares', column: 'slug', ignoreRecord: true)
              ->maxLength(255),
            TextInput::make('install_path')->required()->maxLength(255),
            TextInput::make('namespace')->required()->maxLength(255),
            Toggle::make('is_active')->default(true),
          ]),
        ]),
    ]);
  }
}
