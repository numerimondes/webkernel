<?php declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Widgets\Stats as AccessControlStatsWidget;
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\AccessControlResource;
use Webkernel\Aptitudes\AccessControl\Logic\Resources\AccessControlPolicy;
use Webkernel\Aptitudes\AccessControl\Models\Permission;

class ListAccessControl extends ListRecords
{
  protected static string $resource = AccessControlResource::class;
  protected function getHeaderActions(): array
  {
    return [
      CreateAction::make()->label('New Permission Group')->icon('heroicon-o-plus'),

      Action::make('sync_permissions')
        ->label('Sync Permissions')
        ->icon('heroicon-o-arrow-path')
        ->color('warning')
        ->requiresConfirmation()
        ->modalHeading('Sync Permissions from Resources')
        ->modalDescription('This will discover and create permissions for all registered Filament resources.')
        ->modalSubmitActionLabel('Sync Now')
        ->action(function (): void {
          try {
            AccessControlPolicy::discoverAndCreatePermissions();

            $count = Permission::count();

            Notification::make()
              ->title('Permissions Synchronized')
              ->body("Successfully synchronized permissions. Total: {$count}")
              ->success()
              ->send();
          } catch (\Exception $e) {
            Notification::make()
              ->title('Sync Failed')
              ->body('Failed to sync permissions: ' . $e->getMessage())
              ->danger()
              ->send();
          }
        }),

      Action::make('view_permissions')
        ->label('View All Permissions')
        ->icon('heroicon-o-eye')
        ->color('info')
        ->modalHeading('All System Permissions')
        ->modalWidth('7xl')
        ->modalContent(function (): \Illuminate\View\View {
          $permissions = Permission::orderBy('module')
            ->orderBy('model_class')
            ->orderBy('action')
            ->get()
            ->groupBy('module');

          return view('access-control::permissions-list', [
            'permissions' => $permissions,
          ]);
        })
        ->modalSubmitAction(false)
        ->modalCancelActionLabel('Close'),
    ];
  }

  protected function getHeaderWidgets(): array
  {
    return [AccessControlStatsWidget::class];
  }
}
