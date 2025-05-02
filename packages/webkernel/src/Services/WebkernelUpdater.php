<?php

namespace Webkernel\Services;

use Webkernel\constants\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
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
        $cacheKey = "webkernel_update_info_{$this->package}_{$stableOnly}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $currentVersion = $this->getCurrentVersion();
            $remoteVersionInfo = $this->getRemoteVersionInfo();

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
            Cache::put($cacheKey, $result, 60 * 60);

            return $result;
        } catch (\Exception $e) {
            logger()->error('Error checking for updates: ' . $e->getMessage());

            // Return default result indicating no update available
            return [
                'currentVersion' => $this->getCurrentVersion(),
                'availableVersion' => $this->getCurrentVersion(),
                'isStable' => false,
                'updateAvailable' => false,
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
        // If we're checking webkernel, use the constant
        if ($this->package === 'webkernel') {
            return Application::WEBKERNEL_VERSION;
        }

        // Otherwise, try to get the version from the package's Application class
        $packageNamespace = Str::studly($this->package);
        $appClass = "\\{$packageNamespace}\\constants\\Application";

        if (class_exists($appClass) && defined("{$appClass}::WEBKERNEL_VERSION")) {
            return $appClass::WEBKERNEL_VERSION;
        }

        // Fallback to 0.0.0 if version cannot be determined
        return '0.0.0';
    }

    /**
     * Get remote version information from GitHub
     *
     * @return array
     * @throws \Exception
     */
    protected function getRemoteVersionInfo()
    {
        $repo = config('webkernel.updates.github_repo');
        $branch = config('webkernel.updates.github_branch', 'main');

        if (empty($repo)) {
            throw new \Exception('GitHub repository not configured');
        }

        $filePath = "packages/{$this->package}/src/constants/Application.php";
        $url = "{$this->githubApiUrl}{$repo}/contents/{$filePath}?ref={$branch}";

        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3.raw',
        ])->get($url);

        if ($response->failed()) {
            throw new \Exception("Failed to fetch remote version: {$response->status()}");
        }

        $content = $response->body();

        // Extract version constants
        $versionPattern = '/const\s+WEBKERNEL_VERSION\s*=\s*[\'"]([^\'"]+)[\'"]/';
        $stableVersionPattern = '/const\s+STABLE_VERSION\s*=\s*[\'"]([^\'"]+)[\'"]/';

        if (!preg_match($versionPattern, $content, $versionMatches)) {
            throw new \Exception('Could not extract version from remote file');
        }

        $version = $versionMatches[1];
        $isStable = false;

        // Check if the version matches stable version
        if (preg_match($stableVersionPattern, $content, $stableMatches)) {
            $stableVersion = $stableMatches[1];
            $isStable = ($version === $stableVersion);
        }

        return [
            'version' => $version,
            'isStable' => $isStable,
        ];
    }

    /**
     * Download and update package from GitHub
     *
     * @return array Success status and updated packages
     */
    public function update()
    {
        try {
            $repo = config('webkernel.updates.github_repo');
            $branch = config('webkernel.updates.github_branch', 'main');
            $packagesPath = config('webkernel.updates.packages_path', 'packages');

            if (empty($repo)) {
                throw new \Exception('GitHub repository not configured');
            }

            // Create temp directory
            $tempDir = storage_path('app/temp_webkernel_update_' . time());
            File::makeDirectory($tempDir, 0755, true, true);

            // Download package from GitHub (using zip download feature)
            $zipUrl = "https://github.com/{$repo}/archive/{$branch}.zip";
            $zipPath = "{$tempDir}/update.zip";

            $response = Http::withOptions([
                'sink' => $zipPath
            ])->get($zipUrl);

            if ($response->failed()) {
                throw new \Exception("Failed to download update package: {$response->status()}");
            }

            // Extract zip file
            $zip = new \ZipArchive();
            if ($zip->open($zipPath) !== true) {
                throw new \Exception("Failed to open downloaded zip file");
            }

            $zip->extractTo($tempDir);
            $zip->close();

            // Find the extracted directory (repository root)
            $extractedDir = null;
            foreach (File::directories($tempDir) as $dir) {
                $extractedDir = $dir;
                break;
            }

            if (!$extractedDir) {
                throw new \Exception("Could not find extracted directory");
            }

            // Source directory is the extracted package directory in the full Laravel app
            $sourceDir = "{$extractedDir}/{$packagesPath}/{$this->package}";

            if (!File::isDirectory($sourceDir)) {
                throw new \Exception("Source directory not found: {$sourceDir}");
            }

            // Target directory is the local package directory
            $targetDir = base_path("{$packagesPath}/{$this->package}");

            if (!File::isDirectory($targetDir)) {
                throw new \Exception("Target directory not found: {$targetDir}");
            }

            // Backup current package
            $backupDir = storage_path("app/backup_{$this->package}_" . date('Y-m-d-His'));
            File::copyDirectory($targetDir, $backupDir);

            // Copy files from source to target
            $this->copyDirectory($sourceDir, $targetDir);

            // Check for additional packages to update
            if ($this->package === 'webkernel') {
                $this->updateAdditionalPackages($extractedDir, $packagesPath);
            }

            // Clean up
            File::deleteDirectory($tempDir);

            // Run post-update actions if available
            $this->runPostUpdateActions();

            return [
                'success' => true,
                'updatedPackages' => ['webkernel', ...$this->updatedPackages],
            ];
        } catch (\Exception $e) {
            logger()->error('Update failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'updatedPackages' => []
            ];
        }
    }

    /**
     * Update additional packages specified in the Application class
     *
     * @param string $extractedDir The extracted repository directory
     * @param string $packagesPath The packages directory path
     * @return void
     */
    protected function updateAdditionalPackages($extractedDir, $packagesPath)
    {
        try {
            // Get the list of additional packages to update
            $additionalPackages = $this->getAdditionalPackages();

            foreach ($additionalPackages as $package) {
                // Skip if it's the main package (already updated)
                if ($package === 'webkernel') {
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
                        File::copyDirectory($targetDir, $backupDir);

                        // Copy files from source to target
                        $this->copyDirectory($sourceDir, $targetDir);

                        $this->updatedPackages[] = $package;
                        logger()->info("Updated additional package: {$package}");
                    } else {
                        // If package doesn't exist locally, install it
                        File::copyDirectory($sourceDir, $targetDir);
                        $this->updatedPackages[] = $package;
                        logger()->info("Installed new package: {$package}");
                    }
                }
            }
        } catch (\Exception $e) {
            logger()->error('Error updating additional packages: ' . $e->getMessage());
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
            // Check for ADDITIONAL_PACKAGES constant in Application class
            $packages = [];

            if (defined('\\Webkernel\\constants\\Application::ADDITIONAL_PACKAGES')) {
                $packagesString = Application::ADDITIONAL_PACKAGES;
                $packages = array_map('trim', explode(',', $packagesString));
            }

            return $packages;
        } catch (\Exception $e) {
            logger()->error('Error getting additional packages: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Copy directory with overwrite
     *
     * @param string $source
     * @param string $destination
     * @return void
     */
    protected function copyDirectory($source, $destination)
    {
        // Create destination directory if it doesn't exist
        if (!File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $directoryIterator = new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS);
        $recursiveIterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($recursiveIterator as $item) {
            $sourcePath = $item->getPathname();
            $relativePath = substr($sourcePath, strlen($source) + 1);
            $targetPath = $destination . DIRECTORY_SEPARATOR . $relativePath;

            if ($item->isDir()) {
                // Create directory if it doesn't exist
                if (!File::isDirectory($targetPath)) {
                    File::makeDirectory($targetPath, 0755, true);
                }
            } else {
                // Create target directory if it doesn't exist
                $targetDir = dirname($targetPath);
                if (!File::isDirectory($targetDir)) {
                    File::makeDirectory($targetDir, 0755, true);
                }

                // Copy file
                File::copy($sourcePath, $targetPath, true);
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
        // Check if there's a post-update script in the package
        $postUpdateScript = base_path("packages/{$this->package}/src/scripts/post-update.php");

        if (File::exists($postUpdateScript)) {
            include $postUpdateScript;
        }
    }
}
