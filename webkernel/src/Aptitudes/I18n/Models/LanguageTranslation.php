<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\I18n\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property int $language_id
 * @property int $tenant_id
 * @property string $reference
 * @property string $value
 * @property string $app
 * @property string $theme
 * @property string|null $module
 * @property int|null $category_id
 * @property string $content_type
 * @property array<array-key, mixed>|null $metadata
 * @property bool $is_system
 * @property bool $needs_review
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Webkernel\Aptitudes\I18n\Models\LanguageTranslationCategory|null $category
 * @property-read \Webkernel\Aptitudes\I18n\Models\Language $language
 * @method static Builder<static>|LanguageTranslation byApp(string $app)
 * @method static Builder<static>|LanguageTranslation byContentType(string $contentType)
 * @method static Builder<static>|LanguageTranslation byModule(string $module)
 * @method static Builder<static>|LanguageTranslation byReference(string $reference)
 * @method static Builder<static>|LanguageTranslation byTheme(string $theme)
 * @method static Builder<static>|LanguageTranslation forLanguage(string $languageCode, ?int $tenantId = null)
 * @method static Builder<static>|LanguageTranslation forTenant(int $tenantId)
 * @method static Builder<static>|LanguageTranslation needsReview()
 * @method static Builder<static>|LanguageTranslation newModelQuery()
 * @method static Builder<static>|LanguageTranslation newQuery()
 * @method static Builder<static>|LanguageTranslation query()
 * @method static Builder<static>|LanguageTranslation recentlyUsed(int $days = 30)
 * @method static Builder<static>|LanguageTranslation reviewed()
 * @method static Builder<static>|LanguageTranslation system()
 * @method static Builder<static>|LanguageTranslation unused()
 * @method static Builder<static>|LanguageTranslation user()
 * @method static Builder<static>|LanguageTranslation whereApp($value)
 * @method static Builder<static>|LanguageTranslation whereCategoryId($value)
 * @method static Builder<static>|LanguageTranslation whereContentType($value)
 * @method static Builder<static>|LanguageTranslation whereCreatedAt($value)
 * @method static Builder<static>|LanguageTranslation whereId($value)
 * @method static Builder<static>|LanguageTranslation whereIsSystem($value)
 * @method static Builder<static>|LanguageTranslation whereLanguageId($value)
 * @method static Builder<static>|LanguageTranslation whereLastUsedAt($value)
 * @method static Builder<static>|LanguageTranslation whereMetadata($value)
 * @method static Builder<static>|LanguageTranslation whereModule($value)
 * @method static Builder<static>|LanguageTranslation whereNeedsReview($value)
 * @method static Builder<static>|LanguageTranslation whereReference($value)
 * @method static Builder<static>|LanguageTranslation whereTenantId($value)
 * @method static Builder<static>|LanguageTranslation whereTheme($value)
 * @method static Builder<static>|LanguageTranslation whereUpdatedAt($value)
 * @method static Builder<static>|LanguageTranslation whereValue($value)
 * @mixin \Eloquent
 */
class LanguageTranslation extends Model
{
    protected $table = 'apt_translations';

    protected $fillable = [
        'language_id',
        'tenant_id',
        'reference',
        'value',
        'app',
        'theme',
        'module',
        'category_id',
        'content_type',
        'metadata',
        'is_system',
        'needs_review',
        'last_used_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_system' => 'boolean',
        'needs_review' => 'boolean',
        'last_used_at' => 'datetime',
        'language_id' => 'integer',
        'tenant_id' => 'integer',
        'category_id' => 'integer',
    ];

