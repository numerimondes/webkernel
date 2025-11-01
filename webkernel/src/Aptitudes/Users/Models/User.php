<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Webkernel\Aptitudes\Users\Traits\MergeUserMethodsFromModules;
use Webkernel\Aptitudes\Multitenancy\Traits\HasTenancy;
use Webkernel\Aptitudes\AccessControl\Traits\HasPermissions;

/**
 * Base User Model
 *
 * Extended via App\Models\User proxy
 * Dynamically merges methods from module UserModel extensions
 *
 * All Eloquent, Builder, and trait methods are inherited naturally.
 * IDE support is provided via generated PHPDoc in App\Models\User.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\AccessControl\Models\Permission> $directPermissions
 * @property-read int|null $direct_permissions_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\AccessControl\Models\PermissionGroup> $permissionGroups
 * @property-read int|null $permission_groups_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements FilamentUser, HasTenants
{
  use HasFactory;
  use Notifiable;
  use HasPermissions;
  use MergeUserMethodsFromModules;
  use HasTenancy;

  /**
   * The table associated with the model
   *
   * @var string
   */
  protected $table = 'users';

  /**
   * The attributes that are mass assignable
   *
   * @var array<int, string>
   */
  protected $fillable = ['name', 'email', 'password'];

  /**
   * The attributes that should be hidden for serialization
   *
   * @var array<int, string>
   */
  protected $hidden = ['password', 'remember_token'];

  /**
   * Get the attributes that should be cast
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  /**
   * Check if user can access panel
   *
   * Delegates to AccessControl logic
   *
   * @param \Filament\Panel $panel
   * @return bool
   */
  public function canAccessPanel(\Filament\Panel $panel): bool
  {
    return \Webkernel\Aptitudes\AccessControl\Logic\Panels\CanAccessPanel::canAccessPanel($panel, $this);
  }
}
