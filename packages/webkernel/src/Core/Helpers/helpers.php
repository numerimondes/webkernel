<?php

/**
 * Main Webkernel helpers file
 * 
 * > packages/webkernel/src/Core/Helpers/helpers.php
 * 
 * This file automatically includes all other helper files
 * in the current directory and its subdirectories
 * 
 * El Moumen Yassine - Numerimondes
 * <yassine@numerimondes.com>
 * www.numerimondes.com
 * 
 */

$helpersDirs = [
    __DIR__,
   // 'packages/webkernel/src/PlatformConfig/Helpers',
    'packages/webkernel/src/PlatformConfig/Constants/Static/',
    'packages/webkernel/src/Settings/Helpers',
    'platform/Modules/ReamMar/Core/Helpers',
];

$excludeDirs = [];
$excludeFiles = [];

foreach ($helpersDirs as $helpersDir) {
    if (!is_dir($helpersDir)) {
        continue;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($helpersDir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }
        
        $filePath = $file->getRealPath();
        
        if ($filePath === __FILE__) {
            continue;
        }
        
        $relativePath = str_replace($helpersDir . DIRECTORY_SEPARATOR, '', $filePath);
        
        if (in_array($relativePath, $excludeFiles)) {
            continue;
        }
        
        $shouldExclude = false;
        foreach ($excludeDirs as $excludeDir) {
            if (strpos($relativePath, $excludeDir . DIRECTORY_SEPARATOR) === 0) {
                $shouldExclude = true;
                break;
            }
        }
        
        if ($shouldExclude) {
            continue;
        }
        
        require_once $filePath;
    }
}