    protected $attributes = [
        'app' => 'core',
        'theme' => 'default',
        'content_type' => 'text',
        'is_system' => false,
        'needs_review' => false,
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
     * Get translation by reference and language
     */
    public static function getTranslation(
        string $reference,
        string $languageCode,
        ?string $app = 'core',
        ?string $theme = 'default',
        ?string $module = null,
        ?int $tenantId = null
    ): ?string {
        $tenantId = $tenantId ?? static::getCurrentTenantId();
        $cacheKey = "translation_{$reference}_{$languageCode}_{$app}_{$theme}_{$module}_tenant_{$tenantId}";

        return Cache::remember($cacheKey, 3600, function () use ($reference, $languageCode, $app, $theme, $module, $tenantId) {
            $language = Language::getByCode($languageCode, $tenantId);

            if (!$language) {
                return null;
            }

            $translation = static::query()
                ->where('language_id', $language->id)
                ->where('tenant_id', $tenantId)
                ->where('reference', $reference)
                ->where('app', $app)
                ->where('theme', $theme)
                ->when($module, fn($q) => $q->where('module', $module))
                ->first();

            if ($translation) {
                // Update last used timestamp
                $translation->update(['last_used_at' => now()]);
                return $translation->value;
            }

            return null;
        });
    }

    /**
     * Get or create translation
     */
    public static function getOrCreateTranslation(
        string $reference,
        string $languageCode,
        string $value,
        ?string $app = 'core',
        ?string $theme = 'default',
        ?string $module = null,
        ?int $tenantId = null
    ): self {
        $tenantId = $tenantId ?? static::getCurrentTenantId();
        $language = Language::getByCode($languageCode, $tenantId);

        if (!$language) {
            throw new \InvalidArgumentException("Language '{$languageCode}' not found for tenant {$tenantId}");
        }

        $translation = static::query()
            ->where('language_id', $language->id)
            ->where('tenant_id', $tenantId)
            ->where('reference', $reference)
            ->where('app', $app)
            ->where('theme', $theme)
            ->when($module, fn($q) => $q->where('module', $module))
            ->first();

        if (!$translation) {
            $translation = static::create([
                'language_id' => $language->id,
                'tenant_id' => $tenantId,
                'reference' => $reference,
                'value' => $value,
                'app' => $app,
                'theme' => $theme,
                'module' => $module,
            ]);
        } else {
            $translation->update(['value' => $value]);
        }

        return $translation;
    }

    /**
     * Get translations for a specific language
     */
    public static function getTranslationsForLanguage(
        string $languageCode,
        ?string $app = 'core',
        ?string $theme = 'default',
        ?string $module = null,
        ?int $tenantId = null
    ): \Illuminate\Support\Collection {
        $tenantId = $tenantId ?? static::getCurrentTenantId();
        $language = Language::getByCode($languageCode, $tenantId);

        if (!$language) {
            return collect();
        }

        $cacheKey = "translations_lang_{$languageCode}_{$app}_{$theme}_{$module}_tenant_{$tenantId}";

        return Cache::remember($cacheKey, 3600, function () use ($language, $app, $theme, $module, $tenantId) {
            return static::query()
                ->where('language_id', $language->id)
                ->where('tenant_id', $tenantId)
                ->where('app', $app)
                ->where('theme', $theme)
                ->when($module, fn($q) => $q->where('module', $module))
                ->get()
                ->pluck('value', 'reference');
        });
    }

    /**
     * Get translations grouped by reference
     */
    public static function getTranslationsByReference(
        string $reference,
        ?string $app = 'core',
        ?string $theme = 'default',
        ?string $module = null,
        ?int $tenantId = null
    ): \Illuminate\Support\Collection {
        $tenantId = $tenantId ?? static::getCurrentTenantId();
        $cacheKey = "translations_ref_{$reference}_{$app}_{$theme}_{$module}_tenant_{$tenantId}";

        return Cache::remember($cacheKey, 3600, function () use ($reference, $app, $theme, $module, $tenantId) {
            return static::query()
                ->where('tenant_id', $tenantId)
                ->where('reference', $reference)
                ->where('app', $app)
                ->where('theme', $theme)
                ->when($module, fn($q) => $q->where('module', $module))
                ->with('language')
                ->get()
                ->pluck('value', 'language.code');
        });
    }

    /**
     * Mark translation as needing review
     */
    public function markForReview(): bool
    {
        return $this->update(['needs_review' => true]);
    }

    /**
     * Mark translation as reviewed
     */
    public function markAsReviewed(): bool
    {
        return $this->update(['needs_review' => false]);
    }

    /**
     * Check if translation is HTML content
     */
    public function isHtml(): bool
    {
        return $this->content_type === 'html';
    }

    /**
     * Check if translation is Markdown content
     */
    public function isMarkdown(): bool
    {
        return $this->content_type === 'markdown';
    }

    /**
     * Check if translation is JSON content
     */
    public function isJson(): bool
    {
        return $this->content_type === 'json';
    }

    /**
     * Check if translation is plain text
     */
    public function isText(): bool
    {
        return $this->content_type === 'text';
    }

    /**
     * Get metadata value
     */
    public function getMeta(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Set metadata value
     */
    public function setMeta(string $key, mixed $value): self
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Clear cache
     */
    public static function clearCache(): void
    {
        $patterns = [
            'translation_*',
            'translations_lang_*',
            'translations_ref_*',
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
    public function scopeForLanguage(Builder $query, string $languageCode, ?int $tenantId = null): Builder
    {
        $tenantId = $tenantId ?? static::getCurrentTenantId();
        $language = Language::getByCode($languageCode, $tenantId);

        if (!$language) {
            return $query->whereRaw('1 = 0'); // Return empty result
        }

        return $query->where('language_id', $language->id);
    }

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByReference(Builder $query, string $reference): Builder
    {
        return $query->where('reference', $reference);
    }

    public function scopeByApp(Builder $query, string $app): Builder
    {
        return $query->where('app', $app);
    }

    public function scopeByTheme(Builder $query, string $theme): Builder
    {
        return $query->where('theme', $theme);
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

    public function scopeNeedsReview(Builder $query): Builder
    {
        return $query->where('needs_review', true);
    }

    public function scopeReviewed(Builder $query): Builder
    {
        return $query->where('needs_review', false);
    }

    public function scopeByContentType(Builder $query, string $contentType): Builder
    {
        return $query->where('content_type', $contentType);
    }

    public function scopeRecentlyUsed(Builder $query, int $days = 30): Builder
    {
        return $query->where('last_used_at', '>=', now()->subDays($days));
    }

    public function scopeUnused(Builder $query): Builder
    {
        return $query->whereNull('last_used_at');
    }

    /**
     * Relationships
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LanguageTranslationCategory::class, 'category_id');
    }

    /**
     * Statistics
     */
    public static function getStats(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? static::getCurrentTenantId();

        return [
            'total_translations' => static::where('tenant_id', $tenantId)->count(),
            'system_translations' => static::where('tenant_id', $tenantId)->where('is_system', true)->count(),
            'user_translations' => static::where('tenant_id', $tenantId)->where('is_system', false)->count(),
            'needs_review' => static::where('tenant_id', $tenantId)->where('needs_review', true)->count(),
            'recently_used' => static::where('tenant_id', $tenantId)->where('last_used_at', '>=', now()->subDays(30))->count(),
            'unused' => static::where('tenant_id', $tenantId)->whereNull('last_used_at')->count(),
            'by_content_type' => static::where('tenant_id', $tenantId)
                ->selectRaw('content_type, COUNT(*) as count')
                ->groupBy('content_type')
                ->pluck('count', 'content_type')
                ->toArray(),
            'by_app' => static::where('tenant_id', $tenantId)
                ->selectRaw('app, COUNT(*) as count')
                ->groupBy('app')
                ->pluck('count', 'app')
                ->toArray(),
        ];
    }
}
