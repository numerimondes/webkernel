<?php
namespace Webkernel\Core\Models\RBAC;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Permission extends Model
{
    protected $table = 'rbac_permissions';
    
    protected $fillable = [
        'policy_class', 
        'action', 
        'model_class', 
        'module', 
        'namespace',
        'display_name'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'rbac_role_permissions', 'permission_id', 'role_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'rbac_user_permissions', 'permission_id', 'user_id');
    }
    
    // Scopes
    public function scopeForModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }
    
    public function scopeForNamespace(Builder $query, string $namespace): Builder
    {
        return $query->where('namespace', $namespace);
    }
    
    public function scopeForModel(Builder $query, string $modelClass): Builder
    {
        return $query->where('model_class', $modelClass);
    }
    
    // Accessors
    public function getDisplayNameAttribute($value): string
    {
        if ($value) {
            return $value;
        }
        
        $modelName = class_basename($this->model_class);
        $actionName = ucfirst($this->action);
        
        return "{$actionName} {$modelName}";
    }
    
    public function getFullDisplayNameAttribute(): string
    {
        $parts = [$this->display_name];
        
        if ($this->module) {
            $parts[] = "({$this->module})";
        }
        
        if ($this->namespace) {
            $parts[] = "[{$this->namespace}]";
        }
        
        return implode(' ', $parts);
    }

    public static function canAccess(...$args)
    {
        \Log::warning('STATIC canAccess() called on Permission', [
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10)
        ]);
        return true;
    }
}