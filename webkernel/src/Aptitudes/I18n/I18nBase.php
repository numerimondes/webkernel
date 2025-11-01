<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\I18n;

/**
 *  The system automatically handles:
 *
 *  - Authenticated users get their DB language
 *  - Non-authenticated users get session/browser language
 *  - Database translations override filesystem ones
 *  - Filesystem translations load when no DB override exists
 */

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Webkernel\Arcanes\QueryModules;

/**
 * Ultra-optimized I18n system for Octane/Swoole/FrankenPHP compatibility
 * Microsecond performance with cache-first approach and proper language priority
 */
class I18nBase
{
  private static ?self $instance = null;
  private array $memoryCache = [];
  private array $moduleLangPaths = [];
  private array $availableLocales = [];
  private bool $initialized = false;
  private array $config = [];
  private ?string $cachedBrowserLocale = null;

  public static function instance(): self
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function __construct()
  {
    $this->initialize();
  }

  private function initialize(): void
  {
    if ($this->initialized) {
      return;
    }

    $this->config = config('webkernel-aptitudes-lang', []);

    // Cache les langPaths pour éviter les requêtes répétées
    $cacheKey = 'i18n_module_langpaths_v1';
    $this->moduleLangPaths = Cache::remember($cacheKey, 3600, function () {
      return QueryModules::make()
        ->select(['langPath'])
        ->where('langPath')
        ->isNotNull()
        ->get();
    });

    $this->initialized = true;
  }

  public function translate(string $key, array $replace = [], ?string $locale = null): string
  {
    $locale = $locale ?: $this->determineLocale();
    $locale = $this->normalizeLocale($locale);

    $memoryCacheKey = "{$locale}.{$key}." . ($replace ? md5(serialize($replace)) : 'empty');
    if (isset($this->memoryCache[$memoryCacheKey])) {
      return $this->memoryCache[$memoryCacheKey];
    }

    // Resolve translation directly - file translations are already in memory
    $translation = $this->resolveTranslation($key, $replace, $locale);

    $this->memoryCache[$memoryCacheKey] = $translation;
    return $translation;
  }

  private function determineLocale(): string
  {
    // Priority 1: Authenticated user - use database preference
    if (auth()->check()) {
      $userLocale = $this->getUserLocaleFromDatabase();
      if ($userLocale) {
        return $userLocale;
      }
    }

    // Priority 2: Non-authenticated user with session choice
    $sessionLocale = Session::get('locale');
    if ($sessionLocale && $this->isValidLocale($sessionLocale)) {
      return $sessionLocale;
    }

    // Priority 3: Browser language detection for non-authenticated users
    if (!auth()->check()) {
      $browserLocale = $this->detectBrowserLanguage();
      if ($browserLocale) {
        return $browserLocale;
      }
    }

    // Priority 4: Application default
    return app()->getLocale();
  }

  private function getUserLocaleFromDatabase(): ?string
  {
    // Cache la locale de l'utilisateur pour éviter les requêtes répétées
    static $userLocaleCache = null;
    static $lastUserId = null;

    try {
      $user = auth()->user();
      if (!$user) {
        return null;
      }

      // Utiliser le cache si c'est le même utilisateur
      if ($lastUserId === $user->id && $userLocaleCache !== null) {
        return $userLocaleCache;
      }

      // Assuming user has a 'locale' or 'language' field
      $locale = $user->locale ?? ($user->language ?? null);

      if ($locale && $this->isValidLocale($locale)) {
        $userLocaleCache = $locale;
        $lastUserId = $user->id;
        return $locale;
      }
    } catch (\Throwable) {
      // Silently fail and continue with fallback
    }

    return null;
  }

  private function resolveTranslation(string $key, array $replace, string $locale): string
  {
    // Step 1: Check if key exists in database (for overrides or db-only keys)
    $dbTranslation = $this->getDatabaseTranslation($key, $locale);
    $hasDbTranslation = $dbTranslation !== null;

    // Step 2: Get filesystem translation
    $filesystemTranslation = $this->getModuleTranslation($key, $locale);
    $hasFilesystemTranslation = $filesystemTranslation !== null;

    // Decision logic:
    // - If both exist: database overrides filesystem
    // - If only database exists: use database
    // - If only filesystem exists: use filesystem
    // - If neither exists: fallback chain

    if ($hasDbTranslation) {
      return $this->replacePlaceholders($dbTranslation, $replace);
    }

    if ($hasFilesystemTranslation) {
      return $this->replacePlaceholders($filesystemTranslation, $replace);
    }

    // Fallback chain when no translation found
    return $this->handleFallbackTranslation($key, $replace, $locale);
  }

