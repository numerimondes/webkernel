<?php
declare(strict_types=1);

namespace Platform\Numerimondes\Server\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $avatar_url
 * @property array|null $settings
 * @property bool $is_active
 *
 * Query Builder Methods
 * @method static \Illuminate\Database\Eloquent\Builder<\Platform\Numerimondes\Server\Models\Organization> query()
 * @method static \Illuminate\Database\Eloquent\Builder<\Platform\Numerimondes\Server\Models\Organization> where(string|array|\Closure $column, mixed $operator = null, mixed $value = null)
 * @method static \Platform\Numerimondes\Server\Models\Organization|null find(int|string $id, array $columns = "[\'*\']")
 * @method static \Platform\Numerimondes\Server\Models\Organization findOrFail(int|string $id, array $columns = "[\'*\']")
 * @method static \Platform\Numerimondes\Server\Models\Organization create(array $attributes = '[]')
 * @method static \Platform\Numerimondes\Server\Models\Organization firstOrCreate(array $attributes, array $values = '[]')
 * @method static \Platform\Numerimondes\Server\Models\Organization updateOrCreate(array $attributes, array $values = '[]')
 * @method static \Illuminate\Database\Eloquent\Collection<int, \Platform\Numerimondes\Server\Models\Organization> all(array $columns = "[\'*\']")
 * @method static \Platform\Numerimondes\Server\Models\Organization|null first()
 * @method static \Platform\Numerimondes\Server\Models\Organization firstOrFail()
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization withoutTrashed()
 * @mixin \Eloquent
 */

use Webkernel\Aptitudes\Multitenancy\Contracts\HasTenantAccess as MultitenantAccess;

class Organization extends Model implements MultitenantAccess
{
  protected $fillable = ['name', 'slug', 'avatar_url', 'settings', 'is_active'];

  protected $casts = [
    'settings' => 'array',
    'is_active' => 'boolean',
  ];

  public function getTenantId(): string
  {
    return (string) $this->getKey();
  }

  public function getTenantType(): string
  {
    return 'organization';
  }

  public function getTenantName(): string
  {
    return $this->name;
  }

  public function canBeAccessedBy($user): bool
  {
    return $this->users()->whereKey($user->getKey())->exists();
  }

  public function users(): BelongsToMany
  {
    return $this->belongsToMany(User::class, 'organization_user');
  }
}
