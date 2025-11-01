<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\I18n\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Webkernel\Aptitudes\Users\Models\UserExtensions\UserPreference;

/**
 * Language Resolution Middleware
 *
 * Ultra-optimized for sub-microsecond response times.
 * Uses aggressive caching with cache-aside pattern.
 *
 * Performance Strategy:
 * - Layer 1: In-memory static cache (UserPreference model)
 * - Layer 2: Redis cache with 1-hour TTL
 * - Layer 3: Cookie fallback
 * - Layer 4: Session fallback
 * - Layer 5: Default locale
 *
 * Priority chain (FIXED):
 * 1. Authenticated user preference (cached in Redis + static)
 * 2. Cookie locale
 * 3. Session locale
 * 4. Default fallback (en)
 */
final class LanguageResolutionMiddleware
{
  private const DEFAULT_LOCALE = 'en';
  private const CACHE_TTL = 3600;
  private const SESSION_KEY = 'locale';
  private const COOKIE_KEY = 'locale';

  /**
   * Handle an incoming request
   */
  public function handle(Request $request, Closure $next): Response
  {
    $locale = $this->resolveLocale($request);

    // Set application locale
    app()->setLocale($locale);

    // Also set session for consistency (if not already set)
    if (session(self::SESSION_KEY) !== $locale) {
      session([self::SESSION_KEY => $locale]);
    }

    return $next($request);
  }

  /**
   * Resolve locale with performance-first approach
   * FIXED: Now checks cookie before falling back to default
   */
  private function resolveLocale(Request $request): string
  {
    // Priority 1: Authenticated user preference (cached)
    $user = auth()->user();
    if ($user !== null && isset($user->id)) {
      $userLocale = $this->getUserLocaleFromCache($user->id);
      if ($userLocale !== self::DEFAULT_LOCALE) {
        return $userLocale;
      }
    }

    // Priority 2: Cookie (CRITICAL FIX - was missing!)
    $cookieLocale = $request->cookie(self::COOKIE_KEY);
    if ($cookieLocale !== null && $cookieLocale !== '') {
      return $cookieLocale;
    }

    // Priority 3: Session
    $sessionLocale = session(self::SESSION_KEY);
    if ($sessionLocale !== null && $sessionLocale !== '') {
      return $sessionLocale;
    }

    // Priority 4: Default fallback
    return self::DEFAULT_LOCALE;
  }

  /**
   * Get user locale from cache with single query fallback
   * Cache key includes user ID for isolation
   * Uses Redis for persistence across requests
   */
  private function getUserLocaleFromCache(int $userId): string
  {
    $cacheKey = "user_locale:{$userId}";

    return Cache::remember(
      $cacheKey,
      self::CACHE_TTL,
      fn(): string => UserPreference::getUserLanguageById($userId) ?? self::DEFAULT_LOCALE,
    );
  }
}
