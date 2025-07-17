<?php

namespace Webkernel\Core\Filament\Resources\Lang\LanguageResource\Pages;

use Filament\Actions\CreateAction;
use Webkernel\Core\Filament\Resources\Lang\LanguageResource;
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
