<?php

namespace Webkernel\Filament\Resources\UserResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Webkernel\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
