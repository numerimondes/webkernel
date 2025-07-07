<?php
/**
 * Webkernel Constants Generator
 * Scans PHP files for constants and generates static files for performance
 * Compatible with Composer pre-autoload
 */

// ================================
// INTERNAL CONFIGURATION CONSTANTS
// ================================

/** @var string Base namespace for configuration classes */
const WK_BASE_NAMESPACE = 'Webkernel\\PlatformConfig\\Constants\\Sources\\';

/** @var string Base directory for scanning (relative to this file) */
const WK_SOURCES_DIR = 'Sources';

/** @var string Static files output directory (relative to this file) */
const WK_STATIC_DIR = 'Static';

/** @var string Generated constants file name */
const WK_CONSTANTS_FILE = 'GlobalConstants.php';

/** @var string Generated autoload stubs file name */
const WK_AUTOLOAD_FILE = 'AutoloadStubs.php';

/** @var array Always included directories (relative to Sources) */
const WK_DEFAULT_DIRS = ['Webkernel'];

/** @var array Optional directories to include (relative to Sources) */
const WK_OPTIONAL_DIRS = ['Modules', 'Branding'];

/** @var array Directories to exclude from scanning */
const WK_EXCLUDE_DIRS = ['Static'];

/** @var array Files to exclude from scanning */
const WK_EXCLUDE_FILES = [];

/** @var int Priority for Core files (lower = higher priority) */
const WK_CORE_PRIORITY = 0;

/** @var int Priority for Webkernel files */
const WK_WEBKERNEL_PRIORITY = 1;

/** @var int Priority for other files */
const WK_DEFAULT_PRIORITY = 2;

// ================================
// MAIN EXECUTION
// ================================

$startTime = microtime(true);
$baseDir = __DIR__;
$sourcesDir = $baseDir . DIRECTORY_SEPARATOR . WK_SOURCES_DIR;
$staticDir = $baseDir . DIRECTORY_SEPARATOR . WK_STATIC_DIR;
$constantsFilePath = $staticDir . DIRECTORY_SEPARATOR . WK_CONSTANTS_FILE;
$autoloadStubsPath = $staticDir . DIRECTORY_SEPARATOR . WK_AUTOLOAD_FILE;

// Ensure static directory exists
if (!is_dir($staticDir)) {
    mkdir($staticDir, 0755, true);
}

// ================================
// UTILITY FUNCTIONS
// ================================

/**
 * Validates and exports a value for PHP code generation
 */
function validateAndExportValue($value) {
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
    } else {
        return 'null';
    }
}

/**
 * Checks if a constant name is valid
 */
function isValidConstantName($name) {
    return is_string($name) && preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name);
}

/**
 * Checks if a class name is valid (format only, not existence)
 */
function isValidClassName($value) {
    if (!is_string($value) || empty($value)) {
        return false;
    }
    
    // Check if it's a valid PHP class name format
    return preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\\\[A-Za-z_][A-Za-z0-9_]*)*$/', $value);
}

/**
 * Custom condition checker for directory scanning
 * You can easily modify this function to add new conditions
 */
function shouldIncludeDirectory($dirPath, $relativePath) {
    $pathParts = explode(DIRECTORY_SEPARATOR, $relativePath);
    $firstLevel = isset($pathParts[0]) ? $pathParts[0] : '';
    
    // Always include default directories
    if (in_array($firstLevel, WK_DEFAULT_DIRS)) {
        return true;
    }
    
    // Check optional directories
    if (in_array($firstLevel, WK_OPTIONAL_DIRS)) {
        return true;
    }
    
    // Add custom conditions here
    // Example: include directories containing "Custom" in name
    // if (strpos($relativePath, 'Custom') !== false) {
    //     return true;
    // }
    
    return false;
}

/**
 * Determines file priority based on path
 */
function getFilePriority($filePath) {
    if (strpos($filePath, 'Core') !== false) {
        return WK_CORE_PRIORITY;
    }
    
    if (strpos($filePath, 'Webkernel') !== false) {
        return WK_WEBKERNEL_PRIORITY;
    }
    
    return WK_DEFAULT_PRIORITY;
}

/**
 * Scans directory for PHP files and returns ordered list
 */
function scanPhpFiles($baseDir, $sourcesDir, $excludeDirs = [], $excludeFiles = []) {
    $orderedFiles = [];
    
    if (!is_dir($baseDir)) {
        return $orderedFiles;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }
        
        $filePath = $file->getRealPath();
        
        // Skip this file itself
        if ($filePath === __FILE__) {
            continue;
        }
        
        $relativePath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $filePath);
        
        // Check if file is excluded
        if (in_array($relativePath, $excludeFiles)) {
            continue;
        }
        
        // Check if directory is excluded
        $excluded = false;
        foreach ($excludeDirs as $excludeDir) {
            if (strpos($relativePath, $excludeDir . DIRECTORY_SEPARATOR) === 0 || 
                strpos($relativePath, $excludeDir) === 0) {
                $excluded = true;
                break;
            }
        }
        if ($excluded) {
            continue;
        }
        
        // Check if file is in Sources directory
        $sourceRelativePath = str_replace($sourcesDir . DIRECTORY_SEPARATOR, '', $filePath);
        if ($sourceRelativePath === $filePath) {
            // File is not in Sources directory, apply custom condition
            if (!shouldIncludeDirectory($filePath, $relativePath)) {
                continue;
            }
        }
        
        // Set priority
        $priority = getFilePriority($filePath);
        $orderedFiles[] = [
            'priority' => $priority, 
            'path' => $filePath,
            'relative' => $relativePath
        ];
    }
    
    // Sort by priority
    usort($orderedFiles, function($a, $b) {
        if ($a['priority'] === $b['priority']) {
            return strcmp($a['relative'], $b['relative']);
        }
        return $a['priority'] <=> $b['priority'];
    });
    
    return $orderedFiles;
}

