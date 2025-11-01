<?php

namespace Webkernel\Aptitudes\Platform\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Crypt;
use Webkernel\Aptitudes\Platform\Core\Services\EncryptionService;

class LocalLicense extends Model
{
  use HasFactory;

  protected $table = 'local_licenses';

  protected $fillable = ['token_encrypted', 'domain', 'last_synced_at', 'expires_at', 'status'];

  protected $casts = [
    'last_synced_at' => 'datetime',
    'expires_at' => 'datetime',
    'status' => 'string', // ENUM: active/expired/revoked/pending
  ];

  protected static function boot()
  {
    parent::boot();

    static::saving(function (self $model) {
      if ($model->isDirty('token_encrypted')) {
        // Ensure token is encrypted on save
        if (!empty($model->token_encrypted) && !str_starts_with($model->token_encrypted, 'encrypted:')) {
          $model->token_encrypted = 'encrypted:' . Crypt::encryptString($model->token_encrypted);
        }
      }
    });
  }

  /**
   * Decrypt the token if needed.
   */
  // Change from getDecryptedTokenAttribute to:
  public function getDecryptedToken(): ?string
  {
    if (empty($this->token_encrypted)) {
      return null;
    }
    return app(EncryptionService::class)->decrypt($this->token_encrypted);
  }

  /**
   * Scope for active licenses.
   */
  public function scopeActive($query)
  {
    return $query->where('status', 'active')->where('expires_at', '>', now());
  }

  /**
   * Get the single license instance (assuming one row).
   */
  public static function getLicense(): ?self
  {
    return self::first();
  }
}
