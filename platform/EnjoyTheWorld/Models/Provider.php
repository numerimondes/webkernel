<?php declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Provider Model
 *
 * Represents a service provider in the EnjoyTheWorld platform.
 * Providers are linked to users and can offer multiple services.
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $company_name
 * @property string|null $phone
 * @property string|null $website
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Provider extends Model
{
  use SoftDeletes;

  protected $table = 'enjoy_tw_providers';

  protected $fillable = ['user_id', 'company_name', 'phone', 'website', 'is_active'];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  /**
   * Get the user that owns the provider.
   *
   * @return BelongsTo
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Get all services offered by this provider.
   *
   * @return HasMany
   */
  public function services(): HasMany
  {
    return $this->hasMany(Service::class);
  }

  /**
   * Get only active services for this provider.
   *
   * @return HasMany
   */
  public function activeServices(): HasMany
  {
    return $this->services()->where('is_active', true);
  }

  /**
   * Scope a query to only include active providers.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }
}
