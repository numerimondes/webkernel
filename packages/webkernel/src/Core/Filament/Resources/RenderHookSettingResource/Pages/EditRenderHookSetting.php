<?php

namespace Webkernel\Core\Filament\Resources\RenderHookSettingResource\Pages;

use Webkernel\Core\Filament\Resources\RenderHookSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRenderHookSetting extends EditRecord
{
    protected static string $resource = RenderHookSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }
}
