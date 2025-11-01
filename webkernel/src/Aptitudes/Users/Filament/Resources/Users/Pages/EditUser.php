<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\Users\Filament\Resources\Users\Pages;

use Webkernel\Aptitudes\Users\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
