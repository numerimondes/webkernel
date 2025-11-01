<?php declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ServiceFieldValue Model
 *
 * Stores specific field values for each service based on its type.
 *
 * @property int $id
 * @property int $service_id
 * @property string $field_key
 * @property string|null $value
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class ServiceFieldValue extends Model
{
  protected $table = 'enjoy_tw_service_field_values';

  protected $fillable = ['service_id', 'field_key', 'value'];

  /**
   * Get the service that owns the field value.
   *
   * @return BelongsTo
   */
  public function service(): BelongsTo
  {
    return $this->belongsTo(Service::class);
  }

  /**
   * Get the field definition for this value.
   *
   * @return ServiceField|null
   */
  public function getFieldDefinition(): ?ServiceField
  {
    return ServiceField::where('field_key', $this->field_key)
      ->whereHas('serviceType', function ($query) {
        $query->whereHas('services', function ($subQuery) {
          $subQuery->where('id', $this->service_id);
        });
      })
      ->first();
  }
}
