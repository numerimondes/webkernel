<?php

declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Media Model
 *
 * @property int $id
 * @property int $service_id
 * @property string $type
 * @property string $url
 * @property string|null $caption
 * @property int $order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Media extends Model
{
  protected $table = 'enjoy_tw_media';

  protected $fillable = ['service_id', 'type', 'url', 'caption', 'order'];

  protected $casts = [
    'service_id' => 'integer',
    'order' => 'integer',
  ];

  protected $attributes = [
    'order' => 0,
  ];

  /**
   * Boot the model
   *
   * @return void
   */
  protected static function boot(): void
  {
    parent::boot();

    static::creating(function (self $media): void {
      if ($media->order === 0) {
        $media->order = static::query()->where('service_id', $media->service_id)->max('order') + 1;
      }
    });
  }

  /**
   * Get the service that owns the media
   *
   * @return BelongsTo
   */
  public function service(): BelongsTo
  {
    return $this->belongsTo(Service::class);
  }

  /**
   * Scope: images only
   *
   * @param Builder<self> $query
   * @return Builder<self>
   */
  public function scopeImages(Builder $query): Builder
  {
    return $query->where('type', 'image');
  }

  /**
   * Scope: videos only
   *
   * @param Builder<self> $query
   * @return Builder<self>
   */
  public function scopeVideos(Builder $query): Builder
  {
    return $query->where('type', 'video');
  }

  /**
   * Scope: ordered
   *
   * @param Builder<self> $query
   * @return Builder<self>
   */
  public function scopeOrdered(Builder $query): Builder
  {
    return $query->orderBy('order');
  }

  /**
   * Check if media is image
   *
   * @return bool
   */
  public function isImage(): bool
  {
    return $this->type === 'image';
  }

  /**
   * Check if media is video
   *
   * @return bool
   */
  public function isVideo(): bool
  {
    return $this->type === 'video';
  }

  /**
   * Get full URL
   *
   * @return string
   */
  public function getFullUrl(): string
  {
    if (filter_var($this->url, FILTER_VALIDATE_URL)) {
      return $this->url;
    }

    return asset('storage/' . $this->url);
  }
}
