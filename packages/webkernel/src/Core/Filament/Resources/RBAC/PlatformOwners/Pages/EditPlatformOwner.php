<?php

namespace Webkernel\Core\Filament\Resources\RBAC\PlatformOwners\Pages;

use Filament\Resources\Pages\EditRecord;
use Webkernel\Core\Filament\Resources\RBAC\PlatformOwners\PlatformOwnerResource;

class EditPlatformOwner extends EditRecord
{
    protected static string $resource = PlatformOwnerResource::class;
}
