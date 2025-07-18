<?php

namespace Webkernel\Core\Filament\Resources\RBAC\PlatformOwners\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkernel\Core\Filament\Resources\RBAC\PlatformOwners\PlatformOwnerResource;

class CreatePlatformOwner extends CreateRecord
{
    protected static string $resource = PlatformOwnerResource::class;
}
