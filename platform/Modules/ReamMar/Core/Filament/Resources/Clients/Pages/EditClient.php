<?php

namespace Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Pages;

use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\ClientResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
