<?php

namespace Webkernel\Core\Filament\Resources\UserPermissions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkernel\Core\Filament\Resources\UserPermissions\UserPermissionResource;

class CreateUserPermission extends CreateRecord
{
    protected static string $resource = UserPermissionResource::class;
}
