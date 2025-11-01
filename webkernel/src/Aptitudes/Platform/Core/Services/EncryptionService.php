<?php

namespace Webkernel\Aptitudes\Platform\Core\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class EncryptionService
{
  /**
   * Encrypt a string using AES-256-CBC (Laravel default).
   *
   * @param string $value
   * @return string Encrypted value
   * @throws RuntimeException On encryption failure
   */
  public function encrypt(string $value): string
  {
    try {
      return Crypt::encryptString($value);
    } catch (Exception $e) {
      Log::error('Encryption failed: ' . $e->getMessage());
      throw new RuntimeException('Encryption failed due to configuration error (e.g., invalid APP_KEY).');
    }
  }

  /**
   * Decrypt a string using AES-256-CBC.
   *
   * @param string $value
   * @return string Decrypted value
   * @throws RuntimeException On decryption failure
   */
  public function decrypt(string $value): string
  {
    try {
      return Crypt::decryptString($value);
    } catch (DecryptException $e) {
      Log::warning('Decryption failed: possibly tampered data or key mismatch. ' . $e->getMessage());
      throw new RuntimeException('Decryption failed: data may be corrupted or APP_KEY changed.');
    }
  }

  /**
   * Check if a value is encrypted (Laravel format check).
   *
   * @param string $value
   * @return bool
   */
  public function isEncrypted(string $value): bool
  {
    return str_starts_with($value, 'ey') && str_contains($value, ':'); // Basic Laravel cipher check
  }
}
