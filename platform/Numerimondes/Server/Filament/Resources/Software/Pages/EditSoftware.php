<?php

namespace Platform\Numerimondes\Server\Filament\Resources\Software\Pages;

use Platform\Numerimondes\Server\Filament\Resources\Software\SoftwareResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSoftware extends EditRecord
{
  protected static string $resource = SoftwareResource::class;

  protected function getHeaderActions(): array
  {
    return [DeleteAction::make()];
  }
}
