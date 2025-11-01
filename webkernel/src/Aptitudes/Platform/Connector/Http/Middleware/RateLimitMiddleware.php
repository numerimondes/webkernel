<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\Platform\Connector\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
  private const LIMITS = [
    'default' => ['maxAttempts' => 100, 'decayMinutes' => 60], // Add this
    'auth' => ['maxAttempts' => 60, 'decayMinutes' => 60],
    'download' => ['maxAttempts' => 10, 'decayMinutes' => 60],
    'list' => ['maxAttempts' => 300, 'decayMinutes' => 60],
  ];

  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next, string $type = 'default'): Response
  {
    $key = $this->resolveRateLimitingKey($request);
    $config = self::LIMITS[$type] ?? self::LIMITS['default'];

    $limiter = RateLimiter::for('numerimondes.' . $type, function (Request $req) use ($key) {
      return Limit::perMinute($config['maxAttempts'])->by($key);
    });

    if (RateLimiter::tooManyAttempts($limiter, $request->ip())) {
      $retryAfter = RateLimiter::availableIn($limiter, $request->ip());
      return response()
        ->json(['error' => 'Rate limit exceeded'], 429)
        ->headers([
          'Retry-After' => $retryAfter,
        ]);
    }

    RateLimiter::hit($limiter, $request->ip(), $config['decayMinutes'] * 60);

    return $next($request);
  }

  /**
   * Resolve the rate limiting key (IP or token).
   */
  private function resolveRateLimitingKey(Request $request): string
  {
    return $request->bearerToken() ?: $request->ip();
  }
}
