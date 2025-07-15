<?php

namespace Webkernel\Core\Filament\Resources\UserPermissions;

use App\Models\UserPermission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Webkernel\Core\Filament\Resources\UserPermissions\Pages\CreateUserPermission;
use Webkernel\Core\Filament\Resources\UserPermissions\Pages\EditUserPermission;
use Webkernel\Core\Filament\Resources\UserPermissions\Pages\ListUserPermissions;
use Webkernel\Core\Filament\Resources\UserPermissions\Schemas\UserPermissionForm;
use Webkernel\Core\Filament\Resources\UserPermissions\Tables\UserPermissionsTable;

class UserPermissionResource extends Resource
{
    protected static ?string $model = UserPermission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UserPermissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserPermissionsTable::configure($table);
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
            'index' => ListUserPermissions::route('/'),
            'create' => CreateUserPermission::route('/create'),
            'edit' => EditUserPermission::route('/{record}/edit'),
        ];
    }
}
