<?php
namespace Webkernel\Console\Package;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

/**
 * Command to update platform sub-applications based on license.
 *
 * Dynamically detects sub-applications in the platform/ directory, checks for updates
 * via a license API, and applies updates with backups. Runs webkernel:update first.
 *
 * Usage: php artisan platform:update
 * Options:
 *   --revert : Revert to a previous backup
 *   --automatic : Setup automatic daily updates via cron
 *   --force : Force update even if versions appear equal
 *   --dry-run : Show what would be updated without applying changes
 */
class UpdatePlatforms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webkernel:update-platforms
                            {--revert : Revert to a previous backup}
                            {--automatic : Setup automatic daily updates via cron}
                            {--force : Force update even if versions appear equal}
                            {--dry-run : Show what would be updated without applying changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates platform sub-applications based on license via API.';

    /**
     * License API base URL.
     *
     * @var string
     */
    protected $apiBaseUrl = 'https://software-licence.numerimondes.com/server';

    /**
     * Temporary backup directory.
     *
     * @var string
     */
    protected $backupPath = '.trash';

    /**
     * Unique User-Agent for API requests.
     *
     * @var string
     */
    protected $userAgent = 'Webkernel-Update-Client/1.0';

    /**
     * API timeout in seconds.
     *
     * @var int
     */
    protected $apiTimeout = 60;

    /**
     * Maximum number of backup versions to keep.
     *
     * @var int
     */
    protected $maxBackups = 5;

    /**
     * Maximum number of API retries.
     *
     * @var int
     */
    protected $maxApiRetries = 3;

    /**
     * Initial retry delay in seconds.
     *
     * @var int
     */
    protected $retryDelay = 2;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->logOperation('UPDATE_START', 'Platform update process started');

            if ($this->option('revert')) {
                return $this->handleRevert();
            }

            if ($this->option('automatic')) {
                return $this->handleAutomatic();
            }

            return $this->handleUpdate();

        } catch (Exception $e) {
            $this->logOperation('UPDATE_ERROR', 'Fatal error: ' . $e->getMessage(), $e);
            $this->error("[FATAL ERROR] Update process failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    /**
     * Handle the main update process.
     *
     * @return int
     */
    protected function handleUpdate()
    {
        $this->info('[INFO] Platform Updater');
        $this->newLine();

        // Run webkernel:update first
        if (!$this->runWebkernelUpdate()) {
            return self::FAILURE;
        }

        // Get and validate license key
        $licenceKey = $this->getLicenceKey();
        if (!$licenceKey) {
            $this->error('[ERROR] License key not found in core_platform_settings.');
            $this->logOperation('LICENSE_ERROR', 'License key not found');
            return self::FAILURE;
        }

        $this->info('[INFO] License key validated.');

        // Fetch allowed applications from API
        $apiResponse = $this->fetchAllowedApplicationsWithRetry($licenceKey);
        if (!$apiResponse['success']) {
            $this->error("[ERROR] {$apiResponse['error']}");
            return self::FAILURE;
        }

        $allowedApps = $apiResponse['applications'];
        if (empty($allowedApps)) {
            $this->warn('[WARNING] API reported no applications allowed for this license.');
            $this->logOperation('API_NO_APPS', 'API returned empty applications list');
            return self::SUCCESS;
        }

        // Detect local sub-applications
        $localApps = $this->detectSubApplications();
        if (empty($localApps)) {
            $this->warn('[WARNING] No sub-applications found in platform/ directory.');
            $this->logOperation('NO_LOCAL_APPS', 'No sub-applications detected');
            return self::SUCCESS;
        }

        // Process updates
        return $this->processApplicationUpdates($allowedApps, $localApps, $licenceKey);
    }

    /**
     * Run webkernel:update with proper error handling.
     *
     * @return bool
     */
    protected function runWebkernelUpdate()
    {
        $this->info('[INFO] Running webkernel:update...');

        try {
            $exitCode = $this->call('webkernel:update', ['--no-interaction' => true]);

            if ($exitCode === 0) {
                $this->info('[SUCCESS] webkernel:update completed.');
                $this->logOperation('WEBKERNEL_SUCCESS', 'webkernel:update completed successfully');
                return true;
            } else {
                $this->error('[ERROR] webkernel:update failed with exit code: ' . $exitCode);
                $this->logOperation('WEBKERNEL_ERROR', 'webkernel:update failed', null, ['exit_code' => $exitCode]);
                return false;
            }
        } catch (Exception $e) {
            $this->error('[ERROR] webkernel:update threw exception: ' . $e->getMessage());
            $this->logOperation('WEBKERNEL_EXCEPTION', 'webkernel:update exception', $e);
            return false;
        }
    }

    /**
     * Retrieve license key from core_platform_settings.
     *
     * @return string|null
     */
    protected function getLicenceKey()
    {
        try {
            $licenceKey = DB::table('core_platform_settings')
                ->where('settings_reference', 'PLATFORM_LICENCE')
                ->value('value');

            if (empty($licenceKey) || strlen(trim($licenceKey)) === 0) {
                $this->logOperation('LICENSE_EMPTY', 'License key is empty or contains only whitespace');
                return null;
            }

            return trim($licenceKey);
        } catch (Exception $e) {
            $this->logOperation('LICENSE_DB_ERROR', 'Database error while fetching license', $e);
            return null;
        }
    }

    /**
     * Fetch allowed applications with retry mechanism.
     *
     * @param string $licenceKey
     * @return array
     */
    protected function fetchAllowedApplicationsWithRetry($licenceKey)
    {
        $retryDelay = $this->retryDelay;

        for ($attempt = 1; $attempt <= $this->maxApiRetries; $attempt++) {
            $this->line("[INFO] Fetching allowed applications from API (attempt {$attempt}/{$this->maxApiRetries})...");

            $result = $this->fetchAllowedApplications($licenceKey);

            if ($result['success']) {
                return $result;
            }

            if ($attempt < $this->maxApiRetries) {
                $this->warn("[WARNING] Attempt {$attempt} failed: {$result['error']}. Retrying in {$retryDelay} seconds...");
                sleep($retryDelay);
                $retryDelay *= 2; // Exponential backoff
            }
        }

        return $result; // Return the last attempt's result
    }

    /**
     * Fetch allowed applications and their versions from the license API.
     *
     * @param string $licenceKey
     * @return array
     */
    protected function fetchAllowedApplications($licenceKey)
    {
        try {
            $response = Http::timeout($this->apiTimeout)
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                    'User-Agent' => $this->userAgent,
                ])
                ->get($this->apiBaseUrl, [
                    'request-type' => 'check-version',
                    'licence_key' => $licenceKey,
                ]);

            if (!$response->successful()) {
                $error = "API request failed with status {$response->status()}: {$response->body()}";
                $this->logOperation('API_HTTP_ERROR', $error, null, [
                    'status_code' => $response->status(),
                    'response_body' => substr($response->body(), 0, 500),
                ]);
                return ['success' => false, 'error' => $error, 'applications' => []];
            }

            $data = $response->json();

            if (!is_array($data)) {
                $error = "Invalid API response format (not an array)";
                $this->logOperation('API_FORMAT_ERROR', $error, null, ['response' => $data]);
                return ['success' => false, 'error' => $error, 'applications' => []];
            }

            if (isset($data['error'])) {
                $error = "API error: {$data['error']}";
                $this->logOperation('API_BUSINESS_ERROR', $error, null, ['api_response' => $data]);
                return ['success' => false, 'error' => $error, 'applications' => []];
            }

            $apps = $data['applications'] ?? [];

            if (!is_array($apps)) {
                $error = "Invalid applications format in API response";
                $this->logOperation('API_APPS_FORMAT_ERROR', $error, null, ['applications_field' => $apps]);
                return ['success' => false, 'error' => $error, 'applications' => []];
            }

            $validApps = array_filter($apps, function ($app) {
                return is_array($app) && isset($app['name'], $app['version']) && is_string($app['name']) && is_string($app['version']);
            });

            $this->line("[DEBUG] Found " . count($validApps) . " valid applications:");
            foreach ($validApps as $app) {
                $this->line("  - {$app['name']}: {$app['version']}");
            }

            $this->logOperation('API_SUCCESS', 'Successfully fetched applications', null, [
                'app_count' => count($validApps),
            ]);

            return ['success' => true, 'error' => null, 'applications' => array_values($validApps)];
        } catch (Exception $e) {
            $error = "Error fetching allowed applications: {$e->getMessage()}";
            $this->logOperation('API_EXCEPTION', $error, $e);
            return ['success' => false, 'error' => $error, 'applications' => []];
        }
    }

    /**
     * Dynamically detect sub-applications in platform/ directory.
     *
     * @return array
     */
    protected function detectSubApplications()
    {
        $platformPath = base_path('platform');
        $subApps = [];

        try {
            if (!File::isDirectory($platformPath)) {
                $this->line("[DEBUG] Platform directory does not exist: {$platformPath}");
                $this->logOperation('NO_PLATFORM_DIR', 'Platform directory not found');
                return $subApps;
            }

            $directories = File::directories($platformPath);

            foreach ($directories as $dir) {
                $appName = basename($dir);

                // Skip hidden directories and backup directories
                if (str_starts_with($appName, '.') || $appName === $this->backupPath) {
                    continue;
                }

                $appFile = "{$dir}/Application.php";
                if (File::exists($appFile)) {
                    $subApps[] = $appName;
                    $this->line("[DEBUG] Found application: {$appName}");
                } else {
                    $this->line("[DEBUG] Skipping {$appName} (no Application.php found)");
                }
            }

            $this->line("[DEBUG] Total detected sub-applications: " . count($subApps));
            $this->logOperation('DETECT_SUCCESS', 'Sub-applications detected', null, ['count' => count($subApps)]);

            return $subApps;
        } catch (Exception $e) {
            $this->error("[ERROR] Failed to detect sub-applications: {$e->getMessage()}");
            $this->logOperation('DETECT_ERROR', 'Error detecting sub-applications', $e);
            return [];
        }
    }

    /**
     * Process application updates.
     *
     * @param array $allowedApps
     * @param array $localApps
     * @param string $licenceKey
     * @return int
     */
    protected function processApplicationUpdates($allowedApps, $localApps, $licenceKey)
    {
        $updatesPerformed = false;
        $errors = [];
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('[DRY RUN] Showing what would be updated without making changes');
            $this->newLine();
        }

        foreach ($allowedApps as $app) {
            if (!in_array($app['name'], $localApps)) {
                $this->warn("[WARNING] {$app['name']} not installed locally. Skipping.");
                $this->logOperation('APP_NOT_INSTALLED', "Skipping {$app['name']} (not installed locally)", null, ['app' => $app['name']]);
                continue;
            }

            $this->info("[INFO] Processing sub-application: {$app['name']}");
            $result = $this->processSubApplication($app['name'], $app['version'], $licenceKey, $isDryRun);

            if ($result['status'] === self::FAILURE) {
                $errors[] = $result['error'];
            } elseif ($result['updated']) {
                $updatesPerformed = true;
            }
        }

        return $this->handleUpdateResults($updatesPerformed, $errors, $isDryRun);
    }

    /**
     * Handle the results of the update process.
     *
     * @param bool $updatesPerformed
     * @param array $errors
     * @param bool $isDryRun
     * @return int
     */
    protected function handleUpdateResults($updatesPerformed, $errors, $isDryRun)
    {
        if ($errors) {
            $this->warn('[WARNING] Errors encountered:');
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
        }

        if ($isDryRun) {
            if ($updatesPerformed) {
                $this->info('[DRY RUN] Updates would be available. Run without --dry-run to apply.');
            } else {
                $this->info('[DRY RUN] All sub-applications are up to date.');
            }
            $this->logOperation('DRY_RUN_COMPLETE', 'Dry run completed', null, [
                'updates_needed' => $updatesPerformed,
                'error_count' => count($errors),
            ]);
            return empty($errors) ? self::SUCCESS : self::FAILURE;
        }

        if ($updatesPerformed) {
            $this->info('[SUCCESS] Platform updates completed!');
            $this->newLine();
            $restart = $this->option('no-interaction') ? true : $this->confirm('Do you want to restart the application?', true);
            if ($restart) {
                $this->restartApplication();
            }
        } else {
            $this->info('[SUCCESS] All sub-applications are up to date!');
            $this->newLine();
            $restart = $this->option('no-interaction') ? false : $this->confirm('Do you want to restart the application anyway?', false);
            if ($restart) {
                $this->restartApplication();
            }
        }

        $this->logOperation('UPDATE_COMPLETE', 'Update process completed', null, [
            'updates_performed' => $updatesPerformed,
            'error_count' => count($errors),
        ]);

        return empty($errors) ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Retrieve local version for a sub-application.
     *
     * @param string $app
     * @return string|null
     */
    protected function getLocalVersion($app)
    {
        $filePath = base_path("platform/{$app}/Application.php");

        try {
            if (!File::exists($filePath)) {
                $this->warn("[WARNING] Application.php not found for {$app}.");
                $this->logOperation('VERSION_FILE_MISSING', "Application.php not found for {$app}", null, ['app' => $app]);
                return null;
            }

            $content = File::get($filePath);
            $versionConstant = strtoupper(str_replace('-', '_', $app)) . '_VERSION';

            // Try multiple patterns for version detection
            $patterns = [
                "/const {$versionConstant} = ['\"]([^'\"]+)['\"];/",
                "/define\('{$versionConstant}', ['\"]([^'\"]+)['\"]\);/",
                "/\\\$version = ['\"]([^'\"]+)['\"];/",
                "/@version ([^\s*]+)/",
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $content, $matches)) {
                    $version = trim($matches[1]);
                    $this->line("[DEBUG] Found version {$version} for {$app}");
                    return $version;
                }
            }

            $this->warn("[WARNING] Version constant {$versionConstant} not found in {$app}/Application.php.");
            $this->logOperation('VERSION_NOT_FOUND', "Version constant not found for {$app}", null, ['app' => $app]);
            return null;

        } catch (Exception $e) {
            $this->error("[ERROR] Failed to retrieve version for {$app}: {$e->getMessage()}");
            $this->logOperation('VERSION_READ_ERROR', "Error reading version for {$app}", $e, ['app' => $app]);
            return null;
        }
    }

    /**
     * Process a sub-application for updates.
     *
     * @param string $appName
     * @param string $remoteVersion
     * @param string $licenceKey
     * @param bool $isDryRun
     * @return array
     */
    protected function processSubApplication($appName, $remoteVersion, $licenceKey, $isDryRun = false)
    {
        $this->line("[INFO] Processing sub-application: {$appName}");
        $localPath = "platform/{$appName}";
        $this->line("Local path: {$localPath}");

        try {
            $localVersion = $this->getLocalVersion($appName) ?? '0.0.0';
            $this->info("Local version: {$localVersion}");
            $this->info("Remote version: {$remoteVersion}");

            $forceUpdate = $this->option('force');
            $updateNeeded = $this->checkIfUpdateNeeded($localVersion, $remoteVersion) || $forceUpdate;

            if ($updateNeeded) {
                if ($forceUpdate && !$this->checkIfUpdateNeeded($localVersion, $remoteVersion)) {
                    $this->warn("[FORCE] Forcing update for {$appName} (versions are equal)");
                } else {
                    $this->warn("[UPDATE] Update available for {$appName}!");
                }

                $this->line("Update: {$localVersion} → {$remoteVersion}");

                if ($isDryRun) {
                    $this->info("[DRY RUN] Would update {$appName}");
                    $this->logOperation('DRY_RUN_UPDATE', "Would update {$appName} to {$remoteVersion}", null, [
                        'app' => $appName,
                        'from_version' => $localVersion,
                        'to_version' => $remoteVersion,
                    ]);
                    return ['status' => self::SUCCESS, 'updated' => true, 'error' => null];
                }

                $this->newLine();
                $this->line('[WARNING] This will:');
                $this->line("  * Create a backup in {$localPath}/{$this->backupPath}/");
                $this->line("  * Replace the {$localPath} directory");
                $this->line('  * Download and install the latest version from the API');
                $this->newLine();

                $confirm = $this->option('no-interaction') ? true : $this->confirm("Do you want to proceed with the update for {$appName}?", false);
                if (!$confirm) {
                    $this->info("Update cancelled for {$appName}");
                    $this->logOperation('UPDATE_CANCELLED', "Update cancelled for {$appName}", null, ['app' => $appName]);
                    return ['status' => self::SUCCESS, 'updated' => false, 'error' => null];
                }

                $status = $this->performUpdate($appName, $remoteVersion, $licenceKey);
                if ($status === self::FAILURE) {
                    return ['status' => self::FAILURE, 'updated' => false, 'error' => "Failed to update {$appName}"];
                }
                return ['status' => self::SUCCESS, 'updated' => true, 'error' => null];
            }

            $this->info("[SUCCESS] {$appName} is up to date!");
            $this->logOperation('APP_UP_TO_DATE', "{$appName} is up to date", null, [
                'app' => $appName,
                'version' => $localVersion,
            ]);
            return ['status' => self::SUCCESS, 'updated' => false, 'error' => null];
        } catch (Exception $e) {
            $error = "Error processing {$appName}: {$e->getMessage()}";
            $this->logOperation('APP_PROCESS_ERROR', $error, $e, ['app' => $appName]);
            return ['status' => self::FAILURE, 'updated' => false, 'error' => $error];
        }
    }

    /**
     * Check if an update is needed by comparing versions.
     *
     * @param string $localVersion
     * @param string $remoteVersion
     * @return bool
     */
    protected function checkIfUpdateNeeded($localVersion, $remoteVersion)
    {
        try {
            $localVersion = $this->normalizeVersion($localVersion);
            $remoteVersion = $this->normalizeVersion($remoteVersion);

            $this->line("Comparing normalized versions: {$localVersion} vs {$remoteVersion}");

            $comparison = version_compare($remoteVersion, $localVersion);

            if ($comparison === 1) {
                $this->warn('   -> Update available');
                return true;
            } elseif ($comparison === 0) {
                $this->info('   -> Up to date');
            } else {
                $this->line('   -> Local version is newer');
            }

            return false;
        } catch (Exception $e) {
            $this->warn("[WARNING] Version comparison failed: {$e->getMessage()}");
            $this->logOperation('VERSION_COMPARE_ERROR', 'Version comparison failed', $e);
            return false;
        }
    }

    /**
     * Normalize version string for comparison.
     *
     * @param string $version
     * @return string
     */
    protected function normalizeVersion($version)
    {
        // Remove 'v' prefix
        $version = ltrim($version, 'v');

        // Handle semantic versioning
        if (preg_match('/^(\d+\.\d+\.\d+)/', $version, $matches)) {
            return $matches[0];
        }

        // Handle simple numeric versions
        if (preg_match('/^(\d+\.\d+)/', $version, $matches)) {
            return $matches[0];
        }

        return $version;
    }

    /**
     * Perform the update for a sub-application.
     *
     * @param string $app
     * @param string $version
     * @param string $licenceKey
     * @return int
     */
    protected function performUpdate($app, $version, $licenceKey)
    {
        $this->line("[INFO] Starting update for {$app}...");

        try {
            // Clean old backups
            $this->cleanOldBackups($app);

            // Create backup
            $backupPath = $this->createBackup($app);
            $this->info("[SUCCESS] Backup created: " . basename($backupPath));
            $this->logOperation('BACKUP_CREATED', "Backup created for {$app}", null, [
                'app' => $app,
                'backup_path' => basename($backupPath),
            ]);

            // Download update
            $packagePath = $this->downloadUpdate($app, $version, $licenceKey);
            if (!$packagePath) {
                $this->error("[ERROR] Download failed for {$app}. Backup retained at: " . basename($backupPath));
                return self::FAILURE;
            }

            // Apply update
            if ($this->applyUpdate($app, $packagePath)) {
                $this->info("[SUCCESS] Update completed for {$app}!");
                $this->deleteBackup($app, $backupPath);
                $this->logOperation('APP_UPDATE_SUCCESS', "Successfully updated {$app}", null, [
                    'app' => $app,
                    'version' => $version,
                ]);
                return self::SUCCESS;
            }

            $this->error("[ERROR] Failed to apply update for {$app}. Backup retained at: " . basename($backupPath));
            $this->logOperation('APP_UPDATE_FAILED', "Failed to apply update for {$app}", null, ['app' => $app]);
            return self::FAILURE;

        } catch (Exception $e) {
            $this->error("[ERROR] Update failed for {$app}: {$e->getMessage()}. Backup retained at: " . basename($backupPath));
            $this->logOperation('APP_UPDATE_ERROR', "Update failed for {$app}", $e, ['app' => $app]);
            return self::FAILURE;
        }
    }

    /**
     * Clean old backups, keeping only the most recent ones.
     *
     * @param string $app
     * @return void
     */
    protected function cleanOldBackups($app)
    {
        $trashDir = base_path("platform/{$app}/{$this->backupPath}");

        if (!File::exists($trashDir)) {
            return;
        }

        try {
            $backups = [];
            $directories = File::directories($trashDir);

            foreach ($directories as $directory) {
                $dirname = basename($directory);
                if (preg_match('/backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})/', $dirname, $matches)) {
                    $backups[] = [
                        'path' => $directory,
                        'timestamp' => $matches[1],
                        'created' => strtotime(str_replace('_', ' ', $matches[1])),
                    ];
                }
            }

            if (count($backups) >= $this->maxBackups) {
                // Sort by creation time (oldest first)
                usort($backups, fn($a, $b) => $a['created'] <=> $b['created']);

                // Remove oldest backups
                $toRemove = array_slice($backups, 0, count($backups) - $this->maxBackups + 1);

                foreach ($toRemove as $backup) {
                    File::deleteDirectory($backup['path']);
                    $this->line("[INFO] Removed old backup: " . basename($backup['path']));
                    $this->logOperation('BACKUP_CLEANED', 'Removed old backup', null, [
                        'app' => $app,
                        'backup_path' => basename($backup['path']),
                    ]);
                }
            }

        } catch (Exception $e) {
            $this->warn("[WARNING] Failed to clean old backups for {$app}: {$e->getMessage()}");
            $this->logOperation('BACKUP_CLEAN_ERROR', "Failed to clean old backups for {$app}", $e, ['app' => $app]);
        }
    }

    /**
     * Create a backup for a sub-application.
     *
     * @param string $app
     * @return string
     */
    protected function createBackup($app)
    {
        $sourcePath = base_path("platform/{$app}");
        $trashDir = base_path("platform/{$app}/{$this->backupPath}");
        $backupPath = $trashDir . "/backup_" . date('Y-m-d_H-i-s');

        // Ensure trash directory exists
        if (!File::exists($trashDir)) {
            File::makeDirectory($trashDir, 0755, true);
        }

        // If source doesn't exist, create empty backup directory
        if (!File::exists($sourcePath)) {
            $this->line("[INFO] No existing files to backup for {$app}");
            File::makeDirectory($backupPath, 0755, true);
            $this->logOperation('BACKUP_EMPTY', "No files to backup for {$app}", null, ['app' => $app]);
            return $backupPath;
        }

        $this->line("Creating backup...");
        $this->line("Source: {$sourcePath}");
        $this->line("Backup: {$backupPath}");

        try {
            // Use rsync if available (more efficient)
            if ($this->commandExists('rsync')) {
                $cmd = "rsync -av --exclude='{$this->backupPath}' '{$sourcePath}/' '{$backupPath}/' 2>&1";
                $this->line("[EXEC] Using rsync for backup...");
            } else {
                // Fallback to cp
                File::makeDirectory($backupPath, 0755, true);
                $cmd = "cp -r '{$sourcePath}'/* '{$backupPath}/' 2>/dev/null; find '{$backupPath}' -name '{$this->backupPath}' -type d -exec rm -rf {} + 2>/dev/null || true";
                $this->line("[EXEC] Using cp for backup...");
            }

            $output = shell_exec($cmd);
            $fileCount = File::exists($backupPath) ? count(File::allFiles($backupPath)) : 0;

            // Verify backup
            if (!File::exists($backupPath) || $fileCount === 0) {
                throw new Exception("Backup verification failed - no files copied");
            }

            $this->info("[SUCCESS] Backup completed: {$fileCount} files copied");
            $this->logOperation('BACKUP_SUCCESS', "Backup completed for {$app}", null, [
                'app' => $app,
                'file_count' => $fileCount,
            ]);

            return $backupPath;
        } catch (Exception $e) {
            $this->warn("[WARNING] Backup encountered issues for {$app}: {$e->getMessage()}");
            $this->logOperation('BACKUP_ERROR', "Backup failed for {$app}", $e, ['app' => $app]);
            // Ensure backup directory exists even if backup failed
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }
            return $backupPath;
        }
    }

    /**
     * Delete the backup for a sub-application after successful update.
     *
     * @param string $app
     * @param string $backupPath
     * @return void
     */
    protected function deleteBackup($app, $backupPath)
    {
        try {
            if (File::exists($backupPath)) {
                File::deleteDirectory($backupPath);
                $this->info("[SUCCESS] Backup deleted for {$app}: " . basename($backupPath));
                $this->logOperation('BACKUP_DELETED', "Backup deleted for {$app}", null, [
                    'app' => $app,
                    'backup_path' => basename($backupPath),
                ]);
            }
        } catch (Exception $e) {
            $this->warn("[WARNING] Failed to delete backup for {$app}: {$e->getMessage()}");
            $this->logOperation('BACKUP_DELETE_ERROR', "Failed to delete backup for {$app}", $e, ['app' => $app]);
        }
    }

    /**
     * Check if a command exists on the system.
     *
     * @param string $command
     * @return bool
     */
    protected function commandExists($command)
    {
        $result = shell_exec("which {$command} 2>/dev/null");
        return !empty($result);
    }

    /**
     * Download the update package from the API.
     *
     * @param string $app
     * @param string $version
     * @param string $licenceKey
     * @return string|null
     */
    protected function downloadUpdate($app, $version, $licenceKey)
    {
        $this->line("[INFO] Downloading update for {$app} version {$version}...");

        try {
            $response = Http::timeout($this->apiTimeout)
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                    'User-Agent' => $this->userAgent,
                ])
                ->get($this->apiBaseUrl, [
                    'request-type' => 'download-update',
                    'app' => $app,
                    'version' => $version,
                    'licence_key' => $licenceKey,
                ]);

            if (!$response->successful()) {
                $error = "Download failed for {$app}: HTTP {$response->status()}: {$response->body()}";
                $this->logOperation('DOWNLOAD_HTTP_ERROR', $error, null, [
                    'app' => $app,
                    'version' => $version,
                    'status_code' => $response->status(),
                    'response_body' => substr($response->body(), 0, 500),
                ]);
                $this->error("[ERROR] {$error}");
                return null;
            }

            $contentType = $response->header('Content-Type');
            if ($contentType && !str_contains($contentType, 'application/zip') && !str_contains($contentType, 'application/octet-stream')) {
                $this->warn("[WARNING] Unexpected content type: {$contentType}");
                $this->logOperation('DOWNLOAD_CONTENT_TYPE', "Unexpected content type for {$app}", null, [
                    'app' => $app,
                    'content_type' => $contentType,
                ]);
            }

            $packagePath = storage_path("app/{$app}_{$version}_" . time() . ".zip");

            // Ensure storage directory exists
            $storageDir = dirname($packagePath);
            if (!File::exists($storageDir)) {
                File::makeDirectory($storageDir, 0755, true);
            }

            File::put($packagePath, $response->body());

            // Verify download
            if (!File::exists($packagePath) || File::size($packagePath) === 0) {
                throw new Exception("Downloaded file is empty or doesn't exist");
            }

            $fileSize = File::size($packagePath);
            $this->info("[SUCCESS] Downloaded update: " . $this->formatBytes($fileSize));
            $this->logOperation('DOWNLOAD_SUCCESS', "Successfully downloaded update for {$app}", null, [
                'app' => $app,
                'version' => $version,
                'file_size' => $fileSize,
            ]);

            return $packagePath;
        } catch (Exception $e) {
            $error = "Download error for {$app}: {$e->getMessage()}";
            $this->logOperation('DOWNLOAD_EXCEPTION', $error, $e, ['app' => $app]);
            $this->error("[ERROR] {$error}");
            return null;
        }
    }

    /**
     * Apply the downloaded update package.
     *
     * @param string $app
     * @param string $packagePath
     * @return bool
     */
    protected function applyUpdate($app, $packagePath)
    {
        $this->line("[INFO] Applying update for {$app}...");
        $zip = new ZipArchive;
        $targetPath = base_path("platform/{$app}");

        try {
            if ($zip->open($packagePath) !== true) {
                $error = "Failed to open update package for {$app}";
                $this->logOperation('UPDATE_ZIP_ERROR', $error, null, ['app' => $app]);
                $this->error("[ERROR] {$error}");
                return false;
            }

            // Preserve Application.php
            $appFile = "{$targetPath}/Application.php";
            $tempAppFile = storage_path("app/{$app}_Application_" . time() . ".php");
            if (File::exists($appFile)) {
                File::copy($appFile, $tempAppFile);
                $this->line("[INFO] Preserved Application.php for {$app}");
            }

            // Delete existing directory (except .trash)
            if (File::exists($targetPath)) {
                $files = File::allFiles($targetPath);
                foreach ($files as $file) {
                    if (strpos($file->getPathname(), $this->backupPath) === false) {
                        File::delete($file);
                    }
                }
                File::deleteDirectory($targetPath, true);
                $this->line("[INFO] Cleared existing files for {$app}");
            }

            // Extract new files
            File::makeDirectory($targetPath, 0755, true);
            $zip->extractTo($targetPath);
            $zip->close();

            // Restore Application.php
            if (File::exists($tempAppFile)) {
                File::move($tempAppFile, $appFile);
                $this->line("[INFO] Restored Application.php for {$app}");
            }

            // Clean up downloaded package
            File::delete($packagePath);
            $this->line("[SUCCESS] Update applied for {$app}");

            $this->logOperation('UPDATE_APPLIED', "Update applied successfully for {$app}", null, ['app' => $app]);
            return true;
        } catch (Exception $e) {
            $error = "Failed to apply update for {$app}: {$e->getMessage()}";
            $this->logOperation('UPDATE_APPLY_ERROR', $error, $e, ['app' => $app]);
            $this->error("[ERROR] {$error}");
            return false;
        }
    }

    /**
     * Handle revert option.
     *
     * @return int
     */
    protected function handleRevert()
    {
        $this->info('[INFO] Platform Backup Revert');
        $this->newLine();

        try {
            $subApps = $this->detectSubApplications();
            if (empty($subApps)) {
                $this->error('[ERROR] No sub-applications found.');
                $this->logOperation('REVERT_NO_APPS', 'No sub-applications found for revert');
                return self::FAILURE;
            }

            $this->line('Select sub-application to revert:');
            foreach ($subApps as $index => $app) {
                $this->line("  [{$index}] {$app}");
            }
            $choice = $this->ask('Select sub-application (number)', '0');
            if (!is_numeric($choice) || !isset($subApps[$choice])) {
                $this->error('[ERROR] Invalid selection');
                $this->logOperation('REVERT_INVALID_SELECTION', 'Invalid sub-application selection', null, ['choice' => $choice]);
                return self::FAILURE;
            }

            $selectedApp = $subApps[$choice];
            $trashDir = base_path("platform/{$selectedApp}/{$this->backupPath}");
            if (!File::exists($trashDir)) {
                $this->error('[ERROR] No backup directory found for ' . $selectedApp);
                $this->logOperation('REVERT_NO_BACKUPS', 'No backup directory found', null, ['app' => $selectedApp]);
                return self::FAILURE;
            }

            $backups = [];
            $directories = File::directories($trashDir);
            foreach ($directories as $directory) {
                $dirname = basename($directory);
                if (preg_match('/backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})/', $dirname, $matches)) {
                    $timestamp = $matches[1];
                    $readableDate = date('F j, Y \a\t g:i A', strtotime(str_replace('_', ' ', $timestamp)));
                    $size = $this->getDirectorySize($directory);
                    $backups[] = [
                        'file' => $directory,
                        'timestamp' => $timestamp,
                        'readable' => $readableDate,
                        'size' => $this->formatBytes($size),
                    ];
                }
            }

            if (empty($backups)) {
                $this->error('[ERROR] No backup files found for ' . $selectedApp);
                $this->logOperation('REVERT_NO_BACKUP_FILES', 'No backups found', null, ['app' => $selectedApp]);
                return self::FAILURE;
            }

            usort($backups, fn($a, $b) => strcmp($b['timestamp'], $a['timestamp']));
            $this->line('Available backups:');
            $this->newLine();
            foreach ($backups as $index => $backup) {
                $this->line("  [{$index}] {$backup['readable']} ({$backup['size']})");
            }
            $this->newLine();
            $choice = $this->ask('Select backup to restore (number)', '0');
            if (!is_numeric($choice) || !isset($backups[$choice])) {
                $this->error('[ERROR] Invalid backup selection');
                $this->logOperation('REVERT_INVALID_BACKUP', 'Invalid backup selection', null, ['choice' => $choice]);
                return self::FAILURE;
            }

            $selectedBackup = $backups[$choice];
            $this->warn("This will replace your current {$selectedApp} installation with backup from {$selectedBackup['readable']}");
            $confirm = $this->option('no-interaction') ? true : $this->confirm('Are you sure you want to proceed?', false);
            if (!$confirm) {
                $this->info('Operation cancelled');
                $this->logOperation('REVERT_CANCELLED', 'Revert operation cancelled', null, ['app' => $selectedApp]);
                return self::SUCCESS;
            }

            return $this->performRevert($selectedBackup['file'], "platform/{$selectedApp}", $selectedApp);
        } catch (Exception $e) {
            $this->error("[ERROR] Revert process failed: {$e->getMessage()}");
            $this->logOperation('REVERT_ERROR', 'Revert process failed', $e);
            return self::FAILURE;
        }
    }

    /**
     * Perform the revert operation.
     *
     * @param string $backupPath
     * @param string $localPath
     * @param string $app
     * @return int
     */
    protected function performRevert($backupPath, $localPath, $app)
    {
        $this->line('[INFO] Starting revert process for ' . $app . '...');
        try {
            $currentBackupPath = $this->createBackup($app);
            $this->info("[SUCCESS] Current version backed up to: " . basename($currentBackupPath));
            $this->logOperation('REVERT_BACKUP', 'Current version backed up before revert', null, [
                'app' => $app,
                'backup_path' => basename($currentBackupPath),
            ]);

            $targetPath = base_path($localPath);
            if (File::exists($targetPath)) {
                File::deleteDirectory($targetPath);
            }

            $this->copyDirectorySimple($backupPath, $targetPath);
            $this->info('[SUCCESS] Revert completed successfully for ' . $app);
            $this->logOperation('REVERT_SUCCESS', 'Revert completed successfully', null, ['app' => $app]);
            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error("[ERROR] Revert failed for {$app}: {$e->getMessage()}");
            $this->logOperation('REVERT_FAILED', "Revert failed for {$app}", $e, ['app' => $app]);
            return self::FAILURE;
        }
    }

    /**
     * Handle automatic updates setup.
     *
     * @return int
     */
    protected function handleAutomatic()
    {
        $this->info('[INFO] Platform Automatic Updates Setup');
        $this->newLine();
        $this->line('This will setup a daily cron job to automatically check and update platform sub-applications.');
        $this->line('The cron job will run at 2:00 AM every day and will:');
        $this->line('  • Check for new versions');
        $this->line('  • Create backups before updating');
        $this->line('  • Automatically install updates if available');
        $this->line('  • Log all activities');
        $this->newLine();
        $confirm = $this->option('no-interaction') ? true : $this->confirm('Do you want to setup automatic daily updates?', false);
        if (!$confirm) {
            $this->info('Automatic updates setup cancelled');
            $this->logOperation('AUTOMATIC_CANCELLED', 'Automatic updates setup cancelled');
            return self::SUCCESS;
        }

        try {
            $this->setupCronJob();
            $this->info('[SUCCESS] Automatic updates successfully configured!');
            $this->line('Platform sub-applications will now check for updates daily at 2:00 AM');
            $this->line('To disable: php artisan platform:update --automatic and choose to remove');
            $this->logOperation('AUTOMATIC_SUCCESS', 'Automatic updates configured');
            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error("[ERROR] Failed to setup automatic updates: {$e->getMessage()}");
            $this->logOperation('AUTOMATIC_ERROR', 'Failed to setup automatic updates', $e);
            return self::FAILURE;
        }
    }

    /**
     * Setup cron job for automatic updates.
     *
     * @return void
     */
    protected function setupCronJob()
    {
        $projectPath = base_path();
        $phpPath = $this->getPhpPath();
        $artisanPath = $projectPath . '/artisan';
        $command = "cd {$projectPath} && {$phpPath} {$artisanPath} platform:update --no-interaction";
        $cronEntry = "0 2 * * * {$command} >> {$projectPath}/storage/logs/platform-auto-update.log 2>&1";
        $existingCron = shell_exec('crontab -l 2>/dev/null');

        if ($existingCron && strpos($existingCron, 'platform:update') !== false) {
            $this->warn('[WARNING] Automatic updates already configured');
            $remove = $this->option('no-interaction') ? false : $this->confirm('Do you want to remove existing automatic updates?', false);
            if ($remove) {
                $this->removeCronJob();
                $this->info('[SUCCESS] Automatic updates removed');
                $this->logOperation('AUTOMATIC_REMOVED', 'Existing automatic updates removed');
                return;
            }
            $this->info('Keeping existing configuration');
            $this->logOperation('AUTOMATIC_EXISTS', 'Keeping existing cron configuration');
            return;
        }

        $newCron = trim($existingCron) . "\n" . $cronEntry . "\n";
        $tempFile = tempnam(sys_get_temp_dir(), 'platform_cron');
        file_put_contents($tempFile, $newCron);
        $result = shell_exec("crontab {$tempFile} 2>&1");
        unlink($tempFile);

        if ($result !== null && $result !== '') {
            throw new Exception("Failed to install cron job: {$result}");
        }
        $this->line("Cron job added: {$cronEntry}");
        $this->logOperation('CRON_ADDED', 'Cron job added for automatic updates', null, ['cron_entry' => $cronEntry]);
    }

    /**
     * Remove existing cron job.
     *
     * @return void
     */
    protected function removeCronJob()
    {
        $existingCron = shell_exec('crontab -l 2>/dev/null');
        if (!$existingCron) {
            return;
        }

        $lines = explode("\n", $existingCron);
        $filteredLines = array_filter($lines, fn($line) => strpos($line, 'platform:update') === false);
        $newCron = implode("\n", $filteredLines);
        $tempFile = tempnam(sys_get_temp_dir(), 'platform_cron');
        file_put_contents($tempFile, $newCron);
        shell_exec("crontab {$tempFile} 2>&1");
        unlink($tempFile);
        $this->logOperation('CRON_REMOVED', 'Cron job removed for automatic updates');
    }

    /**
     * Get the PHP executable path.
     *
     * @return string
     */
    protected function getPhpPath()
    {
        $phpPaths = [
            PHP_BINARY,
            '/usr/bin/php',
            '/usr/local/bin/php',
            'php',
        ];
        foreach ($phpPaths as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }
        $this->logOperation('PHP_PATH_WARNING', 'Using default php path', null, ['path' => 'php']);
        return 'php';
    }

    /**
     * Copy a directory recursively.
     *
     * @param string $source
     * @param string $target
     * @return void
     */
    protected function copyDirectorySimple($source, $target)
    {
        $result = shell_exec("cp -r '{$source}' '{$target}' 2>&1");
        if ($result !== null && $result !== '') {
            throw new Exception("Copy failed: {$result}");
        }
        $this->logOperation('COPY_SUCCESS', 'Directory copied successfully', null, [
            'source' => $source,
            'target' => $target,
        ]);
    }

    /**
     * Format bytes to human-readable format.
     *
     * @param int $bytes
     * @return string
     */
    protected function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    /**
     * Get the size of a directory.
     *
     * @param string $directory
     * @return int
     */
    protected function getDirectorySize($directory)
    {
        $size = 0;
        try {
            $files = File::allFiles($directory);
            foreach ($files as $file) {
                $size += $file->getSize();
            }
        } catch (Exception $e) {
            $this->logOperation('DIR_SIZE_ERROR', 'Failed to calculate directory size', $e, ['directory' => $directory]);
        }
        return $size;
    }

    /**
     * Restart the application by clearing caches.
     *
     * @return void
     */
    protected function restartApplication()
    {
        $this->info('[INFO] Restarting application...');
        try {
            $this->call('config:clear');
            $this->call('cache:clear');
            $this->call('route:clear');
            $this->call('view:clear');
            $this->info('[SUCCESS] Application restarted successfully!');
            $this->logOperation('RESTART_SUCCESS', 'Application restarted successfully');
        } catch (Exception $e) {
            $this->error("[ERROR] Failed to restart application: {$e->getMessage()}");
            $this->logOperation('RESTART_ERROR', 'Failed to restart application', $e);
        }
    }

    /**
     * Log operation details to storage/logs/platform-update.log.
     *
     * @param string $event
     * @param string $message
     * @param Exception|null $exception
     * @param array $context
     * @return void
     */
    protected function logOperation($event, $message, $exception = null, $context = [])
    {
        $logData = array_merge([
            'event' => $event,
            'timestamp' => now()->toDateTimeString(),
            'dry_run' => $this->option('dry-run') ? true : false,
            'force' => $this->option('force') ? true : false,
        ], $context);

        if ($exception) {
            $logData['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => array_slice($exception->getTrace(), 0, 5),
            ];
        }

        Log::channel('platform-update')->info($message, $logData);
    }
}
