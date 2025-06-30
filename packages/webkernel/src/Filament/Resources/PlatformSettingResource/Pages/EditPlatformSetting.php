<?php

namespace Webkernel\Filament\Resources\PlatformSettingResource\Pages;

use Webkernel\Filament\Resources\PlatformSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlatformSetting extends EditRecord
{
    protected static string $resource = PlatformSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
