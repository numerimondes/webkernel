<?php

namespace Platform\EnjoyTheWorld\Filament\Resources\Providers\Pages;

use Platform\EnjoyTheWorld\Filament\Resources\Providers\ProviderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProviders extends ListRecords
{
  protected static string $resource = ProviderResource::class;

  protected function getHeaderActions(): array
  {
    return [CreateAction::make()];
  }
}
