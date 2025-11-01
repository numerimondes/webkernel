<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class License extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'token_hash',
    'domain',
    'status',
    'expires_at',
    'metadata',
    'last_validated_at',
    'last_validated_ip',
    'organization_id',
  ];

  protected $casts = [
    'expires_at' => 'datetime',
    'last_validated_at' => 'datetime',
    'metadata' => 'array',
    'status' => 'string',
  ];

  protected $hidden = [
    'token_hash', // Never expose in API responses
  ];

  /**
   * Relationships
   */
  public function organization(): BelongsTo
  {
    return $this->belongsTo(Organization::class);
  }

  public function modules(): BelongsToMany
  {
    return $this->belongsToMany(Module::class, 'license_modules')
      ->withPivot(['granted_at', 'revoked_at'])
      ->withTimestamps()
      ->wherePivotNull('revoked_at');
  }

  public function allModules(): BelongsToMany
  {
    return $this->belongsToMany(Module::class, 'license_modules')
      ->withPivot(['granted_at', 'revoked_at'])
      ->withTimestamps();
  }

  public function downloadLogs(): HasMany
  {
    return $this->hasMany(DownloadLog::class);
  }

  /**
   * Scopes
   */
  public function scopeActive($query)
  {
    return $query->where('status', 'active')->where(function ($q) {
      $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
    });
  }

  public function scopeExpired($query)
  {
    return $query->where('status', 'active')->whereNotNull('expires_at')->where('expires_at', '<=', now());
  }

  public function scopeRevoked($query)
  {
    return $query->where('status', 'revoked');
  }

  public function scopeByDomain($query, string $domain)
  {
    return $query->where('domain', $domain);
  }

  /**
   * Check if license is valid
   */
  public function isValid(): bool
  {
    if ($this->status !== 'active') {
      return false;
    }

    if ($this->expires_at && $this->expires_at->isPast()) {
      return false;
    }

    return true;
  }

  /**
   * Check if license has access to a module
   */
  public function hasModule(int $moduleId): bool
  {
    return $this->modules()->where('module_id', $moduleId)->exists();
  }

  /**
   * Update validation timestamp
   */
  public function touchValidation(string $ipAddress): void
  {
    $this->update([
      'last_validated_at' => now(),
      'last_validated_ip' => $ipAddress,
    ]);
  }

  /**
   * Revoke license
   */
  public function revoke(string $reason = null): void
  {
    $this->update(['status' => 'revoked']);

    Log::warning('License revoked.', [
      'license_id' => $this->id,
      'domain' => $this->domain,
      'reason' => $reason,
    ]);
  }

  /**
   * Expire license
   */
  public function expire(): void
  {
    $this->update(['status' => 'expired']);

    Log::info('License expired.', [
      'license_id' => $this->id,
      'domain' => $this->domain,
    ]);
  }
}
