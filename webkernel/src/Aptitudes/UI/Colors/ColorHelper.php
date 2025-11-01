<?php

namespace Webkernel\Aptitudes\UI\Colors;

class ColorHelper
{
    /**
     * Get a color value from any format (hex, rgb, oklch, hsl, or predefined name)
     */
    public static function get(string $color, string $shade = '600'): string
    {
        return Color::getPalette($color)[$shade] ?? Color::getPalette($color)['600'];
    }

    /**
     * Get a full color palette from any format
     */
    public static function getPalette(string $color): array
    {
        return Color::getPalette($color);
    }

    /**
     * Check if a color is light or dark
     */
    public static function isLight(string $color): bool
    {
        return Color::isLight($color);
    }

    /**
     * Normalize any color format to OKLCH
     */
    public static function normalize(string $color): string
    {
        return Color::normalizeColor($color);
    }

    /**
     * Generate Tailwind-style classes for a color
     */
    public static function getTailwindClasses(string $color, string $name): array
    {
        $palette = Color::getPalette($color);
        $classes = [];

        foreach ($palette as $shade => $value) {
            $classes["bg-{$name}-{$shade}"] = $value;
            $classes["text-{$name}-{$shade}"] = $value;
            $classes["border-{$name}-{$shade}"] = $value;
            $classes["ring-{$name}-{$shade}"] = $value;
            $classes["shadow-{$name}-{$shade}"] = $value;
        }

        return $classes;
    }

    /**
     * Generate CSS custom properties for a color palette
     */
    public static function getCssProperties(string $color, string $name): array
    {
        $palette = Color::getPalette($color);
        $properties = [];

        foreach ($palette as $shade => $value) {
            $properties["--color-{$name}-{$shade}"] = $value;
        }

        return $properties;
    }

    /**
     * Create a button style with color
     */
    public static function getButtonStyle(string $color, string $variant = 'solid'): array
    {
        $baseColor = self::get($color, '600');
        $hoverColor = self::get($color, '700');
        $focusColor = self::get($color, '500');

        return match($variant) {
            'solid' => [
                'background-color' => $baseColor,
                'color' => Color::isLight($baseColor) ? '#000000' : '#ffffff',
                'hover:background-color' => $hoverColor,
                'focus:ring-color' => $focusColor,
            ],
            'outline' => [
                'border-color' => $baseColor,
                'color' => $baseColor,
                'background-color' => 'transparent',
                'hover:background-color' => $baseColor,
                'hover:color' => Color::isLight($baseColor) ? '#000000' : '#ffffff',
            ],
            'ghost' => [
                'color' => $baseColor,
                'background-color' => 'transparent',
                'hover:background-color' => self::get($color, '100'),
            ],
            default => []
        };
    }

    /**
     * Get all available color names
     */
    public static function getAvailableColors(): array
    {
        return array_keys(Color::all());
    }

    /**
     * Validate if a color name exists
     */
    public static function isValidColor(string $color): bool
    {
        return Color::isPredefinedColor($color) || self::isValidColorFormat($color);
    }

    /**
     * Check if a string is a valid color format (hex, rgb, oklch, hsl)
     */
    public static function isValidColorFormat(string $color): bool
    {
        $color = trim($color);

        return str_starts_with($color, '#') ||
               str_starts_with($color, 'rgb(') ||
               str_starts_with($color, 'rgba(') ||
               str_starts_with($color, 'hsl(') ||
               str_starts_with($color, 'hsla(') ||
               str_starts_with($color, 'oklch(') ||
               preg_match('/^(\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})$/', $color);
    }
}
