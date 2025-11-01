<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkernel\Aptitudes\Users\Models\UserExtensions\UserPreference;

/**
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $offset
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $carbon_timezone
 * @property-read mixed $formatted_display_name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereOffset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserPreference> $userPreferences
 * @property-read int|null $user_preferences_count
 * @mixin \Eloquent
 */
class Timezone extends Model
{
  protected $table = 'apt_timezones';

  /**
   * The attributes that are mass assignable.
   */
  protected $fillable = ['name', 'display_name', 'offset', 'is_active'];

  /**
   * The attributes that should be cast.
   */
  protected $casts = [
    'is_active' => 'boolean',
  ];

  /**
   * Get the user preferences that use this timezone.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany<UserPreference, Timezone>
   */
  public function userPreferences(): HasMany
  {
    return $this->hasMany(UserPreference::class, 'timezone_id');
  }

  /**
   * Scope a query to only include active timezones.
   */
  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }

  /**
   * Get the timezone offset as a Carbon timezone.
   */
  public function getCarbonTimezoneAttribute()
  {
    return \Carbon\Carbon::now($this->name)->getTimezone();
  }

  /**
   * Get a formatted display name with offset.
   */
  public function getFormattedDisplayNameAttribute()
  {
    return "{$this->display_name} ({$this->offset})";
  }
}
