<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\Models\UserExtensions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Webkernel\Aptitudes\Users\Models\Timezone;
use Illuminate\Support\Facades\Cache;

/**
 * User Preference Model
 * 
 * High-performance user preference management with multi-layer caching.
 * Optimized for sub-microsecond response times using in-memory cache.
 *
 * @property int $id
 * @property int $user_id
 * @property string $theme_name
 * @property int $timezone_id
 * @property string $user_lang
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Carbon\Carbon $current_time
 * @property-read string $formatted_current_time
 * @property-read Timezone $timezone
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPreference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPreference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPreference query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPreference whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPreference whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPreference whereThemeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPreference whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPreference whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPreference whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPreference whereUserLang($value)
 * @mixin \Eloquent
 */
class UserPreference extends Model
{
  private static array $cacheClearedForUsers = [];

  protected $table = 'users_ext_pref';

  protected $fillable = ['user_id', 'theme_name', 'timezone_id', 'user_lang'];

  protected $casts = [
    'user_lang' => 'string',
    'timezone_id' => 'integer',
    'user_id' => 'integer',
  ];

  protected $attributes = [
    'timezone_id' => 46,
    'theme_name' => 'monochromatic-webkernel-css',
    'user_lang' => 'en',
  ];

  /**
   * In-memory cache for ultra-fast access
   * Critical for sub-microsecond performance target
   *
   * @var array<int, self>
   */
  private static array $userPreferencesCache = [];

  private const CACHE_TTL = 3600;
  private const CACHE_PREFIX = 'user_pref:';

  /**
   * Boot model with cache invalidation hooks
   *
   * @return void
   */
  protected static function boot(): void
  {
    parent::boot();

    static::saved(function (self $model) {
      static::clearUserPreferencesCache($model->user_id); // This now only clears the in-memory cache

      // Only clear the language preference cache if the language has actually changed
      // and it hasn't been cleared for this user in the current request.
      if ($model->wasChanged('user_lang')) {
        $userId = $model->user_id;
        if (!isset(self::$cacheClearedForUsers[$userId])) {
          Cache::forget(self::CACHE_PREFIX . "lang:{$userId}");
          self::$cacheClearedForUsers[$userId] = true;
        }
      }
    });

    static::deleted(function (self $model) {
      static::clearUserPreferencesCache($model->user_id);
    });
  }

  /**
   * Relationship: user
   *
   * @return BelongsTo<User, self>
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Relationship: timezone
   *
   * @return BelongsTo<Timezone, self>
   */
  public function timezone(): BelongsTo
  {
    return $this->belongsTo(Timezone::class, 'timezone_id');
  }

  /**
   * Get user language by ID with Redis cache fallback
   * Used by middleware for fast language resolution
   *
   * @param int $userId
   * @return string|null
   */
  public static function getUserLanguageById(int $userId): ?string
  {
    $cacheKey = self::CACHE_PREFIX . "lang:{$userId}";

    return Cache::remember($cacheKey, self::CACHE_TTL, fn() => static::where('user_id', $userId)->value('user_lang'));
  }

  /**
   * Get or create user preferences with dual-layer caching
   * Layer 1: In-memory static cache (fastest)
   * Layer 2: Redis/Database (fallback)
   *
   * @param User $user
   * @return self
   */
  public static function getOrCreateForUser(User $user): self
  {
    $userId = $user->id;

    if (isset(static::$userPreferencesCache[$userId])) {
      return static::$userPreferencesCache[$userId];
    }

    $preferences = static::firstOrCreate(
      ['user_id' => $userId],
      [
        'user_id' => $userId,
        'theme_name' => 'monochromatic-webkernel-css',
        'user_lang' => 'en',
      ],
    );

    static::$userPreferencesCache[$userId] = $preferences;

    return $preferences;
  }

  /**
   * Clear user preferences cache with selective invalidation
   *
   * @param int|null $userId
   * @return void
   */
  public static function clearUserPreferencesCache(?int $userId = null): void
  {
    if ($userId !== null) {
      unset(static::$userPreferencesCache[$userId]);
      // The 'user_pref:lang' cache is now managed directly in the static::saved event
      // to ensure it's only cleared when the user_lang changes.
    } else {
      static::$userPreferencesCache = [];
    }
  }

