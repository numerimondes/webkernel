<?php

namespace Webkernel\Core\Filament\Resources\UserRoleAssignments\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Webkernel\Core\Filament\Resources\UserRoleAssignments\UserRoleAssignmentResource;

class EditUserRoleAssignment extends EditRecord
{
    protected static string $resource = UserRoleAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
