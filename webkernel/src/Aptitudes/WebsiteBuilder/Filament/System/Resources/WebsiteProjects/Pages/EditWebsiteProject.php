<?php

namespace Webkernel\Aptitudes\WebsiteBuilder\Filament\System\Resources\WebsiteProjects\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Webkernel\Aptitudes\WebsiteBuilder\Filament\System\Resources\WebsiteProjects\WebsiteProjectResource;

class EditWebsiteProject extends EditRecord
{
    protected static string $resource = WebsiteProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_website_builder')
                ->label('Ouvrir Website Builder')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('primary')
                ->url(fn () => route('filament.system.resources.website-projects.website-builder', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record !== null),

            $this->getSaveFormAction(),
            $this->getCancelFormAction(),

            DeleteAction::make(),

        ];
    }
}
