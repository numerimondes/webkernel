<?php

namespace Webkernel\Core\Filament\Resources\UserPermissions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Webkernel\Core\Filament\Resources\UserPermissions\UserPermissionResource;

class EditUserPermission extends EditRecord
{
    protected static string $resource = UserPermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
