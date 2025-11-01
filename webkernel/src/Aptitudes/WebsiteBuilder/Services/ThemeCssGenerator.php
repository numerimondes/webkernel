<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\WebsiteBuilder\Services;

use Webkernel\Aptitudes\WebsiteBuilder\Models\Theme;

class ThemeCssGenerator
{
    private array $cssCache = [];

    /**
     * Generate optimized CSS for theme
     */
    public function generateCss(Theme $theme): string
    {
        // Handle case where theme is not saved to database (fallback theme)
        $timestamp = $theme->updated_at ? $theme->updated_at->timestamp : time();
        $cacheKey = "theme_css_{$theme->id}_{$timestamp}";

        if (isset($this->cssCache[$cacheKey])) {
            return $this->cssCache[$cacheKey];
        }

        $css = $this->generateHeader($theme);
        $css .= $this->generateRootVariables($theme);
        $css .= $this->generateModeClasses($theme);
        $css .= $this->generateUtilityClasses($theme);

        $this->cssCache[$cacheKey] = $css;

        // Prevent memory leaks
        if (count($this->cssCache) > 50) {
            $this->cssCache = array_slice($this->cssCache, -25, null, true);
        }

        return $css;
    }

    /**
     * Generate CSS header
     */
    private function generateHeader(Theme $theme): string
    {
        return "/*\n" .
               " * Theme: {$theme->name}\n" .
               " * Project: {$theme->project_id}\n" .
               " * Generated: " . now()->toISOString() . "\n" .
               " * Modes: " . implode(', ', $theme->getAvailableModes()) . "\n" .
               " */\n\n";
    }

    /**
     * Generate root CSS variables
     */
    private function generateRootVariables(Theme $theme): string
    {
        $css = ":root {\n";

        // Typography variables
        $typography = $theme->getTypography();
        if (isset($typography['fonts'])) {
            foreach ($typography['fonts'] as $key => $value) {
                $css .= "  --font-{$key}: {$value};\n";
            }
        }

        if (isset($typography['sizes'])) {
            foreach ($typography['sizes'] as $key => $size) {
                $css .= "  --font-size-{$key}: {$size}px;\n";
            }
        }

        if (isset($typography['weights'])) {
            foreach ($typography['weights'] as $key => $weight) {
                $css .= "  --font-weight-{$key}: {$weight};\n";
            }
        }

        // Spacing variables
        $spacing = $theme->getSpacing();
        foreach ($spacing as $key => $value) {
            $css .= "  --space-{$key}: {$value}px;\n";
        }

        // Border radius variables
        $radius = $theme->getRadius();
        foreach ($radius as $key => $value) {
            $css .= "  --radius-{$key}: {$value}px;\n";
        }

        // Shadow variables
        $shadows = $theme->getShadows();
        foreach ($shadows as $key => $value) {
            $css .= "  --shadow-{$key}: {$value};\n";
        }

        // Default colors (light mode)
        $lightColors = $theme->getColorsForMode('light');
        foreach ($lightColors as $key => $value) {
            $cssKey = str_replace('_', '-', $key);
            $css .= "  --color-{$cssKey}: {$value};\n";
        }

        $css .= "}\n\n";

        return $css;
    }

    /**
     * Generate mode-specific CSS classes
     */
    private function generateModeClasses(Theme $theme): string
    {
        $css = "/* Theme Mode Classes */\n";

        foreach ($theme->getAvailableModes() as $mode) {
            $colors = $theme->getColorsForMode($mode);

            // Generate both .mode-mode and .mode classes for compatibility
            $css .= "html.{$mode}-mode,\nhtml.{$mode} {\n";
            foreach ($colors as $key => $value) {
                $cssKey = str_replace('_', '-', $key);
                $css .= "  --color-{$cssKey}: {$value};\n";
            }
            $css .= "}\n\n";
        }

        return $css;
    }

