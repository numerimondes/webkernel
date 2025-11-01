<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Pages;

use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\AccessControlResource;
use Webkernel\Aptitudes\AccessControl\Models\PermissionGroup;
use Filament\Actions\{Action, DeleteAction, ViewAction};
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @extends EditRecord<PermissionGroup>
 */
class EditAccessControl extends EditRecord
{
  protected static string $resource = AccessControlResource::class;

  /**
   * @return array<Action>
   */
  protected function getHeaderActions(): array
  {
    return [ViewAction::make(), DeleteAction::make()->disabled(fn(): bool => $this->record->is_system)];
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }

  protected function getSavedNotificationTitle(): ?string
  {
    return 'Permission group updated successfully';
  }

  protected function beforeSave(): void
  {
    $this->clearUserPermissionsCache();
  }

  /**
   * Clear permission cache for all users in this group
   */
  protected function clearUserPermissionsCache(): void
  {
    /** @var PermissionGroup $record */
    $record = $this->record;

    // Utiliser la relation pour récupérer les IDs
    $userIds = $record->users()->pluck('users.id');

    foreach ($userIds as $userId) {
      Cache::forget("user_permissions_{$userId}");
    }
  }
}
