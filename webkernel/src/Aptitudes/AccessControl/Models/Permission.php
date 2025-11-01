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
 * Permission Model
 * 
 * Represents a single permission in the system
 *
 * @property int $id
 * @property string|null $module
 * @property string $name
 * @property string $action
 * @property string $model_class
 * @property string|null $description
 * @property bool $is_system
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\AccessControl\Models\AuditLog> $auditLogs
 * @property-read int|null $audit_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Webkernel\Aptitudes\AccessControl\Models\PermissionGroup> $permissionGroups
 * @property-read int|null $permission_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereIsSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereModelClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Permission extends Model
{
  protected $table = 'users_priv_permissions';

  protected $fillable = ['module', 'name', 'action', 'model_class', 'description', 'is_system'];

  protected $casts = [
    'is_system' => 'boolean',
  ];

  /**
   * Get the permission groups that have this permission
   */
  public function permissionGroups(): BelongsToMany
  {
    return $this->belongsToMany(
      PermissionGroup::class,
      'users_priv_permission_group_permission',
      'permission_id',
      'permission_group_id',
    )
      ->withPivot('is_excluded')
      ->withTimestamps();
  }

  /**
   * Get the users that have this permission directly
   */
  public function users(): BelongsToMany
  {
    return $this->belongsToMany(
      config('auth.providers.users.model', \App\Models\User::class),
      'users_priv_permission_user',
      'permission_id',
      'user_id',
    )
      ->withPivot(['is_granted', 'assigned_at', 'assigned_by', 'expires_at', 'reason'])
      ->withTimestamps();
  }

  /**
   * Get audit logs for this permission
   */
  public function auditLogs(): MorphMany
  {
    return $this->morphMany(AuditLog::class, 'auditable');
  }

  /**
   * Create a permission from model class and action
   */
  public static function createFromModel(string $modelClass, string $action, ?string $module = null): self
  {
    $className = class_basename($modelClass);
    $name = "{$action}_{$className}";

    return self::firstOrCreate(
      ['name' => $name],
      [
        'module' => $module ?? self::detectModule($modelClass),
        'action' => $action,
        'model_class' => $modelClass,
        'description' => ucfirst($action) . ' ' . $className,
      ],
    );
  }

  /**
   * Detect module from model namespace
   */
  protected static function detectModule(string $modelClass): string
  {
    // Extract module from namespace
    // e.g., App\Models\User -> App
    // e.g., Webkernel\I18n\Models\Translation -> I18n
    $parts = explode('\\', $modelClass);

    if (count($parts) > 1) {
      if ($parts[0] === 'App') {
        return 'App';
      }
      if ($parts[0] === 'Webkernel' && isset($parts[1])) {
        return $parts[1];
      }
    }

    return 'System';
  }
}