  private function handleFallbackTranslation(string $key, array $replace, string $locale): string
  {
    // Try Laravel built-in translations
    $laravelTranslation = trans($key, $replace, $locale);
    if ($laravelTranslation !== $key) {
      return $laravelTranslation;
    }

    // Try fallback locale (English) if current locale is different
    if ($locale !== 'en') {
      $fallbackDbTranslation = $this->getDatabaseTranslation($key, 'en');
      if ($fallbackDbTranslation !== null) {
        return $this->replacePlaceholders($fallbackDbTranslation, $replace);
      }

      $fallbackFilesystemTranslation = $this->getModuleTranslation($key, 'en');
      if ($fallbackFilesystemTranslation !== null) {
        return $this->replacePlaceholders($fallbackFilesystemTranslation, $replace);
      }
    }

    // Return the key as last resort
    return $key;
  }

  private function detectBrowserLanguage(): ?string
  {
    if ($this->cachedBrowserLocale !== null) {
      return $this->cachedBrowserLocale === '' ? null : $this->cachedBrowserLocale;
    }

    $acceptLanguage = request()->header('Accept-Language');
    if (!$acceptLanguage) {
      $this->cachedBrowserLocale = '';
      return null;
    }

    $languages = $this->parseBrowserLanguages($acceptLanguage);
    $availableLocales = $this->getAvailableLocales();

    foreach ($languages as $locale => $quality) {
      $normalizedLocale = $this->normalizeBrowserLocale($locale);

      if (in_array($normalizedLocale, $availableLocales)) {
        $this->cachedBrowserLocale = $normalizedLocale;
        return $normalizedLocale;
      }
    }

    $this->cachedBrowserLocale = '';
    return null;
  }

  private function parseBrowserLanguages(string $acceptLanguage): array
  {
    $languages = [];
    $parts = explode(',', $acceptLanguage);

    foreach ($parts as $part) {
      $part = trim($part);
      if (strpos($part, ';') !== false) {
        [$locale, $quality] = explode(';', $part, 2);
        $quality = (float) str_replace('q=', '', trim($quality));
      } else {
        $locale = $part;
        $quality = 1.0;
      }

      $languages[trim($locale)] = $quality;
    }

    arsort($languages);
    return $languages;
  }

  private function normalizeBrowserLocale(string $locale): string
  {
    $locale = strtolower(str_replace('_', '-', trim($locale)));

    // Remove country code if present (e.g., en-US -> en)
    if (strpos($locale, '-') !== false) {
      $locale = explode('-', $locale)[0];
    }

    return $locale;
  }

  private function getDatabaseTranslation(string $key, string $locale): ?string
  {
    try {
      return \DB::table('translations')->where('key', $key)->where('locale', $locale)->value('value');
    } catch (\Throwable) {
      return null;
    }
  }

  public function getModuleTranslation(string $key, string $locale): ?string
  {
    $translations = $this->getModuleTranslations($locale);

    // Direct key lookup
    if (isset($translations[$key])) {
      return $translations[$key];
    }

    // Dot notation support for nested keys
    if (str_contains($key, '.')) {
      $segments = explode('.', $key);
      $value = $translations;

      foreach ($segments as $segment) {
        if (!is_array($value) || !isset($value[$segment])) {
          return null;
        }
        $value = $value[$segment];
      }

      return is_string($value) ? $value : null;
    }

    return null;
  }

  public function getModuleTranslations(string $locale): array
  {
    $cacheKey = "i18n_module_translations_{$locale}_v4";

    return Cache::remember($cacheKey, 3600, function () use ($locale) {
      $allTranslations = [];

      foreach ($this->moduleLangPaths as $module) {
        $langPath = $module['langPath'] ?? null;
        if (!$langPath) {
          continue;
        }

        $localeDir = "{$langPath}/{$locale}";
        if (!is_dir($localeDir)) {
          continue;
        }

        // Load translations.php first (main file)
        $mainFile = "{$localeDir}/translations.php";
        if (file_exists($mainFile)) {
          $translations = $this->loadTranslationFile($mainFile);
          $this->processTranslationStructure($translations, $allTranslations);
        }

        // Load other PHP files
        $phpFiles = glob("{$localeDir}/*.php") ?: [];
        foreach ($phpFiles as $file) {
          if (basename($file) === 'translations.php') {
            continue;
          }

          $fileTranslations = $this->loadTranslationFile($file);
          if (is_array($fileTranslations)) {
            $allTranslations = array_merge($allTranslations, $fileTranslations);
          }
        }
      }

      return $allTranslations;
    });
  }

  private function processTranslationStructure(array $translations, array &$allTranslations): void
  {
    if (isset($translations['lang_ref']) && is_array($translations['lang_ref'])) {
      foreach ($translations['lang_ref'] as $key => $data) {
        if (is_array($data) && isset($data['label'])) {
          $allTranslations[$key] = $data['label'];
        }
      }
    }

    // Also include direct translations
    foreach ($translations as $key => $value) {
      if ($key !== 'lang_ref' && is_string($value)) {
        $allTranslations[$key] = $value;
      }
    }
  }

