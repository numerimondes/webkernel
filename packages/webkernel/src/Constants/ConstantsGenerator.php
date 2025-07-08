<?php
/**
 * Webkernel Constants Generator
 * Scans PHP files for constants and generates static files for performance
 * Compatible with Composer pre-autoload
 */

declare(strict_types=1);

// ================================
// INTERNAL CONFIGURATION CONSTANTS
// ================================

const WK_BASE_NAMESPACE = "Webkernel\\Constants\\Definitions\\";
const WK_SOURCES_DIR = "Definitions";
const WK_STATIC_DIR = "Static";
const WK_CONSTANTS_FILE = "GlobalConstants.php";
const WK_AUTOLOAD_FILE = "AutoloadStubs.php";
const WK_DEFAULT_DIRS = ["Webkernel"];
const WK_OPTIONAL_DIRS = ["Modules", "Branding"];
const WK_EXCLUDE_DIRS = ["Static"];
const WK_EXCLUDE_FILES = [];
const WK_CORE_PRIORITY = 0;
const WK_WEBKERNEL_PRIORITY = 1;
const WK_DEFAULT_PRIORITY = 2;
const WK_CONSTANTS_HASH_FILE = "constants_hash.txt";

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

function validateAndExportValue($value): string
{
    if (is_string($value)) {
        return "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], $value) . "'";
    } elseif (is_numeric($value)) {
        return (string) $value;
    } elseif (is_bool($value)) {
        return $value ? "true" : "false";
    } elseif (is_null($value)) {
        return "null";
    } elseif (is_array($value)) {
        return "'" .
            str_replace(
                "'",
                "\\'",
                json_encode(
                    $value,
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                )
            ) .
            "'";
    }
    return "null";
}

function isValidConstantName($name): bool
{
    return is_string($name) && preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name);
}

function isValidClassName($value): bool
{
    if (!is_string($value) || empty($value)) {
        return false;
    }
    return (bool) preg_match(
        '/^[A-Za-z_][A-Za-z0-9_]*(\\\[A-Za-z_][A-Za-z0-9_]*)*$/',
        $value
    );
}

function shouldIncludeDirectory($dirPath, $relativePath): bool
{
    $pathParts = explode(DIRECTORY_SEPARATOR, $relativePath);
    $firstLevel = $pathParts[0] ?? "";
    return in_array($firstLevel, WK_DEFAULT_DIRS) ||
        in_array($firstLevel, WK_OPTIONAL_DIRS);
}

function getFilePriority($filePath): int
{
    if (strpos($filePath, "Core") !== false) {
        return WK_CORE_PRIORITY;
    }
    if (strpos($filePath, "Webkernel") !== false) {
        return WK_WEBKERNEL_PRIORITY;
    }
    return WK_DEFAULT_PRIORITY;
}

function scanPhpFiles(
    $baseDir,
    $sourcesDir,
    $excludeDirs = [],
    $excludeFiles = []
): array {
    $orderedFiles = [];
    if (!is_dir($baseDir)) {
        return $orderedFiles;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $baseDir,
            RecursiveDirectoryIterator::SKIP_DOTS
        ),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== "php") {
            continue;
        }

        $filePath = $file->getRealPath();
        if ($filePath === __FILE__) {
            continue;
        }

        $relativePath = str_replace(
            $baseDir . DIRECTORY_SEPARATOR,
            "",
            $filePath
        );
        if (in_array($relativePath, $excludeFiles)) {
            continue;
        }

        $excluded = false;
        foreach ($excludeDirs as $excludeDir) {
            if (
                strpos($relativePath, $excludeDir . DIRECTORY_SEPARATOR) ===
                    0 ||
                strpos($relativePath, $excludeDir) === 0
            ) {
                $excluded = true;
                break;
            }
        }
        if ($excluded) {
            continue;
        }

        $sourceRelativePath = str_replace(
            $sourcesDir . DIRECTORY_SEPARATOR,
            "",
            $filePath
        );
        if (
            $sourceRelativePath === $filePath &&
            !shouldIncludeDirectory($filePath, $relativePath)
        ) {
            continue;
        }

        $priority = getFilePriority($filePath);
        $orderedFiles[] = [
            "priority" => $priority,
            "path" => $filePath,
            "relative" => $relativePath,
        ];
    }

    usort($orderedFiles, function ($a, $b) {
        if ($a["priority"] === $b["priority"]) {
            return strcmp($a["relative"], $b["relative"]);
        }
        return $a["priority"] <=> $b["priority"];
    });

    return $orderedFiles;
}

