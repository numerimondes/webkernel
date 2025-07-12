<?php

namespace Webkernel\Core\Filament\Resources\PlatformOwners\Pages;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Webkernel\Core\Models\PlatformOwner;
use Filament\Resources\Pages\ListRecords;
use Webkernel\Core\Filament\Resources\PlatformOwners\PlatformOwnerResource;
use Webkernel\Core\Filament\Resources\PlatformOwners\Schemas\PlatformOwnerForm;

class ListPlatformOwners extends ListRecords
{
    protected static string $resource = PlatformOwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add_platform_owner')
                ->label('Ajouter un Super Admin')
                ->icon('heroicon-o-shield-check')
                ->modalHeading('Nouveau Super Administrateur')
                ->modalDescription('Donnez accès complet à un utilisateur pour gérer tous les modules et panneaux.')
                ->modalWidth('lg')
                ->form(PlatformOwnerForm::getFormSchema())
                ->action(function (array $data): void {
                    PlatformOwner::create([
                        'user_id' => $data['user_id'],
                        'panel_id' => 'all', // Pour super admin
                        'is_eternal_owner' => $data['is_eternal_owner'] ?? true,
                        'when' => $data['when'] ?? null,
                        'until' => $data['until'] ?? null,
                    ]);
                })
                ->modalSubmitActionLabel('Créer le Super Admin')
                ->modalCancelActionLabel('Annuler')
                ->successNotification(
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Super Admin créé')
                        ->body('L\'utilisateur a maintenant accès à tous les modules.')
                ),
        ];
    }
}
