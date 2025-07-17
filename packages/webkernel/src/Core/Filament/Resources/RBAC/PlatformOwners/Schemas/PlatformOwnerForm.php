<?php

namespace Webkernel\Core\Filament\Resources\RBAC\PlatformOwners\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;

class PlatformOwnerForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Utilisateur')
                    ->placeholder('Rechercher un utilisateur...')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Choisissez l\'utilisateur qui aura accès à tous les modules'),
                
                Toggle::make('is_eternal_owner')
                    ->label('Super Administrateur permanent')
                    ->helperText('✅ Accès illimité à tous les modules et panneaux')
                    ->default(true)
                    ->reactive(),
                
                DateTimePicker::make('when')
                    ->label('Accès à partir du')
                    ->placeholder('Date et heure de début')
                    ->helperText('Quand l\'accès commence (optionnel)')
                    ->hidden(fn ($get) => $get('is_eternal_owner')),
                
                DateTimePicker::make('until')
                    ->label('Accès jusqu\'au')
                    ->placeholder('Date et heure de fin')
                    ->helperText('Quand l\'accès se termine (optionnel)')
                    ->hidden(fn ($get) => $get('is_eternal_owner')),
            ]);
    }

    public static function getFormSchema(): array
    {
        return [
            Select::make('user_id')
                ->label('Utilisateur')
                ->placeholder('Rechercher un utilisateur...')
                ->options(User::all()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->required()
                ->helperText('Choisissez l\'utilisateur qui aura accès à tous les modules'),
            
            Toggle::make('is_eternal_owner')
                ->label('Super Administrateur permanent')
                ->helperText('Accès illimité à tous les modules et panneaux')
                ->default(true)
                ->reactive(),
            
            DateTimePicker::make('when')
                ->label('Accès à partir du')
                ->placeholder('Date et heure de début')
                ->helperText('Quand l\'accès commence (optionnel)')
                ->hidden(fn ($get) => $get('is_eternal_owner')),
            
            DateTimePicker::make('until')
                ->label('Accès jusqu\'au')
                ->placeholder('Date et heure de fin')
                ->helperText('Quand l\'accès se termine (optionnel)')
                ->hidden(fn ($get) => $get('is_eternal_owner')),
        ];
    }
}
