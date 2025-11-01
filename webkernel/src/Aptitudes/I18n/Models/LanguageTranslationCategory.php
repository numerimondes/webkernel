<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\I18n\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $app
 * @property string|null $module
 * @property int $tenant_id
 * @property bool $is_system
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\I18n\Models\LanguageTranslation> $translations
 * @property-read int|null $translations_count
 * @method static Builder<static>|LanguageTranslationCategory byApp(string $app)
 * @method static Builder<static>|LanguageTranslationCategory byModule(string $module)
 * @method static Builder<static>|LanguageTranslationCategory bySlug(string $slug)
 * @method static Builder<static>|LanguageTranslationCategory forTenant(int $tenantId)
 * @method static Builder<static>|LanguageTranslationCategory newModelQuery()
 * @method static Builder<static>|LanguageTranslationCategory newQuery()
 * @method static Builder<static>|LanguageTranslationCategory query()
 * @method static Builder<static>|LanguageTranslationCategory system()
 * @method static Builder<static>|LanguageTranslationCategory user()
 * @method static Builder<static>|LanguageTranslationCategory whereApp($value)
 * @method static Builder<static>|LanguageTranslationCategory whereCreatedAt($value)
 * @method static Builder<static>|LanguageTranslationCategory whereDescription($value)
 * @method static Builder<static>|LanguageTranslationCategory whereId($value)
 * @method static Builder<static>|LanguageTranslationCategory whereIsSystem($value)
 * @method static Builder<static>|LanguageTranslationCategory whereModule($value)
 * @method static Builder<static>|LanguageTranslationCategory whereName($value)
 * @method static Builder<static>|LanguageTranslationCategory whereSlug($value)
 * @method static Builder<static>|LanguageTranslationCategory whereTenantId($value)
 * @method static Builder<static>|LanguageTranslationCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LanguageTranslationCategory extends Model
{
    protected $table = 'apt_translation_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'app',
        'module',
        'tenant_id',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'tenant_id' => 'integer',
    ];

    protected $attributes = [
        'app' => 'core',
        'is_system' => false,
    ];

    /**
     * Boot the model
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Get category by slug
     */
    public static function getBySlug(string $slug, ?int $tenantId = null): ?self
    {
        $tenantId = $tenantId ?? static::getCurrentTenantId();
        $cacheKey = "translation_category_{$slug}_tenant_{$tenantId}";

        return Cache::remember($cacheKey, 3600, function () use ($slug, $tenantId) {
            return static::query()
                ->where('tenant_id', $tenantId)
                ->where('slug', $slug)
                ->first();
        });
    }

    /**
     * Get categories for a specific app
     */
    public static function getCategoriesForApp(string $app, ?int $tenantId = null): \Illuminate\Support\Collection
    {
        $tenantId = $tenantId ?? static::getCurrentTenantId();
        $cacheKey = "translation_categories_app_{$app}_tenant_{$tenantId}";

        return Cache::remember($cacheKey, 3600, function () use ($app, $tenantId) {
            return static::query()
                ->where('tenant_id', $tenantId)
                ->where('app', $app)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get categories for a specific module
     */
    public static function getCategoriesForModule(string $module, ?int $tenantId = null): \Illuminate\Support\Collection
    {
        $tenantId = $tenantId ?? static::getCurrentTenantId();
        $cacheKey = "translation_categories_module_{$module}_tenant_{$tenantId}";

        return Cache::remember($cacheKey, 3600, function () use ($module, $tenantId) {
            return static::query()
                ->where('tenant_id', $tenantId)
                ->where('module', $module)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get category options for select dropdowns
     */
    public static function getOptions(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? static::getCurrentTenantId();
        $cacheKey = "translation_category_options_tenant_{$tenantId}";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId) {
            return static::query()
                ->where('tenant_id', $tenantId)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        });
    }

    /**
     * Get or create category
     */
    public static function getOrCreateCategory(
        string $name,
        string $slug,
        ?string $description = null,
        ?string $app = 'core',
        ?string $module = null,
        ?int $tenantId = null
    ): self {
        $tenantId = $tenantId ?? static::getCurrentTenantId();

        $category = static::query()
            ->where('tenant_id', $tenantId)
            ->where('slug', $slug)
            ->first();

        if (!$category) {
            $category = static::create([
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'app' => $app,
                'module' => $module,
                'tenant_id' => $tenantId,
            ]);
        }

        return $category;
    }

    /**
     * Clear cache
     */
    public static function clearCache(): void
    {
        $patterns = [
            'translation_category_*',
            'translation_categories_app_*',
            'translation_categories_module_*',
            'translation_category_options_*',
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Get current tenant ID (implement based on your tenant system)
     */
    protected static function getCurrentTenantId(): int
    {
        // This should be implemented based on your tenant system
        // For now, return 1 as default
        return 1;
    }

    /**
     * Scopes
     */
    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    public function scopeByApp(Builder $query, string $app): Builder
    {
        return $query->where('app', $app);
    }

    public function scopeByModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }

    public function scopeSystem(Builder $query): Builder
    {
        return $query->where('is_system', true);
    }

    public function scopeUser(Builder $query): Builder
    {
        return $query->where('is_system', false);
    }

    /**
     * Relationships
     */
    public function translations(): HasMany
    {
        return $this->hasMany(LanguageTranslation::class, 'category_id');
    }

    /**
     * Statistics
     */
    public static function getStats(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? static::getCurrentTenantId();

        return [
            'total_categories' => static::where('tenant_id', $tenantId)->count(),
            'system_categories' => static::where('tenant_id', $tenantId)->where('is_system', true)->count(),
            'user_categories' => static::where('tenant_id', $tenantId)->where('is_system', false)->count(),
            'by_app' => static::where('tenant_id', $tenantId)
                ->selectRaw('app, COUNT(*) as count')
                ->groupBy('app')
                ->pluck('count', 'app')
                ->toArray(),
            'by_module' => static::where('tenant_id', $tenantId)
                ->whereNotNull('module')
                ->selectRaw('module, COUNT(*) as count')
                ->groupBy('module')
                ->pluck('count', 'module')
                ->toArray(),
        ];
    }
}
