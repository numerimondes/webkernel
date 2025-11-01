<?php declare(strict_types=1);
namespace Webkernel\Aptitudes\WebsiteBuilder\Filament\System\Resources\WebsiteProjects;

use BackedEnum;
use UnitEnum;
use Filament\{Tables\Table, Schemas\Schema, Resources\Resource, Support\Icons\Heroicon};
use Webkernel\Aptitudes\WebsiteBuilder\{
  Filament\System\Resources\WebsiteProjects\Schemas\WebsiteProjectForm,
  Filament\System\Resources\WebsiteProjects\Tables\WebsiteProjectsTable,
  Filament\System\Resources\WebsiteProjects\Pages\EditWebsiteProject,
  Filament\System\Resources\WebsiteProjects\Pages\ListWebsiteProjects,
  Models\WebsiteProject,
  Filament\Pages\WebsiteBuilder,
};

class WebsiteProjectResource extends Resource
{
  protected static ?string $model = WebsiteProject::class;

  protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

  protected static ?string $recordTitleAttribute = 'name';

  protected static string|UnitEnum|null $navigationGroup = 'Platform Tools';

  public static function form(Schema $schema): Schema
  {
    return WebsiteProjectForm::configure($schema);
  }

  public static function createForm(Schema $schema): Schema
  {
    return WebsiteProjectForm::createForm($schema);
  }

  public static function table(Table $table): Table
  {
    return WebsiteProjectsTable::configure($table);
  }

  public static function getNavigationBadge(): ?string
  {
    return (string) static::$model::count();
  }

  public static function getRelations(): array
  {
    return [
        //
      ];
  }

  public static function getPages(): array
  {
    return [
      'index' => ListWebsiteProjects::route('/'),
      'edit' => EditWebsiteProject::route('/{record}/edit'),
      'website-builder' => WebsiteBuilder::route('/{record}/website-builder'),
    ];
  }
}
