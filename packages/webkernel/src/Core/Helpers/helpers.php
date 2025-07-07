<?php

$basePath = dirname(__DIR__, 4);

$criticalFiles = [

];

$helpersDirs = [
    __DIR__,
    $basePath . '/packages/webkernel/src/Settings/Helpers',
    $basePath . '/platform/Modules/ReamMar/Core/Helpers',
];

$excludeDirs = [];
$excludeFiles = [];

function cleanPhpContent($content) {
    $content = preg_replace('/^\s*<\?php\s*/', '', $content);
    $content = preg_replace('/\s*\?>\s*$/', '', $content);
    $content = preg_replace('/^\s*(require|include)(_once)?\s*[^;]*;?\s*$/m', '', $content);
    return trim($content);
}

function getAllPhpFiles($dir, $excludeDirs = [], $excludeFiles = []) {
    $files = [];
    if (!is_dir($dir)) return $files;
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') continue;
        
        $filePath = $file->getRealPath();
        if ($filePath === __FILE__) continue;
        
        $relativePath = str_replace($dir . DIRECTORY_SEPARATOR, '', $filePath);
        
        if (in_array($relativePath, $excludeFiles)) continue;
        
        foreach ($excludeDirs as $excludeDir) {
            if (str_starts_with($relativePath, $excludeDir . DIRECTORY_SEPARATOR)) {
                continue 2;
            }
        }
        
        $files[] = $filePath;
    }
    
    return $files;
}

foreach ($criticalFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $cleanContent = cleanPhpContent($content);
        if ($cleanContent) {
            eval($cleanContent);
        }
    }
}

foreach ($helpersDirs as $dir) {
    $files = getAllPhpFiles($dir, $excludeDirs, $excludeFiles);
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $cleanContent = cleanPhpContent($content);
        if ($cleanContent) {
            eval($cleanContent);
        }
    }
}