  private function loadTranslationFile(string $file): array
  {
    try {
      $translations = include $file;
      return is_array($translations) ? $translations : [];
    } catch (\Throwable) {
      return [];
    }
  }

  private function replacePlaceholders(string $text, array $replace): string
  {
    if (empty($replace)) {
      return $text;
    }

    $placeholders = [];
    foreach ($replace as $key => $value) {
      $placeholders[":{$key}"] = (string) $value;
    }

    return strtr($text, $placeholders);
  }

  private function normalizeLocale(string $locale): string
  {
    $locale = str_replace('_', '-', strtolower(trim($locale)));

    $normalizations = [
      'pt-br' => 'pt-BR',
      'pt-pt' => 'pt-PT',
      'zh-cn' => 'zh-CN',
      'zh-tw' => 'zh-TW',
      'en-us' => 'en-US',
      'en-gb' => 'en-GB',
      'fr-ca' => 'fr-CA',
      'es-mx' => 'es-MX',
    ];

    return $normalizations[$locale] ?? $locale;
  }

  public function getAvailableLocales(): array
  {
    if (!empty($this->availableLocales)) {
      return $this->availableLocales;
    }

    // Load available locales directly - they are static and don't need caching
    $locales = [];

    foreach ($this->moduleLangPaths as $module) {
      $langPath = $module['langPath'] ?? null;
      if (!$langPath || !is_dir($langPath)) {
        continue;
      }

      $dirs = glob("{$langPath}/*", GLOB_ONLYDIR) ?: [];
      foreach ($dirs as $dir) {
        $locale = basename($dir);
        if (!in_array($locale, $locales) && $this->isValidLocale($locale)) {
          $locales[] = $locale;
        }
      }
    }

    sort($locales);
    $this->availableLocales = $locales;

    return $this->availableLocales;
  }

  private function isValidLocale(string $locale): bool
  {
    return preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $locale) === 1;
  }

  public function setSessionLocale(string $locale): void
  {
    if ($this->isValidLocale($locale)) {
      Session::put('locale', $locale);
      // Clear browser locale cache to refresh on next request
      $this->cachedBrowserLocale = null;
    }
  }

  public function getSessionLocale(): ?string
  {
    return Session::get('locale');
  }

  public function clearCache(): void
  {
    $this->memoryCache = [];
    $this->availableLocales = [];
    $this->cachedBrowserLocale = null;

    $patterns = ['i18n_module_translations_*_v4'];

    foreach ($patterns as $pattern) {
      if (str_contains($pattern, '*')) {
        $locales = $this->getAvailableLocales();
        foreach ($locales as $locale) {
          $actualPattern = str_replace('*', $locale, $pattern);
          Cache::forget($actualPattern);
        }
      } else {
        Cache::forget($pattern);
      }
    }
  }

  public function warmCache(): void
  {
    $locales = $this->getAvailableLocales();

    foreach ($locales as $locale) {
      $this->getModuleTranslations($locale);
    }
  }

  public function getStats(): array
  {
    return [
      'memory_cache_size' => count($this->memoryCache),
      'module_paths_count' => count($this->moduleLangPaths),
      'available_locales_count' => count($this->getAvailableLocales()),
      'initialized' => $this->initialized,
      'current_locale' => $this->determineLocale(),
      'is_authenticated' => auth()->check(),
      'session_locale' => $this->getSessionLocale(),
      'browser_locale' => $this->detectBrowserLanguage(),
    ];
  }
}

/**
 * Translation Service - Non-static wrapper for dependency injection
 */
class TranslationService
{
  private I18nBase $i18n;

  public function __construct()
  {
    $this->i18n = I18nBase::instance();
  }

  public function translate(string $key, array $replace = [], ?string $locale = null): string
  {
    return $this->i18n->translate($key, $replace, $locale);
  }

  public function getAvailableLocales(): array
  {
    return $this->i18n->getAvailableLocales();
  }

  public function setSessionLocale(string $locale): void
  {
    $this->i18n->setSessionLocale($locale);
  }

  public function getSessionLocale(): ?string
  {
    return $this->i18n->getSessionLocale();
  }

  public function clearCache(): void
  {
    $this->i18n->clearCache();
  }

  public function warmCache(): void
  {
    $this->i18n->warmCache();
  }

  public function getStats(): array
  {
    return $this->i18n->getStats();
  }
}

/**
 * Translation Resolver - Optimized for microsecond access
 */
class TranslationResolver
{
  private I18nBase $i18n;

  public function __construct()
  {
    $this->i18n = I18nBase::instance();
  }

  public function resolve(string $key, array $replace = [], ?string $locale = null): string
  {
    return $this->i18n->translate($key, $replace, $locale);
  }

  public function bulk(array $keys, array $replace = [], ?string $locale = null): array
  {
    $results = [];
    foreach ($keys as $key) {
      $results[$key] = $this->i18n->translate($key, $replace, $locale);
    }
    return $results;
  }
}
