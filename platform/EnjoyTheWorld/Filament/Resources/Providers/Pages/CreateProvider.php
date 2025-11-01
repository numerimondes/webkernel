<?php

namespace Platform\EnjoyTheWorld\Filament\Resources\Providers\Pages;

use Platform\EnjoyTheWorld\Filament\Resources\Providers\ProviderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProvider extends CreateRecord
{
  protected static string $resource = ProviderResource::class;
}
