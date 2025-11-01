<?php

namespace Platform\Numerimondes\Server\Filament\Resources\Software\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SoftwareTable
{
  public static function configure(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('name')->searchable()->sortable(),
        TextColumn::make('slug')->searchable()->sortable(),
        TextColumn::make('install_path')->searchable(),
        TextColumn::make('namespace')->searchable(),
        IconColumn::make('is_active')->boolean()->sortable(),
        //TextColumn::make('cores_count')->counts('cores')->label('Cores'),
        TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([TernaryFilter::make('is_active')->label('Active')])
      ->recordActions([EditAction::make()])
      ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
  }
}
