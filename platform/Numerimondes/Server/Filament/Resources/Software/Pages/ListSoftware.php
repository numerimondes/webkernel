<?php

namespace Platform\Numerimondes\Server\Filament\Resources\Software\Pages;

use Platform\Numerimondes\Server\Filament\Resources\Software\SoftwareResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSoftware extends ListRecords
{
  protected static string $resource = SoftwareResource::class;

  protected function getHeaderActions(): array
  {
    return [CreateAction::make()];
  }
}
