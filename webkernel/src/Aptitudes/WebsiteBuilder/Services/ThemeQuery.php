<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\WebsiteBuilder\Services;

use Webkernel\Aptitudes\WebsiteBuilder\Models\Theme;

class ThemeQuery
{
    private Theme $theme;
    private ?string $mode = null;
    private array $queryCache = [];

    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Create ThemeQuery instance for active theme of project
     */
    public static function forProject(int $projectId): ?self
    {
        $theme = Theme::getActiveTheme($projectId);
        return $theme ? new self($theme) : null;
    }

    /**
     * Create ThemeQuery instance with fallback CSS if no theme exists
     */
    public static function forProjectWithFallback(int $projectId): self
    {
        $themeQuery = self::forProject($projectId);

        if (!$themeQuery) {
            // Create a minimal theme query with fallback CSS
            return new self(self::createFallbackTheme($projectId));
        }

        return $themeQuery;
    }

    /**
     * Create a fallback theme when none exists
     */
    private static function createFallbackTheme(int $projectId): Theme
    {
        $theme = new Theme();
        $theme->project_id = $projectId;
        $theme->name = 'Default Theme';
        $theme->slug = 'default';
        $theme->is_active = true;
        $theme->is_default = true;
        $theme->theme_config = [];

        return $theme;
    }


    /**
     * Get the underlying theme model
     */
    public function getTheme(): Theme
    {
        return $this->theme;
    }

    /**
     * Get CSS content using ThemeCssGenerator
     */
    public function getCssContent(): string
    {
        $generator = app(\Webkernel\Aptitudes\WebsiteBuilder\Services\ThemeCssGenerator::class);
        return $generator->generateCss($this->theme);
    }

    /**
     * Register minimal helpers
     */
    public function registerMinimalHelpers(string $siteId): void
    {
        Theme::registerMinimalHelpers($this->theme, $siteId);
    }

    /**
     * Get all colors for all modes
     */
    public function getColors(): array
    {
        return $this->theme->getColors();
    }

    /**
     * Get mode configuration
     */
    public function getModeConfiguration(): array
    {
        return $this->theme->getModeConfiguration();
    }

    /**
     * Set mode for color queries (light/dark only)
     * System mode should be handled client-side
     */
    public function mode(string $mode): self
    {
        // Only accept actual theme modes, not system preference
        if (in_array($mode, $this->theme->getAvailableModes())) {
            $this->mode = $mode;
        }

        return $this;
    }

    /**
     * Get primary background color for mode
     */
    public function backgroundPrimary(): string
    {
        return $this->getColorValue('background_primary');
    }

    /**
     * Get secondary background color for mode
     */
    public function backgroundSecondary(): string
    {
        return $this->getColorValue('background_secondary');
    }

    /**
     * Get tertiary background color for mode
     */
    public function backgroundTertiary(): string
    {
        return $this->getColorValue('background_tertiary');
    }

    /**
     * Get primary text color for mode
     */
    public function textPrimary(): string
    {
        return $this->getColorValue('text_primary');
    }

    /**
     * Get secondary text color for mode
     */
    public function textSecondary(): string
    {
        return $this->getColorValue('text_secondary');
    }

    /**
     * Get tertiary text color for mode
     */
    public function textTertiary(): string
    {
        return $this->getColorValue('text_tertiary');
    }

    /**
     * Get primary brand color for mode
     */
    public function brandPrimary(): string
    {
        return $this->getColorValue('brand_primary');
    }

    /**
     * Get secondary brand color for mode
     */
    public function brandSecondary(): string
    {
        return $this->getColorValue('brand_secondary');
    }

    /**
     * Get primary accent color for mode
     */
    public function accentPrimary(): string
    {
        return $this->getColorValue('accent_primary');
    }

    /**
     * Get secondary accent color for mode
     */
    public function accentSecondary(): string
    {
        return $this->getColorValue('accent_secondary');
    }

    /**
     * Get primary border color for mode
     */
    public function borderPrimary(): string
    {
        return $this->getColorValue('border_primary');
    }

    /**
     * Get secondary border color for mode
     */
    public function borderSecondary(): string
    {
        return $this->getColorValue('border_secondary');
    }

