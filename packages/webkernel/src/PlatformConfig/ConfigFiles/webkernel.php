<?php
/**
 * Webkernel Platform Configuration Constants Loader
 *
 * > packages/webkernel/src/PlatformConfig/ConfigFiles/webkernel.php
 *
 * This system module performs the following operations:
 * - Dynamically loads all configuration and helper files from specified directories
 * - Extracts public constants from configuration classes and defines them globally
 * - Generates a static constants file for IDE intellisense and static analysis tools
 * - Ensures optimal performance for real-time constant resolution
 *
 * @author    El Moumen Yassine - Numerimondes
 * @contact   <yassine@numerimondes.com>
 * @website   www.numerimondes.com
 * @license   Proprietary - All rights reserved
 */

$startTime = microtime(true);

$helpersDirs = [
    __DIR__,
];
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

foreach ($orderedFiles as $entry) {
    require_once $entry['path'];
}

$baseNamespace = 'Webkernel\\PlatformConfig\\ConfigFiles\\';
$baseDir = __DIR__;
$staticFilePath = $baseDir . '/constants-globals.php';

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

$needsRegeneration = false;
if (!file_exists($staticFilePath)) {
    $needsRegeneration = true;
} else {
    $staticFileTime = filemtime($staticFilePath);
    $currentFileTime = filemtime(__FILE__);
    if ($currentFileTime > $staticFileTime) {
        $needsRegeneration = true;
    }

    if (!$needsRegeneration) {
        $configIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
        foreach ($configIterator as $configFile) {
            if ($configFile->isFile() && $configFile->getExtension() === 'php' && $configFile->getRealPath() !== __FILE__) {
                if ($configFile->getMTime() > $staticFileTime) {
                    $needsRegeneration = true;
                    break;
                }
            }
        }
    }
}

if (!$needsRegeneration) {
    require_once $staticFilePath;
    return;
}

$executionTime = round((microtime(true) - $startTime) * 1000, 2);
$generationDate = date('Y-m-d H:i:s');

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
                    'class' => $className
                ];
            }
        }
    } catch (Throwable $e) {

        continue;
    }
}

$content = "<?php\n";
$content .= "/*\n";
$content .= "|--------------------------------------------------------------------------\n";
$content .= "| Webkernel Global Constants Registry\n";
$content .= "|--------------------------------------------------------------------------\n";
$content .= "| \n";
$content .= "| This file was auto-generated by the Webkernel Configuration System\n|\n";
$content .= "| Generated on    : {$generationDate}\n";
$content .= "| Generation time : {$executionTime}ms\n";
$content .= "| \n";
$content .= "| Author:  El Moumen Yassine - Numerimondes\n";
$content .= "| Contact: <yassine@numerimondes.com>\n";
$content .= "| Website: www.numerimondes.com\n";
$content .= "|\n";
$content .= "*/\n\n";

$constantsByClass = [];
foreach ($constants as $name => $info) {
    $constantsByClass[$info['class']][$name] = $info['value'];
}

foreach ($constantsByClass as $className => $classConstants) {
    $content .= "// Constants from: {$className}\n";
    foreach ($classConstants as $name => $value) {
        $exportedValue = validateAndExportValue($value);
        $content .= "if (!defined('{$name}')) define('{$name}', {$exportedValue});\n";
        
        if (!defined($name)) {
            define($name, $value);
        }
    }
    $content .= "\n";
}

$success = false;
$attempts = 0;
$maxAttempts = 3;

while (!$success && $attempts < $maxAttempts) {
    $attempts++;
    $tmpFile = $staticFilePath . '.tmp.' . $attempts;
    
    if (file_put_contents($tmpFile, $content, LOCK_EX) !== false) {

        if (file_get_contents($tmpFile) === $content) {

            $tokens = @token_get_all($content);
            if ($tokens !== false) {
                $hasPhpTag = false;
                $syntaxError = false;
                
                foreach ($tokens as $token) {
                    if (is_array($token)) {
                        if ($token[0] === T_OPEN_TAG) {
                            $hasPhpTag = true;
                        }
                        if ($token[0] === T_BAD_CHARACTER) {
                            $syntaxError = true;
                            break;
                        }
                    }
                }
                
                if ($hasPhpTag && !$syntaxError) {
                    // Déplacer le fichier vers sa destination finale
                    if (rename($tmpFile, $staticFilePath)) {
                        chmod($staticFilePath, 0644);
                        $success = true;
                    }
                }
            }
        }
    }
    
    if (!$success && file_exists($tmpFile)) {
        @unlink($tmpFile);
    }
}

for ($i = 1; $i <= $maxAttempts; $i++) {
    $tmpFile = $staticFilePath . '.tmp.' . $i;
    if (file_exists($tmpFile)) {
        @unlink($tmpFile);
    }
}

if ($success && file_exists($staticFilePath)) {
    require_once $staticFilePath;
}