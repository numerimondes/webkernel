<?php

if (!function_exists('autoload_badge_parts')) {
    function autoload_badge_parts() {
        $partsDir = base_path('packages/webkernel/src/Core/Resources/Views/components/webkernel/ui/organism/universal-badge/parts/');
        $parts = [];
        
        if (is_dir($partsDir)) {
            $files = glob($partsDir . '*.blade.php');
            foreach ($files as $file) {
                $filename = basename($file, '.blade.php');
                $parts[] = $filename;
            }
        }
        
        return $parts;
    }
} 