<?php

namespace Webkernel\Aptitudes\Platform\Updator\Services;

use Illuminate\Support\Facades\Log;
use RuntimeException;

class ModuleUpdater
{
  public function __construct(private ModuleInstaller $installer, private ProgressReporter $progress) {}

  public function update(
    string $zipPath,
    string $moduleId,
    string $expectedHash,
    string $targetDir,
    string $currentVersion,
    string $newVersion,
    bool $allowMajor = false,
  ): void {
    $semVerCompare = $this->compareSemVer($currentVersion, $newVersion);
    if ($semVerCompare === 2 && !$allowMajor) {
      // Major update
      throw new RuntimeException('Major version update requires confirmation.');
    }

    $this->progress->report('Starting update', 10);

    $this->installer->install($zipPath, $moduleId . '_update', $expectedHash, $targetDir);

    Log::info('Module updated.', [
      'module_id' => $moduleId,
      'from' => $currentVersion,
      'to' => $newVersion,
      'type' => $semVerCompare === 0 ? 'patch' : ($semVerCompare === 1 ? 'minor' : 'major'),
    ]);
  }

  private function compareSemVer(string $v1, string $v2): int
  {
    $parts1 = explode('.', $v1);
    $parts2 = explode('.', $v2);
    for ($i = 0; $i < 3; $i++) {
      $p1 = (int) ($parts1[$i] ?? 0);
      $p2 = (int) ($parts2[$i] ?? 0);
      if ($p1 < $p2) {
        return 2;
      } // Major/minor increase
      if ($p1 > $p2) {
        return -1;
      }
    }
    return 0; // Patch or equal
  }
}
