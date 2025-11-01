<?php

namespace Webkernel\Aptitudes\Platform\Updator\Services;

use Illuminate\Support\Facades\Log;

class OverrideManager
{
  private array $overridePaths = [
    'composer.json',
    'app/Models/User.php',
    'config/app.php',
    'config/database.php',
    'basix', // Entire directory
  ];

  public function __construct(private BackupService $backupService) {}

  public function applyOverrides(string $moduleTempDir, string $backupName): void
  {
    foreach ($this->overridePaths as $path) {
      $moduleOverride = $moduleTempDir . '/overrides/' . $path;
      if (!is_dir($moduleOverride) && !file_exists($moduleOverride)) {
        continue;
      }

      $hostPath = base_path($path);
      if (is_dir($moduleOverride)) {
        $hostPath = base_path($path);
        if (is_dir($hostPath)) {
          $this->backupService->createBackup($hostPath, $backupName . '_' . basename($path));
          // Copy directory (recursive)
          $this->recursiveCopy($moduleOverride, $hostPath);
        }
      } else {
        if (file_exists($hostPath)) {
          $this->backupService->createBackup($hostPath, $backupName . '_' . basename($path));
        }
        copy($moduleOverride, $hostPath);
      }

      Log::info('Override applied.', ['path' => $path]);
    }
  }

  public function rollbackOverrides(string $backupName): void
  {
    foreach ($this->overridePaths as $path) {
      $backupZip = Storage::path('numerimondes/backups/' . $backupName . '_' . basename($path) . '.zip');
      if (file_exists($backupZip)) {
        $targetDir = base_path($path);
        $this->backupService->restoreBackup($backupZip, dirname($targetDir));
        Log::info('Override rolled back.', ['path' => $path]);
      }
    }
  }

  private function recursiveCopy(string $source, string $dest): void
  {
    $dir = opendir($source);
    @mkdir($dest);
    while (($file = readdir($dir)) !== false) {
      if ($file != '.' && $file != '..') {
        if (is_dir($source . '/' . $file)) {
          $this->recursiveCopy($source . '/' . $file, $dest . '/' . $file);
        } else {
          copy($source . '/' . $file, $dest . '/' . $file);
        }
      }
    }
    closedir($dir);
  }
}
