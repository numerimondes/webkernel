<?php
declare(strict_types=1);

namespace Webkernel\Core\Console\Platform;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Exception;

class UpdatePlatformComposer
{
    private array $namespaceMap;
    private array $specialDirectoryMappings;
    private array $changes = [];

    public function __construct(?array $namespaceMap = null, ?array $specialDirectoryMappings = null)
    {
        $this->namespaceMap = $namespaceMap ?? $this->getDefaultNamespaceMap();
        $this->specialDirectoryMappings = $specialDirectoryMappings ?? $this->getDefaultSpecialDirectoryMappings();
    }

    private function getDefaultNamespaceMap(): array
    {
        return [
            "App\\" => "app/",
            "Database\\Factories\\" => "database/factories/",
            "Database\\Seeders\\" => "database/seeders/",
            "Webkernel\\" => "packages/webkernel/src/",
            "Numerimondes\\" => "platform/"
        ];
    }

    private function getDefaultSpecialDirectoryMappings(): array
    {
        return [
            'components' => 'components',
            'dto' => 'DTO',
        ];
    }

    public function run(): int
    {
        echo "Starting platform composer preparation...\n";

        if (!$this->checkPrerequisites()) {
            return 1;
        }

        if (!$this->validateComposerFile()) {
            return 1;
        }

        $this->updateComposerAutoloading();
        $this->validateAndFixNamespaces();

        if (!empty($this->changes)) {
            echo "Changes made:\n";
            foreach ($this->changes as $change) {
                echo "  - {$change}\n";
            }
        } else {
            echo "No changes were necessary.\n";
        }

        echo "Platform composer preparation completed successfully.\n";
        return 0;
    }

    private function checkPrerequisites(): bool
    {
        echo "Checking platform prerequisites...\n";

        $vendorPath = getcwd() . '/vendor';
        if (!is_dir($vendorPath)) {
            echo "Vendor directory not found. Running composer install...\n";
            return $this->runComposerInstall();
        }

        $autoloadPath = $vendorPath . '/autoload.php';
        if (!file_exists($autoloadPath)) {
            echo "Autoload file not found. Running composer install...\n";
            return $this->runComposerInstall();
        }

        try {
            $autoloadContent = file_get_contents($autoloadPath);
            if (empty($autoloadContent)) {
                echo "Autoload file is empty. Running composer install...\n";
                return $this->runComposerInstall();
            }
        } catch (Exception $e) {
            echo "Error reading autoload file: " . $e->getMessage() . "\n";
            return $this->runComposerInstall();
        }

        $composerLockPath = getcwd() . '/composer.lock';
        if (!file_exists($composerLockPath)) {
            echo "Composer lock file not found. Running composer install...\n";
            return $this->runComposerInstall();
        }

        foreach ($this->namespaceMap as $namespace => $path) {
            $fullPath = getcwd() . '/' . rtrim($path, '/');
            if (!is_dir($fullPath)) {
                echo "Directory for namespace {$namespace} not found at {$path}. Creating directory...\n";
                mkdir($fullPath, 0755, true);
                $this->changes[] = "Created directory: {$path}";
            }
        }

        echo "Platform prerequisites check completed successfully.\n";
        return true;
    }

    private function runComposerInstall(): bool
    {
        echo "Executing composer install to establish autoloading...\n";

        $composerBinary = $this->getComposerBinary();
        if (!$composerBinary) {
            echo "Composer binary not found. Please install Composer first.\n";
            return false;
        }

        $command = "{$composerBinary} install --no-scripts --no-dev --optimize-autoloader";

        echo "Running: {$command}\n";
        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            echo "Composer install failed with return code: {$returnCode}\n";
            echo "Output: " . implode("\n", $output) . "\n";
            return false;
        }

        echo "Composer install completed successfully.\n";
        $this->changes[] = 'Executed composer install to establish autoloading';

        $autoloadPath = getcwd() . '/vendor/autoload.php';
        if (!file_exists($autoloadPath)) {
            echo "Autoload file still not found after composer install.\n";
            return false;
        }

