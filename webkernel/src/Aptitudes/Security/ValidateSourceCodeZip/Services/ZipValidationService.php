<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Security\ValidateSourceCodeZip\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use ZipArchive;
use Exception;

class ZipValidationService
{
  private const TEMP_DIR_PREFIX = 'zip-validation-';
  private const MAX_EXTRACTION_SIZE = 500 * 1024 * 1024; // 500MB
  private const MAX_FILE_SIZE = 100 * 1024 * 1024; // 100MB per file
  private const ALLOWED_EXTENSIONS = ['php', 'js', 'json', 'md', 'txt', 'yaml', 'yml', 'xml', 'html', 'css', 'lock'];
  private const FORBIDDEN_EXTENSIONS = ['sh', 'bat', 'exe', 'dll', 'so', 'bin', 'app', 'jar', 'pyc', 'o'];
  private const MAX_NESTING_DEPTH = 10;

  private string $zipPath;
  private string $tempDir;
  private array $validationResult = [];
  private array $extractedFiles = [];
  private array $metadata = [];

  public function __construct(private readonly string $storageDisk = 'private') {}

  /**
   * Validate a ZIP file with full security checks.
   *
   * @param string $relativePath Relative path from storage disk
   * @return array Validation result with status, checksums, tree, and metadata
   * @throws Exception
   */
  public function validate(string|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $relativePath): array
  {
    try {
      $this->zipPath = Storage::disk($this->storageDisk)->path($relativePath);

      if (!file_exists($this->zipPath)) {
        throw new Exception("ZIP file not found: {$this->zipPath}");
      }

      $this->tempDir = storage_path('app/temp/' . self::TEMP_DIR_PREFIX . Str::uuid());

      return $this->extractZip()
        ->validateStructure()
        ->validateFileTypes()
        ->validateFileSizes()
        ->generateChecksums()
        ->buildFileTree()
        ->extractMetadata()
        ->runStaticAnalysis()
        ->buildResult();
    } finally {
      $this->cleanup();
    }
  }

  /**
   * Extract ZIP archive to temporary directory.
   *
   * @return self
   * @throws Exception
   */
  private function extractZip(): self
  {
    @mkdir($this->tempDir, 0755, true);

    $zip = new ZipArchive();
    $openResult = $zip->open($this->zipPath);

    if ($openResult !== true) {
      throw new Exception("Failed to open ZIP archive: error code {$openResult}");
    }

    $extractResult = $zip->extractTo($this->tempDir);
    $zip->close();

    if (!$extractResult) {
      throw new Exception('Failed to extract ZIP archive');
    }

    $this->extractedFiles = File::allFiles($this->tempDir);
    $this->validationResult['extracted_files_count'] = count($this->extractedFiles);

    return $this;
  }

  /**
   * Validate ZIP structure (nesting depth, file count, total size).
   *
   * @return self
   * @throws Exception
   */
  private function validateStructure(): self
  {
    if (count($this->extractedFiles) === 0) {
      throw new Exception('ZIP archive is empty');
    }

    if (count($this->extractedFiles) > 10000) {
      throw new Exception('ZIP archive contains too many files: ' . count($this->extractedFiles));
    }

    $totalSize = 0;
    foreach ($this->extractedFiles as $file) {
      $totalSize += $file->getSize();
    }

    if ($totalSize > self::MAX_EXTRACTION_SIZE) {
      throw new Exception("Extracted archive exceeds maximum size: {$totalSize} bytes");
    }

    $this->validationResult['total_extracted_size'] = $totalSize;

    $maxDepth = $this->getMaxNestingDepth();
    if ($maxDepth > self::MAX_NESTING_DEPTH) {
      throw new Exception("Directory nesting too deep: depth {$maxDepth}");
    }

    $this->validationResult['max_nesting_depth'] = $maxDepth;

    return $this;
  }

