<?php

namespace Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('civility'),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('folder_name')
                    ->searchable(),
                TextColumn::make('fiscal_address')
                    ->searchable(),
                TextColumn::make('fiscal_postal_code')
                    ->searchable(),
                TextColumn::make('fiscal_city')
                    ->searchable(),
                TextColumn::make('fiscal_country')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('can_login')
                    ->boolean(),
                TextColumn::make('household_status'),
                TextColumn::make('usage_type'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
