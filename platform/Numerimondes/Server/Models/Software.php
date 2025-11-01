<?php
declare(strict_types=1);

namespace Platform\Numerimondes\Server\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Platform\Numerimondes\Server\Models\{Organization, SoftwareCore};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string $slug
 * @property string $namespace
 * @property bool $is_active
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * Query Builder Methods
 * @method static \Illuminate\Database\Eloquent\Builder<static> query()
 * @method static \Illuminate\Database\Eloquent\Builder<static> where(string|array|\Closure $column, mixed $operator = null, mixed $value = null)
 * @method static static|null find(int|string $id, array $columns = ['*'])
 * @method static static findOrFail(int|string $id, array $columns = ['*'])
 * @method static static create(array $attributes = [])
 * @method static static firstOrCreate(array $attributes, array $values = [])
 * @method static static updateOrCreate(array $attributes, array $values = [])
 * @method static \Illuminate\Database\Eloquent\Collection<int, static> all(array $columns = ['*'])
 * @method static static|null first()
 * @method static static firstOrFail()
 * @method static \Illuminate\Database\Eloquent\Builder<static> newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static> newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static> onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static> withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static> withoutTrashed()
 * @mixin \Eloquent
 */
class Software extends Model
{
  protected $table = 'softwares';
  protected $fillable = ['organization_id', 'name', 'slug', 'namespace', 'is_active', 'created_by', 'updated_by'];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  /**
   * Boot method to handle model events
   */
  protected static function boot(): void
  {
    parent::boot();

    static::creating(function (self $model): void {
      if (Auth::check()) {
        $model->created_by = Auth::id();
        $model->updated_by = Auth::id();
      }
    });

    static::updating(function (self $model): void {
      if (Auth::check()) {
        $model->updated_by = Auth::id();
      }
    });
  }

  /**
   * Organization owning this software
   *
   * @return BelongsTo<Organization, $this>
   */
  public function organization(): BelongsTo
  {
    return $this->belongsTo(Organization::class);
  }

  /**
   * User who created this software
   *
   * @return BelongsTo<\App\Models\User, $this>
   */
  public function creator(): BelongsTo
  {
    return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
  }

  /**
   * User who last updated this software
   *
   * @return BelongsTo<\App\Models\User, $this>
   */
  public function updater(): BelongsTo
  {
    return $this->belongsTo(config('auth.providers.users.model'), 'updated_by');
  }

  /**
   * Core softwares associated with this software
   *
   * @return HasMany<SoftwareCore>
   */
  public function coreSoftwares(): HasMany
  {
    return $this->hasMany(SoftwareCore::class, 'software_id');
  }

  /**
   * Scope to active software only
   *
   * @param Builder<Software> $query
   * @return Builder<Software>
   */
  public function scopeActive(Builder $query): Builder
  {
    return $query->where('is_active', true);
  }

  /**
   * Scope to specific organization
   *
   * @param Builder<Software> $query
   * @param int|string $organizationId
   * @return Builder<Software>
   */
  public function scopeForOrganization(Builder $query, int|string $organizationId): Builder
  {
    return $query->where('organization_id', $organizationId);
  }

  /**
   * Check if software is active
   */
  public function isActive(): bool
  {
    return DB::table('softwares')->where('id', $this->id)->where('is_active', true)->exists();
  }

  /**
   * Check if software belongs to given organization
   *
   * @param int|string $organizationId
   */
  public function belongsToOrganization(int|string $organizationId): bool
  {
    return DB::table('softwares')->where('id', $this->id)->where('organization_id', $organizationId)->exists();
  }

  /**
   * Get organization owning this software
   */
  public function getOrganization(): ?Organization
  {
    return Organization::find($this->organization_id);
  }

  /**
   * Get user who created this software
   */
  public function getCreator(): ?User
  {
    return User::find($this->created_by);
  }

  /**
   * Get user who last updated this software
   */
  public function getUpdater(): ?User
  {
    return User::find($this->updated_by);
  }
}
