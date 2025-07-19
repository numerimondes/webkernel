<?php

namespace Numerimondes\Modules\ReamMar\Core\Filament\Resources\Missions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMissions extends ListRecords
{
    protected static string $resource = \Numerimondes\Modules\ReamMar\Core\Filament\Resources\Missions\MissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