/**
 * Checks if regeneration is needed based on file modification times
 */
function needsRegeneration($constantsFilePath, $baseFile, $baseDir, $excludeDirs) {
    if (!file_exists($constantsFilePath)) {
        echo "Constants file missing - regenerating\n";
        return true;
    }
    
    $staticFileTime = filemtime($constantsFilePath);
    $currentFileTime = filemtime($baseFile);
    
    if ($currentFileTime > $staticFileTime) {
        echo "Main file newer - regenerating\n";
        return true;
    }
    
    // Check if any source file is newer
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }
        
        $filePath = $file->getRealPath();
        if ($filePath === $baseFile) {
            continue;
        }
        
        $relativePath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $filePath);
        
        // Skip excluded directories
        $excluded = false;
        foreach ($excludeDirs as $excludeDir) {
            if (strpos($relativePath, $excludeDir . DIRECTORY_SEPARATOR) === 0 || 
                strpos($relativePath, $excludeDir) === 0) {
                $excluded = true;
                break;
            }
        }
        if ($excluded) {
            continue;
        }
        
        if ($file->getMTime() > $staticFileTime) {
            echo "Source file newer: {$relativePath} - regenerating\n";
            return true;
        }
    }
    
    return false;
}

/**
 * Converts file path to namespace and class name
 */
function getClassNameFromPath($filePath, $sourcesDir) {
    $relativePath = str_replace($sourcesDir . DIRECTORY_SEPARATOR, '', $filePath);
    $namespace = WK_BASE_NAMESPACE . str_replace(['/', '\\', '.php'], ['\\', '\\', ''], $relativePath);
    
    // Extract class name from namespace
    $parts = explode('\\', $namespace);
    $className = array_pop($parts);
    $fullNamespace = implode('\\', $parts);
    
    return ['namespace' => $fullNamespace, 'class' => $className, 'full' => $namespace];
}

// ================================
// MAIN LOGIC
// ================================

// First, scan all PHP files
$orderedFiles = scanPhpFiles($baseDir, $sourcesDir, WK_EXCLUDE_DIRS, WK_EXCLUDE_FILES);

echo "Found " . count($orderedFiles) . " PHP files to process\n";

// Load all source files
foreach ($orderedFiles as $entry) {
    $filePath = $entry['path'];
    if (strpos($filePath, $sourcesDir) !== false) {
        try {
            require_once $filePath;
        } catch (Throwable $e) {
            echo "Warning: Could not load {$entry['relative']}: " . $e->getMessage() . "\n";
        }
    }
}

// Check if regeneration is needed
if (!needsRegeneration($constantsFilePath, __FILE__, $baseDir, WK_EXCLUDE_DIRS)) {
    if (file_exists($constantsFilePath)) {
        require_once $constantsFilePath;
    }
    if (file_exists($autoloadStubsPath)) {
        require_once $autoloadStubsPath;
    }
    return;
}

echo "Scanning for constants\n";

// ================================
// CONSTANTS EXTRACTION
// ================================

$constants = [];
$processedClasses = [];

foreach ($orderedFiles as $entry) {
    $path = $entry['path'];
    
    // Only process files in Sources directory
    if (strpos($path, $sourcesDir) === false) {
        continue;
    }
    
    $classInfo = getClassNameFromPath($path, $sourcesDir);
    $className = $classInfo['full'];
    
    if (!class_exists($className) || in_array($className, $processedClasses)) {
        continue;
    }
    
    $processedClasses[] = $className;
    
    try {
        $refClass = new ReflectionClass($className);
        $classConstants = $refClass->getConstants(ReflectionClassConstant::IS_PUBLIC);
        
        foreach ($classConstants as $name => $value) {
            if (isValidConstantName($name)) {
                $constants[$name] = [
                    'value' => $value,
                    'class' => $className,
                    'file' => $path,
                    'namespace' => $classInfo['namespace'],
                    'class_name' => $classInfo['class']
                ];
            }
        }
    } catch (Throwable $e) {
        echo "Warning: Could not reflect class {$className}: " . $e->getMessage() . "\n";
        continue;
    }
}

echo "Found " . count($constants) . " constants from " . count($processedClasses) . " classes\n";

// ================================
// FILE GENERATION
// ================================

$executionTime = round((microtime(true) - $startTime) * 1000, 2);
$generationDate = date('Y-m-d H:i:s');

