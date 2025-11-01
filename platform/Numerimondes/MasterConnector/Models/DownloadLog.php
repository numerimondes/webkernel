<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadLog extends Model
{
  const UPDATED_AT = null; // Only use downloaded_at

  protected $fillable = [
    'license_id',
    'module_id',
    'ip_address',
    'user_agent',
    'success',
    'error_message',
    'metadata',
    'downloaded_at',
  ];

  protected $casts = [
    'success' => 'boolean',
    'metadata' => 'array',
    'downloaded_at' => 'datetime',
  ];

  /**
   * Relationships
   */
  public function license(): BelongsTo
  {
    return $this->belongsTo(License::class);
  }

  public function module(): BelongsTo
  {
    return $this->belongsTo(Module::class);
  }

  /**
   * Scopes
   */
  public function scopeSuccessful($query)
  {
    return $query->where('success', true);
  }

  public function scopeFailed($query)
  {
    return $query->where('success', false);
  }

  public function scopeByLicense($query, int $licenseId)
  {
    return $query->where('license_id', $licenseId);
  }

  public function scopeByModule($query, int $moduleId)
  {
    return $query->where('module_id', $moduleId);
  }

  public function scopeByIp($query, string $ipAddress)
  {
    return $query->where('ip_address', $ipAddress);
  }

  public function scopeRecent($query, int $hours = 24)
  {
    return $query->where('downloaded_at', '>=', now()->subHours($hours));
  }

  /**
   * Static method to log download attempt
   */
  public static function logDownload(
    ?int $licenseId,
    ?int $moduleId,
    string $ipAddress,
    bool $success,
    ?string $errorMessage = null,
    array $metadata = [],
  ): self {
    return self::create([
      'license_id' => $licenseId,
      'module_id' => $moduleId,
      'ip_address' => $ipAddress,
      'user_agent' => request()->userAgent(),
      'success' => $success,
      'error_message' => $errorMessage,
      'metadata' => $metadata,
      'downloaded_at' => now(),
    ]);
  }

  /**
   * Detect potential abuse
   */
  public static function detectAbuse(int $licenseId, int $hours = 1, int $threshold = 20): bool
  {
    $count = self::byLicense($licenseId)->recent($hours)->count();

    return $count >= $threshold;
  }
}
