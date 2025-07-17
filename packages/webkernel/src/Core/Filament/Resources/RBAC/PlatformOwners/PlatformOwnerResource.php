<?php

namespace Webkernel\Core\Filament\Resources\RBAC\PlatformOwners;

use App\Models\User;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Webkernel\Core\Models\RBAC\PlatformOwner;
use Webkernel\Core\Filament\Resources\RBAC\PlatformOwners\Pages;
use Webkernel\Core\Filament\Resources\RBAC\PlatformOwners\Schemas\PlatformOwnerForm;
use Webkernel\Core\Filament\Resources\RBAC\PlatformOwners\Tables\PlatformOwnersTable;

class PlatformOwnerResource extends Resource
{
    protected static ?string $model = PlatformOwner::class;

    public static function form(Schema $schema): Schema
    {
        return PlatformOwnerForm::configure($schema);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function table(Table $table): Table
    {
        return PlatformOwnersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlatformOwners::route('/'),
        ];
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-shield-check';
    }

    public static function getModelLabel(): string
    {
        return 'Platform Owner';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Platform Owners';
    }

   
    public static function getNavigationGroup(): ?string
{
    return lang('system_menu_all_users_management');
}
   

    public static function getNavigationLabel(): string
    {
        return 'Super Admins';
    }
}
