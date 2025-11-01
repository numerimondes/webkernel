<?php

namespace Webkernel\Aptitudes\WebsiteBuilder\Filament\System\Resources\WebsiteProjects\Pages;

use Webkernel\Aptitudes\WebsiteBuilder\Filament\System\Resources\WebsiteProjects\WebsiteProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema;

class ListWebsiteProjects extends ListRecords
{
    protected static string $resource = WebsiteProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->form(fn (Schema $schema) => WebsiteProjectResource::createForm($schema))
                ->modalHeading('Créer un nouveau projet')
                ->modalDescription('Remplissez les informations de base pour démarrer votre projet de site web')
                ->modalSubmitActionLabel('Créer le projet')
                ->modalCancelActionLabel('Annuler'),
        ];
    }
}
