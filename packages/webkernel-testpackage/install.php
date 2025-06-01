<?php

/**
 * Installation script for WebkernelTestpackage
 * Version: 0.0.1
 *
 * This script is executed during package installation
 */

declare(strict_types=1);

echo "Installing WebkernelTestpackage v0.0.1...\n";

// Check for Composer
if (!class_exists('\\Composer\\Autoload\\ClassLoader')) {
    echo "Error: Composer autoloader not detected.\n";
    exit(1);
}

// Check PHP version
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    echo "Error: PHP 8.1 or higher is required.\n";
    exit(1);
}

// Check Laravel
if (!function_exists('app') && !class_exists('\\Illuminate\\Foundation\\Application')) {
    echo "Warning: Laravel not detected. Make sure this package is installed in a Laravel application.\n";
}

echo "✓ System requirements check passed\n";
echo "✓ WebkernelTestpackage v0.0.1 installed successfully.\n";
echo "\nNext steps:\n";
echo "1. Run 'php artisan vendor:publish --tag=webkernel-testpackage-config'\n";
echo "2. Run 'php artisan vendor:publish --tag=webkernel-testpackage-assets'\n";
echo "3. Run 'php artisan migrate' if you have database migrations\n";
echo "\nFor more information, see README.md\n";
