<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'identifier',
    'name',
    'version',
    'description',
    'zip_path',
    'hash',
    'file_size',
    'metadata',
    'status',
    'organization_id',
  ];

  protected $casts = [
    'file_size' => 'integer',
    'metadata' => 'array',
    'status' => 'string',
  ];

  protected $appends = ['formatted_size'];

  /**
   * Relationships
   */
  public function organization(): BelongsTo
  {
    return $this->belongsTo(Organization::class);
  }

  public function licenses(): BelongsToMany
  {
    return $this->belongsToMany(License::class, 'license_modules')
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
    return $query->where('status', 'active');
  }

  public function scopeLatestVersion($query, string $identifier)
  {
    return $query
      ->where('identifier', $identifier)
      ->orderByRaw("CAST(SUBSTRING_INDEX(version, '.', 1) AS UNSIGNED) DESC")
      ->orderByRaw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', 2), '.', -1) AS UNSIGNED) DESC")
      ->orderByRaw("CAST(SUBSTRING_INDEX(version, '.', -1) AS UNSIGNED) DESC")
      ->first();
  }

  public function scopeByIdentifier($query, string $identifier)
  {
    return $query->where('identifier', $identifier);
  }

  /**
   * Accessors
   */
  public function getFormattedSizeAttribute(): string
  {
    $bytes = $this->file_size;
    $units = ['B', 'KiB', 'MiB', 'GiB'];

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
      $bytes /= 1024;
    }

    return round($bytes, 2) . ' ' . $units[$i];
  }

  /**
   * Get changelog from metadata
   */
  public function getChangelog(): ?string
  {
    return $this->metadata['changelog'] ?? null;
  }

  /**
   * Get dependencies from metadata
   */
  public function getDependencies(): array
  {
    return $this->metadata['dependencies'] ?? [];
  }

  /**
   * Check if module is newer than given version
   */
  public function isNewerThan(string $version): bool
  {
    return version_compare($this->version, $version, '>');
  }

  /**
   * Get semantic version type compared to another version
   *
   * @return string 'major'|'minor'|'patch'|'equal'
   */
  public function getUpdateType(string $oldVersion): string
  {
    $old = explode('.', $oldVersion);
    $new = explode('.', $this->version);

    if (($old[0] ?? 0) < ($new[0] ?? 0)) {
      return 'major';
    }

    if (($old[1] ?? 0) < ($new[1] ?? 0)) {
      return 'minor';
    }

    if (($old[2] ?? 0) < ($new[2] ?? 0)) {
      return 'patch';
    }

    return 'equal';
  }

  /**
   * Get download count
   */
  public function getDownloadCount(): int
  {
    return $this->downloadLogs()->where('success', true)->count();
  }
}
