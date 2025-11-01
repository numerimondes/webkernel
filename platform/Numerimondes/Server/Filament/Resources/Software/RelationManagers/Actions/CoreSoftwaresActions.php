<?php
declare(strict_types=1);

namespace Platform\Numerimondes\Server\Filament\Resources\Software\RelationManagers\Actions;

use Filament\Actions\Action;
use Platform\Numerimondes\Server\Models\SoftwareCore;
use Webkernel\Aptitudes\Security\ValidateSourceCodeZip\Filament\SourceCodeUploadZipOnForm;

class CoreSoftwaresActions
{
  public static function getHeaderActions(callable $getFormSchema, $ownerRecord): array
  {
    return [
      Action::make('create')
        ->label('Add Core Module')
        ->icon('heroicon-m-plus')
        ->modalHeading('Create New Core Module')
        ->modalDescription('Add a new core software module to this software')
        ->form($getFormSchema())
        ->mutateFormDataUsing(function (array $data) use ($ownerRecord): array {
          $data['software_id'] = $ownerRecord->id;
          $data['validation_status'] = 'pending';
          $data['hash'] = '';

          if (!isset($data['zip_path']) || empty($data['zip_path'])) {
            $data['zip_path'] = [];
          }

          return $data;
        })
        ->action(function (array $data) use ($ownerRecord): void {
          $zipPath = $data['zip_path'] ?? null;

          $softwareCore = $ownerRecord->coreSoftwares()->create($data);

          if ($zipPath && !empty($zipPath)) {
            SourceCodeUploadZipOnForm::handleZipUpload(zipPath: $zipPath, projectId: (string) $softwareCore->id);
          }
        })
        ->modalSubmitActionLabel('Create Module')
        ->successNotificationTitle('Core module created successfully'),
    ];
  }

  public static function getRowActions(callable $getFormSchema, $ownerRecord): array
  {
    return [
      Action::make('view_details')
        ->label('Details')
        ->icon('heroicon-o-eye')
        ->modalHeading(fn($record) => "Module Details: {$record->name}")
        ->modalContent(fn($record) => view('filament.modals.core-module-details', ['record' => $record]))
        ->modalSubmitAction(false)
        ->modalCancelActionLabel('Close'),

      Action::make('edit')
        ->label('Edit')
        ->icon('heroicon-m-pencil')
        ->modalHeading('Edit Core Module')
        ->form($getFormSchema())
        ->mutateFormDataUsing(function (array $data) use ($ownerRecord): array {
          $data['software_id'] = $ownerRecord->id;

          if (isset($data['zip_path']) && !empty($data['zip_path']) && empty($data['hash'])) {
            $data['hash'] = SourceCodeUploadZipOnForm::getChecksum($data['zip_path']);
          }

          return $data;
        })
        ->action(function (SoftwareCore $record, array $data): void {
          $record->update($data);

          if (isset($data['zip_path']) && $data['zip_path'] !== $record->zip_path) {
            SourceCodeUploadZipOnForm::handleZipUpload(zipPath: $data['zip_path'], projectId: (string) $record->id);
          }
        })
        ->modalSubmitActionLabel('Save Changes')
        ->successNotificationTitle('Core module updated successfully'),

      Action::make('revalidate')
        ->label('Revalidate')
        ->icon('heroicon-o-arrow-path')
        ->color('warning')
        ->requiresConfirmation()
        ->modalHeading('Revalidate Source Code')
        ->modalDescription('This will re-run security validation on the uploaded archive.')
        ->action(function (SoftwareCore $record): void {
          if ($record->zip_path) {
            $record->update(['validation_status' => 'processing']);
            SourceCodeUploadZipOnForm::handleZipUpload(zipPath: $record->zip_path, projectId: (string) $record->id);
          }
        })
        ->successNotificationTitle('Revalidation queued')
        ->visible(fn($record) => $record->zip_path !== null),

      Action::make('delete')
        ->label('Delete')
        ->icon('heroicon-m-trash')
        ->requiresConfirmation()
        ->modalHeading('Delete Core Module')
        ->modalDescription(
          'Are you sure? This action cannot be undone. All associated data will be permanently deleted.',
        )
        ->modalSubmitActionLabel('Delete')
        ->color('danger')
        ->action(fn(SoftwareCore $record) => $record->delete())
        ->successNotificationTitle('Core module deleted'),
    ];
  }

  public static function getEmptyStateActions(callable $getFormSchema, $ownerRecord): array
  {
    return [
      Action::make('create_first')
        ->label('Create Core Module')
        ->icon('heroicon-m-plus')
        ->modalHeading('Create First Core Module')
        ->form($getFormSchema())
        ->mutateFormDataUsing(function (array $data) use ($ownerRecord): array {
          $data['software_id'] = $ownerRecord->id;
          $data['validation_status'] = 'pending';
          $data['hash'] = '';

          if (!isset($data['zip_path']) || empty($data['zip_path'])) {
            $data['zip_path'] = [];
          }

          return $data;
        })
        ->action(function (array $data) use ($ownerRecord): void {
          $zipPath = $data['zip_path'] ?? null;

          $softwareCore = $ownerRecord->coreSoftwares()->create($data);

          if ($zipPath && !empty($zipPath)) {
            SourceCodeUploadZipOnForm::handleZipUpload(zipPath: $zipPath, projectId: (string) $softwareCore->id);
          }
        }),
    ];
  }
}