function getNewestSourceTime($baseDir, $sourcesDir, $excludeDirs): int
{
    $newestTime = 0;
    if (!is_dir($baseDir)) {
        return $newestTime;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $baseDir,
            RecursiveDirectoryIterator::SKIP_DOTS
        )
    );

    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== "php") {
            continue;
        }

        $filePath = $file->getRealPath();
        if ($filePath === __FILE__) {
            continue;
        }

        $relativePath = str_replace(
            $baseDir . DIRECTORY_SEPARATOR,
            "",
            $filePath
        );
        $excluded = false;
        foreach ($excludeDirs as $excludeDir) {
            if (
                strpos($relativePath, $excludeDir . DIRECTORY_SEPARATOR) ===
                    0 ||
                strpos($relativePath, $excludeDir) === 0
            ) {
                $excluded = true;
                break;
            }
        }
        if ($excluded) {
            continue;
        }

        $sourceRelativePath = str_replace(
            $sourcesDir . DIRECTORY_SEPARATOR,
            "",
            $filePath
        );
        if (
            $sourceRelativePath === $filePath &&
            !shouldIncludeDirectory($filePath, $relativePath)
        ) {
            continue;
        }

        $fileTime = $file->getMTime();
        if ($fileTime > $newestTime) {
            $newestTime = $fileTime;
        }
    }

    return $newestTime;
}

function calculateConstantsHash($baseDir, $sourcesDir, $excludeDirs): string
{
    $hashData = [];
    
    if (!is_dir($baseDir)) {
        return md5('no_dir');
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $baseDir,
            RecursiveDirectoryIterator::SKIP_DOTS
        )
    );

    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== "php") {
            continue;
        }

        $filePath = $file->getRealPath();
        if ($filePath === __FILE__) {
            continue;
        }

        $relativePath = str_replace(
            $baseDir . DIRECTORY_SEPARATOR,
            "",
            $filePath
        );
        $excluded = false;
        foreach ($excludeDirs as $excludeDir) {
            if (
                strpos($relativePath, $excludeDir . DIRECTORY_SEPARATOR) ===
                    0 ||
                strpos($relativePath, $excludeDir) === 0
            ) {
                $excluded = true;
                break;
            }
        }
        if ($excluded) {
            continue;
        }

        $sourceRelativePath = str_replace(
            $sourcesDir . DIRECTORY_SEPARATOR,
            "",
            $filePath
        );
        if (
            $sourceRelativePath === $filePath &&
            !shouldIncludeDirectory($filePath, $relativePath)
        ) {
            continue;
        }

        // Lire le contenu du fichier et extraire les constantes
        $content = file_get_contents($filePath);
        if ($content === false) {
            continue;
        }

        // Extraire les constantes avec leurs valeurs
        if (preg_match_all('/const\s+([A-Z_][A-Z0-9_]*)\s*=\s*([^;]+);/m', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $constantName = $match[1];
                $constantValue = trim($match[2]);
                $hashData[$relativePath][$constantName] = $constantValue;
            }
        }
    }

    // Trier pour avoir un hash cohérent
    ksort($hashData);
    foreach ($hashData as &$constants) {
        ksort($constants);
    }

    return md5(serialize($hashData));
}

function getStoredConstantsHash($staticDir): ?string
{
    $hashFile = $staticDir . DIRECTORY_SEPARATOR . WK_CONSTANTS_HASH_FILE;
    if (!file_exists($hashFile)) {
        return null;
    }
    
    $hash = file_get_contents($hashFile);
    return $hash ? trim($hash) : null;
}

function storeConstantsHash($staticDir, string $hash): void
{
    $hashFile = $staticDir . DIRECTORY_SEPARATOR . WK_CONSTANTS_HASH_FILE;
    file_put_contents($hashFile, $hash);
}

