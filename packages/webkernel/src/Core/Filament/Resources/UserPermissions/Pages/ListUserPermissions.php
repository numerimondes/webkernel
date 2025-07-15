<?php

namespace Webkernel\Core\Filament\Resources\UserPermissions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkernel\Core\Filament\Resources\UserPermissions\UserPermissionResource;

class ListUserPermissions extends ListRecords
{
    protected static string $resource = UserPermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