    /**
     * Get success color
     */
    public function success(): string
    {
        return $this->getColorValue('success');
    }

    /**
     * Get warning color
     */
    public function warning(): string
    {
        return $this->getColorValue('warning');
    }

    /**
     * Get danger color
     */
    public function danger(): string
    {
        return $this->getColorValue('danger');
    }

    /**
     * Get info color
     */
    public function info(): string
    {
        return $this->getColorValue('info');
    }

    /**
     * Get all colors for current mode
     */
    public function allColors(): array
    {
        $mode = $this->mode ?? 'light';
        return $this->theme->getColorsForMode($mode);
    }

    /**
     * Get primary font family
     */
    public function fontPrimary(): string
    {
        $typography = $this->theme->getTypography();
        return $typography['fonts']['primary'] ?? '-apple-system, BlinkMacSystemFont, sans-serif';
    }

    /**
     * Get heading font family
     */
    public function fontHeading(): string
    {
        $typography = $this->theme->getTypography();
        return $typography['fonts']['heading'] ?? $this->fontPrimary();
    }

    /**
     * Get monospace font family
     */
    public function fontMono(): string
    {
        $typography = $this->theme->getTypography();
        return $typography['fonts']['mono'] ?? '"SF Mono", Monaco, monospace';
    }

    /**
     * Get font size by key
     */
    public function fontSize(string $size): int
    {
        $typography = $this->theme->getTypography();
        return $typography['sizes'][$size] ?? 16;
    }

    /**
     * Get font weight by key
     */
    public function fontWeight(string $weight): int
    {
        $typography = $this->theme->getTypography();
        return $typography['weights'][$weight] ?? 400;
    }

    /**
     * Get spacing value by key
     */
    public function spacing(string $size): int
    {
        $spacing = $this->theme->getSpacing();
        return $spacing[$size] ?? 16;
    }

    /**
     * Get border radius by key
     */
    public function radius(string $size): int
    {
        $radius = $this->theme->getRadius();
        return $radius[$size] ?? 8;
    }

    /**
     * Get shadow by key
     */
    public function shadow(string $size): string
    {
        $shadows = $this->theme->getShadows();
        return $shadows[$size] ?? '0 1px 3px rgba(0, 0, 0, 0.1)';
    }

    /**
     * Get mode UI configuration
     */
    public function modeConfig(): array
    {
        $mode = $this->mode ?? 'light';
        return $this->theme->getModeUiConfig($mode);
    }

    /**
     * Get all available modes with their UI config
     */
    public function allModeConfigs(): array
    {
        $configs = [];
        foreach ($this->theme->getAvailableModes() as $mode) {
            $configs[$mode] = $this->theme->getModeUiConfig($mode);
        }
        return $configs;
    }

    /**
     * Check if theme has specific mode
     */
    public function hasMode(string $mode): bool
    {
        return in_array($mode, $this->theme->getAvailableModes());
    }

    /**
     * Get specific color value with caching
     */
    private function getColorValue(string $colorKey): string
    {
        $mode = $this->mode ?? 'light';
        $cacheKey = $mode . '_' . $colorKey;

        if (isset($this->queryCache[$cacheKey])) {
            return $this->queryCache[$cacheKey];
        }

        $colors = $this->theme->getColorsForMode($mode);
        $color = $colors[$colorKey] ?? '#000000';

        $this->queryCache[$cacheKey] = $color;

        return $color;
    }

    /**
     * Generate CSS variables for current mode
     */
    public function toCssVariables(): string
    {
        $mode = $this->mode ?? 'light';
        $colors = $this->theme->getColorsForMode($mode);

        $css = '';
        foreach ($colors as $key => $value) {
            $css .= "--color-{$key}: {$value};\n";
        }

        return $css;
    }

    /**
     * Get theme data for JavaScript
     */
    public function toJavaScript(): array
    {
        return [
            'modes' => $this->allModeConfigs(),
            'colors' => $this->theme->getModesConfiguration(),
            'typography' => $this->theme->getTypography(),
            'spacing' => $this->theme->getSpacing(),
            'radius' => $this->theme->getRadius(),
            'shadows' => $this->theme->getShadows()
        ];
    }
}
