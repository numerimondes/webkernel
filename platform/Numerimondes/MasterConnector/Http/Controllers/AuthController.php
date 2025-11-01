<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Platform\Numerimondes\MasterConnector\Services\LicenseManager;

class AuthController extends Controller
{
  public function __construct(private LicenseManager $licenseManager) {}

  /**
   * Validate license token and domain
   *
   * POST /api/auth/validate
   * Body: { "domain": "example.com" }
   * Header: Authorization: Bearer {token}
   */
  public function validate(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'domain' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
      return response()->json(
        [
          'success' => false,
          'error' => 'Invalid request: ' . $validator->errors()->first(),
        ],
        400,
      );
    }

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

    $domain = $request->input('domain');
    $ipAddress = $request->ip();

    try {
      $result = $this->licenseManager->validateLicense($token, $domain, $ipAddress);

      if (!$result['valid']) {
        return response()->json(
          [
            'success' => false,
            'error' => $result['error'],
          ],
          403,
        );
      }

      return response()->json([
        'success' => true,
        'data' => [
          'license_id' => $result['license_id'],
          'expires_at' => $result['expires_at'],
          'status' => $result['status'],
          'modules' => $result['modules'],
        ],
      ]);
    } catch (\Exception $e) {
      Log::error('License validation exception: ' . $e->getMessage(), [
        'domain' => $domain,
        'ip' => $ipAddress,
      ]);

      return response()->json(
        [
          'success' => false,
          'error' => 'Internal server error.',
        ],
        500,
      );
    }
  }
}
