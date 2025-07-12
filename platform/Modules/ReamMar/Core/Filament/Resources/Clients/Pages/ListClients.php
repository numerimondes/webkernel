<?php

namespace Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Pages;

use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\ClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