function needsRegeneration(
    $constantsFilePath,
    $autoloadStubsPath,
    $baseFile,
    $baseDir,
    $sourcesDir,
    $excludeDirs
): bool {
    if (!file_exists($constantsFilePath) || !file_exists($autoloadStubsPath)) {
        return true;
    }

    $constantsFileTime = filemtime($constantsFilePath);
    $autoloadFileTime = filemtime($autoloadStubsPath);
    $staticFileTime = min($constantsFileTime, $autoloadFileTime);
    $currentFileTime = filemtime($baseFile);

    if ($currentFileTime > $staticFileTime) {
        return true;
    }

    $newestSourceTime = getNewestSourceTime(
        $baseDir,
        $sourcesDir,
        $excludeDirs
    );
    if ($newestSourceTime > $staticFileTime) {
        return true;
    }

    // Vérifier si les valeurs des constantes ont changé
    $staticDir = dirname($constantsFilePath);
    $currentHash = calculateConstantsHash($baseDir, $sourcesDir, $excludeDirs);
    $storedHash = getStoredConstantsHash($staticDir);
    
    if ($storedHash !== $currentHash) {
        return true;
    }

    return false;
}

function getClassNameFromPath($filePath, $sourcesDir): array
{
    $relativePath = str_replace(
        $sourcesDir . DIRECTORY_SEPARATOR,
        "",
        $filePath
    );
    $namespace =
        WK_BASE_NAMESPACE .
        str_replace(["/", "\\", ".php"], ["\\", "\\", ""], $relativePath);
    $parts = explode("\\", $namespace);
    $className = array_pop($parts);
    $fullNamespace = implode("\\", $parts);

    return [
        "namespace" => $fullNamespace,
        "class" => $className,
        "full" => $namespace,
    ];
}

// ================================
// MAIN LOGIC
// ================================

$needsRegen = needsRegeneration(
    $constantsFilePath,
    $autoloadStubsPath,
    __FILE__,
    $baseDir,
    $sourcesDir,
    WK_EXCLUDE_DIRS
);

if (!$needsRegen) {
    if (file_exists($constantsFilePath)) {
        require_once $constantsFilePath;
    }
    if (file_exists($autoloadStubsPath)) {
        require_once $autoloadStubsPath;
    }
    return;
}

$orderedFiles = scanPhpFiles(
    $baseDir,
    $sourcesDir,
    WK_EXCLUDE_DIRS,
    WK_EXCLUDE_FILES
);
echo "Regenerating constants - found " .
    count($orderedFiles) .
    " PHP files to process\n";

foreach ($orderedFiles as $entry) {
    $filePath = $entry["path"];
    if (strpos($filePath, $sourcesDir) !== false) {
        try {
            require_once $filePath;
        } catch (Throwable $e) {
            echo "Warning: Could not load {$entry["relative"]}: " .
                $e->getMessage() .
                "\n";
        }
    }
}

echo "Scanning for constants\n";

// ================================
// CONSTANTS EXTRACTION
// ================================

$constants = [];
$processedClasses = [];

foreach ($orderedFiles as $entry) {
    $path = $entry["path"];
    if (strpos($path, $sourcesDir) === false) {
        continue;
    }

    $classInfo = getClassNameFromPath($path, $sourcesDir);
    $className = $classInfo["full"];

    if (!class_exists($className) || in_array($className, $processedClasses)) {
        continue;
    }

    $processedClasses[] = $className;

    try {
        $refClass = new ReflectionClass($className);
        $classConstants = $refClass->getConstants(
            ReflectionClassConstant::IS_PUBLIC
        );

        foreach ($classConstants as $name => $value) {
            if (isValidConstantName($name)) {
                $constants[$name] = [
                    "value" => $value,
                    "class" => $className,
                    "file" => $path,
                    "namespace" => $classInfo["namespace"],
                    "class_name" => $classInfo["class"],
                ];
            }
        }
    } catch (Throwable $e) {
        echo "Warning: Could not reflect class {$className}: " .
            $e->getMessage() .
            "\n";
        continue;
    }
}

echo "Found " .
    count($constants) .
    " constants from " .
    count($processedClasses) .
    " classes\n";

// ================================
// FILE GENERATION
// ================================

$executionTime = round((microtime(true) - $startTime) * 1000, 2);
$generationDate = date("Y-m-d H:i:s");

$constantsByClass = [];
$classAliases = [];
$classEscaped = [];