  /**
   * Validate file types based on extensions.
   *
   * @return self
   * @throws Exception
   */
  private function validateFileTypes(): self
  {
    $invalidFiles = [];

    foreach ($this->extractedFiles as $file) {
      $extension = strtolower($file->getExtension());
      $filename = $file->getFilename();

      if (in_array($extension, self::FORBIDDEN_EXTENSIONS, true)) {
        $invalidFiles[] = [
          'path' => $file->getRelativePathname(),
          'reason' => "Forbidden extension: {$extension}",
        ];
        continue;
      }

      if (str_starts_with($filename, '.') && $extension !== '' && $extension !== 'gitkeep') {
        $invalidFiles[] = [
          'path' => $file->getRelativePathname(),
          'reason' => 'Hidden files not allowed',
        ];
        continue;
      }

      if (!empty($extension) && !in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
        $this->validationResult['warnings'][] = [
          'path' => $file->getRelativePathname(),
          'message' => "Non-standard extension: {$extension}",
        ];
      }
    }

    if (!empty($invalidFiles)) {
      throw new Exception('ZIP contains invalid files: ' . json_encode($invalidFiles));
    }

    return $this;
  }

  /**
   * Validate individual file sizes.
   *
   * @return self
   * @throws Exception
   */
  private function validateFileSizes(): self
  {
    foreach ($this->extractedFiles as $file) {
      if ($file->getSize() > self::MAX_FILE_SIZE) {
        throw new Exception(
          "File exceeds maximum size: {$file->getRelativePathname()} (" . $file->getSize() . ' bytes)',
        );
      }
    }

    return $this;
  }

  /**
   * Generate SHA256 checksums for all files and archive.
   *
   * @return self
   */
  private function generateChecksums(): self
  {
    $checksums = [];

    foreach ($this->extractedFiles as $file) {
      $checksums[$file->getRelativePathname()] = hash_file('sha256', $file->getRealPath());
    }

    $this->validationResult['checksums'] = $checksums;
    $this->validationResult['archive_checksum'] = hash_file('sha256', $this->zipPath);

    return $this;
  }

  /**
   * Build hierarchical file tree structure.
   *
   * @return self
   */
  private function buildFileTree(): self
  {
    $tree = [];

    foreach ($this->extractedFiles as $file) {
      $parts = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname());
      $current = &$tree;

      foreach ($parts as $index => $part) {
        if ($index === count($parts) - 1) {
          $current[$part] = [
            'type' => 'file',
            'size' => $file->getSize(),
            'extension' => $file->getExtension(),
            'checksum' => $this->validationResult['checksums'][$file->getRelativePathname()] ?? null,
          ];
        } else {
          if (!isset($current[$part])) {
            $current[$part] = ['type' => 'directory', 'children' => []];
          }
          $current = &$current[$part]['children'];
        }
      }
    }

    $this->validationResult['tree'] = $tree;

