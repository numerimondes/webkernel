<?php

namespace Webkernel\Core\Filament\Resources\RBAC\UserResource\Pages;

use Filament\Actions\CreateAction;
use Webkernel\Core\Filament\Resources\RBAC\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
