<?php

namespace Webkernel\Core\Filament\Resources\PlatformOwners\Tables;

use App\Models\User;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\TextSize;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Webkernel\Core\Models\PlatformOwner;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Webkernel\Core\Filament\Resources\PlatformOwners\Schemas\PlatformOwnerForm;

class PlatformOwnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                
                IconColumn::make('is_eternal_owner')
                    ->label('Super Admin')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('warning'),
                
                TextColumn::make('when')
                    ->label('De')
                    ->dateTime()
                    ->sortable(),
                
                TextColumn::make('until')
                    ->label('À')
                    ->dateTime()
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->size(TextSize::Small),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('edit_platform_owner')
                    ->label('Modifier')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->modalHeading('Modifier le Super Administrateur')
                    ->modalDescription('Modifiez les paramètres d\'accès de cet utilisateur.')
                    ->modalWidth('lg')
                    ->form(PlatformOwnerForm::getFormSchema())
                    ->fillForm(function (PlatformOwner $record): array {
                        return [
                            'user_id' => $record->user_id,
                            'is_eternal_owner' => $record->is_eternal_owner,
                            'when' => $record->when,
                            'until' => $record->until,
                        ];
                    })
                    ->action(function (PlatformOwner $record, array $data): void {
                        $record->update([
                            'user_id' => $data['user_id'],
                            'is_eternal_owner' => $data['is_eternal_owner'] ?? true,
                            'when' => $data['when'] ?? null,
                            'until' => $data['until'] ?? null,
                        ]);
                    })
                    ->modalSubmitActionLabel('Sauvegarder')
                    ->modalCancelActionLabel('Annuler')
                    ->successNotification(
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Super Admin modifié')
                            ->body('Les paramètres ont été mis à jour avec succès.')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
