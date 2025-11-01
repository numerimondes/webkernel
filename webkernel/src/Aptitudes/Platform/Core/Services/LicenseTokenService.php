<?php

namespace Webkernel\Aptitudes\Platform\Core\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class LicenseTokenService
{
  /**
   * Generate a secure token (256-bit entropy, base64url encoded).
   *
   * @return string Token (64 characters)
   * @throws RuntimeException On entropy generation failure
   */
  public function generateToken(): string
  {
    try {
      $bytes = random_bytes(32);
      return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    } catch (Exception $e) {
      throw new RuntimeException('Failed to generate secure token: insufficient entropy.', 0, $e);
    }
  }

  /**
   * Create a SHA256 hash of the token for server storage.
   *
   * @param string $token
   * @return string Hash
   */
  public function hashToken(string $token): string
  {
    return Hash::make($token); // Uses SHA256 by default in Laravel
  }

  /**
   * Validate a token against a stored hash (server-side).
   *
   * @param string $token
   * @param string $storedHash
   * @return bool
   */
  public function validateTokenAgainstHash(string $token, string $storedHash): bool
  {
    return Hash::check($token, $storedHash);
  }

  /**
   * Check if a token is well-formed (base64url, 64 chars).
   *
   * @param string $token
   * @return bool
   */
  public function isValidFormat(string $token): bool
  {
    return Str::length($token) === 64 && preg_match('/^[A-Za-z0-9\-_]+$/', $token);
  }
}
