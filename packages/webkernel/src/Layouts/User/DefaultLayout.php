<?php

namespace Webkernel\Layouts\User;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Webkernel\Filament\Resources\UserResource\Pages\ListUsers;
use Webkernel\Filament\Resources\UserResource\Pages\CreateUser;
use Webkernel\Filament\Resources\UserResource\Pages\EditUser;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;

class DefaultLayout
{
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('User Information')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),

                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),

                    TextInput::make('password')
                        ->password()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255)
                        ->columnSpan(1),

                    DateTimePicker::make('email_verified_at')
                        ->columnSpan(1),
                ])
                ->columns(2)
        ]);
    }

    public static function table($table)
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function pages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
