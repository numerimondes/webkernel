<?php

declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Filament\Resources\Providers;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Platform\EnjoyTheWorld\Filament\Resources\Providers\Pages\CreateProvider;
use Platform\EnjoyTheWorld\Filament\Resources\Providers\Pages\EditProvider;
use Platform\EnjoyTheWorld\Filament\Resources\Providers\Pages\ListProviders;
use Platform\EnjoyTheWorld\Filament\Resources\Providers\Schemas\ProviderForm;
use Platform\EnjoyTheWorld\Filament\Resources\Providers\Tables\ProvidersTable;
use Platform\EnjoyTheWorld\Models\Provider;

class ProviderResource extends Resource
{
  protected static ?string $model = Provider::class;

  protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

  protected static ?string $recordTitleAttribute = 'company_name';

  // protected static ?string $navigationGroup = 'Services Management';

  protected static ?int $navigationSort = 10;

  /**
   * Get navigation label
   *
   * @return string
   */
  public static function getNavigationLabel(): string
  {
    return __('Providers');
  }

  /**
   * Get plural label
   *
   * @return string
   */
  public static function getPluralLabel(): string
  {
    return __('Providers');
  }

  /**
   * Get model label
   *
   * @return string
   */
  public static function getModelLabel(): string
  {
    return __('Provider');
  }

  /**
   * Configure form schema
   *
   * @param Schema $schema
   * @return Schema
   */
  public static function form(Schema $schema): Schema
  {
    return ProviderForm::configure($schema);
  }

  /**
   * Configure table
   *
   * @param Table $table
   * @return Table
   */
  public static function table(Table $table): Table
  {
    return ProvidersTable::configure($table);
  }

  /**
   * Get relation managers
   *
   * @return array<class-string>
   */
  public static function getRelations(): array
  {
    return [RelationManagers\ServicesRelationManager::class];
  }

  /**
   * Get pages configuration
   *
   * @return array<string, \Filament\Resources\Pages\PageRegistration>
   */
  public static function getPages(): array
  {
    return [
      'index' => ListProviders::route('/'),
      'create' => CreateProvider::route('/create'),
      'edit' => EditProvider::route('/{record}/edit'),
    ];
  }

  /**
   * Get Eloquent query for route model binding
   *
   * @return Builder
   */
  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
  }

  /**
   * Can view any
   *
   * @return bool
   */
  public static function canViewAny(): bool
  {
    return true;
  }

  /**
   * Can create
   *
   * @return bool
   */
  public static function canCreate(): bool
  {
    return true;
  }

  /**
   * Can edit
   *
   * @param Provider $record
   * @return bool
   */
  public static function canEdit($record): bool
  {
    return true;
  }

  /**
   * Can delete
   *
   * @param Provider $record
   * @return bool
   */
  public static function canDelete($record): bool
  {
    return true;
  }
}
