<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Security\ValidateSourceCodeZip\Filament;

use Webkernel\Aptitudes\Security\ValidateSourceCodeZip\Jobs\ValidateSourceCodeZipJob;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Auth;
use Closure;

class SourceCodeUploadZipOnForm
{
  protected string $fieldName = 'zip_path';
  protected string $label = 'Archive Path';
  protected string $directory = 'software-cores/app/source-code';
  protected string $visibility = 'private';
  protected string $placeholder = 'Upload Source Code Here';
  protected string $helperText = 'ZIP archives are scanned automatically for security. Processing begins after upload.';
  protected bool $required = true;
  protected ?Closure $afterUploadCallback = null;
  protected ?Closure $fileNameGenerator = null;
  protected array $acceptedTypes = ['application/zip'];
  protected ?string $modelClass = null;
  protected ?string $modelIdField = null;

  /**
   * Create a new instance with fluent API.
   *
   * @param string $fieldName
   * @return static
   */
  public static function make(string $fieldName = 'zip_path'): static
  {
    $instance = new static();
    $instance->fieldName = $fieldName;
    return $instance;
  }

  /**
   * Set the field label.
   *
   * @param string $label
   * @return $this
   */
  public function label(string $label): self
  {
    $this->label = $label;
    return $this;
  }

  /**
   * Set the storage directory.
   *
   * @param string $directory
   * @return $this
   */
  public function directory(string $directory): self
  {
    $this->directory = $directory;
    return $this;
  }

  /**
   * Set the visibility (public or private).
   *
   * @param string $visibility
   * @return $this
   */
  public function visibility(string $visibility): self
  {
    $this->visibility = $visibility;
    return $this;
  }

  /**
   * Set the placeholder text.
   *
   * @param string $placeholder
   * @return $this
   */
  public function placeholder(string $placeholder): self
  {
    $this->placeholder = $placeholder;
    return $this;
  }

  /**
   * Set the helper text.
   *
   * @param string $helperText
   * @return $this
   */
  public function helperText(string $helperText): self
  {
    $this->helperText = $helperText;
    return $this;
  }

  /**
   * Set whether the field is required.
   *
   * @param bool $required
   * @return $this
   */
  public function required(bool $required = true): self
  {
    $this->required = $required;
    return $this;
  }

  /**
   * Set accepted file types.
   *
   * @param array $types
   * @return $this
   */
  public function acceptedFileTypes(array $types): self
  {
    $this->acceptedTypes = $types;
    return $this;
  }

  /**
   * Set a callback to execute after upload.
   *
   * @param Closure $callback
   * @return $this
   */
  public function afterUpload(Closure $callback): self
  {
    $this->afterUploadCallback = $callback;
    return $this;
  }

  /**
   * Set a custom file name generator.
   *
   * @param Closure $generator
   * @return $this
   */
  public function fileNameUsing(Closure $generator): self
  {
    $this->fileNameGenerator = $generator;
    return $this;
  }

  /**
   * Configure automatic validation dispatch on upload for a given model.
   *
   * @param string $modelClass
   * @param string $modelIdField
   * @return $this
   */
  public function validateForModel(string $modelClass, string $modelIdField = 'id'): self
  {
    $this->modelClass = $modelClass;
    $this->modelIdField = $modelIdField;
    return $this;
  }

  /**
   * Get the configured FileUpload field.
   *
   * @return FileUpload
   */
  public function getField(): FileUpload
  {
    $field = FileUpload::make($this->fieldName)
      ->label($this->label)
      ->directory($this->directory)
      ->visibility($this->visibility)
      ->acceptedFileTypes($this->acceptedTypes)
      ->placeholder($this->placeholder)
      ->helperText($this->helperText);

    if ($this->required) {
      $field->required();
    }

    if ($this->fileNameGenerator) {
      $field->getUploadedFileNameForStorageUsing($this->fileNameGenerator);
    } else {
      $field->getUploadedFileNameForStorageUsing(
        fn($file): string => 'source-code-' . time() . '-' . $file->getClientOriginalName(),
      );
    }

    $field->afterStateUpdated(function ($state, callable $set, $livewire): void {
      if ($state) {
        $set($this->fieldName, $state);

        if ($this->afterUploadCallback) {
          call_user_func($this->afterUploadCallback, $state, $set, $livewire);
        }

        if ($this->modelClass && $this->modelIdField) {
          $this->dispatchAutoValidation($state, $livewire);
        }
      }
    });

    return $field;
  }

