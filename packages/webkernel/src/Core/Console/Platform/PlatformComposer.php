<?php

//packages/webkernel/src/Console/Platform/PlatformComposer.php

declare(strict_types=1);

namespace Webkernel\Core\Console\Platform;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PlatformComposer extends Command
{
    protected $signature = 'webkernel:prepare-platform-composer';
    
    protected $description = 'Prepare and maintain composer.json with proper PSR-4 autoloading and namespace validation';

    private array $namespaceMap = [
        'packages/webkernel' => 'Webkernel',
        'platform' => 'Numerimondes',
    ];

    private array $additionalNamespaceMap = [
        // Add more custom namespace mappings here as needed
    ];

    private array $specialDirectoryMappings = [
        'components' => 'components',
        'views' => 'Views',
        'dto' => 'DTO',
        'resources' => 'Resources',
        'middleware' => 'Middleware',
        'providers' => 'Providers',
        'models' => 'Models',
        'controllers' => 'Controllers',
        'commands' => 'Commands',
        'console' => 'Console',
        'jobs' => 'Jobs',
        'events' => 'Events',
        'listeners' => 'Listeners',
        'policies' => 'Policies',
        'rules' => 'Rules',
        'requests' => 'Requests',
        'exceptions' => 'Exceptions',
        'services' => 'Services',
        'repositories' => 'Repositories',
        'observers' => 'Observers',
        'notifications' => 'Notifications',
        'mail' => 'Mail',
        'broadcasting' => 'Broadcasting',
    ];
    private array $changes = [];
    public function handle(): int
    {
        $this->info('Starting platform composer preparation...');

        if (!$this->checkWholePlatformPreRequisitesOnPreAutoloadDump()) {
            return self::FAILURE;
        }

        if (!$this->validateComposerFile()) {
            return self::FAILURE;
        }

        $this->validateAndFixNamespaces();
        $this->updateComposerAutoloading();

        if (!empty($this->changes)) {
            $this->info('Changes made:');
            foreach ($this->changes as $change) {
                $this->line("  - {$change}");
            }
        } else {
            $this->info('No changes were necessary.');
        }

        $this->info('Platform composer preparation completed successfully.');
        return self::SUCCESS;
    }

    private function checkWholePlatformPreRequisitesOnPreAutoloadDump(): bool
    {
        $this->info('Checking platform prerequisites before autoload dump...');
        
        // Check if vendor directory exists
        $vendorPath = base_path('vendor');
        if (!File::isDirectory($vendorPath)) {
            $this->warn('Vendor directory not found. Running composer install...');
            return $this->runComposerInstall();
        }

        // Check if autoload.php exists
        $autoloadPath = $vendorPath . '/autoload.php';
        if (!File::exists($autoloadPath)) {
            $this->warn('Autoload file not found. Running composer install...');
            return $this->runComposerInstall();
        }

        // Check if autoload is functional
        try {
            $autoloadContent = File::get($autoloadPath);
            if (empty($autoloadContent)) {
                $this->warn('Autoload file is empty. Running composer install...');
                return $this->runComposerInstall();
            }
        } catch (\Exception $e) {
            $this->error('Error reading autoload file: ' . $e->getMessage());
            return $this->runComposerInstall();
        }

        // Check if composer.lock exists and is valid
        $composerLockPath = base_path('composer.lock');
        if (!File::exists($composerLockPath)) {
            $this->warn('Composer lock file not found. Running composer install...');
            return $this->runComposerInstall();
        }

        // Verify that essential directories exist
        $requiredDirectories = [
            'packages/webkernel' => 'WebKernel package directory',
            'platform' => 'Platform directory'
        ];

        foreach ($requiredDirectories as $directory => $description) {
            $fullPath = base_path($directory);
            if (!File::isDirectory($fullPath)) {
                $this->warn("{$description} not found at {$directory}. Creating directory...");
                File::makeDirectory($fullPath, 0755, true);
                $this->changes[] = "Created directory: {$directory}";
            }
        }

        $this->info('Platform prerequisites check completed successfully.');
        return true;
    }

