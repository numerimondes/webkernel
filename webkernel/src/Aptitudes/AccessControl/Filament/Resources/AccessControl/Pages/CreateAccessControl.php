<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Pages;

use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\AccessControlResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateAccessControl extends CreateRecord
{
  protected static string $resource = AccessControlResource::class;

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }

  protected function getCreatedNotificationTitle(): ?string
  {
    return 'Permission group created successfully';
  }

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    if (empty($data['slug'])) {
      $data['slug'] = Str::slug($data['name']);
    }

    return $data;
  }
}
