<?php

namespace Webkernel\Core\Filament\Resources\Lang;

use BackedEnum;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use WEBKERNEL__LANGUAGE__MODEL as Language;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\IconSize;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkernel\Core\Filament\Resources\Lang\LanguageResource\Pages;
use Webkernel\Core\Filament\Resources\Lang\LanguageResource\Pages\EditLanguage;
use Webkernel\Core\Filament\Resources\Lang\LanguageResource\Pages\ViewLanguage;
use Webkernel\Core\Filament\Resources\Lang\LanguageResource\Pages\ListLanguages;
use Webkernel\Core\Filament\Resources\Lang\LanguageResource\Pages\CreateLanguage;
use Webkernel\Core\Filament\Resources\Lang\LanguageResource\RelationManagers\LanguageTranslationsRelationManager;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;


public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
{

        return 'heroicon-o-language';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

//    public static function getNavigationGroup(): ?string
//    {
//    return lang('system_menu_core_settings');
//    }

 //  public static function getNavigationParentItem(): ?string
  //  {
  //      return 'Settings';
  //  }

    public static function getModelLabel(): string
    {
        return lang('language');
    }

    public static function getPluralModelLabel(): string
    {
        return lang('languages');
    }
 
    protected static ?string $recordTitleAttribute = 'label';

    protected static ?int $navigationSort = 999;

    public static function form(Schema $schema): Schema
    {
        return $schema
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
