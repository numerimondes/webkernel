<?php declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static \Illuminate\Database\Eloquent\Builder|\Webkernel\Aptitudes\AccessControl\Models\PanelAccessConfig where(...$parameters)
 * @property int $id
 * @property int $user_id
 * @property string $panel_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read User $user
 * @method static Builder<static>|PanelAccessConfig active()
 * @method static Builder<static>|PanelAccessConfig forPanel(string $panelId)
 * @method static Builder<static>|PanelAccessConfig forUser(int $userId)
 * @method static Builder<static>|PanelAccessConfig newModelQuery()
 * @method static Builder<static>|PanelAccessConfig newQuery()
 * @method static Builder<static>|PanelAccessConfig query()
 * @method static Builder<static>|PanelAccessConfig whereCreatedAt($value)
 * @method static Builder<static>|PanelAccessConfig whereId($value)
 * @method static Builder<static>|PanelAccessConfig whereIsActive($value)
 * @method static Builder<static>|PanelAccessConfig wherePanelId($value)
 * @method static Builder<static>|PanelAccessConfig whereUpdatedAt($value)
 * @method static Builder<static>|PanelAccessConfig whereUserId($value)
 * @mixin \Eloquent
 */
class PanelAccessConfig extends Model
{
  protected $table = 'users_priv_panel_access';

  /**
   * @var array<int, string>
   */
  protected $fillable = ['user_id', 'panel_id', 'is_active'];

  /**
   * @var array<string, string>
   */
  protected $casts = [
    'is_active' => 'boolean',
    'user_id' => 'integer',
  ];

  protected static function boot(): void
  {
    parent::boot();
  }

  /**
   * @return BelongsTo<User, PanelAccessConfig>
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeActive($query): Builder
  {
    return $query->where('is_active', true);
  }

  /**
   * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
   * @param int $userId
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeForUser($query, int $userId): Builder
  {
    return $query->where('user_id', $userId);
  }

  /**
   * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
   * @param string $panelId
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeForPanel($query, string $panelId): Builder
  {
    return $query->where('panel_id', $panelId);
  }
}