foreach ($constants as $name => $info) {
    $constantsByClass[$info["class"]][] = [
        "name" => $name,
        "value" => $info["value"],
        "file" => $info["file"],
    ];

    if (
        str_ends_with($name, "__CLASS_ALIAS_SIMPLE") &&
        isValidClassName($info["value"])
    ) {
        $aliasName = str_replace("__CLASS_ALIAS_SIMPLE", "", $name);
        $classAliases[$aliasName] = $info["value"];
    } elseif (
        str_ends_with($name, "__CLASS_ESCAPED") &&
        isValidClassName($info["value"])
    ) {
        $classEscaped[$name] = $info["value"];
    }
}

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
    $relativeFile = str_replace(
        $baseDir . DIRECTORY_SEPARATOR,
        "",
        $firstConstant["file"]
    );
    $constantsContent .= "// {$className}\n";
    $constantsContent .= "// Source: {$relativeFile}\n";

    foreach ($classConstants as $constantInfo) {
        $name = $constantInfo["name"];
        $value = $constantInfo["value"];
        $exportedValue = validateAndExportValue($value);
        $constantsContent .= "if (!defined('{$name}')) define('{$name}', {$exportedValue});\n";
        if (!defined($name)) {
            define($name, $value);
        }
    }
    $constantsContent .= "\n";
}

$autoloadContent = "<?php\n";
$autoloadContent .= "/**\n";
$autoloadContent .= " * Auto-generated autoload stubs - DO NOT EDIT\n";
$autoloadContent .= " * Generated: {$generationDate}\n";
$autoloadContent .= " * Total aliases: " . count($classAliases) . "\n";
$autoloadContent .= " * Total escaped: " . count($classEscaped) . "\n";
$autoloadContent .= " */\n\n";

if (!empty($classAliases)) {
    $autoloadContent .= "// Class aliases for *_CLASS_ALIAS_SIMPLE constants\n";
    foreach ($classAliases as $aliasName => $className) {
        $autoloadContent .= "if (!class_exists('{$aliasName}') && !interface_exists('{$aliasName}') && !trait_exists('{$aliasName}')) {\n";
        $autoloadContent .= "    if (class_exists('{$className}') || interface_exists('{$className}') || trait_exists('{$className}')) {\n";
        $autoloadContent .= "        class_alias('{$className}', '{$aliasName}');\n";
        $autoloadContent .= "    }\n";
        $autoloadContent .= "}\n";

        if (
            !class_exists($aliasName) &&
            !interface_exists($aliasName) &&
            !trait_exists($aliasName)
        ) {
            if (
                class_exists($className) ||
                interface_exists($className) ||
                trait_exists($className)
            ) {
                try {
                    class_alias($className, $aliasName);
                    echo "Created alias: {$aliasName} -> {$className}\n";
                } catch (Throwable $e) {
                    echo "Failed to create alias {$aliasName} for {$className}: " .
                        $e->getMessage() .
                        "\n";
                }
            } else {
                echo "Deferred alias (target not loaded): {$aliasName} -> {$className}\n";
            }
        }
    }
    $autoloadContent .= "\n";
}

if (!empty($classEscaped)) {
    $autoloadContent .=
        "// Escaped class references for *_CLASS_ESCAPED constants\n";
    foreach ($classEscaped as $name => $value) {
        $autoloadContent .=
            "// {$name} = " . validateAndExportValue($value) . "\n";
    }
    $autoloadContent .= "\n";
}

// ================================
// FILE WRITING
// ================================

if (
    file_put_contents($constantsFilePath, $constantsContent, LOCK_EX) !== false
) {
    echo "Generated constants file: " . WK_CONSTANTS_FILE . "\n";
} else {
    echo "ERROR: Failed to write constants file\n";
    exit(1);
}

if (
    file_put_contents($autoloadStubsPath, $autoloadContent, LOCK_EX) !== false
) {
    echo "Generated autoload-stubs file: " . WK_AUTOLOAD_FILE . "\n";
} else {
    echo "ERROR: Failed to write autoload-stubs file\n";
    exit(1);
}

if (file_exists($constantsFilePath)) {
    require_once $constantsFilePath;
}
if (file_exists($autoloadStubsPath)) {
    require_once $autoloadStubsPath;
}

// Sauvegarder le hash des constantes pour la prochaine vérification
$currentHash = calculateConstantsHash($baseDir, $sourcesDir, WK_EXCLUDE_DIRS);
storeConstantsHash($staticDir, $currentHash);

echo "Constants generation completed in {$executionTime}ms\n";
echo "Processed " .
    count($processedClasses) .
    " classes with " .
    count($constants) .
    " constants\n";

if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    try {
        // Replace this with the actual generation logic for ConstantsGenerator
        // Suppression de l'appel à generate_constants car la fonction n'existe pas
        exit(0);
    } catch (\Throwable $e) {
        file_put_contents('php://stderr', 'ConstantsGenerator error: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}
