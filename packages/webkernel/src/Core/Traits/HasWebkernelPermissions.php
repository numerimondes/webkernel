<?php
namespace Webkernel\Core\Traits;

use Webkernel\Core\Models\Role;
use Webkernel\Core\Models\Permission;
use Webkernel\Core\Models\UserPanels;
use Webkernel\Core\Models\PlatformOwner;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

trait HasWebkernelPermissions
{
    // Relations existantes
    public function userPanels(): HasOne
    {
        return $this->hasOne(UserPanels::class);
    }

    public function platformOwners()
    {
        return $this->hasMany(PlatformOwner::class);
    }

    // Nouvelles relations RBAC
    public function webkernelRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'rbac_user_roles', 'user_id', 'role_id')
            ->withTimestamps();
    }

    public function webkernelPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'rbac_user_permissions', 'user_id', 'permission_id')
            ->withTimestamps();
    }

    // Méthode principale pour vérifier les permissions
    public function hasWebkernelPermission(string $policyClass, string $action): bool
    {
        // 1. Vérifier si c'est un platform owner
        if ($this->platformOwners()->exists()) {
            return true;
        }

        // 2. Vérifier les permissions directes
        if ($this->webkernelPermissions()
            ->where('policy_class', $policyClass)
            ->where('action', $action)
            ->exists()) {
            return true;
        }

        // 3. Vérifier via les rôles actifs
        return $this->webkernelRoles()
            ->active()
            ->whereHas('permissions', function($q) use ($policyClass, $action) {
                $q->where('policy_class', $policyClass)
                  ->where('action', $action);
            })
            ->exists();
    }

    // Méthodes de gestion des rôles
    public function assignRole($role): void
    {
        $roleId = $role instanceof Role ? $role->id : $role;
        $this->webkernelRoles()->syncWithoutDetaching([$roleId]);
    }

    public function assignRoles(array $roles): void
    {
        $roleIds = collect($roles)->map(function($role) {
            return $role instanceof Role ? $role->id : $role;
        })->toArray();
        
        $this->webkernelRoles()->syncWithoutDetaching($roleIds);
    }

    public function removeRole($role): void
    {
        $roleId = $role instanceof Role ? $role->id : $role;
        $this->webkernelRoles()->detach($roleId);
    }

    public function syncRoles(array $roles): void
    {
        $roleIds = collect($roles)->map(function($role) {
            return $role instanceof Role ? $role->id : $role;
        })->toArray();
        
        $this->webkernelRoles()->sync($roleIds);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->webkernelRoles()
            ->where('name', $roleName)
            ->exists();
    }

    public function hasAnyRole(array $roleNames): bool
    {
        return $this->webkernelRoles()
            ->whereIn('name', $roleNames)
            ->exists();
    }

    // Méthodes de gestion des permissions directes
    public function assignPermission($permission): void
    {
        $permissionId = $permission instanceof Permission ? $permission->id : $permission;
        $this->webkernelPermissions()->syncWithoutDetaching([$permissionId]);
    }

    public function assignPermissions(array $permissions): void
    {
        $permissionIds = collect($permissions)->map(function($permission) {
            return $permission instanceof Permission ? $permission->id : $permission;
        })->toArray();
        
        $this->webkernelPermissions()->syncWithoutDetaching($permissionIds);
    }

    public function removePermission($permission): void
    {
        $permissionId = $permission instanceof Permission ? $permission->id : $permission;
        $this->webkernelPermissions()->detach($permissionId);
    }

    public function syncPermissions(array $permissions): void
    {
        $permissionIds = collect($permissions)->map(function($permission) {
            return $permission instanceof Permission ? $permission->id : $permission;
        })->toArray();
        
        $this->webkernelPermissions()->sync($permissionIds);
    }

    // Méthodes utilitaires
    public function getAllPermissions(): Collection
    {
        $directPermissions = $this->webkernelPermissions;
        
        $rolePermissions = $this->webkernelRoles()
            ->active()
            ->with('permissions')
            ->get()
            ->flatMap(function($role) {
                return $role->permissions;
            });

        return $directPermissions->merge($rolePermissions)->unique('id');
    }

    public function getPermissionsByModule(string $module): Collection
    {
        return $this->getAllPermissions()
            ->where('module', $module);
    }

    public function canAccessModule(string $module): bool
    {
        return $this->getAllPermissions()
            ->where('module', $module)
            ->isNotEmpty();
    }

    public function getAccessibleModules(): array
    {
        return $this->getAllPermissions()
            ->whereNotNull('module')
            ->pluck('module')
            ->unique()
            ->values()
            ->toArray();
    }
}
