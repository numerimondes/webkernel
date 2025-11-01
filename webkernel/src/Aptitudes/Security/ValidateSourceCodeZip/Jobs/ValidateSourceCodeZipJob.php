<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Security\ValidateSourceCodeZip\Jobs;

use Webkernel\Aptitudes\Security\ValidateSourceCodeZip\Services\ZipValidationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class ValidateSourceCodeZipJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public int $timeout = 300;
  public int $tries = 1;
  public int $backoff = 60;

  public function __construct(
    private readonly string $zipPath,
    private readonly string $developerId,
    private readonly string $projectId,
  ) {}

  /**
   * Execute the job.
   *
   * @param ZipValidationService $validationService
   * @return void
   */
  public function handle(ZipValidationService $validationService): void
  {
    try {
      Log::channel('uploads')->info('Starting ZIP validation', [
        'zip_path' => $this->zipPath,
        'developer_id' => $this->developerId,
        'project_id' => $this->projectId,
      ]);

      $result = $validationService->validate($this->zipPath);

      Log::channel('uploads')->info('ZIP validation successful', [
        'zip_path' => $this->zipPath,
        'status' => $result['status'],
        'file_count' => $result['extracted_files_count'],
        'total_size' => $result['total_extracted_size'],
        'checksum' => $result['archive_checksum'],
        'has_license' => $result['metadata']['license']['exists'] ?? false,
        'has_composer' => $result['metadata']['composer']['valid'] ?? false,
      ]);

      $this->dispatchToBackend($result);
      $this->updateModel($result);
    } catch (Exception $e) {
      Log::channel('uploads')->error('ZIP validation failed', [
        'zip_path' => $this->zipPath,
        'error' => $e->getMessage(),
        'developer_id' => $this->developerId,
      ]);

      $this->updateModelAsFailed($e->getMessage());
      throw $e;
    }
  }

  /**
   * Dispatch validation result to backend service.
   *
   * @param array $validationResult
   * @return void
   */
  private function dispatchToBackend(array $validationResult): void
  {
    if (!config('services.numeremondes.enabled', false)) {
      Log::channel('uploads')->info('Backend dispatch skipped (disabled in config)');
      return;
    }

    try {
      $response = Http::withToken(config('services.numeremondes.token'))
        ->timeout(30)
        ->post(config('services.numeremondes.endpoint'), [
          'project_id' => $this->projectId,
          'developer_id' => $this->developerId,
          'validation_result' => $validationResult,
          'timestamp' => now()->toIso8601String(),
        ]);

      if ($response->successful()) {
        Log::channel('uploads')->info('Validation result dispatched to backend', [
          'project_id' => $this->projectId,
          'response_status' => $response->status(),
        ]);
      } else {
        Log::channel('uploads')->warning('Backend dispatch failed', [
          'project_id' => $this->projectId,
          'status' => $response->status(),
          'body' => $response->body(),
        ]);
      }
    } catch (Exception $e) {
      Log::channel('uploads')->error('Backend dispatch exception', [
        'project_id' => $this->projectId,
        'error' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Update the related model with validation results.
   *
   * @param array $result
   * @return void
   */
  private function updateModel(array $result): void
  {
    $modelClass = config('webkernel.security.validation_model');

    if (!$modelClass || !class_exists($modelClass)) {
      return;
    }

    try {
      $model = $modelClass::find($this->projectId);

      if ($model) {
        $model->update([
          'validation_status' => 'validated',
          'checksum' => $result['archive_checksum'],
          'file_tree' => $result['tree'],
          'metadata' => $result['metadata'],
          'validated_at' => now(),
        ]);

        Log::channel('uploads')->info('Model updated with validation results', [
          'project_id' => $this->projectId,
          'model_class' => $modelClass,
        ]);
      }
    } catch (Exception $e) {
      Log::channel('uploads')->error('Failed to update model', [
        'project_id' => $this->projectId,
        'error' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Update the related model with failure information.
   *
   * @param string $errorMessage
   * @return void
   */
  private function updateModelAsFailed(string $errorMessage): void
  {
    $modelClass = config('webkernel.security.validation_model');

    if (!$modelClass || !class_exists($modelClass)) {
      return;
    }

    try {
      $model = $modelClass::find($this->projectId);

      if ($model) {
        $model->update([
          'validation_status' => 'failed',
          'validation_error' => $errorMessage,
          'validated_at' => now(),
        ]);
      }
    } catch (Exception $e) {
      Log::channel('uploads')->error('Failed to update model with error', [
        'project_id' => $this->projectId,
        'error' => $e->getMessage(),
      ]);
    }
  }
}
