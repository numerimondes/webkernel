<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\Base\Commands\MergeElementsCommand;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ChecksumGenerator
{
  /**
   * @var Command
   */
  private Command $command;

  public function __construct(Command $command)
  {
    $this->command = $command;
  }

  /**
   * Generate checksums for an array of file paths.
   *
   * @param array<string> $files
   * @return string
   */
  public function generate(array $files): string
  {
    $this->command->info('Generating checksums for ' . count($files) . ' files...');

    $projectRoot = base_path();
    $output = "# CHECKSUM INTEGRITY VERIFICATION\n";
    $output .= '# Generated on: ' . date('Y-m-d H:i:s') . "\n";
    $output .= "# Algorithm: SHA256\n";
    $output .= '# Total files: ' . count($files) . "\n\n";

    $progressBar = $this->command->getOutput()->createProgressBar(count($files));
    $progressBar->start();

    foreach ($files as $file) {
      if (!File::exists($file)) {
        $progressBar->advance();
        continue;
      }

      $hash = hash_file('sha256', $file);
      $relativePath = ltrim(str_replace($projectRoot, '', $file), DIRECTORY_SEPARATOR);
      $output .= "{$hash}  {$relativePath}\n";

      $progressBar->advance();
    }

    $progressBar->finish();
    $this->command->newLine(2);

    return $output;
  }

  /**
   * Save checksums to a separate file.
   *
   * @param array<string> $files
   * @param string $outputPath
   * @return void
   */
  public function saveToFile(array $files, string $outputPath): void
  {
    $content = $this->generate($files);
    File::put($outputPath, $content);
    $this->command->comment("Checksum file saved: {$outputPath}");
  }

  /**
   * Verify checksums from a checksum file.
   *
   * @param string $checksumFile
   * @return array{passed: int, failed: int, missing: int, errors: array<string>}
   */
  public function verify(string $checksumFile): array
  {
    if (!File::exists($checksumFile)) {
      $this->command->error("Checksum file not found: {$checksumFile}");
      return ['passed' => 0, 'failed' => 0, 'missing' => 0, 'errors' => []];
    }

    $content = File::get($checksumFile);
    $lines = explode("\n", $content);

    $passed = 0;
    $failed = 0;
    $missing = 0;
    $errors = [];

    foreach ($lines as $line) {
      $line = trim($line);

      if (empty($line) || str_starts_with($line, '#')) {
        continue;
      }

      if (preg_match('/^([a-f0-9]{64})\s+(.+)$/', $line, $matches)) {
        $expectedHash = $matches[1];
        $filePath = base_path($matches[2]);

        if (!File::exists($filePath)) {
          $missing++;
          $errors[] = "Missing: {$matches[2]}";
          continue;
        }

        $actualHash = hash_file('sha256', $filePath);

        if ($expectedHash === $actualHash) {
          $passed++;
        } else {
          $failed++;
          $errors[] = "Failed: {$matches[2]} (hash mismatch)";
        }
      }
    }

    return [
      'passed' => $passed,
      'failed' => $failed,
      'missing' => $missing,
      'errors' => $errors,
    ];
  }
}
