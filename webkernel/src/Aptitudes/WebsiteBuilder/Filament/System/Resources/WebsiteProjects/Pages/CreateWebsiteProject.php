<?php

namespace Webkernel\Aptitudes\WebsiteBuilder\Filament\System\Resources\WebsiteProjects\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Webkernel\Aptitudes\WebsiteBuilder\Filament\System\Resources\WebsiteProjects\WebsiteProjectResource;

class CreateWebsiteProject extends CreateRecord
{
    protected static string $resource = WebsiteProjectResource::class;
    protected function getHeaderActions(): array
    {
        return [
            $this->getCreateFormAction(),
            ...($this->canCreateAnother() ? [$this->getCreateAnotherFormAction()] : []),
            $this->getCancelFormAction(),
        ];
    }
}
