<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\I18n\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

/**
 * Language Model
 * 
 * Optimized for high-performance multi-tenant language management.
 * Cache-first architecture with automatic invalidation.
 *
 * @property int $id
 * @property string $code
 * @property string $iso
 * @property string $label
 * @property string|null $native_label
 * @property string $direction
 * @property bool $active
 * @property bool $is_default
 * @property int $tenant_id
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\I18n\Models\LanguageTranslation> $translations
 * @property-read int|null $translations_count
 * @method static Builder<static>|Language active()
 * @method static Builder<static>|Language byCode(string $code)
 * @method static Builder<static>|Language byIso(string $iso)
 * @method static Builder<static>|Language default()
 * @method static Builder<static>|Language forTenant(int $tenantId)
 * @method static Builder<static>|Language ltr()
 * @method static Builder<static>|Language newModelQuery()
 * @method static Builder<static>|Language newQuery()
 * @method static Builder<static>|Language query()
 * @method static Builder<static>|Language rtl()
 * @method static Builder<static>|Language whereActive($value)
 * @method static Builder<static>|Language whereCode($value)
 * @method static Builder<static>|Language whereCreatedAt($value)
 * @method static Builder<static>|Language whereDirection($value)
 * @method static Builder<static>|Language whereId($value)
 * @method static Builder<static>|Language whereIsDefault($value)
 * @method static Builder<static>|Language whereIso($value)
 * @method static Builder<static>|Language whereLabel($value)
 * @method static Builder<static>|Language whereMetadata($value)
 * @method static Builder<static>|Language whereNativeLabel($value)
 * @method static Builder<static>|Language whereTenantId($value)
 * @method static Builder<static>|Language whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Language extends Model
{
  protected $table = 'apt_languages';

  protected $fillable = [
    'code',
    'iso',
    'label',
    'native_label',
    'direction',
    'active',
    'is_default',
    'tenant_id',
    'metadata',
  ];

  protected $casts = [
    'metadata' => 'array',
    'active' => 'boolean',
    'is_default' => 'boolean',
    'tenant_id' => 'integer',
  ];

  protected $attributes = [
    'direction' => 'ltr',
    'active' => true,
    'is_default' => false,
  ];

  private const CACHE_TTL = 3600;
  private const CACHE_PREFIX = 'lang:';

  /**
   * Boot the model with automatic cache invalidation
   *
   * @return void
   */
  protected static function boot(): void
  {
    parent::boot();

    static::saved(fn() => static::clearCache());
    static::deleted(fn() => static::clearCache());
  }

  /**
   * Get all active languages for a tenant
   * Cached with tenant isolation
   *
   * @param int|null $tenantId
   * @return Collection<int, self>
   */
  public static function getActiveLanguages(?int $tenantId = null): Collection
  {
    $tenantId = $tenantId ?? static::getCurrentTenantId();
    $cacheKey = self::CACHE_PREFIX . "active_tenant_{$tenantId}";

    return Cache::remember(
      $cacheKey,
      self::CACHE_TTL,
      fn() => static::query()
        ->where('tenant_id', $tenantId)
        ->where('active', true)
        ->orderBy('is_default', 'desc')
        ->orderBy('label')
        ->get(),
    );
  }

  /**
   * Get the default language for a tenant
   *
   * @param int|null $tenantId
   * @return self|null
   */
  public static function getDefaultLanguage(?int $tenantId = null): ?self
  {
    $tenantId = $tenantId ?? static::getCurrentTenantId();
    $cacheKey = self::CACHE_PREFIX . "default_tenant_{$tenantId}";

    return Cache::remember(
      $cacheKey,
      self::CACHE_TTL,
      fn() => static::query()->where('tenant_id', $tenantId)->where('is_default', true)->where('active', true)->first(),
    );
  }

  /**
   * Get language by code with cache-aside pattern
   *
   * @param string $code
   * @param int|null $tenantId
   * @return self|null
   */
  public static function getByCode(string $code, ?int $tenantId = null): ?self
  {
    $tenantId = $tenantId ?? static::getCurrentTenantId();
    $cacheKey = self::CACHE_PREFIX . "code_{$code}_tenant_{$tenantId}";

    return Cache::remember(
      $cacheKey,
      self::CACHE_TTL,
      fn() => static::query()->where('tenant_id', $tenantId)->where('code', $code)->where('active', true)->first(),
    );
  }

  /**
   * Get language by ISO code
   *
   * @param string $iso
   * @param int|null $tenantId
   * @return self|null
   */
  public static function getByIso(string $iso, ?int $tenantId = null): ?self
  {
    $tenantId = $tenantId ?? static::getCurrentTenantId();
    $cacheKey = self::CACHE_PREFIX . "iso_{$iso}_tenant_{$tenantId}";

    return Cache::remember(
      $cacheKey,
      self::CACHE_TTL,
      fn() => static::query()->where('tenant_id', $tenantId)->where('iso', $iso)->where('active', true)->first(),
    );
  }

  /**
   * Get language options for select dropdowns
   *
   * @param int|null $tenantId
   * @return array<int, string>
   */
  public static function getOptions(?int $tenantId = null): array
  {
    return static::getActiveLanguages($tenantId)->pluck('label', 'id')->toArray();
  }

  /**
   * Get language options with codes as keys
   *
   * @param int|null $tenantId
   * @return array<string, string>
   */
  public static function getOptionsWithCodes(?int $tenantId = null): array
  {
    return static::getActiveLanguages($tenantId)->pluck('label', 'code')->toArray();
  }

  /**
   * Set as default language with atomic transaction
   * Ensures only one default per tenant
   *
   * @return bool
   */
  public function setAsDefault(): bool
  {
    return \DB::transaction(function () {
      static::query()
        ->where('tenant_id', $this->tenant_id)
        ->where('id', '!=', $this->id)
        ->update(['is_default' => false]);

      $this->is_default = true;
      return $this->save();
    });
  }

  /**
   * Check if this is a right-to-left language
   *
   * @return bool
   */
  public function isRtl(): bool
  {
    return $this->direction === 'rtl';
  }

  /**
   * Check if this is a left-to-right language
   *
   * @return bool
   */
  public function isLtr(): bool
  {
    return $this->direction === 'ltr';
  }

  /**
   * Get display name with native label preference
   *
   * @return string
   */
  public function getDisplayName(): string
  {
    return $this->native_label ?: $this->label;
  }

  /**
   * Get metadata value with type safety
   *
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function getMeta(string $key, mixed $default = null): mixed
  {
    return is_array($this->metadata) ? $this->metadata[$key] ?? $default : $default;
  }

  /**
   * Set metadata value immutably
   *
   * @param string $key
   * @param mixed $value
   * @return self
   */
  public function setMeta(string $key, mixed $value): self
  {
    $metadata = is_array($this->metadata) ? $this->metadata : [];
    $metadata[$key] = $value;
    $this->metadata = $metadata;

    return $this;
  }

  /**
   * Get flag SVG path
   *
   * @return string
   */
  public function getFlagPath(): string
  {
    return "webkernel/src/Aptitudes/I18n/Resources/Assets/flags/language/{$this->code}.svg";
  }

  /**
   * Render flag SVG with error handling
   *
   * @param array<string, mixed> $options
   * @return string
   */
  public function renderFlagSvg(array $options = []): string
  {
    try {
      $uiQuery = app(\Webkernel\Aptitudes\Base\Resources\Views\components\UIQuery\UIQuery::class);

      $defaultOptions = [
        'inject-class' => 'fi-icon',
        'inject-style' => 'height: 20px; width: 20px;',
        'return' => 'raw',
      ];

      $mergedOptions = array_merge($defaultOptions, $options);
      $flagPath = $this->getFlagPath();

      if (!file_exists(base_path($flagPath))) {
        \Log::warning("Flag file not found: {$flagPath}");
        return $this->getFallbackFlagSvg();
      }

      $result = $uiQuery->renderAssetByPath($flagPath, $mergedOptions);

      return empty($result) ? $this->getFallbackFlagSvg() : $result;
    } catch (\Exception $e) {
      \Log::error("Error rendering flag SVG for language {$this->code}: {$e->getMessage()}");
      return $this->getFallbackFlagSvg();
    }
  }

  /**
   * Get fallback flag SVG
   *
   * @return string
   */
  protected function getFallbackFlagSvg(): string
  {
    return '<svg class="fi-icon" style="height: 20px; width: 20px;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6z"/>
                </svg>';
  }

  /**
   * Get language selector data for UI components
   *
   * @param int|null $tenantId
   * @return array{languages: Collection, current_code: string, current_label: string, current_language: self|null}
   */
  public static function getSelectorData(?int $tenantId = null): array
  {
    try {
      $languages = static::getActiveLanguages($tenantId);
      $currentLanguage = static::getCurrentUserLanguage();
      $currentLangObject = $languages->firstWhere('code', $currentLanguage);

      return [
        'languages' => $languages,
        'current_code' => $currentLanguage,
        'current_label' => $currentLangObject?->getDisplayName() ?? $currentLanguage,
        'current_language' => $currentLangObject,
      ];
    } catch (\Exception $e) {
      \Log::error("Error getting language selector data: {$e->getMessage()}");

      return [
        'languages' => collect([]),
        'current_code' => 'en',
        'current_label' => 'English',
        'current_language' => null,
      ];
    }
  }

  /**
   * Get current user language code
   *
   * @return string
   */
  public static function getCurrentUserLanguage(): string
  {
    try {
      return \Webkernel\Aptitudes\Users\Models\UserExtensions\UserPreference::getCurrentUserLanguage();
    } catch (\Exception $e) {
      \Log::error("Error getting current user language: {$e->getMessage()}");
      return 'en';
    }
  }

  /**
   * Clear all language caches with pattern matching
   *
   * @return void
   */
  public static function clearCache(): void
  {
    $patterns = [
      self::CACHE_PREFIX . 'active_tenant_*',
      self::CACHE_PREFIX . 'default_tenant_*',
      self::CACHE_PREFIX . 'code_*',
      self::CACHE_PREFIX . 'iso_*',
    ];

    foreach ($patterns as $pattern) {
      Cache::forget($pattern);
    }
  }

  /**
   * Get current tenant ID
   * Override this method to integrate with your tenant system
   *
   * @return int
   */
  protected static function getCurrentTenantId(): int
  {
    return 1;
  }

  /**
   * Scope: active languages only
   *
   * @param Builder<self> $query
   * @return Builder<self>
   */
  public function scopeActive(Builder $query): Builder
  {
    return $query->where('active', true);
  }

  /**
   * Scope: default language
   *
   * @param Builder<self> $query
   * @return Builder<self>
   */
  public function scopeDefault(Builder $query): Builder
  {
    return $query->where('is_default', true);
  }

  /**
   * Scope: filter by tenant
   *
   * @param Builder<self> $query
   * @param int $tenantId
   * @return Builder<self>
   */
  public function scopeForTenant(Builder $query, int $tenantId): Builder
  {
    return $query->where('tenant_id', $tenantId);
  }

  /**
   * Scope: filter by code
   *
   * @param Builder<self> $query
   * @param string $code
   * @return Builder<self>
   */
  public function scopeByCode(Builder $query, string $code): Builder
  {
    return $query->where('code', $code);
  }

  /**
   * Scope: filter by ISO code
   *
   * @param Builder<self> $query
   * @param string $iso
   * @return Builder<self>
   */
  public function scopeByIso(Builder $query, string $iso): Builder
  {
    return $query->where('iso', $iso);
  }

  /**
   * Scope: RTL languages
   *
   * @param Builder<self> $query
   * @return Builder<self>
   */
  public function scopeRtl(Builder $query): Builder
  {
    return $query->where('direction', 'rtl');
  }

  /**
   * Scope: LTR languages
   *
   * @param Builder<self> $query
   * @return Builder<self>
   */
  public function scopeLtr(Builder $query): Builder
  {
    return $query->where('direction', 'ltr');
  }

  /**
   * Relationship: translations
   *
   * @return HasMany<LanguageTranslation>
   */
  public function translations(): HasMany
  {
    return $this->hasMany(LanguageTranslation::class, 'language_id');
  }

  /**
   * Get language statistics for a tenant
   *
   * @param int|null $tenantId
   * @return array{total_languages: int, active_languages: int, default_language: string|null, rtl_languages: int, ltr_languages: int}
   */
  public static function getStats(?int $tenantId = null): array
  {
    $tenantId = $tenantId ?? static::getCurrentTenantId();

    return [
      'total_languages' => static::where('tenant_id', $tenantId)->count(),
      'active_languages' => static::where('tenant_id', $tenantId)->where('active', true)->count(),
      'default_language' => static::where('tenant_id', $tenantId)->where('is_default', true)->first()?->label,
      'rtl_languages' => static::where('tenant_id', $tenantId)->where('direction', 'rtl')->count(),
      'ltr_languages' => static::where('tenant_id', $tenantId)->where('direction', 'ltr')->count(),
    ];
  }
}
