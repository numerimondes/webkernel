<?php

namespace Webkernel\Aptitudes\Platform\Updator\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MigrationRunner
{
  public function runMigrations(string $modulePath): void
  {
    $migrationsPath = $modulePath . '/database/migrations';
    if (!is_dir($migrationsPath)) {
      Log::info('No migrations found for module.');
      return;
    }

    $exitCode = Artisan::call('migrate', [
      '--path' => $migrationsPath,
      '--force' => true, // Forward-only in prod
    ]);

    if ($exitCode !== 0) {
      Log::error('Migrations failed.', ['exit_code' => $exitCode]);
      throw new RuntimeException('Module migrations failed.');
    }

    Log::info('Migrations completed successfully.');
  }
}
