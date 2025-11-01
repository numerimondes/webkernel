<?php declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ServiceField Model
 *
 * Defines dynamic fields per service type (e.g., duration, location).
 *
 * @property int $id
 * @property int $service_type_id
 * @property string $field_key
 * @property string $field_label
 * @property string $field_type
 * @property bool $is_required
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class ServiceField extends Model
{
  protected $table = 'enjoy_tw_service_fields';

  protected $fillable = ['service_type_id', 'field_key', 'field_label', 'field_type', 'is_required'];

  protected $casts = [
    'is_required' => 'boolean',
  ];

  /**
   * Get the service type that owns this field.
   *
   * @return BelongsTo
   */
  public function serviceType(): BelongsTo
  {
    return $this->belongsTo(ServiceType::class);
  }

  /**
   * Get all field values for this field.
   *
   * @return HasMany
   */
  public function fieldValues(): HasMany
  {
    return $this->hasMany(ServiceFieldValue::class, 'field_key', 'field_key');
  }

  /**
   * Scope a query to only include required fields.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeRequired($query)
  {
    return $query->where('is_required', true);
  }
}
