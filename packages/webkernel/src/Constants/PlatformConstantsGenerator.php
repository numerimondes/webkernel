<?php
/**
 * Platform Constants Generator
 * Generates versioned constants with hierarchical resolution
 * Compatible with Composer pre-autoload
 */
declare(strict_types=1);

// ================================
// CONFIGURATION CONSTANTS
// ================================
const PC_BASE_NAMESPACE = 'Webkernel\\Constants\\Definitions\\';
const PC_DEFINITIONS_DIR = 'Definitions';
const PC_STATIC_DIR = 'Static';
const PC_PLATFORM_FILE = 'PlatformDynamicConstants.php';
const PC_MODULES_DIR = 'platform/Modules';
const PC_WEBKERNEL_FALLBACK = 'Webkernel';
const PC_CORE_FALLBACK = 'Core';
const PC_VERSION_PATTERN = '/^v_\d+_\d+_\d+$/';

// ================================
// MAIN EXECUTION
// ================================
$startTime = microtime(true);
$baseDir = __DIR__;
$definitionsDir = $baseDir . DIRECTORY_SEPARATOR . PC_DEFINITIONS_DIR;
$staticDir = $baseDir . DIRECTORY_SEPARATOR . PC_STATIC_DIR;
$platformFilePath = $staticDir . DIRECTORY_SEPARATOR . PC_PLATFORM_FILE;
$modulesDir = dirname($baseDir, 4) . DIRECTORY_SEPARATOR . PC_MODULES_DIR;

if (!is_dir($staticDir)) {
    mkdir($staticDir, 0755, true);
}

// ================================
// UTILITY FUNCTIONS
// ================================
function validateAndExportValue($value): string
{
    if (is_string($value)) {
        return "'" . str_replace(['\\', "'"], ['\\\\', "\\'"], $value) . "'";
    } elseif (is_numeric($value)) {
        return (string)$value;
    } elseif (is_bool($value)) {
        return $value ? 'true' : 'false';
    } elseif (is_null($value)) {
        return 'null';
    } elseif (is_array($value)) {
        return "'" . str_replace("'", "\\'", json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) . "'";
    }
    return 'null';
}

function isValidVersion($version): bool
{
    return is_string($version) && preg_match(PC_VERSION_PATTERN, $version);
}

function versionToDirectory($version): string
{
    return 'v_' . str_replace('.', '_', $version);
}

function directoryToVersion($directory): string
{
    return str_replace(['v_', '_'], ['', '.'], $directory);
}

function compareVersions($a, $b): int
{
    $versionA = directoryToVersion($a);
    $versionB = directoryToVersion($b);
    return version_compare($versionA, $versionB);
}

