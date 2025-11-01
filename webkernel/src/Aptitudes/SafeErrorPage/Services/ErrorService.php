<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\SafeErrorPage\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Service for managing error data storage and retrieval.
 *
 * Uses cache instead of session to survive redirects and session regeneration.
 */
class ErrorService
{
  /**
   * Retrieve error data by token.
   *
   * @param string $token Cache token (must be lowercase)
   * @return array<string, mixed>|null
   */
  public function retrieveError(string $token): ?array
  {
    $token = strtolower($token);

    try {
      $data = Cache::get('error_' . $token);

      if (config('app.debug') && $data === null) {
        Log::warning('Error token not found in cache', [
          'token' => $token,
          'cache_driver' => config('cache.default'),
        ]);
      }

      return $data;
    } catch (\Throwable $e) {
      Log::error('Error retrieving from cache', [
        'token' => $token,
        'exception' => $e->getMessage(),
      ]);

      return null;
    }
  }

  /**
   * Store error data with token.
   *
   * @param string $token Cache token
   * @param array<string, mixed> $errorData Error information
   * @param int $ttlMinutes Time to live in minutes
   * @return bool
   */
  public function storeError(string $token, array $errorData, int $ttlMinutes = 10): bool
  {
    $token = strtolower($token);

    try {
      $expiresAt = now()->addMinutes($ttlMinutes);
      Cache::put('error_' . $token, $errorData, $expiresAt);

      if (config('app.debug')) {
        Log::info('Error stored in cache', [
          'token' => $token,
          'expires_at' => $expiresAt->toDateTimeString(),
          'cache_driver' => config('cache.default'),
        ]);
      }

      return true;
    } catch (\Throwable $e) {
      Log::error('Error storing to cache', [
        'token' => $token,
        'exception' => $e->getMessage(),
      ]);

      return false;
    }
  }

  /**
   * Clear error data after display.
   *
   * @param string $token Cache token
   * @return bool
   */
  public function clearError(string $token): bool
  {
    $token = strtolower($token);

    try {
      Cache::forget('error_' . $token);

      if (config('app.debug')) {
        Log::info('Error cleared from cache', [
          'token' => $token,
          'cache_driver' => config('cache.default'),
        ]);
      }

      return true;
    } catch (\Throwable $e) {
      Log::error('Error clearing from cache', [
        'token' => $token,
        'exception' => $e->getMessage(),
      ]);

      return false;
    }
  }

  /**
   * Check if error data exists.
   *
   * @param string $token Cache token
   * @return bool
   */
  public function hasError(string $token): bool
  {
    $token = strtolower($token);

    try {
      return Cache::has('error_' . $token);
    } catch (\Throwable $e) {
      Log::error('Error checking cache existence', [
        'token' => $token,
        'exception' => $e->getMessage(),
      ]);

      return false;
    }
  }
}
