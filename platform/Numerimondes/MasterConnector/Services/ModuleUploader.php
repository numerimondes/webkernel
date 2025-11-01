<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Platform\Numerimondes\MasterConnector\Models\Module;
use Webkernel\Aptitudes\Platform\Updator\Services\ModuleExtractor;
use ZipArchive;

class ModuleUploader
{
  public function __construct(private ModuleExtractor $extractor, private ModuleCatalog $catalog) {}

  /**
   * Upload and register a new module
   */
  public function uploadModule(UploadedFile $zipFile, ?int $organizationId = null): Module
  {
    return DB::transaction(function () use ($zipFile, $organizationId) {
      // Validate ZIP structure
      $tempDir = $this->extractToTemp($zipFile);
      $composerData = $this->validateAndExtractMetadata($tempDir);

      // Calculate hash
      $hash = hash_file('sha256', $zipFile->getRealPath());

      // Check for duplicates
      $existing = Module::where('identifier', $composerData['identifier'])
        ->where('version', $composerData['version'])
        ->first();

      if ($existing) {
        $this->cleanup($tempDir);
        throw new \RuntimeException(
          "Module {$composerData['identifier']} version {$composerData['version']} already exists.",
        );
      }

      // Store ZIP file
      $disk = Storage::disk(config('master-connector.storage_disk'));
      $storagePath =
        config('master-connector.modules_path') .
        '/' .
        $composerData['identifier'] .
        '/' .
        $composerData['version'] .
        '.zip';

      $disk->putFileAs(dirname($storagePath), $zipFile, basename($storagePath));

      // Create module record
      $module = Module::create([
        'identifier' => $composerData['identifier'],
        'name' => $composerData['name'],
        'version' => $composerData['version'],
        'description' => $composerData['description'] ?? null,
        'zip_path' => $storagePath,
        'hash' => $hash,
        'file_size' => $zipFile->getSize(),
        'metadata' => [
          'changelog' => $composerData['changelog'] ?? null,
          'dependencies' => $composerData['dependencies'] ?? [],
          'uploaded_at' => now()->toISOString(),
        ],
        'status' => 'active',
        'organization_id' => $organizationId,
      ]);

      $this->cleanup($tempDir);
      $this->catalog->invalidateCache();

      Log::info('Module uploaded successfully.', [
        'module_id' => $module->id,
        'identifier' => $module->identifier,
        'version' => $module->version,
        'size' => $module->formatted_size,
      ]);

      return $module;
    });
  }

  /**
   * Update existing module (new version)
   */
  public function updateModule(string $identifier, UploadedFile $zipFile, ?int $organizationId = null): Module
  {
    $existingModule = Module::where('identifier', $identifier)->orderByDesc('version')->first();

    if (!$existingModule && $organizationId) {
      // Check organization ownership
      if ($existingModule->organization_id !== $organizationId) {
        throw new \RuntimeException('Organization does not own this module.');
      }
    }

    return $this->uploadModule($zipFile, $organizationId);
  }

  /**
   * Delete a module
   */
  public function deleteModule(int $moduleId): void
  {
    DB::transaction(function () use ($moduleId) {
      $module = Module::findOrFail($moduleId);

      // Delete ZIP file
      $disk = Storage::disk(config('master-connector.storage_disk'));
      if ($disk->exists($module->zip_path)) {
        $disk->delete($module->zip_path);
      }

      // Soft delete module
      $module->delete();

      $this->catalog->invalidateCache();

      Log::info('Module deleted.', [
        'module_id' => $moduleId,
        'identifier' => $module->identifier,
      ]);
    });
  }

  /**
   * Extract ZIP to temporary directory
   */
  private function extractToTemp(UploadedFile $zipFile): string
  {
    $tempDir = storage_path('app/temp/module_upload_' . uniqid());

    $zip = new ZipArchive();
    if ($zip->open($zipFile->getRealPath()) !== true) {
      throw new \RuntimeException('Failed to open ZIP file.');
    }

    $zip->extractTo($tempDir);
    $zip->close();

    return $tempDir;
  }

  /**
   * Validate structure and extract metadata from composer.json
   */
  private function validateAndExtractMetadata(string $tempDir): array
  {
    $composerPath = $tempDir . '/composer.json';

    if (!file_exists($composerPath)) {
      throw new \RuntimeException('Invalid module: missing composer.json.');
    }

    $composer = json_decode(file_get_contents($composerPath), true);

    if (!isset($composer['name'])) {
      throw new \RuntimeException('Invalid composer.json: missing name field.');
    }

    if (!isset($composer['version'])) {
      throw new \RuntimeException('Invalid composer.json: missing version field.');
    }

    if (!isset($composer['extra']['webkernel']['app-class'])) {
      throw new \RuntimeException('Invalid module: missing Webkernel app class in composer.json.');
    }

    // Validate SemVer
    if (!preg_match('/^\d+\.\d+\.\d+$/', $composer['version'])) {
      throw new \RuntimeException('Invalid version format. Use SemVer (e.g., 1.0.0).');
    }

    return [
      'identifier' => explode('/', $composer['name'])[1] ?? $composer['name'],
      'name' => $composer['description'] ?? $composer['name'],
      'version' => $composer['version'],
      'description' => $composer['description'] ?? null,
      'changelog' => $composer['extra']['changelog'] ?? null,
      'dependencies' => $composer['require'] ?? [],
    ];
  }

  /**
   * Cleanup temporary directory
   */
  private function cleanup(string $tempDir): void
  {
    if (is_dir($tempDir)) {
      $this->recursiveDelete($tempDir);
    }
  }

  /**
   * Recursively delete directory
   */
  private function recursiveDelete(string $dir): void
  {
    $files = array_diff(scandir($dir), ['.', '..']);

    foreach ($files as $file) {
      $path = $dir . '/' . $file;
      is_dir($path) ? $this->recursiveDelete($path) : unlink($path);
    }

    rmdir($dir);
  }
}
