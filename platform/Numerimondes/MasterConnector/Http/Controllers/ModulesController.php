<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Platform\Numerimondes\MasterConnector\Models\DownloadLog;
use Platform\Numerimondes\MasterConnector\Models\License;
use Platform\Numerimondes\MasterConnector\Services\ModuleCatalog;
use ZipArchive;

class ModulesController extends Controller
{
  public function __construct(private ModuleCatalog $catalog) {}

  /**
   * List authorized modules for authenticated license
   *
   * GET /api/modules/list
   */
  public function list(Request $request): JsonResponse
  {
    $license = $request->attributes->get('license');

    try {
      $modules = $this->catalog->getAuthorizedModules($license);

      return response()->json([
        'success' => true,
        'data' => [
          'modules' => $modules->toArray(),
        ],
      ]);
    } catch (\Exception $e) {
      Log::error('Module list error: ' . $e->getMessage());

      return response()->json(
        [
          'success' => false,
          'error' => 'Failed to retrieve modules.',
        ],
        500,
      );
    }
  }

  /**
   * Download modules as ZIP
   *
   * GET /api/modules/download?modules=1,2,3
   */
  public function download(Request $request): Response
  {
    $validator = Validator::make($request->all(), [
      'modules' => 'required|string',
    ]);

    if ($validator->fails()) {
      return response('Invalid request: ' . $validator->errors()->first(), 400);
    }

    $license = $request->attributes->get('license');
    $moduleIds = array_map('intval', explode(',', $request->query('modules')));

    try {
      // Verify authorization
      $authorizedIds = $license->modules()->pluck('id')->toArray();
      $unauthorizedIds = array_diff($moduleIds, $authorizedIds);

      if (!empty($unauthorizedIds)) {
        DownloadLog::logDownload(
          $license->id,
          null,
          $request->ip(),
          false,
          'Unauthorized module IDs: ' . implode(',', $unauthorizedIds),
        );

        return response('Unauthorized access to some modules.', 403);
      }

      // Create combined ZIP
      $zipPath = $this->createCombinedZip($moduleIds, $license);

      DownloadLog::logDownload($license->id, null, $request->ip(), true);

      return response()->download($zipPath)->deleteFileAfterSend(true);
    } catch (\Exception $e) {
      Log::error('Module download error: ' . $e->getMessage());

      DownloadLog::logDownload($license->id, null, $request->ip(), false, $e->getMessage());

      return response('Download failed.', 500);
    }
  }

  /**
   * Get module checksum
   *
   * GET /api/modules/checksum/{identifier}
   */
  public function checksum(Request $request, string $identifier): JsonResponse
  {
    try {
      $checksum = $this->catalog->getChecksum($identifier);

      if (!$checksum) {
        return response()->json(
          [
            'success' => false,
            'error' => 'Module not found.',
          ],
          404,
        );
      }

      return response()->json([
        'success' => true,
        'data' => $checksum,
      ]);
    } catch (\Exception $e) {
      Log::error('Checksum error: ' . $e->getMessage());

      return response()->json(
        [
          'success' => false,
          'error' => 'Failed to retrieve checksum.',
        ],
        500,
      );
    }
  }

  /**
   * Check for module updates
   *
   * POST /api/modules/updates
   * Body: { "modules": [{"id": 1, "version": "1.0.0", "hash": "..."}] }
   */
  public function updates(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'modules' => 'required|array',
      'modules.*.identifier' => 'required|string',
      'modules.*.version' => 'required|string',
    ]);

    if ($validator->fails()) {
      return response()->json(
        [
          'success' => false,
          'error' => 'Invalid request: ' . $validator->errors()->first(),
        ],
        400,
      );
    }

    try {
      $updates = $this->catalog->checkUpdates($request->input('modules'));

      return response()->json([
        'success' => true,
        'data' => [
          'updates' => $updates,
        ],
      ]);
    } catch (\Exception $e) {
      Log::error('Updates check error: ' . $e->getMessage());

      return response()->json(
        [
          'success' => false,
          'error' => 'Failed to check updates.',
        ],
        500,
      );
    }
  }

  /**
   * Create combined ZIP with manifest
   */
  private function createCombinedZip(array $moduleIds, License $license): string
  {
    $tempZip = storage_path('app/temp/download_' . uniqid() . '.zip');
    $zip = new ZipArchive();

    if ($zip->open($tempZip, ZipArchive::CREATE) !== true) {
      throw new \RuntimeException('Failed to create ZIP file.');
    }

    // Add manifest
    $manifest = [
      'license_domain' => $license->domain,
      'downloaded_at' => now()->toISOString(),
      'modules' => [],
    ];

    foreach ($moduleIds as $moduleId) {
      $modulePath = $this->catalog->getModuleDownloadPath($moduleId);

      if (!$modulePath) {
        continue;
      }

      $module = \Platform\Numerimondes\MasterConnector\Models\Module::find($moduleId);
      $zip->addFile($modulePath, 'modules/' . $module->identifier . '.zip');

      $manifest['modules'][] = [
        'id' => $module->id,
        'identifier' => $module->identifier,
        'version' => $module->version,
        'hash' => $module->hash,
      ];
    }

    $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
    $zip->close();

    return $tempZip;
  }
}
