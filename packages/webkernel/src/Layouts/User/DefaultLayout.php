<?php

namespace Webkernel\Layouts\User;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;

class DefaultLayout
{
    public static function form(Form $form): Form
    {
        return $form->schema([
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
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function pages(): array
    {
        return [
            'index' => \Webkernel\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \Webkernel\Filament\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit' => \Webkernel\Filament\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
