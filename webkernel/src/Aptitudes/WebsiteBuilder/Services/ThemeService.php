<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\WebsiteBuilder\Services;

use Webkernel\Aptitudes\WebsiteBuilder\Models\Theme;
use Illuminate\Support\Facades\Cache;

class ThemeService
{
    private array $themeCache = [];
    private ThemeCssGenerator $cssGenerator;

    public function __construct(ThemeCssGenerator $cssGenerator)
    {
        $this->cssGenerator = $cssGenerator;
    }

    /**
     * Register theme for site with ultra-fast access
     */
    public function registerTheme(Theme $theme, string $siteId, bool $minimal = false): void
    {
        $cacheKey = "theme_config_{$siteId}_{$theme->id}";

        if ($minimal) {
            $this->registerMinimalTheme($theme, $siteId);
        } else {
            $this->registerFullTheme($theme, $siteId, $cacheKey);
        }
    }

    /**
     * Register minimal theme (ultra-fast, basic colors only)
     */
    private function registerMinimalTheme(Theme $theme, string $siteId): void
    {
        $lightColors = $theme->getColorsForMode('light');

        config([
            'theme.minimal' => [
                'site_id' => $siteId,
                'theme_id' => $theme->id,
                'background' => $lightColors['background_primary'] ?? '#ffffff',
                'text' => $lightColors['text_primary'] ?? '#000000',
                'brand' => $lightColors['brand_primary'] ?? '#3b82f6',
                'accent' => $lightColors['accent_primary'] ?? '#8b5cf6',
                'success' => $lightColors['success'] ?? '#10b981',
                'warning' => $lightColors['warning'] ?? '#f59e0b',
                'danger' => $lightColors['danger'] ?? '#ef4444'
            ]
        ]);

        $this->registerMinimalHelpers();
    }

    /**
     * Register full theme configuration
     */
    private function registerFullTheme(Theme $theme, string $siteId, string $cacheKey): void
    {
        $themeData = Cache::remember($cacheKey, 3600, function () use ($theme) {
            return [
                'modes' => $theme->getModesConfiguration(),
                'base' => $theme->getBaseConfiguration(),
                'available_modes' => $theme->getAvailableModes()
            ];
        });

        config([
            'theme.full' => array_merge($themeData, [
                'site_id' => $siteId,
                'theme_id' => $theme->id
            ])
        ]);

        $this->registerFullHelpers();
    }

    /**
     * Register minimal helper functions
     */
    private function registerMinimalHelpers(): void
    {
        if (!function_exists('theme_color')) {
            function theme_color(string $color = 'brand'): string {
                $config = config('theme.minimal', []);
                return $config[$color] ?? '#3b82f6';
            }
        }

        if (!function_exists('theme_background')) {
            function theme_background(): string {
                return theme_color('background');
            }
        }

        if (!function_exists('theme_text')) {
            function theme_text(): string {
                return theme_color('text');
            }
        }

        if (!function_exists('theme_brand')) {
            function theme_brand(): string {
                return theme_color('brand');
            }
        }
    }

    /**
     * Register full helper functions
     */
    private function registerFullHelpers(): void
    {
        if (!function_exists('theme_color')) {
            function theme_color(string $color, string $mode = 'light'): string {
                $config = config('theme.full', []);
                return $config['modes'][$mode]['colors'][$color] ?? '#3b82f6';
            }
        }

        if (!function_exists('theme_config')) {
            function theme_config(?string $key = null) {
                $config = config('theme.full', []);
                return $key ? ($config[$key] ?? null) : $config;
            }
        }

        if (!function_exists('theme_font')) {
            function theme_font(string $type = 'primary'): string {
                $config = config('theme.full', []);
                return $config['base']['typography']['fonts'][$type] ?? 'system-ui, sans-serif';
            }
        }

        if (!function_exists('theme_spacing')) {
            function theme_spacing(string $size = 'md'): int {
                $config = config('theme.full', []);
                return $config['base']['spacing'][$size] ?? 16;
            }
        }

        if (!function_exists('theme_modes')) {
            function theme_modes(): array {
                $config = config('theme.full', []);
                return $config['available_modes'] ?? ['light'];
            }
        }
    }

    /**
     * Get theme for project with caching
     */
    public function getThemeForProject(int $projectId): ?Theme
    {
        $cacheKey = "project_theme_{$projectId}";

        if (isset($this->themeCache[$cacheKey])) {
            return $this->themeCache[$cacheKey];
        }

        $theme = Theme::activeForProject($projectId)->first();

        if ($theme) {
            $this->themeCache[$cacheKey] = $theme;
        }

        return $theme;
    }

    /**
     * Generate CSS for theme
     */
    public function generateCss(Theme $theme): string
    {
        return $this->cssGenerator->generateCss($theme);
    }

    /**
     * Get color variants for components
     */
    public function getColorVariants(Theme $theme): array
    {
        return $this->cssGenerator->generateColorVariants($theme);
    }

    /**
     * Create theme query builder
     */
    public function query(Theme $theme): ThemeQuery
    {
        return new ThemeQuery($theme);
    }

    /**
     * Get theme colors for JavaScript frontend
     */
    public function getThemeForJavaScript(Theme $theme): array
    {
        $query = new ThemeQuery($theme);
        return $query->toJavaScript();
    }

    /**
     * Clear theme cache
     */
    public function clearCache(): void
    {
        $this->themeCache = [];
        $this->cssGenerator->clearCache();
    }

    /**
     * Validate theme configuration
     */
    public function validateThemeConfig(array $config): array
    {
        $errors = [];

        // Check required base configuration
        if (!isset($config['base_configuration']['typography']['fonts']['primary'])) {
            $errors[] = 'Primary font is required';
        }

        // Check modes configuration
        if (!isset($config['modes_configuration']) || empty($config['modes_configuration'])) {
            $errors[] = 'At least one theme mode is required';
        }

        foreach ($config['modes_configuration'] ?? [] as $mode => $modeConfig) {
            if (!isset($modeConfig['colors']['background_primary'])) {
                $errors[] = "Primary background color is required for mode: {$mode}";
            }
            if (!isset($modeConfig['colors']['text_primary'])) {
                $errors[] = "Primary text color is required for mode: {$mode}";
            }
            if (!isset($modeConfig['ui']['name'])) {
                $errors[] = "UI name is required for mode: {$mode}";
            }
        }

        return $errors;
    }

    /**
     * Create default theme for project
     */
    public function createDefaultTheme(int $projectId, string $name = 'Default Theme'): Theme
    {
        return Theme::create([
            'name' => $name,
            'description' => 'Default theme with light and dark modes',
            'project_id' => $projectId,
            'is_active' => true,
            'is_default' => true,
            'base_configuration' => [
                'typography' => [
                    'fonts' => [
                        'primary' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                        'heading' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                        'mono' => '"SF Mono", Monaco, "Cascadia Code", monospace'
                    ]
                ]
            ],
            'modes_configuration' => [] // Will use defaults from model
        ]);
    }
}
