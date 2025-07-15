<?php

namespace Webkernel\Core\Filament\Resources\UserRoleAssignments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkernel\Core\Filament\Resources\UserRoleAssignments\UserRoleAssignmentResource;

class CreateUserRoleAssignment extends CreateRecord
{
    protected static string $resource = UserRoleAssignmentResource::class;
}
