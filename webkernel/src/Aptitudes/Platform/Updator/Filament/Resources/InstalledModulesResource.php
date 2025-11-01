<?php

namespace Webkernel\Aptitudes\Platform\Updator\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InstalledModulesResource extends Resource
{
  protected static ?string $model = \stdClass::class; // Placeholder, scan-based

  protected static ?string $navigationIcon = 'heroicon-o-archive-box';

  public static function form(Form $form): Form
  {
    return $form->schema([
      // Forms if editable
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')->sortable(),
        Tables\Columns\TextColumn::make('version')->badge(),
        Tables\Columns\TextColumn::make('path'),
      ])
      ->filters([
        //
      ])
      ->actions([Tables\Actions\Action::make('update')->action('update')])
      ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
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
      'index' => Pages\ListInstalledModules::route('/'),
    ];
  }
}
