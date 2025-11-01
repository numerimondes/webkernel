<?php

namespace Platform\EnjoyTheWorld\Filament\Resources\Providers\Pages;

use Platform\EnjoyTheWorld\Filament\Resources\Providers\ProviderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProvider extends EditRecord
{
  protected static string $resource = ProviderResource::class;

  protected function getHeaderActions(): array
  {
    return [DeleteAction::make(), ForceDeleteAction::make(), RestoreAction::make()];
  }
}
