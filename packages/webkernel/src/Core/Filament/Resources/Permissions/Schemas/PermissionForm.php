<?php

namespace Webkernel\Core\Filament\Resources\Permissions\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms;
use Filament\Resources\Resource;
use App\Models\User;
use Webkernel\Core\Models\RBAC\Permission;
class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
          
                    Forms\Components\TextInput::make('policy_class')
                        ->required()
                        ->label('Policy Class'),
                    
                    Forms\Components\TextInput::make('action')
                        ->required()
                        ->label('Action'),
                    
                    Forms\Components\TextInput::make('model_class')
                        ->required()
                        ->label('Model Class'),
                    
                    Forms\Components\Select::make('users')
                        ->relationship('users', 'name')
                        ->multiple()
                        ->searchable()
                        ->label('Utilisateurs'),
                 ]);
    }
}
