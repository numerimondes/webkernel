<?php

namespace Numerimondes\Modules\ReamMar\Core\Filament\Resources\Missions;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Missions\Pages\CreateMission;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Missions\Pages\EditMission;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Missions\Pages\ListMissions;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Missions\Pages\ViewMission;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Missions\Schemas\MissionForm;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Missions\Schemas\MissionInfolist;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Missions\Tables\MissionsTable;
use Numerimondes\Modules\ReamMar\Core\Models\Mission;

class MissionResource extends Resource
{
    protected static ?string $model = Mission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;
    protected static ?string $navigationLabel = 'Missions MAR';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return MissionForm::configure($schema);
    }

  //  public static function infolist(Schema $schema): Schema
  //  {
  //      return MissionInfolist::configure($schema);
  //  }

    public static function table(Table $table): Table
    {
        return MissionsTable::configure($table);
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
            'index' => ListMissions::route('/'),
            'create' => CreateMission::route('/create'),
            'view' => ViewMission::route('/{record}'),
            'edit' => EditMission::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }
}
