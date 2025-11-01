<?php

namespace Webkernel\Aptitudes\Platform\Updator\Filament\Resources\InstalledModulesResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkernel\Aptitudes\Platform\Updator\Filament\Resources\InstalledModulesResource;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Collection;

class ListInstalledModules extends ListRecords
{
  protected static string $resource = InstalledModulesResource::class;

  /**
   * Get table query - scan platform directory
   */
  protected function getTableQuery(): Collection
  {
    $platformDir = base_path('platform');

    if (!is_dir($platformDir)) {
      return collect([]);
    }

    $modules = [];

    foreach (File::directories($platformDir) as $dir) {
      $composerPath = $dir . '/composer.json';

      if (file_exists($composerPath)) {
        $data = json_decode(file_get_contents($composerPath), true);

        $modules[] = (object) [
          'id' => basename($dir),
          'name' => $data['name'] ?? basename($dir),
          'version' => $data['version'] ?? 'unknown',
          'path' => basename($dir),
          'description' => $data['description'] ?? '',
        ];
      }
    }

    return collect($modules);
  }

  protected function getHeaderActions(): array
  {
    return [
      \Filament\Actions\Action::make('check_updates')
        ->label('Check for Updates')
        ->icon('heroicon-o-arrow-path')
        ->action(function () {
          $license = \Webkernel\Aptitudes\Platform\Core\Models\LocalLicense::first();

          if (!$license) {
            \Filament\Notifications\Notification::make()->danger()->title('No license configured')->send();
            return;
          }

          // Sync to get latest info
          $result = app(\Webkernel\Aptitudes\Platform\Connector\Services\SyncService::class)->sync($license);

          if ($result['success'] && !empty($result['updates'])) {
            \Filament\Notifications\Notification::make()
              ->success()
              ->title(count($result['updates']) . ' update(s) available')
              ->send();
          } else {
            \Filament\Notifications\Notification::make()->info()->title('All modules up to date')->send();
          }
        }),
    ];
  }
}
