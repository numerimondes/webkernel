<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Platform\Numerimondes\MasterConnector\Models\License;

class AuthenticateToken
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next): Response
  {
    $token = $request->bearerToken();

    if (!$token) {
      return response()->json(
        [
          'success' => false,
          'error' => 'Missing authorization token.',
        ],
        401,
      );
    }

    // Hash token for lookup
    $tokenHash = hash('sha256', $token);

    // Find license by token hash
    $license = License::where('token_hash', $tokenHash)->first();

    if (!$license) {
      Log::warning('Authentication failed: invalid token.', [
        'ip' => $request->ip(),
        'token_hash_partial' => substr($tokenHash, 0, 8),
      ]);

      return response()->json(
        [
          'success' => false,
          'error' => 'Invalid token.',
        ],
        403,
      );
    }

    // Validate domain (strict matching per spec)
    $requestDomain = $this->extractDomain($request);

    if (config('master-connector.validation.strict_domain') && $license->domain !== $requestDomain) {
      Log::warning('Authentication failed: domain mismatch.', [
        'license_id' => $license->id,
        'expected' => $license->domain,
        'received' => $requestDomain,
        'ip' => $request->ip(),
      ]);

      return response()->json(
        [
          'success' => false,
          'error' => 'Domain mismatch.',
        ],
        403,
      );
    }

    // Check license status
    if (!$license->isValid()) {
      Log::warning('Authentication failed: license invalid.', [
        'license_id' => $license->id,
        'status' => $license->status,
        'expires_at' => $license->expires_at?->toISOString(),
      ]);

      return response()->json(
        [
          'success' => false,
          'error' => 'License is ' . $license->status . '.',
        ],
        403,
      );
    }

    // Attach license to request for controllers
    $request->attributes->set('license', $license);

    return $next($request);
  }

  /**
   * Extract domain from request using HTTP_HOST header
   */
  private function extractDomain(Request $request): string
  {
    // Use getHost() for normalized domain extraction
    $host = $request->getHost();

    // Remove port if present
    return explode(':', $host)[0];
  }
}
