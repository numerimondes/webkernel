<?php

if (!function_exists('autoload_badge_parts')) {
    function autoload_badge_parts() {
        static $parts = null;
        
        // Éviter la récursion
        if ($parts !== null) {
            return $parts;
        }
        
        $partsDir = base_path('packages/webkernel/src/Core/Resources/Views/components/webkernel/ui/organism/universal-badge/parts/');
        $parts = [];
        
        if (is_dir($partsDir)) {
            $files = glob($partsDir . '*.blade.php');
            foreach ($files as $file) {
                $filename = basename($file, '.blade.php');
                // Éviter les fichiers qui pourraient causer des boucles
                if (!str_contains($filename, 'universal-badges')) {
                $parts[] = $filename;
                }
            }
        }
        
        // Debug: limiter le nombre de parts pour éviter les boucles
        $parts = array_slice($parts, 0, 10);
        
        return $parts;
    }
} 