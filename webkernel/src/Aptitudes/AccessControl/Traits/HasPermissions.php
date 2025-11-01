<?php declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Webkernel\Aptitudes\AccessControl\Models\Permission;
use Webkernel\Aptitudes\AccessControl\Models\PermissionGroup;
use Webkernel\Aptitudes\AccessControl\Models\Role;
use Webkernel\Aptitudes\AccessControl\Models\AuditLog;

/**
 * User Permission Trait
 *
 * Add this trait to your User model to enable permission checking
 */
trait HasPermissions
{
  /**
   * Get the permission groups for this user
   */
  public function permissionGroups(): BelongsToMany
  {
    return $this->belongsToMany(
      PermissionGroup::class,
      'users_priv_permission_group_user',
      'user_id',
      'permission_group_id',
    )
      ->withPivot(['assigned_at', 'assigned_by', 'expires_at'])
      ->withTimestamps();
  }

  /**
   * Get direct permissions for this user
   */
  public function directPermissions(): BelongsToMany
  {
    return $this->belongsToMany(Permission::class, 'users_priv_permission_user', 'user_id', 'permission_id')
      ->withPivot(['is_granted', 'assigned_at', 'assigned_by', 'expires_at', 'reason'])
      ->withTimestamps();
  }

  /**
   * Get all permissions for this user (from groups and direct)
   */
  public function getAllPermissions(): Collection
  {
    $cacheKey = "user_permissions_{$this->id}";

    return Cache::remember($cacheKey, 300, function () {
      $groupPermissions = $this->permissionGroups()
        ->with('grantedPermissions')
        ->get()
        ->pluck('grantedPermissions')
        ->flatten();

      $directGrants = $this->directPermissions()
        ->wherePivot('is_granted', true)
        ->wherePivotNull('expires_at')
        ->orWherePivot('expires_at', '>', now())
        ->get();

      $directRevokes = $this->directPermissions()
        ->wherePivot('is_granted', false)
        ->wherePivotNull('expires_at')
        ->orWherePivot('expires_at', '>', now())
        ->get()
        ->pluck('name');

      return $groupPermissions
        ->concat($directGrants)
        ->unique('id')
        ->reject(fn($permission) => $directRevokes->contains($permission->name));
    });
  }

  /**
   * Check if user has a specific permission
   */
  public function hasPermission(string $permission): bool
  {
    // Check for superadmin permission group
    if ($this->isSuperAdmin()) {
      return true;
    }

    return $this->getAllPermissions()->pluck('name')->contains($permission);
  }

  /**
   * Check if user is super admin
   */
  public function isSuperAdmin(): bool
  {
    return $this->permissionGroups()->where('slug', 'superadmin')->exists();
  }

  /**
   * Assign a permission group to this user
   */
  public function assignPermissionGroup(PermissionGroup $group, ?string $reason = null): void
  {
    $this->permissionGroups()->attach($group->id, [
      'assigned_at' => now(),
      'assigned_by' => auth()->id(),
    ]);

    Cache::forget("user_permissions_{$this->id}");

    AuditLog::log('permission_group_assigned', $this, $this->id, null, ['permission_group' => $group->name], $reason);
  }

  /**
   * Remove a permission group from this user
   */
  public function removePermissionGroup(PermissionGroup $group, ?string $reason = null): void
  {
    $this->permissionGroups()->detach($group->id);

    Cache::forget("user_permissions_{$this->id}");

    AuditLog::log('permission_group_removed', $this, $this->id, ['permission_group' => $group->name], null, $reason);
  }

  /**
   * Grant a direct permission to this user
   */
  public function grantPermission(Permission $permission, ?string $reason = null, ?\DateTime $expiresAt = null): void
  {
    $this->directPermissions()->syncWithoutDetaching([
      $permission->id => [
        'is_granted' => true,
        'assigned_at' => now(),
        'assigned_by' => auth()->id(),
        'expires_at' => $expiresAt,
        'reason' => $reason,
      ],
    ]);

    Cache::forget("user_permissions_{$this->id}");

    AuditLog::log('permission_granted', $this, $this->id, null, ['permission' => $permission->name], $reason);
  }

  /**
   * Revoke a direct permission from this user
   */
  public function revokePermission(Permission $permission, ?string $reason = null): void
  {
    $this->directPermissions()->syncWithoutDetaching([
      $permission->id => [
        'is_granted' => false,
        'assigned_at' => now(),
        'assigned_by' => auth()->id(),
        'reason' => $reason,
      ],
    ]);

    Cache::forget("user_permissions_{$this->id}");

    AuditLog::log('permission_revoked', $this, $this->id, ['permission' => $permission->name], null, $reason);
  }

  /**
   * Clear permission cache for this user
   */
  public function clearPermissionCache(): void
  {
    Cache::forget("user_permissions_{$this->id}");
  }
}
