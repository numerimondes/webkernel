<?php

namespace Webkernel\Core\Filament\Resources\LanguageResource\Pages;

use Filament\Actions\EditAction;
use Webkernel\Core\Filament\Resources\LanguageResource\RelationManagers\LanguageTranslationsRelationManager;
use Webkernel\Core\Filament\Resources\LanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Table;
use Filament\Tables;
class ViewLanguage extends ViewRecord
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getRelations(): array
    {
        return [
            LanguageTranslationsRelationManager::class,
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return false;
    }
}
