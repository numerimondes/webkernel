<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;
use Webkernel\Aptitudes\I18n\Http\Middleware\LanguageResolutionMiddleware;
use Webkernel\Aptitudes\I18n\Models\Language;
use Webkernel\Aptitudes\Users\Models\UserExtensions\UserPreference;

/**
 * Language Switching Routes
 *
 * Optimized for reliability and performance:
 * - Validates language exists and is active before switching
 * - Immediate cache invalidation for consistency
 * - Multi-layer persistence (database, session, cookie)
 * - Secure cookie with 1-year expiration
 */
Route::middleware(['web', LanguageResolutionMiddleware::class])->group(function (): void {
  /**
   * Switch language endpoint
   *
   * Validates language, updates user preference, and sets session/cookie.
   * Returns to previous page after successful switch.
   *
   * @param string $lang Language code (e.g., 'en', 'fr', 'ar')
   * @return RedirectResponse
   */
  Route::get('lang/{lang}', function (string $lang): RedirectResponse {
    // Validation: check if language exists and is active
    $validLanguage = Cache::remember(
      "valid_lang:{$lang}",
      3600,
      fn(): bool => Language::byCode($lang)->active()->exists(),
    );

    if (!$validLanguage) {
      abort(404, 'Language not found or inactive');
    }

    // Persist for authenticated users with immediate cache invalidation
    $user = auth()->user();
    if ($user !== null) {
      UserPreference::setUserLanguage($user, $lang);
      // Invalidate user locale cache immediately for consistency
      Cache::forget("user_locale:{$user->id}");
    }

    // Always update session for immediate effect
    session(['locale' => $lang]);

    // Set long-lived cookie (1 year) with proper security settings
    // CRITICAL: Use cookie() helper with queue for proper cookie setting
    $cookie = cookie(
      'locale',
      $lang,
      525600, // 1 year in minutes
      '/',
      null,
      request()->secure(), // secure only on HTTPS
      false, // httpOnly = false (accessible to JS if needed)
      false, // raw
      'Lax', // sameSite
    );

    return redirect()->back()->withCookie($cookie);
  })->name('lang.switch');
});
