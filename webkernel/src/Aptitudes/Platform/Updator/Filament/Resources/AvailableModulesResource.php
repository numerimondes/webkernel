<?php

namespace Webkernel\Aptitudes\Platform\Updator\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AvailableModulesResource extends Resource
{
  protected static ?string $model = \stdClass::class; // Cache-based

  protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

  public static function form(Form $form): Form
  {
    return $form->schema([
      // Forms for install
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('identifier'),
        Tables\Columns\TextColumn::make('name'),
        Tables\Columns\TextColumn::make('version')->badge(),
        Tables\Columns\TextColumn::make('description'),
        Tables\Columns\TextColumn::make('size'),
      ])
      ->filters([
        //
      ])
      ->actions([Tables\Actions\Action::make('install')->action('install')])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([Tables\Actions\BulkAction::make('installBulk')->action('installBulk')]),
      ]);
  }

  public static function getRelations(): array
  {
    return [
        //
      ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListAvailableModules::route('/'),
    ];
  }
}
