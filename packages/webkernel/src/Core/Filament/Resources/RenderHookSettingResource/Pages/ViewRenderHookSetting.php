<?php

namespace Webkernel\Core\Filament\Resources\RenderHookSettingResource\Pages;

use Filament\Actions\EditAction;
use Webkernel\Core\Filament\Resources\RenderHookSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRenderHookSetting extends ViewRecord
{
    protected static string $resource = RenderHookSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
