<?php
namespace Webkernel\Core\Models\RBAC;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Role extends Model
{
    protected $table = 'rbac_roles';
    
    protected $fillable = [
        'name', 
        'module', 
        'namespace', 
        'description', 
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'rbac_role_permissions', 'role_id', 'permission_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'rbac_user_roles', 'role_id', 'user_id');
    }
    
    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
    
    public function scopeForModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }
    
    public function scopeForNamespace(Builder $query, string $namespace): Builder
    {
        return $query->where('namespace', $namespace);
    }
    
    // Accessors
    public function getFullNameAttribute(): string
    {
        $parts = [$this->name];
        
        if ($this->module) {
            $parts[] = "({$this->module})";
        }
        
        if ($this->namespace) {
            $parts[] = "[{$this->namespace}]";
        }
        
        return implode(' ', $parts);
    }
    
    // Methods
    public function assignPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }
    
    public function hasPermission(string $policyClass, string $action): bool
    {
        return $this->permissions()
            ->where('policy_class', $policyClass)
            ->where('action', $action)
            ->exists();
    }
}