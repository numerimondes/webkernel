<?php

namespace Webkernel\Aptitudes\Platform\Updator\Filament\Resources\AvailableModulesResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkernel\Aptitudes\Platform\Updator\Filament\Resources\AvailableModulesResource;
use Webkernel\Aptitudes\Platform\Core\Services\LicenseCacheService;
use Illuminate\Database\Eloquent\Collection;

class ListAvailableModules extends ListRecords
{
  protected static string $resource = AvailableModulesResource::class;

  /**
   * Get table query - load from cache
   */
  protected function getTableQuery(): Collection
  {
    $cache = app(LicenseCacheService::class)->getCache();

    if (!$cache || !isset($cache['modules'])) {
      return collect([]);
    }

    return collect($cache['modules'])->map(function ($module) {
      return (object) [
        'id' => $module['id'],
        'identifier' => $module['identifier'],
        'name' => $module['name'],
        'version' => $module['version'],
        'description' => $module['description'] ?? '',
        'size' => $module['formatted_size'] ?? '',
        'hash' => $module['hash'] ?? '',
      ];
    });
  }

  protected function getHeaderActions(): array
  {
    return [
      \Filament\Actions\Action::make('sync')
        ->label('Sync Modules')
        ->icon('heroicon-o-arrow-path')
        ->action(function () {
          $license = \Webkernel\Aptitudes\Platform\Core\Models\LocalLicense::first();

          if (!$license) {
            \Filament\Notifications\Notification::make()->danger()->title('No license configured')->send();
            return;
          }

          $result = app(\Webkernel\Aptitudes\Platform\Connector\Services\SyncService::class)->sync($license);

          if ($result['success']) {
            \Filament\Notifications\Notification::make()->success()->title('Sync successful')->send();
          } else {
            \Filament\Notifications\Notification::make()
              ->danger()
              ->title('Sync failed')
              ->body($result['error'])
              ->send();
          }
        }),
    ];
  }
}
