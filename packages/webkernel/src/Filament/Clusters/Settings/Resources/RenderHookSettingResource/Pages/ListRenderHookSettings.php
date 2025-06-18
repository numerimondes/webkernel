<?php

namespace Webkernel\Filament\Clusters\Settings\Resources\RenderHookSettingResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Actions;
use Illuminate\Support\HtmlString;
use Filament\Resources\Pages\ListRecords;
use Webkernel\Filament\Clusters\Settings\Resources\RenderHookSettingResource;

class ListRenderHookSettings extends ListRecords
{
    protected static string $resource = RenderHookSettingResource::class;
    public function getHeading(): string
    {
        return lang('components_display_settings_heading');
   }
    public function getSubheading(): string
    {
        return lang('components_display_settings_subheading');
    }
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getCreateAction(): Action
    {

        return parent::getCreateAction()->label('New label');

    }
}

