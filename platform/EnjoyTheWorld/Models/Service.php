<?php declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Service Model
 *
 * Represents a service offer without exposing provider information publicly.
 *
 * @property int $id
 * @property int $provider_id
 * @property int $service_type_id
 * @property float $price
 * @property string|null $duration
 * @property string|null $location
 * @property bool $is_active
 * @property bool $is_featured
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Service extends Model
{
  use SoftDeletes;

  protected $table = 'enjoy_tw_services';

  protected $fillable = ['provider_id', 'service_type_id', 'price', 'duration', 'location', 'is_active', 'is_featured'];

  protected $casts = [
    'price' => 'decimal:2',
    'is_active' => 'boolean',
    'is_featured' => 'boolean',
  ];

  /**
   * Get the provider that owns the service.
   *
   * @return BelongsTo
   */
  public function provider(): BelongsTo
  {
    return $this->belongsTo(Provider::class);
  }

  /**
   * Get the service type.
   *
   * @return BelongsTo
   */
  public function serviceType(): BelongsTo
  {
    return $this->belongsTo(ServiceType::class);
  }

  /**
   * Get all translations for this service.
   *
   * @return HasMany
   */
  public function translations(): HasMany
  {
    return $this->hasMany(ServiceTranslation::class);
  }

  /**
   * Get translation for a specific language.
   *
   * @param string $languageCode
   * @return ServiceTranslation|null
   */
  public function translation(string $languageCode): ?ServiceTranslation
  {
    return $this->translations()->where('language_code', $languageCode)->first();
  }

  /**
   * Get all media items for this service.
   *
   * @return HasMany
   */
  public function media(): HasMany
  {
    return $this->hasMany(Media::class);
  }

  /**
   * Get only images for this service.
   *
   * @return HasMany
   */
  public function images(): HasMany
  {
    return $this->media()->where('type', 'image');
  }

  /**
   * Get only videos for this service.
   *
   * @return HasMany
   */
  public function videos(): HasMany
  {
    return $this->media()->where('type', 'video');
  }

  /**
   * Get all field values for this service.
   *
   * @return HasMany
   */
  public function fieldValues(): HasMany
  {
    return $this->hasMany(ServiceFieldValue::class);
  }

  /**
   * Get a specific field value by key.
   *
   * @param string $fieldKey
   * @return ServiceFieldValue|null
   */
  public function getFieldValue(string $fieldKey): ?ServiceFieldValue
  {
    return $this->fieldValues()->where('field_key', $fieldKey)->first();
  }

  /**
   * Scope a query to only include active services.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }

  /**
   * Scope a query to only include featured services.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeFeatured($query)
  {
    return $query->where('is_featured', true);
  }

  /**
   * Scope a query to filter by service type.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param int $serviceTypeId
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOfType($query, int $serviceTypeId)
  {
    return $query->where('service_type_id', $serviceTypeId);
  }
}
