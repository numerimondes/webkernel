<?php

namespace Webkernel\Aptitudes\Platform\Updator\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupService
{
  private const BACKUP_DIR = 'numerimondes/backups';
  private const TTL_HOURS = 12;

  public function createBackup(string $sourceDir, string $backupName): string
  {
    $backupPath = Storage::path(self::BACKUP_DIR . '/' . $backupName . '.zip');

    $zip = new \ZipArchive();
    if ($zip->open($backupPath, \ZipArchive::CREATE) !== true) {
      throw new \RuntimeException('Failed to create backup ZIP.');
    }

    $files = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($sourceDir),
      \RecursiveIteratorIterator::LEAVES_ONLY,
    );

    foreach ($files as $file) {
      if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($sourceDir) + 1);
        $zip->addFile($filePath, $relativePath);
      }
    }

    $zip->close();

    $metadata = [
      'created_at' => now()->toISOString(),
      'source_dir' => $sourceDir,
      'size' => filesize($backupPath),
    ];

    file_put_contents(Storage::path(self::BACKUP_DIR . '/' . $backupName . '.json'), json_encode($metadata));

    Log::info('Backup created.', ['path' => $backupPath]);

    return $backupPath;
  }

  public function restoreBackup(string $backupPath, string $targetDir): void
  {
    $zip = new \ZipArchive();
    if ($zip->open($backupPath) !== true) {
      throw new \RuntimeException('Failed to open backup ZIP.');
    }

    $zip->extractTo($targetDir);
    $zip->close();

    Log::info('Backup restored.', ['path' => $backupPath]);
  }

  public function cleanupOldBackups(): void
  {
    $disk = Storage::disk('local');
    $files = $disk->files(self::BACKUP_DIR);

    foreach ($files as $file) {
      if (str_ends_with($file, '.zip') || str_ends_with($file, '.json')) {
        $createdAt = Carbon::createFromTimestamp(Storage::lastModified(self::BACKUP_DIR . '/' . $file));
        if ($createdAt->diffInHours(now()) > self::TTL_HOURS) {
          $disk->delete($file);
        }
      }
    }
  }
}
