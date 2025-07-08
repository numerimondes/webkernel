<?php

declare(strict_types=1);

namespace Webkernel\Core\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Platform Update Controller
 * 
 * Handles platform updates from frontend with rolling release support.
 * 
 * @author El Moumen Yassine
 * @email yassine@numerimondes.com
 * @website www.numerimondes.com
 * @license MPL-2.0
 */
class PlatformUpdateController extends Controller
{
    /**
     * Check for updates
     */
    public function checkForUpdates(Request $request): JsonResponse
    {
        try {
            $result = Artisan::call('webkernel:update', [
                '--status' => true,
                '--json' => true,
                '--silent' => true
            ]);

            $output = Artisan::output();
            $status = json_decode($output, true);

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to parse update status',
                    'raw_output' => $output
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $status,
                'update_available' => $status['update_needed'] ?? false
            ]);

        } catch (Exception $e) {
            Log::error('Platform update check failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to check for updates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform update
     */
    public function performUpdate(Request $request): JsonResponse
    {
        try {
            $options = [
                '--silent' => true
            ];

            // Add force option if requested
            if ($request->boolean('force')) {
                $options['--force'] = true;
            }

            // Add dry-run option if requested
            if ($request->boolean('dry_run')) {
                $options['--dry-run'] = true;
            }

            // Add custom repository if provided
            if ($request->filled('remote_repo')) {
                $options['--remote-repo'] = $request->string('remote_repo');
            }

            // Add custom branch if provided
            if ($request->filled('branch')) {
                $options['--branch'] = $request->string('branch');
            }

            $result = Artisan::call('webkernel:update', $options);
            $output = Artisan::output();

            // Check if update was successful
            $success = $result === 0;

            return response()->json([
                'success' => $success,
                'exit_code' => $result,
                'output' => $output,
                'message' => $success ? 'Update completed successfully' : 'Update failed'
            ], $success ? 200 : 500);

        } catch (Exception $e) {
            Log::error('Platform update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-update (rolling release)
     */
    public function autoUpdate(Request $request): JsonResponse
    {
        try {
            // Check if auto-update is enabled
            if (!config('webkernel.auto_update_enabled', false)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Auto-update is disabled'
                ], 403);
            }

            // Check if we're in rolling release mode
            if (!$request->boolean('rolling_release') && !config('webkernel.rolling_release_enabled', false)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Rolling release is disabled'
                ], 403);
            }

            // Check for updates first
            $checkResult = Artisan::call('webkernel:update', [
                '--status' => true,
                '--json' => true,
                '--silent' => true
            ]);

            $checkOutput = Artisan::output();
            $status = json_decode($checkOutput, true);

            if (!$status || !($status['update_needed'] ?? false)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No update needed',
                    'data' => $status
                ]);
            }

            // Perform the update
            $updateResult = Artisan::call('webkernel:update', [
                '--silent' => true,
                '--auto-run' => true
            ]);

            $updateOutput = Artisan::output();

            return response()->json([
                'success' => $updateResult === 0,
                'exit_code' => $updateResult,
                'output' => $updateOutput,
                'message' => $updateResult === 0 ? 'Auto-update completed successfully' : 'Auto-update failed'
            ], $updateResult === 0 ? 200 : 500);

        } catch (Exception $e) {
            Log::error('Auto-update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Auto-update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get update status
     */
    public function getStatus(Request $request): JsonResponse
    {
        try {
            $result = Artisan::call('webkernel:update', [
                '--status' => true,
                '--json' => true,
                '--silent' => true
            ]);

            $output = Artisan::output();
            $status = json_decode($output, true);

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (Exception $e) {
            Log::error('Failed to get update status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to get status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available backups
     */
    public function getBackups(Request $request): JsonResponse
    {
        try {
            $result = Artisan::call('webkernel:update', [
                '--backups' => true,
                '--silent' => true
            ]);

            $output = Artisan::output();

            // Parse the table output to extract backup information
            $backups = $this->parseBackupOutput($output);

            return response()->json([
                'success' => true,
                'data' => $backups
            ]);

        } catch (Exception $e) {
            Log::error('Failed to get backups: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to get backups: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore from backup
     */
    public function restoreFromBackup(Request $request): JsonResponse
    {
        try {
            $backupPath = $request->string('backup_path');
            
            if (empty($backupPath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Backup path is required'
                ], 400);
            }

            $result = Artisan::call('webkernel:update', [
                '--restore' => $backupPath,
                '--silent' => true
            ]);

            $output = Artisan::output();

            return response()->json([
                'success' => $result === 0,
                'exit_code' => $result,
                'output' => $output,
                'message' => $result === 0 ? 'Restore completed successfully' : 'Restore failed'
            ], $result === 0 ? 200 : 500);

        } catch (Exception $e) {
            Log::error('Restore failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Restore failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear version cache
     */
    public function clearCache(Request $request): JsonResponse
    {
        try {
            $result = Artisan::call('webkernel:update', [
                '--clear-cache' => true,
                '--silent' => true
            ]);

            return response()->json([
                'success' => $result === 0,
                'message' => 'Version cache cleared successfully'
            ]);

        } catch (Exception $e) {
            Log::error('Failed to clear cache: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse backup table output
     */
    private function parseBackupOutput(string $output): array
    {
        $lines = explode("\n", trim($output));
        $backups = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip header lines and empty lines
            if (empty($line) || strpos($line, 'Backup') === 0 || strpos($line, 'Available') === 0) {
                continue;
            }
            
            // Parse table row (assuming tabular format)
            $parts = preg_split('/\s{2,}/', $line);
            
            if (count($parts) >= 4) {
                $backups[] = [
                    'backup' => trim($parts[0]),
                    'date' => trim($parts[1]),
                    'size' => trim($parts[2]),
                    'path' => trim($parts[3])
                ];
            }
        }
        
        return $backups;
    }
} 