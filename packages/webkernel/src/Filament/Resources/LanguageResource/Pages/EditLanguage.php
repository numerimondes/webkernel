<?php

namespace Webkernel\Filament\Resources\LanguageResource\Pages;

use Webkernel\Filament\Resources\LanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLanguage extends EditRecord
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getRelationManagers(): array
    {
        return [];
    }
}
