<?php

namespace Platform\EnjoyTheWorld\Filament\Resources\Services\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Platform\EnjoyTheWorld\Filament\Resources\Services\ServiceResource;

class ListServices extends ListRecords
{
  protected static string $resource = ServiceResource::class;

  protected function getHeaderActions(): array
  {
    return [CreateAction::make()];
  }
}
