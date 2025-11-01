<?php

namespace Webkernel\Aptitudes\Platform\Updator\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ModuleInstaller
{
  public function __construct(
    private ModuleExtractor $extractor,
    private BackupService $backupService,
    private OverrideManager $overrideManager,
    private MigrationRunner $migrationRunner,
    private ProgressReporter $progress,
  ) {}

  public function install(string $zipPath, string $moduleId, string $expectedHash, string $targetDir): void
  {
    $tempDir = storage_path('app/temp/' . $moduleId);
    $backupName = 'install_' . $moduleId . '_' . now()->timestamp;

    try {
      $this->progress->report('Starting installation', 10);

      $this->extractor->extractAndValidate($zipPath, $tempDir, $expectedHash);

      $this->progress->report('Applying overrides', 40);
      $this->overrideManager->applyOverrides($tempDir, $backupName);

      $this->progress->report('Running migrations', 60);
      $this->migrationRunner->runMigrations($tempDir);

      $this->progress->report('Moving to target', 80);
      $this->moveModule($tempDir, $targetDir);

      Artisan::call('cache:clear');
      Artisan::call('config:clear');
      Artisan::call('route:clear');

      $this->progress->report('Installation completed', 100);

      Log::info('Module installed successfully.', ['module_id' => $moduleId]);
    } catch (\Exception $e) {
      $this->rollback($tempDir, $backupName, $targetDir);
      throw new RuntimeException('Installation failed: ' . $e->getMessage(), 0, $e);
    }
  }

  private function moveModule(string $tempDir, string $targetDir): void
  {
    if (is_dir($targetDir)) {
      $this->backupService->createBackup($targetDir, 'old_' . basename($targetDir));
      // Remove old if needed
    }
    rename($tempDir, $targetDir);
  }

  private function rollback(string $tempDir, string $backupName, string $targetDir): void
  {
    $this->overrideManager->rollbackOverrides($backupName);
    if (is_dir($tempDir)) {
      // Clean temp
      $this->backupService->cleanupOldBackups(); // Includes this one
    }
    Log::warning('Installation rolled back.', ['module_id' => basename($targetDir)]);
  }
}