        return true;
    }

    private function getComposerBinary(): ?string
    {
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

        exec('which composer 2>/dev/null', $output, $returnCode);
        if ($returnCode === 0 && !empty($output)) {
            return trim($output[0]);
        }

        if (file_exists('composer.phar')) {
            return 'php composer.phar';
        }

        return null;
    }

    private function validateComposerFile(): bool
    {
        $composerPath = getcwd() . '/composer.json';

        if (!file_exists($composerPath)) {
            echo "composer.json file not found in project root.\n";
            return false;
        }

        $composerContent = file_get_contents($composerPath);
        $composerData = json_decode($composerContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Invalid JSON in composer.json: " . json_last_error_msg() . "\n";
            return false;
        }

        echo "composer.json validation passed.\n";
        return true;
    }

    private function validateAndFixNamespaces(): void
    {
        $namespaceValidator = new NamespaceValidator($this->namespaceMap, $this->specialDirectoryMappings);

        foreach ($this->namespaceMap as $namespace => $path) {
            $fullPath = getcwd() . '/' . rtrim($path, '/');

            if (!is_dir($fullPath)) {
                continue;
            }

            echo "Validating namespaces in {$path}...\n";
            $expectedNamespace = rtrim($namespace, '\\');
            $violations = $namespaceValidator->validateDirectory($fullPath, $expectedNamespace);

            if (!empty($violations)) {
                echo "Found " . count($violations) . " namespace violations in {$path}\n";
                $fixed = $namespaceValidator->fixViolations($violations);

                foreach ($fixed as $file => $oldNamespace) {
                    $this->changes[] = "Fixed namespace in {$file}";
                }
            }
        }

        $this->discoverAdditionalPackages();
    }

    private function discoverAdditionalPackages(): void
    {
        $packagesPath = getcwd() . '/packages';

        if (!is_dir($packagesPath)) {
            return;
        }

        $directories = glob($packagesPath . '/*', GLOB_ONLYDIR);

        foreach ($directories as $directory) {
            $packageName = basename($directory);

            $alreadyMapped = false;
            foreach ($this->namespaceMap as $namespace => $path) {
                if (strpos($path, "packages/{$packageName}") !== false) {
                    $alreadyMapped = true;
                    break;
                }
            }

            if ($alreadyMapped) {
                continue;
            }

            $composerFile = $directory . '/composer.json';
            if (file_exists($composerFile)) {
                $packageComposer = json_decode(file_get_contents($composerFile), true);
                $autoload = $packageComposer['autoload']['psr-4'] ?? [];

                foreach ($autoload as $namespace => $path) {
                    $this->namespaceMap[$namespace] = "packages/{$packageName}/" . ltrim($path, '/');

                    echo "Discovered package {$packageName} with namespace {$namespace}\n";
                    break;
                }
            }
        }
    }

    private function updateComposerAutoloading(): void
    {
        echo "Updating composer autoloading...\n";

        $composerUpdater = new ComposerUpdater($this->namespaceMap);
        $composerPath = getcwd() . '/composer.json';

        $composerContent = file_get_contents($composerPath);
        $composerData = json_decode($composerContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Error reading composer.json: " . json_last_error_msg() . "\n";
            return;
        }

        $originalData = $composerData;

        echo "Updating PSR-4 namespaces...\n";
        $composerData = $composerUpdater->updatePsr4Autoloading($composerData);

        echo "Updating autoload files...\n";
        $composerData = $composerUpdater->updateFilesAutoloading($composerData);

        $generatedComposerData = $this->getGeneratedComposerData();
        if ($generatedComposerData) {
            echo "Merging with generated data...\n";
            $composerData = $composerUpdater->mergeWithGeneratedData($composerData, $generatedComposerData);
        }

        if ($originalData !== $composerData) {
            echo "Writing updated composer.json...\n";

            $formattedJson = json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            if (file_put_contents($composerPath, $formattedJson) !== false) {
                $this->changes[] = 'Updated composer.json with new autoloading configuration';
                echo "composer.json updated successfully\n";
            } else {
                echo "Error writing composer.json\n";
            }
        } else {
            echo "No changes necessary in composer.json\n";
        }

        if ($generatedComposerData) {
            $outputPath = getcwd() . '/composer.generated.json';
            $formattedGeneratedJson = json_encode($generatedComposerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            if (file_put_contents($outputPath, $formattedGeneratedJson) !== false) {
                $this->changes[] = 'Generated composer.generated.json';
                echo "composer.generated.json created successfully\n";
            } else {
                echo "Error creating composer.generated.json\n";
            }
        }

        echo "Composer autoloading update completed\n";
    }

    private function getGeneratedComposerData(): ?array
    {
        if (class_exists('Webkernel\PlatformConfig\WebkernelConfigFile')) {
            try {
                return \Webkernel\PlatformConfig\WebkernelConfigFile::generateComposerArray();
            } catch (Exception $e) {
                echo "Warning: Could not generate composer data from WebkernelConfigFile: " . $e->getMessage() . "\n";
            }
        }

        $configPath = getcwd() . '/platform/config/webkernel.php';
        if (file_exists($configPath)) {
            try {
                $config = include $configPath;
                return $this->generateComposerDataFromConfig($config);
            } catch (Exception $e) {
                echo "Warning: Could not load platform config: " . $e->getMessage() . "\n";
            }
        }

        return null;
    }

    private function generateComposerDataFromConfig(array $config): array
    {
        $composerData = [];

        if (isset($config['require'])) {
            $composerData['require'] = $config['require'];
        }

        if (isset($config['require-dev'])) {
            $composerData['require-dev'] = $config['require-dev'];
        }

        if (isset($config['autoload'])) {
            $composerData['autoload'] = $config['autoload'];
        }

        if (isset($config['repositories'])) {
            $composerData['repositories'] = $config['repositories'];
        }

        if (isset($config['scripts'])) {
            $composerData['scripts'] = $config['scripts'];
        }

        return $composerData;
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
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
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
        $content = file_get_contents($filePath);
        $currentNamespace = $this->extractNamespace($content);

        if (!$currentNamespace) {
            return null;
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

        $cleanDirectory = preg_replace('/^src\//', '', $directory);

        $namespaceParts = explode('/', $cleanDirectory);
        $namespaceParts = array_filter($namespaceParts, function($part) {
            return $part !== '.' && $part !== '';
        });

        $mappedParts = [];
        foreach ($namespaceParts as $part) {
            $lowerPart = strtolower($part);
            if (isset($this->specialDirectoryMappings[$lowerPart])) {
                $mappedParts[] = $this->specialDirectoryMappings[$lowerPart];
            } else {
                $mappedParts[] = ucfirst($part);
            }
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

            file_put_contents($violation['file'], $newContent);
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
    private array $namespaceMap;

    public function __construct(array $namespaceMap)
    {
        $this->namespaceMap = $namespaceMap;
    }

    public function updatePsr4Autoloading(array $composerData): array
    {
        if (!isset($composerData['autoload'])) {
            $composerData['autoload'] = [];
        }

        if (!isset($composerData['autoload']['psr-4'])) {
            $composerData['autoload']['psr-4'] = [];
        }

        foreach ($this->namespaceMap as $namespace => $path) {
            $composerData['autoload']['psr-4'][$namespace] = $path;
        }

        return $composerData;
    }

    public function updateFilesAutoloading(array $composerData): array
    {
        $requiredFiles = [
            'packages/webkernel/src/PlatformConfig/ConfigFiles/webkernel.php'
        ];

        echo "Searching for autoload files...\n";

        foreach ($this->namespaceMap as $namespace => $path) {
            $possibleHelperFiles = [
                $path . 'helpers.php',
                $path . 'bootstrap/helpers.php',
                $path . 'Core/helpers.php',
                rtrim($path, '/') . '/helpers.php'
            ];

            foreach ($possibleHelperFiles as $file) {
                $fullPath = getcwd() . '/' . $file;
                if (file_exists($fullPath)) {
                    $requiredFiles[] = $file;
                    echo "  - File found: {$file}\n";
                }
            }
        }

        $requiredFiles = array_unique($requiredFiles);
        $requiredFiles = array_values(array_filter($requiredFiles, function($file) {
            return !empty($file) && file_exists(getcwd() . '/' . $file);
        }));

        echo "Autoload files to add: " . count($requiredFiles) . "\n";
        foreach ($requiredFiles as $file) {
            echo "  - {$file}\n";
        }

        if (!isset($composerData['autoload'])) {
            $composerData['autoload'] = [];
            echo "Initializing autoload section\n";
        }

        if (!isset($composerData['autoload']['files'])) {
            $composerData['autoload']['files'] = [];
            echo "Initializing autoload.files section\n";
        }

        $originalFiles = $composerData['autoload']['files'];

        $composerData['autoload']['files'] = array_values(
            array_unique(array_merge($composerData['autoload']['files'], $requiredFiles))
        );

        $addedFiles = array_diff($composerData['autoload']['files'], $originalFiles);
        if (!empty($addedFiles)) {
            echo "Files added to composer.json:\n";
            foreach ($addedFiles as $file) {
                echo "  + {$file}\n";
            }
        } else {
            echo "No new files to add\n";
        }

        echo "Total autoload files: " . count($composerData['autoload']['files']) . "\n";
        return $composerData;
    }

    public function mergeWithGeneratedData(array $composerData, array $generatedData): array
    {
        if (isset($generatedData['require'])) {
            $composerData['require'] = array_merge($composerData['require'] ?? [], $generatedData['require']);
        }

        if (isset($generatedData['require-dev'])) {
            $composerData['require-dev'] = array_merge($composerData['require-dev'] ?? [], $generatedData['require-dev']);
        }

        if (isset($generatedData['autoload']['psr-4'])) {
            $composerData['autoload']['psr-4'] = array_merge($composerData['autoload']['psr-4'] ?? [], $generatedData['autoload']['psr-4']);
        }

        if (isset($generatedData['autoload']['files'])) {
            $composerData['autoload']['files'] = array_unique(array_merge($composerData['autoload']['files'] ?? [], $generatedData['autoload']['files']));
        }

        if (isset($generatedData['repositories'])) {
            $composerData['repositories'] = array_merge($composerData['repositories'] ?? [], $generatedData['repositories']);
        }

        if (isset($generatedData['scripts'])) {
            $composerData['scripts'] = array_merge($composerData['scripts'] ?? [], $generatedData['scripts']);
        }

        return $composerData;
    }
}

$customNamespaceMap = [];
$composer = new UpdatePlatformComposer($customNamespaceMap);
exit($composer->run());
