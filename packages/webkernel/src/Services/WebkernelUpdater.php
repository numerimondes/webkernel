<?php

namespace Webkernel\Services;

use Webkernel\constants\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebkernelUpdater
{
    /**
     * The package to update
     *
     * @var string
     */
    protected $package;

    /**
     * Packages that were updated during the update process
     *
     * @var array
     */
    protected $updatedPackages = [];

    /**
     * GitHub API base URL
     *
     * @var string
     */
    protected $githubApiUrl = 'https://api.github.com/repos/';

    /**
     * Timeout for HTTP requests (seconds)
     *
     * @var int
     */
    protected $timeout = 30;

    /**
     * Create a new updater instance.
     *
     * @param string|null $package
     * @return void
     */
    public function __construct($package = null)
    {
        $this->package = $package ?: Application::getPackageName();
    }

    /**
     * Check if updates are available
     *
     * @param bool $stableOnly Check only for stable updates
     * @return bool
     */
    public function checkForUpdates($stableOnly = false)
    {
        $updateInfo = $this->getUpdateInfo($stableOnly);
        return $updateInfo['updateAvailable'];
    }

    /**
     * Get update information
     *
     * @param bool $stableOnly Check only for stable updates
     * @return array
     */
    public function getUpdateInfo($stableOnly = false)
    {
        // Use cache to avoid hitting GitHub API too often
        $cacheKey = "webkernel_update_info_{$this->package}_" . ($stableOnly ? 'stable' : 'all');

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $currentVersion = $this->getCurrentVersion();
            $remoteVersionInfo = $this->getRemoteVersionInfo();

            if (!$remoteVersionInfo) {
                throw new \Exception('Unable to fetch remote version information');
            }

            $remoteVersion = $remoteVersionInfo['version'];
            $isStable = $remoteVersionInfo['isStable'];

            // If we're only checking for stable updates and the remote version is not stable, ignore it
            if ($stableOnly && !$isStable) {
                $updateAvailable = false;
            } else {
                $updateAvailable = version_compare($remoteVersion, $currentVersion, '>');
            }

            $result = [
                'currentVersion' => $currentVersion,
                'availableVersion' => $remoteVersion,
                'isStable' => $isStable,
                'updateAvailable' => $updateAvailable,
            ];

            // Cache for 1 hour
            Cache::put($cacheKey, $result, 3600);

            return $result;
        } catch (\Exception $e) {
            Log::error('Error checking for updates: ' . $e->getMessage(), [
                'package' => $this->package,
                'exception' => $e
            ]);

            // Return default result indicating no update available
            return [
                'currentVersion' => $this->getCurrentVersion(),
                'availableVersion' => $this->getCurrentVersion(),
                'isStable' => false,
                'updateAvailable' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get current version of the package
     *
     * @return string
     */
    protected function getCurrentVersion()
    {
        try {
            // If we're checking webkernel, use the constant
            if ($this->package === 'webkernel') {
                return defined('Webkernel\constants\Application::WEBKERNEL_VERSION')
                    ? Application::WEBKERNEL_VERSION
                    : '0.0.0';
            }

            // Otherwise, try to get the version from the package's Application class
            $packageNamespace = Str::studly($this->package);
            $appClass = "\\{$packageNamespace}\\constants\\Application";

            if (class_exists($appClass) && defined("{$appClass}::WEBKERNEL_VERSION")) {
                return constant("{$appClass}::WEBKERNEL_VERSION");
            }

            // Fallback to 0.0.0 if version cannot be determined
            return '0.0.0';
        } catch (\Exception $e) {
            Log::warning('Unable to determine current version: ' . $e->getMessage());
            return '0.0.0';
        }
    }

    /**
     * Get remote version information from GitHub
     *
     * @return array|null
     */
    protected function getRemoteVersionInfo()
    {
        try {
            $repo = config('webkernel.updates.github_repo');
            $branch = config('webkernel.updates.github_branch', 'main');

            if (empty($repo)) {
                throw new \Exception('GitHub repository not configured in webkernel.updates.github_repo');
            }

            $filePath = "packages/{$this->package}/src/constants/Application.php";
            $url = "{$this->githubApiUrl}{$repo}/contents/{$filePath}?ref={$branch}";

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Accept' => 'application/vnd.github.v3.raw',
                    'User-Agent' => 'WebkernelUpdater/1.0'
                ])
                ->get($url);

            if ($response->failed()) {
                throw new \Exception("Failed to fetch remote version. HTTP Status: {$response->status()}. Response: " . $response->body());
            }

            $content = $response->body();

            if (empty($content)) {
                throw new \Exception('Empty response from GitHub API');
            }

            // Extract version constants with more robust patterns
            $versionPattern = '/const\s+WEBKERNEL_VERSION\s*=\s*[\'"]([^\'"\s]+)[\'"]/';
            $stableVersionPattern = '/const\s+STABLE_VERSION\s*=\s*[\'"]([^\'"\s]+)[\'"]/';

            if (!preg_match($versionPattern, $content, $versionMatches)) {
                throw new \Exception('Could not extract WEBKERNEL_VERSION from remote file');
            }

            $version = trim($versionMatches[1]);
            $isStable = false;

            // Check if the version matches stable version
            if (preg_match($stableVersionPattern, $content, $stableMatches)) {
                $stableVersion = trim($stableMatches[1]);
                $isStable = ($version === $stableVersion);
            }

            return [
                'version' => $version,
                'isStable' => $isStable,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching remote version info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Download and update package from GitHub
     *
     * @return array Success status and updated packages
     */
    public function update()
    {
        $tempDir = null;
        $backupDirs = [];

        try {
            $repo = config('webkernel.updates.github_repo');
            $branch = config('webkernel.updates.github_branch', 'main');
            $packagesPath = config('webkernel.updates.packages_path', 'packages');

            if (empty($repo)) {
                throw new \Exception('GitHub repository not configured in webkernel.updates.github_repo');
            }

            // Create temp directory with unique name
            $tempDir = storage_path('app/temp_webkernel_update_' . uniqid() . '_' . time());
            if (!File::makeDirectory($tempDir, 0755, true, true)) {
                throw new \Exception("Failed to create temporary directory: {$tempDir}");
            }

            // Download package from GitHub
            $zipUrl = "https://github.com/{$repo}/archive/{$branch}.zip";
            $zipPath = "{$tempDir}/update.zip";

            Log::info("Downloading update from: {$zipUrl}");

            $response = Http::timeout(120) // Increased timeout for download
                ->withOptions([
                    'sink' => $zipPath,
                    'verify' => false // Disable SSL verification if needed
                ])
                ->get($zipUrl);

            if ($response->failed()) {
                throw new \Exception("Failed to download update package. HTTP Status: {$response->status()}");
            }

            if (!File::exists($zipPath) || File::size($zipPath) === 0) {
                throw new \Exception("Downloaded file is empty or doesn't exist");
            }

            // Extract zip file
            $zip = new \ZipArchive();
            $zipResult = $zip->open($zipPath);

            if ($zipResult !== true) {
                throw new \Exception("Failed to open downloaded zip file. Error code: {$zipResult}");
            }

            if (!$zip->extractTo($tempDir)) {
                $zip->close();
                throw new \Exception("Failed to extract zip file");
            }

            $zip->close();

            // Find the extracted directory (repository root)
            $extractedDir = null;
            $directories = File::directories($tempDir);

            foreach ($directories as $dir) {
                if (basename($dir) !== 'update.zip') {
                    $extractedDir = $dir;
                    break;
                }
            }

            if (!$extractedDir || !File::isDirectory($extractedDir)) {
                throw new \Exception("Could not find extracted directory in: " . implode(', ', $directories));
            }

            // Source directory is the extracted package directory
            $sourceDir = "{$extractedDir}/{$packagesPath}/{$this->package}";

            if (!File::isDirectory($sourceDir)) {
                throw new \Exception("Source package directory not found: {$sourceDir}");
            }

            // Target directory is the local package directory
            $targetDir = base_path("{$packagesPath}/{$this->package}");

            if (!File::isDirectory($targetDir)) {
                throw new \Exception("Target package directory not found: {$targetDir}");
            }

            // Backup current package
            $backupDir = storage_path("app/backup_{$this->package}_" . date('Y-m-d-His'));
            if (!File::copyDirectory($targetDir, $backupDir)) {
                throw new \Exception("Failed to create backup directory: {$backupDir}");
            }
            $backupDirs[] = $backupDir;

            // Copy files from source to target
            $this->copyDirectory($sourceDir, $targetDir);

            Log::info("Successfully updated package: {$this->package}");

            // Check for additional packages to update
            if ($this->package === 'webkernel') {
                $this->updateAdditionalPackages($extractedDir, $packagesPath, $backupDirs);
            }

            // Run post-update actions if available
            $this->runPostUpdateActions();

            return [
                'success' => true,
                'updatedPackages' => [$this->package, ...$this->updatedPackages],
                'backups' => $backupDirs
            ];

        } catch (\Exception $e) {
            Log::error('Update failed: ' . $e->getMessage(), [
                'package' => $this->package,
                'exception' => $e
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'updatedPackages' => [],
                'backups' => $backupDirs
            ];
        } finally {
            // Clean up temp directory
            if ($tempDir && File::isDirectory($tempDir)) {
                try {
                    File::deleteDirectory($tempDir);
                } catch (\Exception $e) {
                    Log::warning("Failed to clean up temporary directory: {$tempDir}");
                }
            }
        }
    }

    /**
     * Update additional packages specified in the Application class
     *
     * @param string $extractedDir The extracted repository directory
     * @param string $packagesPath The packages directory path
     * @param array &$backupDirs Array to store backup directory paths
     * @return void
     */
    protected function updateAdditionalPackages($extractedDir, $packagesPath, &$backupDirs)
    {
        try {
            // Get the list of additional packages to update
            $additionalPackages = $this->getAdditionalPackages();

            foreach ($additionalPackages as $package) {
                $package = trim($package);

                // Skip empty packages or the main package
                if (empty($package) || $package === $this->package) {
                    continue;
                }

                // Check if package exists in the downloaded repository
                $sourceDir = "{$extractedDir}/{$packagesPath}/{$package}";
                $targetDir = base_path("{$packagesPath}/{$package}");

                if (File::isDirectory($sourceDir)) {
                    // If package exists locally, update it
                    if (File::isDirectory($targetDir)) {
                        // Backup current package
                        $backupDir = storage_path("app/backup_{$package}_" . date('Y-m-d-His'));
                        if (File::copyDirectory($targetDir, $backupDir)) {
                            $backupDirs[] = $backupDir;
                        }

                        // Copy files from source to target
                        $this->copyDirectory($sourceDir, $targetDir);

                        $this->updatedPackages[] = $package;
                        Log::info("Updated additional package: {$package}");
                    } else {
                        // If package doesn't exist locally, install it
                        if (File::copyDirectory($sourceDir, $targetDir)) {
                            $this->updatedPackages[] = $package;
                            Log::info("Installed new package: {$package}");
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error updating additional packages: ' . $e->getMessage());
        }
    }

    /**
     * Get the list of additional packages to update
     *
     * @return array
     */
    protected function getAdditionalPackages()
    {
        try {
            $packages = [];

            if (defined('Webkernel\constants\Application::ADDITIONAL_PACKAGES')) {
                $packagesString = Application::ADDITIONAL_PACKAGES;
                $packages = array_filter(array_map('trim', explode(',', $packagesString)));
            }

            return $packages;
        } catch (\Exception $e) {
            Log::error('Error getting additional packages: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Copy directory with overwrite and better error handling
     *
     * @param string $source
     * @param string $destination
     * @return void
     * @throws \Exception
     */
    protected function copyDirectory($source, $destination)
    {
        if (!File::isDirectory($source)) {
            throw new \Exception("Source directory does not exist: {$source}");
        }

        // Create destination directory if it doesn't exist
        if (!File::isDirectory($destination)) {
            if (!File::makeDirectory($destination, 0755, true)) {
                throw new \Exception("Failed to create destination directory: {$destination}");
            }
        }

        $directoryIterator = new \RecursiveDirectoryIterator(
            $source,
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        $recursiveIterator = new \RecursiveIteratorIterator(
            $directoryIterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($recursiveIterator as $item) {
            $sourcePath = $item->getPathname();
            $relativePath = substr($sourcePath, strlen($source) + 1);
            $targetPath = $destination . DIRECTORY_SEPARATOR . $relativePath;

            if ($item->isDir()) {
                // Create directory if it doesn't exist
                if (!File::isDirectory($targetPath)) {
                    if (!File::makeDirectory($targetPath, 0755, true)) {
                        throw new \Exception("Failed to create directory: {$targetPath}");
                    }
                }
            } else {
                // Create target directory if it doesn't exist
                $targetDir = dirname($targetPath);
                if (!File::isDirectory($targetDir)) {
                    if (!File::makeDirectory($targetDir, 0755, true)) {
                        throw new \Exception("Failed to create parent directory: {$targetDir}");
                    }
                }

                // Copy file with error checking
                if (!File::copy($sourcePath, $targetPath)) {
                    throw new \Exception("Failed to copy file from {$sourcePath} to {$targetPath}");
                }
            }
        }
    }

    /**
     * Run post-update actions
     *
     * @return void
     */
    protected function runPostUpdateActions()
    {
        try {
            $postUpdateScript = base_path("packages/{$this->package}/src/scripts/post-update.php");

            if (File::exists($postUpdateScript)) {
                Log::info("Running post-update script for {$this->package}");
                include $postUpdateScript;
            }
        } catch (\Exception $e) {
            Log::error("Error running post-update actions: " . $e->getMessage());
        }
    }
}