function getModuleVersions($modulesDir): array
{
    $versions = [];
    if (!is_dir($modulesDir)) {
        return $versions;
    }
    
    $moduleIterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($modulesDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($moduleIterator as $file) {
        if ($file->isFile() && $file->getFilename() === 'Constants.php') {
            $filePath = $file->getRealPath();
            $relativePath = str_replace($modulesDir . DIRECTORY_SEPARATOR, '', $filePath);
            $pathParts = explode(DIRECTORY_SEPARATOR, $relativePath);
            
            if (count($pathParts) >= 3) {
                $module = $pathParts[0];
                $subModule = $pathParts[1];
                
                try {
                    $content = file_get_contents($filePath);
                    if (preg_match("/const\s+VERSION\s*=\s*['\"]([^'\"]+)['\"]/", $content, $matches)) {
                        $versions[$module][$subModule] = $matches[1];
                    }
                } catch (Throwable $e) {
                    echo "Warning: Could not read version from {$relativePath}: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    return $versions;
}

function getAvailableVersions($definitionsDir): array
{
    $availableVersions = [];
    if (!is_dir($definitionsDir)) {
        return $availableVersions;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($definitionsDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isDir()) {
            $dirName = $file->getFilename();
            if (isValidVersion($dirName)) {
                $relativePath = str_replace($definitionsDir . DIRECTORY_SEPARATOR, '', $file->getRealPath());
                $pathParts = explode(DIRECTORY_SEPARATOR, $relativePath);
                
                if (count($pathParts) >= 3) {
                    $module = $pathParts[0];
                    $subModule = $pathParts[1];
                    $version = $pathParts[2];
                    
                    if (!isset($availableVersions[$module][$subModule])) {
                        $availableVersions[$module][$subModule] = [];
                    }
                    $availableVersions[$module][$subModule][] = $version;
                }
            }
        }
    }
    
    foreach ($availableVersions as $module => $subModules) {
        foreach ($subModules as $subModule => $versions) {
            usort($availableVersions[$module][$subModule], 'compareVersions');
        }
    }
    
    return $availableVersions;
}

function mapToPhysicalVersion($requestedVersion, $availableVersions): ?string
{
    $requestedVersionDir = versionToDirectory($requestedVersion);
    
    if (in_array($requestedVersionDir, $availableVersions)) {
        return $requestedVersionDir;
    }
    
    $compatibleVersions = array_filter($availableVersions, function($version) use ($requestedVersion) {
        $versionValue = directoryToVersion($version);
        return version_compare($versionValue, $requestedVersion) <= 0;
    });
    
    if (empty($compatibleVersions)) {
        return null;
    }
    
    usort($compatibleVersions, 'compareVersions');
    return end($compatibleVersions);
}

function loadConstantsFromClass($className): array
{
    $constants = [];
    
    try {
        if (!class_exists($className)) {
            return $constants;
        }
        
        $refClass = new ReflectionClass($className);
        $classConstants = $refClass->getConstants(ReflectionClassConstant::IS_PUBLIC);
        
        foreach ($classConstants as $name => $value) {
            if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
                $constants[$name] = $value;
            }
        }
    } catch (Throwable $e) {
        echo "Warning: Could not reflect class {$className}: " . $e->getMessage() . "\n";
    }
    
    return $constants;
}

function loadVersionedConstants($definitionsDir, $module, $subModule, $versionDir): array
{
    $constants = [];
    $versionPath = $definitionsDir . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $subModule . DIRECTORY_SEPARATOR . $versionDir;
    
    if (!is_dir($versionPath)) {
        return $constants;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($versionPath, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filePath = $file->getRealPath();
            
            try {
                require_once $filePath;
                
                $relativePath = str_replace($versionPath . DIRECTORY_SEPARATOR, '', $filePath);
                $classPath = str_replace(['/', '.php'], ['\\', ''], $relativePath);
                $className = PC_BASE_NAMESPACE . $module . '\\' . $subModule . '\\' . $versionDir . '\\' . $classPath;
                
                $classConstants = loadConstantsFromClass($className);
                $constants = array_merge($constants, $classConstants);
                
            } catch (Throwable $e) {
                echo "Warning: Could not load {$filePath}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    return $constants;
}

function loadCoreConstants($definitionsDir, $module): array
{
    $constants = [];
    $coreFile = $definitionsDir . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . PC_CORE_FALLBACK . '.php';
    
    if (file_exists($coreFile)) {
        try {
            require_once $coreFile;
            $className = PC_BASE_NAMESPACE . $module . '\\' . PC_CORE_FALLBACK;
            $constants = loadConstantsFromClass($className);
        } catch (Throwable $e) {
            echo "Warning: Could not load core constants for {$module}: " . $e->getMessage() . "\n";
        }
    }
    
    return $constants;
}

function loadWebkernelConstants($definitionsDir): array
{
    $constants = [];
    $webkernelFile = $definitionsDir . DIRECTORY_SEPARATOR . PC_WEBKERNEL_FALLBACK . DIRECTORY_SEPARATOR . PC_CORE_FALLBACK . '.php';
    
    if (file_exists($webkernelFile)) {
        try {
            require_once $webkernelFile;
            $className = PC_BASE_NAMESPACE . PC_WEBKERNEL_FALLBACK . '\\' . PC_CORE_FALLBACK;
            $constants = loadConstantsFromClass($className);
        } catch (Throwable $e) {
            echo "Warning: Could not load Webkernel constants: " . $e->getMessage() . "\n";
        }
    }
    
    return $constants;
}

function getDatabaseConstants(): array
{
    return [];
}

function resolveConstantValue($constantName, $allConstants, $resolutionRules): mixed
{
    $candidates = [];
    
    foreach ($allConstants as $source => $constants) {
        if (isset($constants[$constantName])) {
            $candidates[$source] = $constants[$constantName];
        }
    }
    
    if (empty($candidates)) {
        return null;
    }
    
    foreach ($resolutionRules as $source) {
        if (isset($candidates[$source])) {
            return $candidates[$source];
        }
    }
    
    return array_values($candidates)[0];
}

function needsRegeneration($platformFilePath, $definitionsDir, $modulesDir): bool
{
    if (!file_exists($platformFilePath)) {
        return true;
    }
    
    $staticFileTime = filemtime($platformFilePath);
    $currentFileTime = filemtime(__FILE__);
    
    if ($currentFileTime > $staticFileTime) {
        return true;
    }
    
    if (is_dir($definitionsDir)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($definitionsDir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                if ($file->getMTime() > $staticFileTime) {
                    return true;
                }
            }
        }
    }
    
    if (is_dir($modulesDir)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($modulesDir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === 'Constants.php') {
                if ($file->getMTime() > $staticFileTime) {
                    return true;
                }
            }
        }
    }
    
    return false;
}

// ================================
// MAIN LOGIC
// ================================
$needsRegen = needsRegeneration($platformFilePath, $definitionsDir, $modulesDir);

if (!$needsRegen) {
    if (file_exists($platformFilePath)) {
        require_once $platformFilePath;
    }
    return;
}

echo "Generating platform constants\n";

$moduleVersions = getModuleVersions($modulesDir);
$availableVersions = getAvailableVersions($definitionsDir);
$allConstants = [];

$allConstants['database'] = getDatabaseConstants();

foreach ($moduleVersions as $module => $subModules) {
    foreach ($subModules as $subModule => $declaredVersion) {
        $moduleAvailableVersions = $availableVersions[$module][$subModule] ?? [];
        $physicalVersion = mapToPhysicalVersion($declaredVersion, $moduleAvailableVersions);
        
        if ($physicalVersion) {
            $constants = loadVersionedConstants($definitionsDir, $module, $subModule, $physicalVersion);
            $allConstants["versioned_{$module}_{$subModule}"] = $constants;
            $readableVersion = directoryToVersion($physicalVersion);
            echo "Loaded {$module}/{$subModule} v{$readableVersion} (" . count($constants) . " constants)\n";
        } else {
            echo "Warning: No compatible version found for {$module}/{$subModule} v{$declaredVersion}\n";
        }
    }
}

foreach ($availableVersions as $module => $subModules) {
    $coreConstants = loadCoreConstants($definitionsDir, $module);
    if (!empty($coreConstants)) {
        $allConstants["core_{$module}"] = $coreConstants;
        echo "Loaded {$module} core constants (" . count($coreConstants) . " constants)\n";
    }
}

$webkernelConstants = loadWebkernelConstants($definitionsDir);
if (!empty($webkernelConstants)) {
    $allConstants['webkernel'] = $webkernelConstants;
    echo "Loaded Webkernel constants (" . count($webkernelConstants) . " constants)\n";
}

$allConstantNames = [];
foreach ($allConstants as $constants) {
    $allConstantNames = array_merge($allConstantNames, array_keys($constants));
}
$allConstantNames = array_unique($allConstantNames);

$resolutionRules = array_keys($allConstants);

$finalConstants = [];
foreach ($allConstantNames as $constantName) {
    $value = resolveConstantValue($constantName, $allConstants, $resolutionRules);
    if ($value !== null) {
        $finalConstants[$constantName] = $value;
    }
}

$executionTime = round((microtime(true) - $startTime) * 1000, 2);
$generationDate = date('Y-m-d H:i:s');

$platformContent = "<?php\n";
$platformContent .= "/**\n";
$platformContent .= " * Auto-generated platform constants - DO NOT EDIT\n";
$platformContent .= " * Generated: {$generationDate}\n";
$platformContent .= " * Generation time: {$executionTime}ms\n";
$platformContent .= " * Total constants: " . count($finalConstants) . "\n";
$platformContent .= " */\n\n";

foreach ($finalConstants as $name => $value) {
    $exportedValue = validateAndExportValue($value);
    $platformContent .= "if (!defined('{$name}')) define('{$name}', {$exportedValue});\n";
    
    if (!defined($name)) {
        define($name, $value);
    }
}

if (file_put_contents($platformFilePath, $platformContent, LOCK_EX) !== false) {
    echo "Generated platform constants file: " . PC_PLATFORM_FILE . "\n";
} else {
    echo "ERROR: Failed to write platform constants file\n";
    exit(1);
}

if (file_exists($platformFilePath)) {
    require_once $platformFilePath;
}

echo "Platform constants generation completed in {$executionTime}ms\n";
echo "Resolved " . count($finalConstants) . " constants from " . count($allConstants) . " sources\n";

if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    try {
        // Suppression de l'appel à generate_platform_constants car la fonction n'existe pas
        exit(0);
    } catch (\Throwable $e) {
        file_put_contents('php://stderr', 'PlatformConstantsGenerator error: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}