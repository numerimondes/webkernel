<?php declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ServiceTranslation Model
 *
 * Manages multilingual titles and descriptions for services.
 *
 * @property int $id
 * @property int $service_id
 * @property string $language_code
 * @property string $title
 * @property string $description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class ServiceTranslation extends Model
{
  protected $table = 'enjoy_tw_service_translations';

  protected $fillable = ['service_id', 'language_code', 'title', 'description'];

  /**
   * Get the service that owns the translation.
   *
   * @return BelongsTo
   */
  public function service(): BelongsTo
  {
    return $this->belongsTo(Service::class);
  }
}