    private function runComposerInstall(): bool
    {
        $this->info('Executing composer install to establish autoloading...');
        
        // Get the composer binary path
        $composerBinary = $this->getComposerBinary();
        if (!$composerBinary) {
            $this->error('Composer binary not found. Please install Composer first.');
            return false;
        }

        // Run composer install without scripts to avoid circular dependency
        $command = "{$composerBinary} install --no-scripts --no-dev --optimize-autoloader";
        
        $output = [];
        $returnCode = 0;
        
        $this->info("Running: {$command}");
        exec($command . ' 2>&1', $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->error('Composer install failed with return code: ' . $returnCode);
            $this->error('Output: ' . implode("\n", $output));
            return false;
        }

        $this->info('Composer install completed successfully.');
        $this->changes[] = 'Executed composer install to establish autoloading';
        
        // Verify that autoload was created
        $autoloadPath = base_path('vendor/autoload.php');
        if (!File::exists($autoloadPath)) {
            $this->error('Autoload file still not found after composer install.');
            return false;
        }

        return true;
    }

    private function getComposerBinary(): ?string
    {
        // Try common composer locations
        $composerPaths = [
            '/usr/local/bin/composer',
            '/usr/bin/composer',
            getcwd() . '/composer.phar',
            'composer.phar',
            'composer'
        ];

        foreach ($composerPaths as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }

        // Try to find composer in PATH
        exec('which composer 2>/dev/null', $output, $returnCode);
        if ($returnCode === 0 && !empty($output)) {
            return trim($output[0]);
        }

        // Try composer.phar in current directory
        if (file_exists('composer.phar')) {
            return 'php composer.phar';
        }

        return null;
    }

    private function validateComposerFile(): bool
    {
        $composerPath = base_path('composer.json');
        
        if (!File::exists($composerPath)) {
            $this->error('composer.json file not found in project root.');
            return false;
        }

        $composerContent = File::get($composerPath);
        $composerData = json_decode($composerContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON in composer.json: ' . json_last_error_msg());
            return false;
        }

        $this->info('composer.json validation passed.');
        return true;
    }

    private function validateAndFixNamespaces(): void
    {
        $namespaceValidator = new NamespaceValidator($this->namespaceMap, $this->specialDirectoryMappings);
        
        // Process main namespace mappings
        foreach ($this->namespaceMap as $directory => $expectedNamespace) {
            $fullPath = base_path($directory);
            
            if (!File::isDirectory($fullPath)) {
                continue;
            }

            $this->info("Validating namespaces in {$directory}...");
            $violations = $namespaceValidator->validateDirectory($fullPath, $expectedNamespace);
            
            if (!empty($violations)) {
                $this->warn("Found " . count($violations) . " namespace violations in {$directory}");
                $fixed = $namespaceValidator->fixViolations($violations);
                
                foreach ($fixed as $file => $oldNamespace) {
                    $this->changes[] = "Fixed namespace in {$file}";
                }
            }
        }

        // Process additional namespace mappings
        foreach ($this->additionalNamespaceMap as $directory => $expectedNamespace) {
            $fullPath = base_path($directory);
            
            if (!File::isDirectory($fullPath)) {
                continue;
            }

            $this->info("Validating namespaces in {$directory}...");
            $violations = $namespaceValidator->validateDirectory($fullPath, $expectedNamespace);
            
            if (!empty($violations)) {
                $this->warn("Found " . count($violations) . " namespace violations in {$directory}");
                $fixed = $namespaceValidator->fixViolations($violations);
                
                foreach ($fixed as $file => $oldNamespace) {
                    $this->changes[] = "Fixed namespace in {$file}";
                }
            }
        }

        // Discover additional packages
        $this->discoverAdditionalPackages();
    }

