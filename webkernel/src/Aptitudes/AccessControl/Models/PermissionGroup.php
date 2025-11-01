<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Permission Group Model (formerly Role)
 * 
 * Represents a group of permissions that can be assigned to users
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property bool $is_system
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\AccessControl\Models\AuditLog> $auditLogs
 * @property-read int|null $audit_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\AccessControl\Models\Permission> $grantedPermissions
 * @property-read int|null $granted_permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\AccessControl\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereIsSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PermissionGroup extends Model
{
  protected $table = 'users_priv_permission_groups';

  protected $fillable = ['name', 'slug', 'description', 'is_system', 'priority'];

  protected $casts = [
    'is_system' => 'boolean',
    'priority' => 'integer',
  ];

  /**
   * Get the permissions for this group
   */
  public function permissions(): BelongsToMany
  {
    return $this->belongsToMany(
      Permission::class,
      'users_priv_permission_group_permission',
      'permission_group_id',
      'permission_id',
    )
      ->withPivot('is_excluded')
      ->withTimestamps();
  }

  /**
   * Get only granted permissions (not excluded)
   */
  public function grantedPermissions(): BelongsToMany
  {
    return $this->permissions()->wherePivot('is_excluded', false);
  }

  /**
   * Get the users that belong to this group
   */
  public function users(): BelongsToMany
  {
    return $this->belongsToMany(
      config('auth.providers.users.model', \App\Models\User::class),
      'users_priv_permission_group_user',
      'permission_group_id',
      'user_id',
    )
      ->withPivot(['assigned_at', 'assigned_by', 'expires_at'])
      ->withTimestamps();
  }

  /**
   * Get audit logs for this permission group
   */
  public function auditLogs(): MorphMany
  {
    return $this->morphMany(AuditLog::class, 'auditable');
  }

  /**
   * Check if this group has a specific permission
   */
  public function hasPermission(string $permissionName): bool
  {
    return $this->grantedPermissions()->where('name', $permissionName)->exists();
  }

  /**
   * Grant a permission to this group
   */
  public function grantPermission(Permission $permission): void
  {
    $this->permissions()->syncWithoutDetaching([
      $permission->id => ['is_excluded' => false],
    ]);
  }

  /**
   * Revoke a permission from this group
   */
  public function revokePermission(Permission $permission): void
  {
    $this->permissions()->syncWithoutDetaching([
      $permission->id => ['is_excluded' => true],
    ]);
  }
}
