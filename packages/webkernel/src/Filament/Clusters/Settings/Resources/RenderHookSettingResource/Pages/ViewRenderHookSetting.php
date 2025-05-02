<?php

namespace Webkernel\Filament\Clusters\Settings\Resources\RenderHookSettingResource\Pages;

use Webkernel\Filament\Clusters\Settings\Resources\RenderHookSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRenderHookSetting extends ViewRecord
{
    protected static string $resource = RenderHookSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
