<?php

namespace Webkernel\Core\Filament\Resources\Roles\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms;
use Webkernel\Core\Filament\Resources\Roles\RoleResource;
use Webkernel\Core\Helpers\Modules\ModuleAccessHelper;
use Webkernel\Core\Models\RBAC\Permission;

class PermissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';

    protected static ?string $relatedResource = RoleResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label('Permission')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color('info'),
                TextColumn::make('model_class')
                    ->label('Modèle')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->searchable(),
                TextColumn::make('module')
                    ->label('Module')
                    ->badge()
                    ->color('success'),
                TextColumn::make('namespace')
                    ->label('Namespace')
                    ->badge()
                    ->color('gray'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        Forms\Components\Select::make('permission_id')
                            ->label('Permission')
                            ->options(function () {
                                $role = $this->getOwnerRecord();
                                $query = Permission::query();
                                
                                if ($role->namespace && $role->module) {
                                    $query->where('namespace', $role->namespace)
                                          ->where('module', $role->module);
                                }
                                
                                return $query->get()
                                    ->mapWithKeys(function ($permission) {
                                        return [$permission->id => $permission->display_name];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $role = $this->getOwnerRecord();
                        $role->permissions()->attach($data['permission_id']);
                    }),
            ])
            ->actions([
                DetachAction::make()
                    ->label('Retirer')
                    ->color('danger'),
            ])
            ->bulkActions([
                DetachAction::make()
                    ->label('Retirer la sélection'),
            ])
            ->defaultSort('display_name');
    }
}
