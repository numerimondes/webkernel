<?php

namespace Webkernel\Aptitudes\Platform\Updator\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ListModulesCommand extends Command
{
  protected $signature = 'numerimondes:list {--updates}';
  protected $description = 'List installed or available modules';

  public function handle(): int
  {
    $showUpdates = $this->option('updates');
    $platformDir = base_path('platform');

    if (!is_dir($platformDir)) {
      $this->error('Platform directory not found.');
      return 1;
    }

    $modules = [];
    foreach (File::directories($platformDir) as $dir) {
      $composer = $dir . '/composer.json';
      if (file_exists($composer)) {
        $data = json_decode(file_get_contents($composer), true);
        $modules[] = [
          'name' => $data['name'] ?? basename($dir),
          'version' => $data['version'] ?? 'unknown',
          'path' => basename($dir),
        ];
      }
    }

    $headers = ['Name', 'Version', 'Path'];
    $this->table($headers, $modules);

    if ($showUpdates) {
      $this->warn('Updates check requires sync first.');
    }

    return 0;
  }
}
