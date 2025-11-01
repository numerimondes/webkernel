<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Logic\Resources;

use Illuminate\Database\Eloquent\Model;
use Webkernel\Aptitudes\AccessControl\Models\Permission;

/**
 * Dynamic policy for access control
 *
 * This policy controls access to resources and models by delegating
 * permission checks to the user's permissions.
 */
class AccessControlPolicy
{
  /**
   * Determine if the user can view any models of the given class.
   *
   * @param  mixed  $user
   * @param  string|null  $modelClassFQCN
   * @return bool
   */
  public function viewAny($user, $modelClassFQCN = null): bool
  {
    if (!$user || !method_exists($user, 'hasPermission')) {
      return false;
    }

    if ($modelClassFQCN === null) {
      return false;
    }

    $permission = $this->buildPermissionName('viewAny', $modelClassFQCN);
    return $user->hasPermission($permission);
  }

  /**
   * Determine if the user can view the given model instance.
   *
   * @param  mixed  $user
   * @param  Model  $model
   * @return bool
   */
  public function view($user, Model $model): bool
  {
    if (!$user || !method_exists($user, 'hasPermission')) {
      return false;
    }

    $permission = $this->buildPermissionName('view', get_class($model));
    return $user->hasPermission($permission);
  }

  /**
   * Determine if the user can create models of the given class.
   *
   * @param  mixed  $user
   * @param  string|null  $modelClassFQCN
   * @return bool
   */
  public function create($user, $modelClassFQCN = null): bool
  {
    if (!$user || !method_exists($user, 'hasPermission')) {
      return false;
    }

    if ($modelClassFQCN === null) {
      return false;
    }

    $permission = $this->buildPermissionName('create', $modelClassFQCN);
    return $user->hasPermission($permission);
  }

  /**
   * Determine if the user can update the given model instance.
   *
   * @param  mixed  $user
   * @param  Model  $model
   * @return bool
   */
  public function update($user, Model $model): bool
  {
    if (!$user || !method_exists($user, 'hasPermission')) {
      return false;
    }

    $permission = $this->buildPermissionName('update', get_class($model));
    return $user->hasPermission($permission);
  }

  /**
   * Determine if the user can delete the given model instance.
   *
   * @param  mixed  $user
   * @param  Model  $model
   * @return bool
   */
  public function delete($user, Model $model): bool
  {
    if (!$user || !method_exists($user, 'hasPermission')) {
      return false;
    }

    $permission = $this->buildPermissionName('delete', get_class($model));
    return $user->hasPermission($permission);
  }

  /**
   * Determine if the user can delete any models of the given class.
   *
   * @param  mixed  $user
   * @param  string|null  $modelClassFQCN
   * @return bool
   */
  public function deleteAny($user, $modelClassFQCN = null): bool
  {
    if (!$user || !method_exists($user, 'hasPermission')) {
      return false;
    }

    if ($modelClassFQCN === null) {
      return false;
    }

    $permission = $this->buildPermissionName('deleteAny', $modelClassFQCN);
    return $user->hasPermission($permission);
  }

  /**
   * Determine if the user can restore the given model instance.
   *
   * @param  mixed  $user
   * @param  Model  $model
   * @return bool
   */
  public function restore($user, Model $model): bool
  {
    if (!$user || !method_exists($user, 'hasPermission')) {
      return false;
    }

    $permission = $this->buildPermissionName('restore', get_class($model));
    return $user->hasPermission($permission);
  }

  /**
   * Determine if the user can restore any models of the given class.
   *
   * @param  mixed  $user
   * @param  string|null  $modelClassFQCN
   * @return bool
   */
  public function restoreAny($user, $modelClassFQCN = null): bool
  {
    if (!$user || !method_exists($user, 'hasPermission')) {
      return false;
    }

    if ($modelClassFQCN === null) {
      return false;
    }

    $permission = $this->buildPermissionName('restoreAny', $modelClassFQCN);
    return $user->hasPermission($permission);
  }

  /**
   * Determine if the user can force delete the given model instance.
   *
   * @param  mixed  $user
   * @param  Model  $model
   * @return bool
   */
  public function forceDelete($user, Model $model): bool
  {
    if (!$user || !method_exists($user, 'hasPermission')) {
      return false;
    }

    $permission = $this->buildPermissionName('forceDelete', get_class($model));
    return $user->hasPermission($permission);
  }