  /**
   * Dispatch automatic validation for configured model.
   *
   * @param string $zipPath
   * @param mixed $livewire
   * @return void
   */
  protected function dispatchAutoValidation(string $zipPath, mixed $livewire): void
  {
    if (!isset($livewire->ownerRecord)) {
      return;
    }

    $modelId = $livewire->ownerRecord->{$this->modelIdField} ?? null;

    if ($modelId) {
      self::handleZipUpload($zipPath, (string) $modelId);
    }
  }

  /**
   * Static helper: Get a basic ZIP upload field.
   *
   * @return FileUpload
   */
  public static function getZipUploadField(): FileUpload
  {
    return self::make()->getField();
  }

  /**
   * Dispatch ZIP validation job.
   *
   * @param string $zipPath
   * @param string $projectId
   * @param string|null $developerId
   * @return void
   */
  public static function handleZipUpload(string $zipPath, string $projectId, ?string $developerId = null): void
  {
    $developerId = $developerId ?? (string) (Auth::id() ?? 'unknown');

    ValidateSourceCodeZipJob::dispatch(zipPath: $zipPath, developerId: $developerId, projectId: $projectId);
  }

  /**
   * Validate a ZIP immediately and return the result (synchronous).
   *
   * @param string $zipPath
   * @return array
   */
  public static function validateNow(string|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $zipPath): array
  {
    $service = app(\Webkernel\Aptitudes\Security\ValidateSourceCodeZip\Services\ZipValidationService::class);
    return $service->validate($zipPath);
  }

  /**
   * Get checksum from a validated ZIP.
   *
   * @param string|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $zipPath
   * @return string|null
   */
  public static function getChecksum(
    string|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $zipPath,
  ): ?string {
    try {
      // Convert TemporaryUploadedFile to path if needed
      $path = is_string($zipPath) ? $zipPath : $zipPath->getRealPath();

      $result = self::validateNow($path);
      return $result['archive_checksum'] ?? null;
    } catch (\Exception $e) {
      return null;
    }
  }

  /**
   * Get file tree from a validated ZIP.
   *
   * @param string $zipPath
   * @return array
   */
  public static function getTree(string|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $zipPath): array
  {
    try {
      $result = self::validateNow($zipPath);
      return $result['tree'] ?? [];
    } catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Get metadata from a validated ZIP.
   *
   * @param string $zipPath
   * @return array
   */
  public static function getMetadata(string|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $zipPath): array
  {
    try {
      $result = self::validateNow($zipPath);
      return $result['metadata'] ?? [];
    } catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Check if a ZIP contains a specific file at root.
   *
   * @param string $zipPath
   * @param string $fileName
   * @return bool
   */
  public static function hasRootFile(string $zipPath, string $fileName): bool
  {
    $tree = self::getTree($zipPath);
    return isset($tree[$fileName]);
  }

  /**
   * Check if a ZIP contains a valid composer.json.
   *
   * @param string $zipPath
   * @return bool
   */
  public static function hasValidComposer(string $zipPath): bool
  {
    $metadata = self::getMetadata($zipPath);
    return ($metadata['composer']['valid'] ?? false) === true;
  }

  /**
   * Check if a ZIP contains a LICENSE file.
   *
   * @param string $zipPath
   * @return bool
   */
  public static function hasLicense(string $zipPath): bool
  {
    $metadata = self::getMetadata($zipPath);
    return ($metadata['license']['exists'] ?? false) === true;
  }
}
