<?php declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Pages\{
  CreateAccessControl,
  EditAccessControl,
  ListAccessControl,
  ViewAccessControl,
};
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\AccessControlForm;
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Tables\AccessControlTable;
use Webkernel\Aptitudes\AccessControl\Models\PermissionGroup;

class AccessControlResource extends Resource
{
  protected static ?string $model = PermissionGroup::class;
  protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
  protected static string|UnitEnum|null $navigationGroup = 'Administration';
  protected static ?string $navigationLabel = 'Access Control';
  protected static ?string $modelLabel = 'Permission Group';
  protected static ?string $pluralModelLabel = 'Permission Groups';
  protected static ?int $navigationSort = 100;
  protected static ?string $recordTitleAttribute = 'name';
  protected static ?string $slug = 'access-control';

  public static function form(Schema $schema): Schema
  {
    return AccessControlForm::configure($schema);
  }

  public static function table(Table $table): Table
  {
    return AccessControlTable::configure($table);
  }

  public static function getRelations(): array
  {
    return [];
  }

  public static function getPages(): array
  {
    return [
      'index' => ListAccessControl::route('/'),
      'create' => CreateAccessControl::route('/create'),
      'edit' => EditAccessControl::route('/{record}/edit'),
      'view' => ViewAccessControl::route('/{record}'),
    ];
  }

  public static function getNavigationBadge(): ?string
  {
    return (string) static::$model::count();
  }

  public static function getNavigationBadgeColor(): ?string
  {
    return 'primary';
  }
}
