<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Pages;

use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\AccessControlResource;
use Webkernel\Aptitudes\AccessControl\Models\{AuditLog, PermissionGroup};
use Filament\Actions\{Action, EditAction};
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends ViewRecord<PermissionGroup>
 */
class ViewAccessControl extends ViewRecord
{
  protected static string $resource = AccessControlResource::class;

  /**
   * @return array<Action>
   */
  protected function getHeaderActions(): array
  {
    return [
      EditAction::make()->disabled(fn(): bool => $this->record->slug === 'superadmin'),
      Action::make('audit_log')
        ->label('View Audit Log')
        ->icon('heroicon-o-document-text')
        ->color('warning')
        ->modalHeading(fn(): string => 'Audit Log for ' . $this->record->name)
        ->modalWidth('6xl')
        ->modalContent(function (): \Illuminate\View\View {
          $logs = $this->getAuditLogsForRecord();

          return view('access-control::audit-log', [
            'logs' => $logs,
          ]);
        })
        ->modalSubmitAction(false)
        ->modalCancelActionLabel('Close'),
    ];
  }

  /**
   * Retrieve audit logs with related user data
   *
   * @return Collection<int, AuditLog>
   */
  protected function getAuditLogsForRecord(): Collection
  {
    /** @var PermissionGroup $record */
    $record = $this->record;

    return AuditLog::query()
      ->where('auditable_type', PermissionGroup::class)
      ->where('auditable_id', $record->id)
      ->with(['user', 'performedBy'])
      ->latest('created_at')
      ->limit(50)
      ->get();
  }
}