    private function discoverAdditionalPackages(): void
    {
        $packagesPath = base_path('packages');
        
        if (!File::isDirectory($packagesPath)) {
            return;
        }

        $directories = File::directories($packagesPath);
        
        foreach ($directories as $directory) {
            $packageName = basename($directory);
            
            if ($packageName === 'webkernel') {
                continue; // Already handled
            }

            $composerFile = $directory . '/composer.json';
            if (File::exists($composerFile)) {
                $packageComposer = json_decode(File::get($composerFile), true);
                $autoload = $packageComposer['autoload']['psr-4'] ?? [];
                
                foreach ($autoload as $namespace => $path) {
                    $cleanNamespace = rtrim($namespace, '\\');
                    $this->namespaceMap["packages/{$packageName}"] = $cleanNamespace;
                    
                    $this->info("Discovered package {$packageName} with namespace {$cleanNamespace}");
                    break; // Take first namespace as primary
                }
            }
        }
    }

    private function updateComposerAutoloading(): void
    {
        $composerUpdater = new ComposerUpdater();
        $composerPath = base_path('composer.json');
        $composerData = json_decode(File::get($composerPath), true);

        $originalData = $composerData;

        // Merge additional namespace mappings with main mappings
        $allNamespaceMappings = array_merge($this->namespaceMap, $this->additionalNamespaceMap);

        // Update PSR-4 autoloading
        $composerData = $composerUpdater->updatePsr4Autoloading($composerData, $allNamespaceMappings);
        
        // Update files autoloading
        $composerData = $composerUpdater->updateFilesAutoloading($composerData);
        
        // Update dependencies
        $composerData = $composerUpdater->updateDependencies($composerData);
        
        // Update repositories
        $composerData = $composerUpdater->updateRepositories($composerData);

        if ($originalData !== $composerData) {
            File::put($composerPath, json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->changes[] = 'Updated composer.json with new autoloading configuration';
        }
    }
}

class NamespaceValidator
{
    private array $namespaceMap;
    private array $specialDirectoryMappings;

    public function __construct(array $namespaceMap, array $specialDirectoryMappings = [])
    {
        $this->namespaceMap = $namespaceMap;
        $this->specialDirectoryMappings = $specialDirectoryMappings;
    }

