<?php declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ServiceType Model
 *
 * Represents a category of services (e.g., nautical, wellness).
 * Supports translations and dynamic fields.
 *
 * @property int $id
 * @property string $slug
 * @property string|null $icon
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class ServiceType extends Model
{
  protected $table = 'enjoy_tw_service_types';

  protected $fillable = ['slug', 'icon', 'is_active'];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  /**
   * Get all translations for this service type.
   *
   * @return HasMany
   */
  public function translations(): HasMany
  {
    return $this->hasMany(ServiceTypeTranslation::class);
  }

  /**
   * Get translation for a specific language.
   *
   * @param string $languageCode
   * @return ServiceTypeTranslation|null
   */
  public function translation(string $languageCode): ?ServiceTypeTranslation
  {
    return $this->translations()->where('language_code', $languageCode)->first();
  }

  /**
   * Get all dynamic fields for this service type.
   *
   * @return HasMany
   */
  public function fields(): HasMany
  {
    return $this->hasMany(ServiceField::class);
  }

  /**
   * Get all services of this type.
   *
   * @return HasMany
   */
  public function services(): HasMany
  {
    return $this->hasMany(Service::class);
  }

  /**
   * Get required fields for this service type.
   *
   * @return HasMany
   */
  public function requiredFields(): HasMany
  {
    return $this->fields()->where('is_required', true);
  }

  /**
   * Scope a query to only include active service types.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }
}
