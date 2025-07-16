<?php

namespace Webkernel\Core\Filament\Resources\Roles\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Webkernel\Core\Helpers\Modules\ModuleAccessHelper;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom du rôle')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Textarea::make('description')
                            ->label('Description')
                            ->maxLength(65535)
                            ->rows(3),
                        Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),
                    ])
                    ->columns(2),
                Section::make('Contexte')
                    ->schema([
                        Select::make('namespace')
                            ->label('Namespace')
                            ->options(function () {
                                try {
                                    $allModules = ModuleAccessHelper::getAllModules();
                                    $options = [];
                                    
                                    foreach ($allModules as $namespace => $data) {
                                        $options[$namespace] = $namespace;
                                    }
                                    
                                    return $options;
                                } catch (\Exception $e) {
                                    return [];
                                }
                            })
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('module', null)),
                        
                        Select::make('module')
                            ->label('Module')
                            ->options(function (callable $get) {
                                $namespace = $get('namespace');
                                if (!$namespace) return [];
                                
                                try {
                                    $allModules = ModuleAccessHelper::getAllModules();
                                    $options = [];
                                    
                                    if (isset($allModules[$namespace]['modules'])) {
                                        foreach ($allModules[$namespace]['modules'] as $moduleName => $moduleData) {
                                            $options[$moduleName] = $moduleName;
                                        }
                                    }
                                    
                                    return $options;
                                } catch (\Exception $e) {
                                    return [];
                                }
                            })
                            ->nullable()
                            ->reactive(),
                    ])
                    ->columns(2),
            ]);
    }
}
