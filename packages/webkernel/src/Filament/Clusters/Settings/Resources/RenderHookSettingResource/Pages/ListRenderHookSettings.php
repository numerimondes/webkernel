<?php

namespace Webkernel\Filament\Clusters\Settings\Resources\RenderHookSettingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Webkernel\Filament\Clusters\Settings\Resources\RenderHookSettingResource;
use Filament\Tables\Actions\Action;
class ListRenderHookSettings extends ListRecords
{
    protected static string $resource = RenderHookSettingResource::class;
    public function getHeading(): string
    {
        return lang('components_display_settings');
    }
    public function getSubheading(): string
    {
        return lang('ds');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getCreateAction(): Action
    {

        return parent::getCreateAction()->label('New label');

    }
}

