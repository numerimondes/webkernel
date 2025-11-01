<?php

namespace Webkernel\Aptitudes\Platform\Updator\Console;

use Illuminate\Console\Command;
use Webkernel\Aptitudes\Platform\Connector\Services\SyncService;

class SyncCommand extends Command
{
  protected $signature = 'numerimondes:sync';
  protected $description = 'Sync modules from master';

  public function handle(SyncService $syncService): int
  {
    $license = \Webkernel\Aptitudes\Platform\Core\Models\LocalLicense::first();
    if (!$license) {
      $this->error('No license configured.');
      return 1;
    }

    $result = $syncService->sync($license);
    if ($result['success']) {
      $this->info('Sync successful. Modules: ' . count($result['modules']) . ', Updates: ' . count($result['updates']));
      return 0;
    } else {
      $this->error('Sync failed: ' . $result['error']);
      return 1;
    }
  }
}
