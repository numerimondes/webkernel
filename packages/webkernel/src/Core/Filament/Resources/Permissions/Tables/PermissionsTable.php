<?php

namespace Webkernel\Core\Filament\Resources\Permissions\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('policy_class')
                ->searchable()
                ->sortable(),
            
            TextColumn::make('action')
                ->searchable()
                ->sortable(),
            
            TextColumn::make('model_class')
                ->searchable()
                ->sortable(),
            
            TextColumn::make('users_count')
                ->counts('users')
                ->label('Utilisateurs'),
        ])
        ->filters([
            SelectFilter::make('action')
                ->options([
                    'viewAny' => 'View Any',
                    'view' => 'View',
                    'create' => 'Create',
                    'update' => 'Update',
                    'delete' => 'Delete',
                    'deleteAny' => 'Delete Any',
                    'restore' => 'Restore',
                    'forceDelete' => 'Force Delete',
                ])->searchable(),
        ])
        ->actions([
            EditAction::make(),
           // DeleteAction::make(),
        ]);
    }
}
