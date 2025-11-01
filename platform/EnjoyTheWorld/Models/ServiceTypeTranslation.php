<?php declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ServiceTypeTranslation Model
 *
 * Handles multilingual names and descriptions for service types.
 *
 * @property int $id
 * @property int $service_type_id
 * @property string $language_code
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class ServiceTypeTranslation extends Model
{
  protected $table = 'enjoy_tw_service_type_translations';

  protected $fillable = ['service_type_id', 'language_code', 'name', 'description'];

  /**
   * Get the service type that owns the translation.
   *
   * @return BelongsTo
   */
  public function serviceType(): BelongsTo
  {
    return $this->belongsTo(ServiceType::class);
  }
}
