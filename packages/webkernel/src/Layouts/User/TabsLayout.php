<?php

namespace Webkernel\Layouts\User;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;

class TabsLayout
{
    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('User Details')
                ->tabs([
                    Tabs\Tab::make('Personal Information')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                        ]),

                    Tabs\Tab::make('Security')
                        ->schema([
                            TextInput::make('password')
                                ->password()
                                ->required(fn (string $context): bool => $context === 'create')
                                ->maxLength(255),

                            DateTimePicker::make('email_verified_at'),
                        ]),
                ])
        ]);
    }

    public static function getTableColumns(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'email_verified_at' => 'Verified',
        ];
    }
}
