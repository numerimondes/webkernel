<?php
/**
 * IDE Helper Configuration for Webkernel
 * 
 * Add this file to your IDE's "Include Path" or "External Libraries" to get 
 * proper intellisense for your constant-based class aliases.
 * 
 * For PhpStorm:
 * 1. Go to File → Settings → Languages & Frameworks → PHP
 * 2. Click on "Include Path" tab
 * 3. Add this file's directory
 * 
 * For VS Code with PHP Intelephense:
 * 1. Add this to your settings.json:
 * "intelephense.files.associations": [
 *     "*.php"
 * ],
 * "intelephense.stubs": [
 *     "path/to/this/file"
 * ]
 */

// Include the generated IDE stubs
$ideStubFile = __DIR__ . '/WebkernelStaticGeneratedFiles/ide-stubs.php';
if (file_exists($ideStubFile)) {
    require_once $ideStubFile;
}