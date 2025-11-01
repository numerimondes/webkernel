<?php

namespace Webkernel\Aptitudes\Platform\Updator\Console;

use Illuminate\Console\Command;
use Webkernel\Aptitudes\Platform\Updator\Services\ModuleUpdater;

class UpdateModuleCommand extends Command
{
  protected $signature = 'numerimondes:update {moduleId} {--zip=} {--hash=} {--current=} {--new=} {--allow-major} {--target=}';
  protected $description = 'Update a module from ZIP';

  public function handle(ModuleUpdater $updater): int
  {
    $moduleId = $this->argument('moduleId');
    $zipPath = $this->option('zip') ?? storage_path('app/temp/' . $moduleId . '.zip');
    $hash = $this->option('hash');
    $currentVersion = $this->option('current');
    $newVersion = $this->option('new');
    $allowMajor = $this->option('allow-major');
    $target = $this->option('target') ?? base_path('platform/' . $moduleId);

    if (!$hash || !$currentVersion || !$newVersion) {
      $this->error('Hash, current and new versions are required.');
      return 1;
    }

    try {
      $updater->update($zipPath, $moduleId, $hash, $target, $currentVersion, $newVersion, $allowMajor);
      $this->info('Update successful.');
      return 0;
    } catch (\Exception $e) {
      $this->error($e->getMessage());
      return 1;
    }
  }
}