    /**
     * Generate utility CSS classes
     */
    private function generateUtilityClasses(Theme $theme): string
    {
        $css = "/* Theme Utility Classes */\n";

        // Get all available colors from the first mode (light)
        $lightColors = $theme->getColorsForMode('light');
        $colorKeys = array_keys($lightColors);

        // Background utilities - Full names
        foreach ($colorKeys as $color) {
            $safeName = str_replace('_', '-', $color);
            $cssVar = str_replace('_', '-', $color);
            $css .= ".bg-{$safeName} { background-color: var(--color-{$cssVar}); }\n";
        }

        // Text utilities - Full names
        foreach ($colorKeys as $color) {
            $safeName = str_replace('_', '-', $color);
            $cssVar = str_replace('_', '-', $color);
            $css .= ".text-{$safeName} { color: var(--color-{$cssVar}); }\n";
        }

        // Border utilities - Full names
        foreach ($colorKeys as $color) {
            $safeName = str_replace('_', '-', $color);
            $cssVar = str_replace('_', '-', $color);
            $css .= ".border-{$safeName} { border-color: var(--color-{$cssVar}); }\n";
        }

        // Intelligent short utility classes - Smart defaults
        $intelligentClasses = [
            // Backgrounds
            'bg' => 'background-primary',
            'background' => 'background-primary', // Alias for compatibility
            'bg-muted' => 'background-secondary',
            'bg-subtle' => 'background-tertiary',
            'bg-surface' => 'surface',
            'bg-surface-muted' => 'surface-variant',
            'bg-overlay' => 'overlay',

            // Text
            'text' => 'text-primary',
            'text-muted' => 'text-secondary',
            'text-subtle' => 'text-tertiary',
            'text-disabled' => 'text-disabled',
            'text-on-primary' => 'text-on-primary',
            'text-on-brand' => 'text-on-brand',
            'text-on-accent' => 'text-on-accent',

            // Brand colors
            'primary' => 'brand-primary',
            'primary-muted' => 'brand-secondary',
            'primary-light' => 'brand-light',

            // Accent colors
            'accent' => 'accent-primary',
            'accent-muted' => 'accent-secondary',
            'accent-light' => 'accent-light',

            // States
            'success' => 'success',
            'success-light' => 'success-light',
            'warning' => 'warning',
            'warning-light' => 'warning-light',
            'danger' => 'danger',
            'danger-light' => 'danger-light',
            'info' => 'info',
            'info-light' => 'info-light',

            // Borders
            'border' => 'border-primary',
            'border-muted' => 'border-secondary',
            'border-focus' => 'border-focus',

            // Interactive states
            'hover' => 'hover',
            'active' => 'active',
            'focus' => 'focus',
            'disabled' => 'disabled',
        ];

        // Generate intelligent utilities
        foreach ($intelligentClasses as $class => $colorVar) {
            $cssVar = str_replace('_', '-', $colorVar);

            // Background utilities
            $css .= ".bg-{$class} { background-color: var(--color-{$cssVar}); }\n";

            // Text utilities (skip for background-only colors)
            if (!in_array($class, ['bg', 'bg-muted', 'bg-subtle', 'bg-surface', 'bg-surface-muted', 'bg-overlay', 'hover', 'active', 'focus', 'disabled'])) {
                $css .= ".text-{$class} { color: var(--color-{$cssVar}); }\n";
            }

            // Border utilities
            if (strpos($class, 'border') === 0 || in_array($class, ['primary', 'accent', 'success', 'warning', 'danger', 'info'])) {
                $css .= ".border-{$class} { border-color: var(--color-{$cssVar}); }\n";
            }
        }

        // Semantic color utilities (like Tailwind/Filament)
        $css .= "\n/* Semantic Color Utilities */\n";
        $semanticColors = [
            // Neutral colors
            'white' => 'background-primary',
            'black' => 'text-primary',
            'gray' => 'text-secondary',
            'gray-light' => 'text-tertiary',
            'gray-dark' => 'text-primary',

            // Brand colors
            'blue' => 'brand-primary',
            'indigo' => 'brand-secondary',
            'purple' => 'accent-primary',
            'violet' => 'accent-secondary',

            // State colors
            'green' => 'success',
            'emerald' => 'success',
            'yellow' => 'warning',
            'amber' => 'warning',
            'red' => 'danger',
            'rose' => 'danger',
            'cyan' => 'info',
            'sky' => 'info',
        ];

        // Generate semantic utilities with intelligent text colors
        foreach ($semanticColors as $semantic => $themeVar) {
            $cssVar = str_replace('_', '-', $themeVar);

            // Background utilities
            $css .= ".bg-{$semantic} { background-color: var(--color-{$cssVar}); }\n";

            // Text utilities - intelligent contrast
            if (in_array($semantic, ['white', 'gray-light'])) {
                // Light backgrounds need dark text
                $css .= ".text-{$semantic} { color: var(--color-text-primary); }\n";
            } elseif (in_array($semantic, ['black', 'gray-dark'])) {
                // Dark backgrounds need light text
                $css .= ".text-{$semantic} { color: var(--color-background-primary); }\n";
            } elseif (in_array($semantic, ['blue', 'indigo', 'purple', 'violet', 'green', 'emerald', 'red', 'rose', 'cyan', 'sky'])) {
                // Brand colors use text-on-brand
                $css .= ".text-{$semantic} { color: var(--color-text-on-brand); }\n";
            } else {
                // Default text color
                $css .= ".text-{$semantic} { color: var(--color-{$cssVar}); }\n";
            }

            // Border utilities
            $css .= ".border-{$semantic} { border-color: var(--color-{$cssVar}); }\n";
        }

        // Interactive state utilities
        $css .= "\n/* Interactive State Utilities */\n";
        $css .= ".hover\\:bg-hover:hover { background-color: var(--color-hover); }\n";
        $css .= ".hover\\:bg-active:hover { background-color: var(--color-active); }\n";
        $css .= ".hover\\:bg-primary:hover { background-color: var(--color-brand-primary); }\n";
        $css .= ".hover\\:bg-accent:hover { background-color: var(--color-accent-primary); }\n";
        $css .= ".hover\\:bg-surface:hover { background-color: var(--color-surface); }\n";
        $css .= ".hover\\:bg-surface-muted:hover { background-color: var(--color-surface-variant); }\n";

        $css .= ".hover\\:text-primary:hover { color: var(--color-brand-primary); }\n";
        $css .= ".hover\\:text-accent:hover { color: var(--color-accent-primary); }\n";
        $css .= ".hover\\:text-success:hover { color: var(--color-success); }\n";
        $css .= ".hover\\:text-warning:hover { color: var(--color-warning); }\n";
        $css .= ".hover\\:text-danger:hover { color: var(--color-danger); }\n";
        $css .= ".hover\\:text-info:hover { color: var(--color-info); }\n";

        $css .= ".focus\\:border-focus:focus { border-color: var(--color-border-focus); }\n";
        $css .= ".focus\\:ring-focus:focus { box-shadow: 0 0 0 3px var(--color-border-focus); }\n";

        $css .= ".active\\:bg-active:active { background-color: var(--color-active); }\n";
        $css .= ".active\\:bg-primary:active { background-color: var(--color-brand-primary); }\n";
        $css .= ".active\\:bg-accent:active { background-color: var(--color-accent-primary); }\n";

        // Semantic color hover states
        $css .= "\n/* Semantic Color Hover States */\n";
        foreach ($semanticColors as $semantic => $themeVar) {
            $cssVar = str_replace('_', '-', $themeVar);
            $css .= ".hover\\:bg-{$semantic}:hover { background-color: var(--color-{$cssVar}); }\n";
            $css .= ".hover\\:text-{$semantic}:hover { color: var(--color-{$cssVar}); }\n";
            $css .= ".hover\\:border-{$semantic}:hover { border-color: var(--color-{$cssVar}); }\n";
        }

        // Font utilities
        $typography = $theme->getTypography();
        if (isset($typography['fonts'])) {
            foreach ($typography['fonts'] as $key => $value) {
                $css .= ".font-{$key} { font-family: var(--font-{$key}); }\n";
            }
        }

        // Typography size utilities
        if (isset($typography['sizes'])) {
            foreach ($typography['sizes'] as $key => $size) {
                $css .= ".text-{$key} { font-size: var(--font-size-{$key}); }\n";
            }
        }

        // Font weight utilities
        if (isset($typography['weights'])) {
            foreach ($typography['weights'] as $key => $weight) {
                $css .= ".font-{$key} { font-weight: var(--font-weight-{$key}); }\n";
            }
        }

        // Spacing utilities
        $spacing = $theme->getSpacing();
        foreach ($spacing as $key => $value) {
            $css .= ".p-{$key} { padding: var(--space-{$key}); }\n";
            $css .= ".m-{$key} { margin: var(--space-{$key}); }\n";
        }

        // Border radius utilities
        $radius = $theme->getRadius();
        foreach ($radius as $key => $value) {
            $css .= ".rounded-{$key} { border-radius: var(--radius-{$key}); }\n";
        }

        // Shadow utilities
        $shadows = $theme->getShadows();
        foreach ($shadows as $key => $value) {
            $css .= ".shadow-{$key} { box-shadow: var(--shadow-{$key}); }\n";
        }

        $css .= "\n/* Theme Component Styles */\n";
        $css .= "body {\n";
        $css .= "  font-family: var(--font-primary);\n";
        $css .= "  font-size: var(--font-size-base);\n";
        $css .= "  background-color: var(--color-background-primary);\n";
        $css .= "  color: var(--color-text-primary);\n";
        $css .= "  transition: background-color 0.3s ease, color 0.3s ease;\n";
        $css .= "}\n\n";

        return $css;
    }

