<?php

$excludedDirs = ['vendor', '.git'];
$excludedFiles = ['helpers.php'];

foreach ($iterator as $file) {
    $filePath = $file->getRealPath();
    $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $filePath);

    if (
        $file->isFile() &&
        $file->getExtension() === 'php' &&
        !in_array(basename($filePath), $excludedFiles) &&
        !str_contains($relativePath, implode(DIRECTORY_SEPARATOR, $excludedDirs))
    ) {
        require_once $filePath;
    }
}

