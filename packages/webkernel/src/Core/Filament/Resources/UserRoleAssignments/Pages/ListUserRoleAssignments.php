<?php

namespace Webkernel\Core\Filament\Resources\UserRoleAssignments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkernel\Core\Filament\Resources\UserRoleAssignments\UserRoleAssignmentResource;

class ListUserRoleAssignments extends ListRecords
{
    protected static string $resource = UserRoleAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