    /**
     * Generate specific color variants for components
     */
    public function generateColorVariants(Theme $theme): array
    {
        $variants = [];

        foreach ($theme->getAvailableModes() as $mode) {
            $colors = $theme->getColorsForMode($mode);
            $variants[$mode] = [];

            // Primary variant (brand primary)
            $variants[$mode]['primary'] = sprintf(
                'bg-%s text-%s hover:bg-%s focus:ring-%s',
                str_replace('_', '-', 'brand_primary'),
                str_replace('_', '-', 'text_primary'),
                str_replace('_', '-', 'brand_secondary'),
                str_replace('_', '-', 'brand_primary')
            );

            // Secondary variant
            $variants[$mode]['secondary'] = sprintf(
                'bg-%s text-%s hover:bg-%s focus:ring-%s',
                str_replace('_', '-', 'background_secondary'),
                str_replace('_', '-', 'text_primary'),
                str_replace('_', '-', 'background_tertiary'),
                str_replace('_', '-', 'brand_primary')
            );

            // Success variant
            $variants[$mode]['success'] = sprintf(
                'bg-success text-%s hover:opacity-90 focus:ring-success',
                str_replace('_', '-', 'text_primary')
            );

            // Warning variant
            $variants[$mode]['warning'] = sprintf(
                'bg-warning text-%s hover:opacity-90 focus:ring-warning',
                str_replace('_', '-', 'text_primary')
            );

            // Danger variant
            $variants[$mode]['danger'] = sprintf(
                'bg-danger text-%s hover:opacity-90 focus:ring-danger',
                str_replace('_', '-', 'text_primary')
            );
        }

        return $variants;
    }

    /**
     * Clear CSS cache
     */
    public function clearCache(): void
    {
        $this->cssCache = [];
    }
}
