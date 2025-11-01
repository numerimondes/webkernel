<?php

declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Filament\Resources\Services;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Platform\EnjoyTheWorld\Filament\Resources\Providers\Schemas\ServiceForm;
use Platform\EnjoyTheWorld\Filament\Resources\Services\Pages\{ListServices, CreateService, EditService};
use Platform\EnjoyTheWorld\Models\Service;

class ServiceResource extends Resource
{
  protected static ?string $model = Service::class;

  protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

  //  protected static ?string $navigationGroup = 'Services Management';

  protected static ?int $navigationSort = 20;

  protected static bool $shouldRegisterNavigation = false;

  /**
   * Get navigation label
   *
   * @return string
   */
  public static function getNavigationLabel(): string
  {
    return __('Services');
  }

  /**
   * Get plural label
   *
   * @return string
   */
  public static function getPluralLabel(): string
  {
    return __('Services');
  }

  /**
   * Get model label
   *
   * @return string
   */
  public static function getModelLabel(): string
  {
    return __('Service');
  }

  /**
   * Configure form schema
   *
   * @param Schema $schema
   * @return Schema
   */
  public static function form(Schema $schema): Schema
  {
    return ServiceForm::configure($schema);
  }

  /**
   * Configure table (not used, only for completeness)
   *
   * @param Table $table
   * @return Table
   */
  public static function table(Table $table): Table
  {
    return $table;
  }

  /**
   * Get pages configuration
   *
   * @return array<string, \Filament\Resources\Pages\PageRegistration>
   */
  public static function getPages(): array
  {
    return [
      'index' => ListServices::route('/'),
      'create' => CreateService::route('/create'),
      'edit' => EditService::route('/{record}/edit'),
    ];
  }
}
