<?php

namespace Webkernel\Core\Filament\Resources\Roles;

use App\Models\Role;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Webkernel\Core\Filament\Resources\Roles\Pages\CreateRole;
use Webkernel\Core\Filament\Resources\Roles\Pages\EditRole;
use Webkernel\Core\Filament\Resources\Roles\Pages\ListRoles;
use Webkernel\Core\Filament\Resources\Roles\Schemas\RoleForm;
use Webkernel\Core\Filament\Resources\Roles\Tables\RolesTable;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
