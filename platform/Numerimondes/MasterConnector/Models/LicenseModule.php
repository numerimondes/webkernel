<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseModule extends Pivot
{
  protected $table = 'license_modules';

  public $incrementing = true;

  protected $fillable = ['license_id', 'module_id', 'granted_at', 'revoked_at'];

  protected $casts = [
    'granted_at' => 'datetime',
    'revoked_at' => 'datetime',
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
  public function scopeActive($query)
  {
    return $query->whereNull('revoked_at');
  }

  public function scopeRevoked($query)
  {
    return $query->whereNotNull('revoked_at');
  }

  /**
   * Check if currently active
   */
  public function isActive(): bool
  {
    return is_null($this->revoked_at);
  }

  /**
   * Revoke access
   */
  public function revoke(): void
  {
    $this->update(['revoked_at' => now()]);
  }

  /**
   * Restore access
   */
  public function restore(): void
  {
    $this->update(['revoked_at' => null]);
  }
}
