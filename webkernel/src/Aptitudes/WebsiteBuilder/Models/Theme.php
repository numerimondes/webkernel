<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\WebsiteBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $project_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property array<array-key, mixed>|null $theme_config
 * @property bool $is_active
 * @property bool $is_default
 * @property string $version
 * @property string|null $css_file_path
 * @property string|null $generated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme activeForProject(int $projectId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereCssFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereThemeConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Theme whereVersion($value)
 * @mixin \Eloquent
 */
class Theme extends Model
{
    use HasFactory;

    protected $table = 'apt_website_themes';

    protected $fillable = [
        'name',
        'description',
        'project_id',
        'is_active',
        'is_default',
        'theme_config',
        'css_content'
    ];

    protected $casts = [
        'theme_config' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get the complete base configuration (non-mode specific)
     */
    public function getBaseConfiguration(): array
    {
        $default = [
            'typography' => [
                'fonts' => [
                    'primary' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                    'heading' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                    'mono' => '"SF Mono", Monaco, "Cascadia Code", monospace'
                ],
                'sizes' => [
                    'xs' => 12,
                    'sm' => 14,
                    'base' => 16,
                    'lg' => 18,
                    'xl' => 20,
                    '2xl' => 24,
                    '3xl' => 30,
                    '4xl' => 36
                ],
                'weights' => [
                    'light' => 300,
                    'normal' => 400,
                    'medium' => 500,
                    'semibold' => 600,
                    'bold' => 700
                ]
            ],
            'spacing' => [
                'xs' => 4,
                'sm' => 8,
                'md' => 16,
                'lg' => 24,
                'xl' => 32,
                '2xl' => 48,
                '3xl' => 64
            ],
            'radius' => [
                'none' => 0,
                'sm' => 4,
                'md' => 8,
                'lg' => 12,
                'xl' => 16,
                'full' => 9999
            ],
            'shadows' => [
                'sm' => '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                'md' => '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                'lg' => '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
                'xl' => '0 20px 25px -5px rgba(0, 0, 0, 0.1)'
            ]
        ];

        return array_merge_recursive($default, $this->theme_config['base'] ?? []);
    }

    /**
     * Get modes configuration with clear structure
     */
    public function getModesConfiguration(): array
    {
        $default = [
            'light' => [
                'ui' => [
                    'name' => 'Light Mode',
                    'description' => 'Clean and bright interface',
                    'icon' => 'sun',
                    'type' => 'light'
                ],
                'colors' => [
                    // Backgrounds
                    'background_primary' => '#ffffff',
                    'background_secondary' => '#f8fafc',
                    'background_tertiary' => '#f1f5f9',
                    'surface' => '#ffffff',
                    'surface_variant' => '#f8fafc',
                    'overlay' => 'rgba(0, 0, 0, 0.5)',

                    // Text colors
                    'text_primary' => '#0f172a',
                    'text_secondary' => '#475569',
                    'text_tertiary' => '#64748b',
                    'text_on_primary' => '#0f172a',
                    'text_on_brand' => '#ffffff',
                    'text_on_accent' => '#ffffff',
                    'text_disabled' => '#9ca3af',

                    // Brand colors
                    'brand_primary' => '#3b82f6',
                    'brand_secondary' => '#1e40af',
                    'brand_light' => '#dbeafe',

                    // Accent colors
                    'accent_primary' => '#8b5cf6',
                    'accent_secondary' => '#7c3aed',
                    'accent_light' => '#ede9fe',

                    // Borders
                    'border_primary' => '#e2e8f0',
                    'border_secondary' => '#cbd5e1',
                    'border_focus' => '#3b82f6',

                    // States
                    'success' => '#10b981',
                    'success_light' => '#dcfce7',
                    'warning' => '#f59e0b',
                    'warning_light' => '#fef3c7',
                    'danger' => '#ef4444',
                    'danger_light' => '#fee2e2',
                    'info' => '#3b82f6',
                    'info_light' => '#dbeafe',

                    // Interactive states
                    'hover' => '#f1f5f9',
                    'active' => '#e2e8f0',
                    'focus' => '#3b82f6',
                    'disabled' => '#f1f5f9',
                ]
            ],
            'dark' => [
                'ui' => [
                    'name' => 'Real Dark',
                    'description' => 'Minimal, modern, and high contrast',
                    'icon' => 'moon-stars',
                    'type' => 'dark'
                ],
                'colors' => [
                    // Backgrounds
                    'background_primary' => '#0b0f19',
                    'background_secondary' => '#111827',
                    'background_tertiary' => '#1f2937',
                    'surface' => '#111827',
                    'surface_variant' => '#1f2937',
                    'overlay' => 'rgba(15, 23, 42, 0.85)',

                    // Text colors
                    'text_primary' => '#f9fafb',
                    'text_secondary' => '#d1d5db',
                    'text_tertiary' => '#9ca3af',
                    'text_on_primary' => '#ffffff',
                    'text_on_brand' => '#ffffff',
                    'text_on_accent' => '#ffffff',
                    'text_disabled' => '#6b7280',

                    // Brand colors
                    'brand_primary' => '#3b82f6',
                    'brand_secondary' => '#4f46e5',
                    'brand_light' => '#3730a3',

                    // Accent colors
                    'accent_primary' => '#ec4899',
                    'accent_secondary' => '#db2777',
                    'accent_light' => '#9d174d',

                    // Borders
                    'border_primary' => '#374151',
                    'border_secondary' => '#4b5563',
                    'border_focus' => '#6366f1',

                    // States
                    'success' => '#22c55e',
                    'success_light' => '#14532d',
                    'warning' => '#eab308',
                    'warning_light' => '#78350f',
                    'danger' => '#ef4444',
                    'danger_light' => '#7f1d1d',
                    'info' => '#3b82f6',
                    'info_light' => '#1e3a8a',

                    // Interactive states
                    'hover' => '#1f2937',
                    'active' => '#374151',
                    'focus' => '#6366f1',
                    'disabled' => '#1f2937',
                ]
            ]

        ];

        return array_merge_recursive($default, $this->theme_config['modes'] ?? []);
    }

    /**
     * Get available modes (excluding system preference)
     */
    public function getAvailableModes(): array
    {
        return array_keys($this->getModesConfiguration());
    }

    /**
     * Get colors for specific mode with fallback
     */
    public function getColorsForMode(string $mode): array
    {
        $modes = $this->getModesConfiguration();

        if (!isset($modes[$mode])) {
            $mode = 'light'; // fallback to light
        }

        return $modes[$mode]['colors'] ?? [];
    }

    /**
     * Get mode UI configuration
     */
    public function getModeUiConfig(string $mode): array
    {
        $modes = $this->getModesConfiguration();

        if (!isset($modes[$mode])) {
            $mode = 'light'; // fallback to light
        }

        return $modes[$mode]['ui'] ?? [];
    }

    /**
     * Get typography configuration
     */
    public function getTypography(): array
    {
        return $this->theme_config['typography'] ?? [
            'fonts' => [
                'primary' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                'heading' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                'mono' => '"SF Mono", Monaco, "Cascadia Code", monospace'
            ],
            'sizes' => [
                'xs' => 12, 'sm' => 14, 'base' => 16, 'lg' => 18,
                'xl' => 20, '2xl' => 24, '3xl' => 30, '4xl' => 36
            ],
            'weights' => [
                'light' => 300, 'normal' => 400, 'medium' => 500,
                'semibold' => 600, 'bold' => 700
            ]
        ];
    }

    /**
     * Get spacing configuration
     */
    public function getSpacing(): array
    {
        return $this->theme_config['spacing'] ?? [
            'xs' => 4, 'sm' => 8, 'md' => 16, 'lg' => 24,
            'xl' => 32, '2xl' => 48, '3xl' => 64
        ];
    }

    /**
     * Get border radius configuration
     */
    public function getRadius(): array
    {
        return $this->theme_config['radius'] ?? [
            'none' => 0, 'sm' => 4, 'md' => 8, 'lg' => 12,
            'xl' => 16, 'full' => 9999
        ];
    }

    /**
     * Get shadows configuration
     */
    public function getShadows(): array
    {
        return $this->theme_config['shadows'] ?? [
            'sm' => '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
            'md' => '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
            'lg' => '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
            'xl' => '0 20px 25px -5px rgba(0, 0, 0, 0.1)'
        ];
    }

    /**
     * Get all colors for all modes
     */
    public function getColors(): array
    {
        $allColors = [];
        $modes = $this->getModesConfiguration();

        foreach ($modes as $mode => $config) {
            $allColors[$mode] = $config['colors'] ?? [];
        }

        return $allColors;
    }

    /**
     * Get mode configuration (alias for getModesConfiguration)
     */
    public function getModeConfiguration(): array
    {
        return $this->getModesConfiguration();
    }


    /**
     * Get active theme for project
     */
    public function scopeActiveForProject($query, int $projectId)
    {
        return $query->where('project_id', $projectId)->where('is_active', true);
    }

    /**
     * Create ThemeQuery builder
     */
    public function themeQuery(): \Webkernel\Aptitudes\WebsiteBuilder\Services\ThemeQuery
    {
        return new \Webkernel\Aptitudes\WebsiteBuilder\Services\ThemeQuery($this);
    }

    /**
     * Get active theme for project
     */
    public static function getActiveTheme(int $projectId): ?self
    {
        return static::activeForProject($projectId)->first();
    }

    /**
     * Register minimal helpers for theme
     */
    public static function registerMinimalHelpers(self $theme, string $siteId): void
    {
        // This method can be implemented to register any minimal theme helpers
        // For now, it's a placeholder to prevent errors
    }

    /**
     * Boot model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($theme) {
            if ($theme->is_default) {
                static::where('project_id', $theme->project_id)
                    ->where('id', '!=', $theme->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}
