<?php
$startTime = microtime(true);
$helpersDirs = [__DIR__];
$excludeDirs = [];
$excludeFiles = [];
$orderedFiles = [];

foreach ($helpersDirs as $helpersDir) {
    if (!is_dir($helpersDir)) continue;
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($helpersDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') continue;
        
        $filePath = $file->getRealPath();
        if ($filePath === __FILE__) continue;
        
        $relativePath = str_replace($helpersDir . DIRECTORY_SEPARATOR, '', $filePath);
        if (in_array($relativePath, $excludeFiles)) continue;
        
        $excluded = false;
        foreach ($excludeDirs as $excludeDir) {
            if (strpos($relativePath, $excludeDir . DIRECTORY_SEPARATOR) === 0) {
                $excluded = true;
                break;
            }
        }
        if ($excluded) continue;
        
        $priority = (strpos($filePath, 'Core') !== false) ? 0 : 1;
        $orderedFiles[] = ['priority' => $priority, 'path' => $filePath];
    }
}

usort($orderedFiles, fn($a, $b) => $a['priority'] <=> $b['priority']);

echo "Loading " . count($orderedFiles) . " PHP files\n";

foreach ($orderedFiles as $entry) {
    require_once $entry['path'];
}

$baseNamespace = 'Webkernel\\PlatformConfig\\ConfigFiles\\';
$baseDir = __DIR__;
$staticGeneratedDir = $baseDir . '/WebkernelStaticGeneratedFiles';
$constantsFilePath = $staticGeneratedDir . '/constants-globals.php';
$autoloadStubsPath = $staticGeneratedDir . '/autoload-stubs.php';

if (!is_dir($staticGeneratedDir)) {
    mkdir($staticGeneratedDir, 0755, true);
}

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

function isValidConstantName($name) {
    return is_string($name) && preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name);
}

function isValidClassName($value) {
    if (!is_string($value) || empty($value)) {
        return false;
    }
    
    if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\\\[A-Za-z_][A-Za-z0-9_]*)*$/', $value)) {
        return false;
    }
    
    return class_exists($value) || interface_exists($value) || trait_exists($value);
}

$needsRegeneration = false;
if (!file_exists($constantsFilePath)) {
    $needsRegeneration = true;
    echo "Constants file missing - regenerating\n";
} else {
    $staticFileTime = filemtime($constantsFilePath);
    $currentFileTime = filemtime(__FILE__);
    if ($currentFileTime > $staticFileTime) {
        $needsRegeneration = true;
        echo "Main file newer - regenerating\n";
    }
    
    if (!$needsRegeneration) {
        $configIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
        foreach ($configIterator as $configFile) {
            if ($configFile->isFile() && $configFile->getExtension() === 'php' && $configFile->getRealPath() !== __FILE__) {
                if ($configFile->getMTime() > $staticFileTime) {
                    $needsRegeneration = true;
                    echo "Config file newer - regenerating\n";
                    break;
                }
            }
        }
    }
}

if (!$needsRegeneration) {
    if (file_exists($constantsFilePath)) {
        require_once $constantsFilePath;
    }
    if (file_exists($autoloadStubsPath)) {
        require_once $autoloadStubsPath;
    }
    return;
}

echo "Scanning for constants\n";

$constants = [];
$processedClasses = [];

foreach ($orderedFiles as $entry) {
    $path = $entry['path'];
    $relativePath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $path);
    $className = $baseNamespace . str_replace(['/', '\\', '.php'], ['\\', '\\', ''], $relativePath);
    
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
                    'file' => $path
                ];
            }
        }
    } catch (Throwable $e) {
        continue;
    }
}

echo "Found " . count($constants) . " constants\n";

$executionTime = round((microtime(true) - $startTime) * 1000, 2);
$generationDate = date('Y-m-d H:i:s');

// Generate constants file
$constantsContent = "<?php\n";
$constantsContent .= "// Auto-generated constants - DO NOT EDIT\n";
$constantsContent .= "// Generated: {$generationDate}\n";
$constantsContent .= "// Time: {$executionTime}ms\n\n";

$constantsByClass = [];
$classAliases = [];
$classEscaped = [];

foreach ($constants as $name => $info) {
    $constantsByClass[$info['class']][] = [
        'name' => $name,
        'value' => $info['value'],
        'file' => $info['file']
    ];
    
    if (str_ends_with($name, '_CLASS_ALIAS_SIMPLE') && isValidClassName($info['value'])) {
        $aliasName = str_replace('_CLASS_ALIAS_SIMPLE', '', $name);
        $classAliases[$aliasName] = $info['value'];
    } elseif (str_ends_with($name, '_CLASS_ESCAPED')) {
        $classEscaped[$name] = $info['value'];
    }
}

foreach ($constantsByClass as $className => $classConstants) {
    $firstConstant = $classConstants[0];
    $relativeFile = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $firstConstant['file']);
    
    $constantsContent .= "// {$className} - {$relativeFile}\n";
    
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
$autoloadContent .= "// Auto-generated autoload stubs - DO NOT EDIT\n";
$autoloadContent .= "// Generated: {$generationDate}\n\n";

if (!empty($classAliases)) {
    $autoloadContent .= "// Class aliases for *_CLASS_ALIAS_SIMPLE\n";
    
    foreach ($classAliases as $aliasName => $className) {
        $autoloadContent .= "if (!class_exists('{$aliasName}') && !interface_exists('{$aliasName}') && !trait_exists('{$aliasName}')) {\n";
        $autoloadContent .= "    if (class_exists('{$className}') || interface_exists('{$className}') || trait_exists('{$className}')) {\n";
        $autoloadContent .= "        class_alias('{$className}', '{$aliasName}');\n";
        $autoloadContent .= "    }\n";
        $autoloadContent .= "}\n";
        
        if (!class_exists($aliasName) && !interface_exists($aliasName) && !trait_exists($aliasName)) {
            if (class_exists($className) || interface_exists($className) || trait_exists($className)) {
                try {
                    class_alias($className, $aliasName);
                    echo "Created alias: {$aliasName} -> {$className}\n";
                } catch (Throwable $e) {
                    echo "Failed to create alias {$aliasName} for {$className}: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    $autoloadContent .= "\n";
}

if (!empty($classEscaped)) {
    $autoloadContent .= "// Escaped class references for *_CLASS_ESCAPED\n";
    foreach ($classEscaped as $name => $value) {
        $autoloadContent .= "// {$name} = {$value}\n";
    }
    $autoloadContent .= "\n";
}

// Write constants file
if (file_put_contents($constantsFilePath, $constantsContent, LOCK_EX) !== false) {
    echo "Generated constants file\n";
} else {
    echo "Failed to write constants file\n";
}

// Write autoload-stubs file
if (file_put_contents($autoloadStubsPath, $autoloadContent, LOCK_EX) !== false) {
    echo "Generated autoload-stubs file\n";
} else {
    echo "Failed to write autoload-stubs file\n";
}

if (file_exists($constantsFilePath)) {
    require_once $constantsFilePath;
}
if (file_exists($autoloadStubsPath)) {
    require_once $autoloadStubsPath;
}