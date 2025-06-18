<?php
namespace Webkernel\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Webkernel\Filament\Resources\LanguageResource\Pages\ListLanguages;
use Webkernel\Filament\Resources\LanguageResource\Pages\CreateLanguage;
use Webkernel\Filament\Resources\LanguageResource\Pages\EditLanguage;
use Webkernel\Filament\Resources\LanguageResource\Pages\ViewLanguage;
use Webkernel\Filament\Resources\LanguageResource\Pages;
use Webkernel\Models\Language;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkernel\Filament\Resources\LanguageResource\RelationManagers\LanguageTranslationsRelationManager;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Actions\DeleteAction;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;
    protected static ?string $navigationIcon = 'heroicon-o-language';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                TextInput::make('ISO')
                    ->required()
                    ->maxLength(255),
                TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_system_lang')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->searchable(),
                TextColumn::make('code')
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->afterStateUpdated(fn() => redirect(request()->header('Referer'))),
                // Tables\Columns\IconColumn::make('is_system_lang')->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            LanguageTranslationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLanguages::route('/'),
            'create' => CreateLanguage::route('/create'),
            'edit' => EditLanguage::route('/{record}/edit'),
            'view' => ViewLanguage::route('/{record}/view'),
        ];
    }
}