    return $this;
  }

  /**
   * Extract metadata from special files (composer.json, LICENSE, README, etc.).
   *
   * @return self
   */
  private function extractMetadata(): self
  {
    $rootFiles = $this->getRootFiles();

    if (isset($rootFiles['composer.json'])) {
      $this->metadata['composer'] = $this->parseComposerJson($rootFiles['composer.json']);
    }

    if (isset($rootFiles['LICENSE']) || isset($rootFiles['LICENSE.md']) || isset($rootFiles['LICENSE.txt'])) {
      $licenseFile = $rootFiles['LICENSE'] ?? ($rootFiles['LICENSE.md'] ?? $rootFiles['LICENSE.txt']);
      $this->metadata['license'] = [
        'exists' => true,
        'file' => basename($licenseFile),
        'content_preview' => substr(file_get_contents($licenseFile), 0, 500),
      ];
    } else {
      $this->metadata['license'] = ['exists' => false];
    }

    if (isset($rootFiles['README.md']) || isset($rootFiles['README.txt'])) {
      $readmeFile = $rootFiles['README.md'] ?? $rootFiles['README.txt'];
      $this->metadata['readme'] = [
        'exists' => true,
        'file' => basename($readmeFile),
        'content_preview' => substr(file_get_contents($readmeFile), 0, 500),
      ];
    } else {
      $this->metadata['readme'] = ['exists' => false];
    }

    $phpFiles = array_filter($this->extractedFiles, fn($file) => $file->getExtension() === 'php');
    $this->metadata['statistics'] = [
      'php_files_count' => count($phpFiles),
      'total_lines_of_code' => $this->countTotalLines($phpFiles),
    ];

    $this->validationResult['metadata'] = $this->metadata;

    return $this;
  }

  /**
   * Parse composer.json file and extract relevant information.
   *
   * @param string $composerPath
   * @return array
   */
  private function parseComposerJson(string $composerPath): array
  {
    try {
      $content = file_get_contents($composerPath);
      $composer = json_decode($content, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        return ['valid' => false, 'error' => 'Invalid JSON'];
      }

      return [
        'valid' => true,
        'name' => $composer['name'] ?? null,
        'description' => $composer['description'] ?? null,
        'version' => $composer['version'] ?? null,
        'type' => $composer['type'] ?? null,
        'license' => $composer['license'] ?? null,
        'authors' => $composer['authors'] ?? [],
        'require' => array_keys($composer['require'] ?? []),
        'require_dev' => array_keys($composer['require-dev'] ?? []),
        'autoload' => $composer['autoload'] ?? [],
      ];
    } catch (Exception $e) {
      return ['valid' => false, 'error' => $e->getMessage()];
    }
  }

  /**
   * Get all files in root directory of extracted archive.
   *
   * @return array
   */
  private function getRootFiles(): array
  {
    $rootFiles = [];

    foreach ($this->extractedFiles as $file) {
      if (substr_count($file->getRelativePathname(), DIRECTORY_SEPARATOR) === 0) {
        $rootFiles[$file->getFilename()] = $file->getRealPath();
      }
    }

    return $rootFiles;
  }

  /**
   * Count total lines of code in given files.
   *
   * @param array $files
   * @return int
   */
  private function countTotalLines(array $files): int
  {
    $totalLines = 0;

    foreach ($files as $file) {
      $totalLines += count(file($file->getRealPath()));
    }

    return $totalLines;
  }

  /**
   * Run static analysis on PHP files using PHPStan if available.
   *
   * @return self
   */
  private function runStaticAnalysis(): self
  {
    $phpFiles = array_filter($this->extractedFiles, fn($file) => $file->getExtension() === 'php');

    if (empty($phpFiles) || !$this->hasPhpstan()) {
      return $this;
    }

    try {
      $process = new Process(['phpstan', 'analyse', '--no-progress', '--error-format=json', $this->tempDir]);
      $process->setTimeout(30);
      $process->run();

      $output = $process->getOutput();
      $result = json_decode($output, true);

      $this->validationResult['static_analysis'] = [
        'status' => $process->isSuccessful() ? 'passed' : 'warnings',
        'errors_count' => $result['totals']['errors'] ?? 0,
        'file_errors' => $result['totals']['file_errors'] ?? 0,
      ];
    } catch (Exception $e) {
      $this->validationResult['static_analysis'] = [
        'status' => 'skipped',
        'reason' => 'PHPStan analysis failed',
      ];
    }

    return $this;
  }

  /**
   * Build final validation result.
   *
   * @return array
   */
  private function buildResult(): array
  {
    return array_merge(
      [
        'status' => 'valid',
        'validated_at' => now()->toIso8601String(),
        'archive_path' => $this->zipPath,
      ],
      $this->validationResult,
      [
        'warnings' => $this->validationResult['warnings'] ?? [],
      ],
    );
  }

  /**
   * Calculate maximum directory nesting depth.
   *
   * @return int
   */
  private function getMaxNestingDepth(): int
  {
    $maxDepth = 0;

    foreach ($this->extractedFiles as $file) {
      $depth = substr_count($file->getRelativePathname(), DIRECTORY_SEPARATOR);
      if ($depth > $maxDepth) {
        $maxDepth = $depth;
      }
    }

    return $maxDepth;
  }

  /**
   * Check if PHPStan is available.
   *
   * @return bool
   */
  private function hasPhpstan(): bool
  {
    return file_exists(base_path('vendor/bin/phpstan'));
  }

  /**
   * Clean up temporary directory.
   *
   * @return void
   */
  private function cleanup(): void
  {
    if (isset($this->tempDir) && file_exists($this->tempDir)) {
      File::deleteDirectory($this->tempDir);
    }
  }

  /**
   * Get validation warnings.
   *
   * @return array
   */
  public function getWarnings(): array
  {
    return $this->validationResult['warnings'] ?? [];
  }

  /**
   * Get extracted metadata.
   *
   * @return array
   */
  public function getMetadata(): array
  {
    return $this->metadata;
  }

  /**
   * Get file tree.
   *
   * @return array
   */
  public function getTree(): array
  {
    return $this->validationResult['tree'] ?? [];
  }

  /**
   * Get archive checksum.
   *
   * @return string|null
   */
  public function getArchiveChecksum(): ?string
  {
    return $this->validationResult['archive_checksum'] ?? null;
  }

  /**
   * Get file checksums.
   *
   * @return array
   */
  public function getChecksums(): array
  {
    return $this->validationResult['checksums'] ?? [];
  }
}