  /**
   * Determine if the user can force delete any models of the given class.
   *
   * @param  mixed  $user
   * @param  string|null  $modelClassFQCN
   * @return bool
   */
  public function forceDeleteAny($user, $modelClassFQCN = null): bool
  {
    if (!$user || !method_exists($user, 'hasPermission')) {
      return false;
    }

    if ($modelClassFQCN === null) {
      return false;
    }

    $permission = $this->buildPermissionName('forceDeleteAny', $modelClassFQCN);
    return $user->hasPermission($permission);
  }

  /**
   * Determine if the user can replicate the given model instance.
   *
   * @param  mixed  $user
   * @param  Model  $model
   * @return bool
   */
  public function replicate($user, Model $model): bool
  {
    if (!$user || !method_exists($user, 'hasPermission')) {
      return false;
    }

    $permission = $this->buildPermissionName('replicate', get_class($model));
    return $user->hasPermission($permission);
  }

  /**
   * Determine if the user can reorder models of the given class.
   *
   * @param  mixed  $user
   * @param  string|null  $modelClassFQCN
   * @return bool
   */
  public function reorder($user, $modelClassFQCN = null): bool
  {
    if (!$user || !method_exists($user, 'hasPermission')) {
      return false;
    }

    if ($modelClassFQCN === null) {
      return false;
    }

    $permission = $this->buildPermissionName('reorder', $modelClassFQCN);
    return $user->hasPermission($permission);
  }

  /**
   * Before authorization check hook.
   *
   * This method is called before any other authorization checks.
   * It allows for overriding authorization logic for specific users or roles.
   * In this case, we grant all permissions to users with the 'Super Administrator' group.
   *
   * @param mixed $user
   * @param string $ability
   * @return bool|null
   */
  public function before($user, string $ability): bool|null
  {
    if (!$user || !method_exists($user, 'permissionGroups')) {
      return null;
    }

    foreach ($user->permissionGroups()->get() as $group) {
      if ($group->name === 'Super Administrator') {
        return true;
      }
    }

    return null;
  }

  /**
   * Build permission name from action and model class
   *
   * @param  string  $action
   * @param  string  $modelClassFQCN
   * @return string
   */
  protected function buildPermissionName(string $action, string $modelClassFQCN): string
  {
    $className = class_basename($modelClassFQCN);
    return "{$action}_{$className}";
  }

  /**
   * Auto-discover and create permissions for all registered models
   *
   * This method scans all Filament resources and creates permissions
   * for each model and action combination.
   */
  public static function discoverAndCreatePermissions(): void
  {
    $actions = [
      'viewAny',
      'view',
      'create',
      'update',
      'delete',
      'deleteAny',
      'restore',
      'restoreAny',
      'forceDelete',
      'forceDeleteAny',
      'replicate',
      'reorder',
    ];

    // Get all Filament panels
    $panels = \Filament\Facades\Filament::getPanels();

    foreach ($panels as $panel) {
      $resources = $panel->getResources();

      foreach ($resources as $resourceClass) {
        $model = $resourceClass::getModel();

        if (!$model || !class_exists($model)) {
          continue;
        }

        // Detect module from resource namespace
        $module = self::detectModuleFromResource($resourceClass);

        foreach ($actions as $action) {
          Permission::createFromModel($model, $action, $module);
        }
      }
    }
  }

  /**
   * Detect module from resource class namespace
   *
   * @param  string  $resourceClass
   * @return string
   */
  protected static function detectModuleFromResource(string $resourceClass): string
  {
    $namespace = explode('\\', $resourceClass);

    // Check for Filament resources pattern
    if (in_array('Filament', $namespace)) {
      $moduleIndex = array_search('Filament', $namespace);
      if ($moduleIndex > 0 && isset($namespace[$moduleIndex - 1])) {
        return $namespace[$moduleIndex - 1];
      }
    }

    // Default detection from first namespace part
    if (count($namespace) > 1) {
      if ($namespace[0] === 'App') {
        return 'App';
      }
      if ($namespace[0] === 'Webkernel' && isset($namespace[1])) {
        return $namespace[1];
      }
    }

    return 'System';
  }
}
