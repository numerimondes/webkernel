<?php

namespace Webkernel\Aptitudes\Platform\Updator\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

class ModuleExtractor
{
  public function __construct(private ProgressReporter $progressReporter) {}

  public function extractAndValidate(string $zipPath, string $tempDir, string $expectedHash): bool
  {
    if (!hash_file('sha256', $zipPath, false)) {
      throw new RuntimeException('Hash calculation failed.');
    }

    $calculatedHash = hash_file('sha256', $zipPath);
    if (!Hash::check($expectedHash, $calculatedHash)) {
      Log::error('Hash mismatch for module ZIP.', ['expected' => $expectedHash, 'calculated' => $calculatedHash]);
      return false;
    }

    $zip = new ZipArchive();
    if ($zip->open($zipPath) !== true) {
      throw new RuntimeException('Failed to open ZIP file.');
    }

    $extracted = $zip->extractTo($tempDir);
    $zip->close();

    if (!$extracted) {
      throw new RuntimeException('Extraction failed.');
    }

    $this->validateStructure($tempDir);

    $this->progressReporter->report('Extracted and validated module files.');

    return true;
  }

  private function validateStructure(string $tempDir): void
  {
    $composerPath = $tempDir . '/composer.json';
    if (!file_exists($composerPath)) {
      throw new RuntimeException('Invalid module: missing composer.json.');
    }

    $composer = json_decode(file_get_contents($composerPath), true);
    if (!isset($composer['extra']['webkernel']['app-class'])) {
      throw new RuntimeException('Invalid module: missing Webkernel app class in composer.json.');
    }
  }
}
