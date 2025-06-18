<?php
namespace Webkernel\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Models\User;
use Webkernel\Filament\Resources\UserResource\Pages\ListUsers;
use Webkernel\Filament\Resources\UserResource\Pages\CreateUser;
use Webkernel\Filament\Resources\UserResource\Pages\EditUser;
use Webkernel\Filament\Resources\UserResource\Pages;
use Webkernel\Filament\Resources\UserResource\Pages\ViewUser;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static string $webkernel_layout = 'avatar'; // Layout avec avatar et structure 3 parties

    public static function form(Form $form): Form
    {
        return webkernel_form($form, static::class);
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
}