    public function validateDirectory(string $directory, string $expectedNamespace): array
    {
        $violations = [];
        $phpFiles = $this->getPhpFiles($directory);

        foreach ($phpFiles as $file) {
            $violation = $this->validateFile($file, $directory, $expectedNamespace);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        return $violations;
    }

    private function getPhpFiles(string $directory): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function validateFile(string $filePath, string $baseDirectory, string $expectedNamespace): ?array
    {
        $content = File::get($filePath);
        $currentNamespace = $this->extractNamespace($content);

        if (!$currentNamespace) {
            return null; // No namespace found, might be a bootstrap file
        }

        $expectedFileNamespace = $this->calculateExpectedNamespace($filePath, $baseDirectory, $expectedNamespace);
        
        if ($currentNamespace !== $expectedFileNamespace) {
            return [
                'file' => $filePath,
                'current' => $currentNamespace,
                'expected' => $expectedFileNamespace,
                'content' => $content
            ];
        }

        return null;
    }

    private function extractNamespace(string $content): ?string
    {
        if (preg_match('/^\s*namespace\s+([^;]+);/m', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function calculateExpectedNamespace(string $filePath, string $baseDirectory, string $baseNamespace): string
    {
        $relativePath = str_replace($baseDirectory . '/', '', $filePath);
        $directory = dirname($relativePath);
        
        if ($directory === '.' || $directory === 'src') {
            return $baseNamespace;
        }

        // Remove 'src' prefix if present
        $cleanDirectory = preg_replace('/^src\//', '', $directory);
        
        // Handle special directory mappings to preserve Laravel conventions
        $namespaceParts = explode('/', $cleanDirectory);
        $namespaceParts = array_filter($namespaceParts, function($part) {
            return $part !== '.' && $part !== '';
        });

        $mappedParts = [];
        foreach ($namespaceParts as $part) {
            $mappedParts[] = $this->specialDirectoryMappings[$part] ?? ucfirst($part);
        }

        $additionalNamespace = implode('\\', $mappedParts);
        
        return $baseNamespace . ($additionalNamespace ? '\\' . $additionalNamespace : '');
    }

    public function fixViolations(array $violations): array
    {
        $fixed = [];

        foreach ($violations as $violation) {
            $newContent = $this->replaceNamespace(
                $violation['content'],
                $violation['current'],
                $violation['expected']
            );

            File::put($violation['file'], $newContent);
            $fixed[$violation['file']] = $violation['current'];
        }

        return $fixed;
    }

    private function replaceNamespace(string $content, string $oldNamespace, string $newNamespace): string
    {
        $pattern = '/^(\s*namespace\s+)' . preg_quote($oldNamespace, '/') . '(\s*;)/m';
        return preg_replace($pattern, '$1' . $newNamespace . '$2', $content);
    }
}

class ComposerUpdater
{
    private array $requiredDependencies = [
        'illuminate/console' => '^12.0',
        'illuminate/support' => '^12.0',
        'filament/filament' => '^4.0@beta',
    ];

    private array $requiredRepositories = [
        [
            'type' => 'vcs',
            'url' => 'https://github.com/numerimondes/WebKernel'
        ]
    ];

    public function updatePsr4Autoloading(array $composerData, array $namespaceMap): array
    {
        if (!isset($composerData['autoload'])) {
            $composerData['autoload'] = [];
        }

        if (!isset($composerData['autoload']['psr-4'])) {
            $composerData['autoload']['psr-4'] = [];
        }

        // Process main namespace mappings
        foreach ($namespaceMap as $directory => $namespace) {
            $namespacePsr4 = $namespace . '\\';
            $path = $directory . '/src/';
            
            // Check if src directory exists, otherwise use directory directly
            if (!File::isDirectory(base_path($path))) {
                $path = $directory . '/';
            }

            $composerData['autoload']['psr-4'][$namespacePsr4] = $path;
        }

        // Add app namespace if not present
        if (!isset($composerData['autoload']['psr-4']['App\\'])) {
            $composerData['autoload']['psr-4']['App\\'] = 'app/';
        }

        return $composerData;
    }

    public function updateFilesAutoloading(array $composerData): array
    {
        $autoloadFiles = [
            'packages/webkernel/src/Core/helpers.php',
            'platform/bootstrap/helpers.php',
        ];

        $existingFiles = [];
        foreach ($autoloadFiles as $file) {
            if (File::exists(base_path($file))) {
                $existingFiles[] = $file;
            }
        }

        if (!empty($existingFiles)) {
            if (!isset($composerData['autoload']['files'])) {
                $composerData['autoload']['files'] = [];
            }

            $composerData['autoload']['files'] = array_unique(
                array_merge($composerData['autoload']['files'], $existingFiles)
            );
        }

        return $composerData;
    }

    public function updateDependencies(array $composerData): array
    {
        if (!isset($composerData['require'])) {
            $composerData['require'] = [];
        }

        foreach ($this->requiredDependencies as $package => $version) {
            if (!isset($composerData['require'][$package])) {
                $composerData['require'][$package] = $version;
            }
        }

        return $composerData;
    }

    public function updateRepositories(array $composerData): array
    {
        if (!isset($composerData['repositories'])) {
            $composerData['repositories'] = [];
        }

        foreach ($this->requiredRepositories as $repository) {
            $exists = false;
            foreach ($composerData['repositories'] as $existingRepo) {
                if ($existingRepo['url'] === $repository['url']) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $composerData['repositories'][] = $repository;
            }
        }

        return $composerData;
    }
}