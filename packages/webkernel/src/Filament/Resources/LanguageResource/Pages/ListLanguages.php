<?php

namespace Webkernel\Filament\Resources\LanguageResource\Pages;

use Filament\Actions\CreateAction;
use Webkernel\Filament\Resources\LanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLanguages extends ListRecords
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