// Organize constants by class
$constantsByClass = [];
$classAliases = [];
$classEscaped = [];

foreach ($constants as $name => $info) {
    $constantsByClass[$info['class']][] = [
        'name' => $name,
        'value' => $info['value'],
        'file' => $info['file']
    ];
    
    // Handle CLASS_ALIAS_SIMPLE constants
    if (str_ends_with($name, '_CLASS_ALIAS_SIMPLE') && isValidClassName($info['value'])) {
        $aliasName = str_replace('_CLASS_ALIAS_SIMPLE', '', $name);
        $classAliases[$aliasName] = $info['value'];
    } 
    // Handle CLASS_ESCAPED constants  
    elseif (str_ends_with($name, '_CLASS_ESCAPED') && isValidClassName($info['value'])) {
        $classEscaped[$name] = $info['value'];
    }
}

// Generate constants file
$constantsContent = "<?php\n";
$constantsContent .= "/**\n";
$constantsContent .= " * Auto-generated constants - DO NOT EDIT\n";
$constantsContent .= " * Generated: {$generationDate}\n";
$constantsContent .= " * Generation time: {$executionTime}ms\n";
$constantsContent .= " * Total constants: " . count($constants) . "\n";
$constantsContent .= " * Total classes: " . count($processedClasses) . "\n";
$constantsContent .= " */\n\n";

foreach ($constantsByClass as $className => $classConstants) {
    $firstConstant = $classConstants[0];
    $relativeFile = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $firstConstant['file']);
    
    $constantsContent .= "// {$className}\n";
    $constantsContent .= "// Source: {$relativeFile}\n";
    
    foreach ($classConstants as $constantInfo) {
        $name = $constantInfo['name'];
        $value = $constantInfo['value'];
        $exportedValue = validateAndExportValue($value);
        
        $constantsContent .= "if (!defined('{$name}')) define('{$name}', {$exportedValue});\n";
        
        if (!defined($name)) {
            define($name, $value);
        }
    }
    $constantsContent .= "\n";
}

// Generate autoload-stubs file
$autoloadContent = "<?php\n";
$autoloadContent .= "/**\n";
$autoloadContent .= " * Auto-generated autoload stubs - DO NOT EDIT\n";
$autoloadContent .= " * Generated: {$generationDate}\n";
$autoloadContent .= " * Total aliases: " . count($classAliases) . "\n";
$autoloadContent .= " */\n\n";

if (!empty($classAliases)) {
    $autoloadContent .= "// Class aliases for *_CLASS_ALIAS_SIMPLE constants\n";
    
    foreach ($classAliases as $aliasName => $className) {
        $autoloadContent .= "if (!class_exists('{$aliasName}') && !interface_exists('{$aliasName}') && !trait_exists('{$aliasName}')) {\n";
        $autoloadContent .= "    if (class_exists('{$className}') || interface_exists('{$className}') || trait_exists('{$className}')) {\n";
        $autoloadContent .= "        class_alias('{$className}', '{$aliasName}');\n";
        $autoloadContent .= "    }\n";
        $autoloadContent .= "}\n";
        
        // Try to create alias immediately if possible (but don't fail if target doesn't exist)
        if (!class_exists($aliasName) && !interface_exists($aliasName) && !trait_exists($aliasName)) {
            if (class_exists($className) || interface_exists($className) || trait_exists($className)) {
                try {
                    class_alias($className, $aliasName);
                    echo "Created alias: {$aliasName} -> {$className}\n";
                } catch (Throwable $e) {
                    echo "Failed to create alias {$aliasName} for {$className}: " . $e->getMessage() . "\n";
                }
            } else {
                echo "Deferred alias (target not loaded): {$aliasName} -> {$className}\n";
            }
        }
    }
    $autoloadContent .= "\n";
}

if (!empty($classEscaped)) {
    $autoloadContent .= "// Escaped class references for *_CLASS_ESCAPED constants\n";
    foreach ($classEscaped as $name => $value) {
        $autoloadContent .= "// {$name} = " . validateAndExportValue($value) . "\n";
    }
    $autoloadContent .= "\n";
}

// ================================
// FILE WRITING
// ================================

// Write constants file
if (file_put_contents($constantsFilePath, $constantsContent, LOCK_EX) !== false) {
    echo "Generated constants file: " . WK_CONSTANTS_FILE . "\n";
} else {
    echo "ERROR: Failed to write constants file\n";
    exit(1);
}

// Write autoload-stubs file
if (file_put_contents($autoloadStubsPath, $autoloadContent, LOCK_EX) !== false) {
    echo "Generated autoload-stubs file: " . WK_AUTOLOAD_FILE . "\n";
} else {
    echo "ERROR: Failed to write autoload-stubs file\n";
    exit(1);
}

// Load the generated files
if (file_exists($constantsFilePath)) {
    require_once $constantsFilePath;
}
if (file_exists($autoloadStubsPath)) {
    require_once $autoloadStubsPath;
}

echo "Constants generation completed in {$executionTime}ms\n";
echo "Processed " . count($processedClasses) . " classes with " . count($constants) . " constants\n";