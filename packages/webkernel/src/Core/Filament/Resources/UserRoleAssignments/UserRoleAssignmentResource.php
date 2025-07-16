<?php

namespace Webkernel\Core\Filament\Resources\UserRoleAssignments;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Webkernel\Core\Filament\Resources\UserRoleAssignments\Pages\CreateUserRoleAssignment;
use Webkernel\Core\Filament\Resources\UserRoleAssignments\Pages\EditUserRoleAssignment;
use Webkernel\Core\Filament\Resources\UserRoleAssignments\Pages\ListUserRoleAssignments;
use Webkernel\Core\Filament\Resources\UserRoleAssignments\Schemas\UserRoleAssignmentForm;
use Webkernel\Core\Filament\Resources\UserRoleAssignments\Tables\UserRoleAssignmentsTable;
use App\Models\User;

class UserRoleAssignmentResource extends Resource
{
    protected static ?string $model = User::class;
    protected static  string|BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Assignation Utilisateurs';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return UserRoleAssignmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserRoleAssignmentsTable::configure($table);
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
            'index' => ListUserRoleAssignments::route('/'),
            'create' => CreateUserRoleAssignment::route('/create'),
            'edit' => EditUserRoleAssignment::route('/{record}/edit'),
        ];
    }
}
