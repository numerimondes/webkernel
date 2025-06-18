<?php

namespace Webkernel\Layouts\User;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;

class TabsLayout
{
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('User Details')
                ->tabs([
                    Tab::make('Personal Information')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                        ]),

                    Tab::make('Security')
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
