<?php

namespace Webkernel\Aptitudes\Platform\Connector\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Webkernel\Aptitudes\Platform\Core\Services\LicenseTokenService;

class MasterApiClient
{
  private string $baseUrl;
  private LicenseTokenService $tokenService;

  public function __construct(?string $masterUrl = null, LicenseTokenService $tokenService)
  {
    $this->baseUrl = rtrim($masterUrl ?? config('numerimondes.master_url'), '/') . '/api';
    //$this->baseUrl = rtrim($masterUrl ?? config('numerimondes.master_url'), '/') . '/api';
    $this->tokenService = $tokenService;
  }

  /**
   * Create a pending request with defaults: timeout, retries, SSL.
   */
  private function createRequest(string $token): PendingRequest
  {
    $timeout = 30; // Base 30s + 10s per MB (calculated in caller if needed)

    return Http::withToken($token, 'Bearer')
      ->withOptions([
        'verify' => true, // Strict SSL
        'timeout' => $timeout,
        'connect_timeout' => 10,
      ])
      ->retry(3, 100) // 3 retries with 100ms backoff
      ->baseUrl($this->baseUrl);
  }

  /**
   * Validate license via API.
   */
  public function validateLicense(string $token, string $domain): array
  {
    if (!$this->tokenService->isValidFormat($token)) {
      throw new \InvalidArgumentException('Invalid token format.');
    }

    try {
      $response = $this->createRequest($token)
        ->timeout(60)
        ->post('/auth/validate', ['domain' => $domain]);

      if ($response->failed()) {
        Log::warning('License validation failed', ['status' => $response->status(), 'body' => $response->body()]);
        throw new RequestException($response);
      }

      return $response->json('data', []);
    } catch (RequestException $e) {
      Log::error('API validation error: ' . $e->getMessage());
      throw $e;
    }
  }

  /**
   * List available modules.
   */
  public function listModules(string $token): array
  {
    try {
      $response = $this->createRequest($token)->get('/modules/list');

      if ($response->failed()) {
        throw new RequestException($response);
      }

      return $response->json('data.modules', []);
    } catch (RequestException $e) {
      Log::error('API list modules error: ' . $e->getMessage());
      return [];
    }
  }

  /**
   * Download modules as ZIP stream.
   */
  public function downloadModules(string $token, array $moduleIds): \Illuminate\Http\Client\Response
  {
    $query = http_build_query(['modules' => implode(',', $moduleIds)]);
    $timeout = 30 + count($moduleIds) * 10; // Adaptative: +10s per module

    try {
      return $this->createRequest($token)
        ->timeout($timeout)
        ->asDownload()
        ->get('/modules/download?' . $query);
    } catch (RequestException $e) {
      Log::error('API download error: ' . $e->getMessage());
      throw $e;
    }
  }

  /**
   * Check for updates.
   */
  public function checkUpdates(string $token, array $currentModules): array
  {
    try {
      $response = $this->createRequest($token)->post('/modules/updates', ['modules' => $currentModules]);

      if ($response->failed()) {
        throw new RequestException($response);
      }

      return $response->json('data.updates', []);
    } catch (RequestException $e) {
      Log::error('API updates check error: ' . $e->getMessage());
      return [];
    }
  }

  /**
   * Get checksum for a module.
   */
  public function getChecksum(string $token, string $identifier): ?array
  {
    try {
      $response = $this->createRequest($token)->get("/modules/checksum/{$identifier}");

      if ($response->failed()) {
        return null;
      }

      return $response->json('data');
    } catch (RequestException $e) {
      Log::error('API checksum error: ' . $e->getMessage());
      return null;
    }
  }
}
