<?php

declare(strict_types=1);

const AVAILABLE_THEME_COLORS = [
    'blue', 'green', 'red', 'amber', 'gray',
    'purple', 'pink', 'indigo'
];

if (!function_exists('theme_class')) {
    function theme_class(string $component, string $variant = 'primary'): string
    {
        $config = config('theme.config', []);

        if ($variant === 'primary') {
            $variant = $config['primary_color'] ?? 'blue';
        }

        return "btn-{$variant}";
    }
}

if (!function_exists('get_theme_config')) {
    function get_theme_config(?string $key = null)
    {
        $config = config('theme.config', []);
        return $key ? ($config[$key] ?? null) : $config;
    }
}

if (!function_exists('validate_theme_color')) {
    function validate_theme_color(string $color): bool
    {
        return in_array($color, AVAILABLE_THEME_COLORS, true);
    }
}
