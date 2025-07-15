<?php

namespace Webkernel\Core\Filament\Resources\Permissions;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Webkernel\Core\Filament\Resources\Permissions\Pages\CreatePermission;
use Webkernel\Core\Filament\Resources\Permissions\Pages\EditPermission;
use Webkernel\Core\Filament\Resources\Permissions\Pages\ListPermissions;
use Webkernel\Core\Filament\Resources\Permissions\Schemas\PermissionForm;
use Webkernel\Core\Filament\Resources\Permissions\Tables\PermissionsTable;
use Webkernel\Core\Models\RBAC\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
    protected static string|BackedEnum|null  $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $label = 'Permissions Utilisateurs';

    public static function form(Schema $schema): Schema
    {
        return PermissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermissionsTable::configure($table);
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
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }


    public static function getNavigationGroup(): ?string
{
    return lang('system_menu_all_users_management');
}
   
}