  /**
   * Get theme data for current user
   *
   * @return array{options: array<string, mixed>, current: string}
   */
  public static function getThemeData(): array
  {
    $originalThemes = app(\Webkernel\Arcanes\ModuleAssetService::class)->getElementsFromPath(
      'users',
      'webkernel-css',
      'blade.php',
    );

    $userTheme = auth()->check() ? static::getOrCreateForUser(auth()->user())->theme_name : null;

    $currentTheme = $userTheme && isset($originalThemes[$userTheme]) ? $userTheme : array_key_first($originalThemes);

    return [
      'options' => $originalThemes,
      'current' => $currentTheme,
    ];
  }

  /**
   * Save theme preference with cache invalidation
   *
   * @param string $themeName
   * @return void
   */
  public static function saveTheme(string $themeName): void
  {
    if (auth()->check()) {
      $preferences = static::getOrCreateForUser(auth()->user());
      $preferences->theme_name = $themeName;
      $preferences->save();

      static::clearUserPreferencesCache(auth()->user()->id);
    }
  }

  /**
   * Format theme name for display
   *
   * @param string $themeName
   * @return string
   */
  public static function formatThemeName(string $themeName): string
  {
    return ucwords(str_replace(['_', '-'], ' ', $themeName));
  }

  /**
   * Get user language preference
   *
   * @param User $user
   * @return string
   */
  public static function getUserLanguage(User $user): string
  {
    $preferences = static::getOrCreateForUser($user);
    return $preferences->user_lang ?? 'en';
  }

  /**
   * Set user language preference with cache invalidation
   *
   * @param User $user
   * @param string $languageCode
   * @return void
   */
  public static function setUserLanguage(User $user, string $languageCode): void
  {
    $preferences = static::getOrCreateForUser($user);
    $preferences->user_lang = $languageCode;
    $preferences->save();

    static::clearUserPreferencesCache($user->id);
  }

  /**
   * Get current authenticated user's language with session fallback
   * Critical method for middleware performance
   *
   * @return string
   */
  public static function getCurrentUserLanguage(): string
  {
    if (!auth()->check()) {
      return session('locale', 'en');
    }

    return static::getUserLanguage(auth()->user());
  }

  /**
   * Get list of attributes handled by this model
   *
   * @return array<int, string>
   */
  public static function getHandledAttributes(): array
  {
    return ['theme_name', 'user_lang', 'timezone_id'];
  }

  /**
   * Get user timezone name
   *
   * @param User $user
   * @return string
   */
  public static function getUserTimezone(User $user): string
  {
    $preferences = static::getOrCreateForUser($user);

    return $preferences->timezone?->name ?? 'Europe/Paris';
  }

  /**
   * Get user timezone display name
   *
   * @param User $user
   * @return string
   */
  public static function getUserTimezoneDisplay(User $user): string
  {
    $preferences = static::getOrCreateForUser($user);

    return $preferences->timezone?->display_name ?? 'Paris';
  }

  /**
   * Set user timezone with cache invalidation
   *
   * @param User $user
   * @param int $timezoneId
   * @return void
   */
  public static function setUserTimezone(User $user, int $timezoneId): void
  {
    $preferences = static::getOrCreateForUser($user);
    $preferences->timezone_id = $timezoneId;
    $preferences->save();

    static::clearUserPreferencesCache($user->id);
  }

  /**
   * Get current authenticated user's timezone name
   *
   * @return string
   */
  public static function getCurrentUserTimezone(): string
  {
    if (!auth()->check()) {
      return 'Europe/Paris';
    }

    return static::getUserTimezone(auth()->user());
  }

  /**
   * Get current authenticated user's timezone display name
   *
   * @return string
   */
  public static function getCurrentUserTimezoneDisplay(): string
  {
    if (!auth()->check()) {
      return 'Paris';
    }

    return static::getUserTimezoneDisplay(auth()->user());
  }

  /**
   * Get current time in user's timezone
   *
   * @return \Carbon\Carbon
   */
  public function getCurrentTimeAttribute(): \Carbon\Carbon
  {
    return $this->timezone ? \Carbon\Carbon::now($this->timezone->name) : \Carbon\Carbon::now();
  }

  /**
   * Get formatted current time
   *
   * @return string
   */
  public function getFormattedCurrentTimeAttribute(): string
  {
    return $this->current_time->format('Y-m-d H:i:s T');
  }
}
