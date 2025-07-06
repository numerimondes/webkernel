<?php

if (!function_exists('consoleColor')) {
    function consoleColor($message, $type = 'info')
    {
        $colors = [
            'info'  => "\033[32m", // Green
            'error' => "\033[31m", // Red
            'warn'  => "\033[33m", // Yellow
        ];

        $color = $colors[$type] ?? $colors['info'];
        echo $color . $message . "\033[0m\n";
    }
}
