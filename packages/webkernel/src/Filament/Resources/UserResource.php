<?php
namespace Webkernel\Filament\Resources;

use BackedEnum;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Webkernel\Filament\Resources\UserResource\Pages;
use Webkernel\Filament\Resources\UserResource\Pages\EditUser;
use Webkernel\Filament\Resources\UserResource\Pages\ViewUser;
use Webkernel\Filament\Resources\UserResource\Pages\ListUsers;
use Webkernel\Filament\Resources\UserResource\Pages\CreateUser;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-users';
    }

public static function getModelLabel(): string
{
    return lang('user');
}

public static function getPluralModelLabel(): string
{
    return lang('users');
}
protected static ?string $recordTitleAttribute = 'name';


    public static function getNavigationGroup(): ?string
{
    return lang('system_menu_all_users_management');
}
    public static string $webkernel_layout = 'avatar';

    public static function form(Schema $schema): Schema
    {
        return webkernel_form($schema, static::class);
    }

    public static function table(Table $table): Table
    {
        return webkernel_table($table, static::class);
    }

    public static function getPages(): array
    {
        return array_merge(
            webkernel_pages(),
            [
                'index' => ListUsers::route('/'),
                'create' => CreateUser::route('/create'),
                'edit' => EditUser::route('/{record}/edit'),
                'view' => ViewUser::route('/{record}/view'),

            ]
        );
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
