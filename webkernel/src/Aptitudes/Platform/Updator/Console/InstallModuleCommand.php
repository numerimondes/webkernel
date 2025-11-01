<?php

namespace Webkernel\Aptitudes\Platform\Updator\Console;

use Illuminate\Console\Command;
use Webkernel\Aptitudes\Platform\Updator\Services\ModuleInstaller;

class InstallModuleCommand extends Command
{
  protected $signature = 'numerimondes:install {moduleId} {--zip=} {--hash=} {--target=}';
  protected $description = 'Install a module from ZIP';

  public function handle(ModuleInstaller $installer): int
  {
    $moduleId = $this->argument('moduleId');
    $zipPath = $this->option('zip') ?? storage_path('app/temp/' . $moduleId . '.zip');
    $hash = $this->option('hash');
    $target = $this->option('target') ?? base_path('platform/' . $moduleId);

    if (!$hash) {
      $this->error('Hash is required.');
      return 1;
    }

    try {
      $installer->install($zipPath, $moduleId, $hash, $target);
      $this->info('Installation successful.');
      return 0;
    } catch (\Exception $e) {
      $this->error($e->getMessage());
      return 1;
    }
  }
}
