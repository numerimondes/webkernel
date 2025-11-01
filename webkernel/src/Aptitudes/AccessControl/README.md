# Access Control System

A simple, intuitive, and maintainable permission management system for Laravel/Filament applications without external dependencies like Spatie.

## Features

- **Permission Groups** (formerly roles) - Organize permissions into logical groups
- **Direct Permissions** - Grant/revoke specific permissions to users
- **Dynamic Policy Registration** - Automatically registers policies for all Filament resources
- **Audit Logging** - Track all permission changes
- **Module-based Organization** - Permissions are organized by module
- **Superadmin Support** - Built-in superadmin permission group with full access
- **Cache Management** - Automatic cache clearing for performance
- **Filament Integration** - Complete admin interface for permission management

## Installation

### 1. Add the Service Provider

Register the service provider in your application:

```php
// config/app.php or your module service provider
'providers' => [
    // ...
    \Webkernel\Aptitudes\AccessControl\Providers\AccessControlServiceProvider::class,
],
```

### 2. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `users_priv_permission_groups` - Permission groups (roles)
- `users_priv_permissions` - Individual permissions
- `users_priv_permission_group_user` - User to permission group assignments
- `users_priv_permission_group_permission` - Permission group to permission assignments
- `users_priv_permission_user` - Direct user permissions
- `users_priv_audit_logs` - Audit trail for all changes

### 3. Add Trait to User Model

Add the `HasPermissions` trait to your User model:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Webkernel\Aptitudes\AccessControl\Models\HasPermissions;

class User extends Authenticatable
{
    use HasPermissions;
    
    // ... rest of your model
}
```

### 4. Sync Permissions

After installation, sync permissions from your Filament resources:

```bash
php artisan access:sync-permissions
```

This command discovers all your Filament resources and creates the corresponding permissions.

### 5. Create Superadmin

Create a superadmin permission group and optionally assign it to a user:

```bash
# Create superadmin group only
php artisan access:create-superadmin

# Create and assign to user by email
php artisan access:create-superadmin --user=admin@example.com

# Create and assign to user by ID
php artisan access:create-superadmin --user=1
```

## Usage

### In Filament Admin Panel

The system provides two main interfaces:

1. **Access Control Resource** - Manage permission groups and their permissions
   - Create/edit/delete permission groups
   - Assign permissions to groups
   - View users in each group
   - Sync permissions from resources

2. **User Permissions Action** - Add to your existing User resource:

```php
use Webkernel\Aptitudes\AccessControl\Filament\Actions\ManageUserPermissionsAction;

// In your UserResource table() method:
->actions([
    ManageUserPermissionsAction::make(),
    // ... other actions
])
```

### Programmatic Usage

#### Check Permissions

```php
// Check if user has a permission
if ($user->hasPermission('update_Post')) {
    // User can update posts
}

// Check if user is superadmin
if ($user->isSuperAdmin()) {
    // User has full access
}

// Get all user permissions
$permissions = $user->getAllPermissions();
```

#### Manage Permission Groups

```php
// Assign a permission group to user
$group = PermissionGroup::where('slug', 'editor')->first();
$user->assignPermissionGroup($group, 'Promoted to editor');

// Remove a permission group
$user->removePermissionGroup($group, 'Demoted from editor');

// Check if user has a specific group
$hasGroup = $user->permissionGroups()
    ->where('slug', 'editor')
    ->exists();
```

#### Direct Permissions

```php
// Grant a direct permission
$permission = Permission::where('name', 'delete_Post')->first();
$user->grantPermission($permission, 'Special access granted', $expiresAt);

// Revoke a direct permission
$user->revokePermission($permission, 'Access revoked due to policy');
```

#### Create Custom Permissions

```php
use Webkernel\Aptitudes\AccessControl\Models\Permission;

// Create a permission manually
$permission = Permission::create([
    'module' => 'Blog',
    'name' => 'moderate_comments',
    'action' => 'moderate',
    'model_class' => 'App\\Models\\Comment',
    'description' => 'Moderate blog comments'
]);

// Or use the helper method
$permission = Permission::createFromModel(
    'App\\Models\\Comment',
    'moderate',
    'Blog'
);
```

## Artisan Commands

### Sync Permissions
```bash
# Sync permissions from all Filament resources
php artisan access:sync-permissions

# Delete existing non-system permissions and sync fresh
php artisan access:sync-permissions --fresh
```

### List Permission Groups
```bash
# List all permission groups
php artisan access:list-groups

# Include permissions for each group
php artisan access:list-groups --with-permissions
```

### Assign Permission Group
```bash
# Assign by user email and group slug
php artisan access:assign-group admin@example.com editor

# Assign by user ID and group ID
php artisan access:assign-group 1 2
```

### Check User Permissions
```bash
# Check by user email
php artisan access:check-user admin@example.com

# Check by user ID
php artisan access:check-user 1
```

## Permission Naming Convention

Permissions follow a consistent naming pattern:
- Format: `{action}_{ModelName}`
- Examples:
  - `viewAny_Post`
  - `create_User`
  - `update_Category`
  - `delete_Comment`

## Policy Integration

The system automatically registers dynamic policies for all Filament resources. The `AccessControlPolicy` class handles all authorization checks based on user permissions.

### Supported Actions

- `viewAny` - View list of models
- `view` - View specific model
- `create` - Create new model
- `update` - Update existing model
- `delete` - Delete model
- `deleteAny` - Bulk delete models
- `restore` - Restore soft-deleted model
- `restoreAny` - Bulk restore models
- `forceDelete` - Permanently delete model
- `forceDeleteAny` - Bulk permanent delete
- `replicate` - Duplicate model
- `reorder` - Reorder models

## Module Organization

Permissions are automatically organized by module based on the namespace:
- `App\Models\*` → Module: `App`
- `Webkernel\Blog\Models\*` → Module: `Blog`
- `Webkernel\I18n\Models\*` → Module: `I18n`

## Audit Logging

All permission changes are automatically logged in `users_priv_audit_logs`:

```php
// Manual audit log entry
use Webkernel\Aptitudes\AccessControl\Models\AuditLog;

AuditLog::log(
    'custom_event',
    $model,
    $userId,
    ['old' => 'value'],
    ['new' => 'value'],
    'Reason for change'
);
```

## Cache Management

User permissions are cached for 5 minutes for performance. Cache is automatically cleared when:
- Permission groups are assigned/removed
- Direct permissions are granted/revoked
- Permission group permissions are modified

Manual cache clearing:
```php
$user->clearPermissionCache();
```

## Security Features

1. **System Permissions/Groups** - Cannot be deleted via UI
2. **Priority System** - Higher priority groups override lower ones
3. **Expiring Permissions** - Set expiration dates on direct permissions
4. **Permission Exclusion** - Exclude specific permissions from groups
5. **Audit Trail** - Complete history of all permission changes

## Best Practices

1. **Use Permission Groups** - Organize related permissions into groups rather than assigning individual permissions
2. **Minimal Direct Permissions** - Use direct permissions only for exceptions
3. **Regular Sync** - Run `access:sync-permissions` after adding new resources
4. **Document Reasons** - Always provide reasons when granting/revoking permissions
5. **Review Audit Logs** - Regularly review permission changes for security

## Troubleshooting

### User doesn't have expected permissions
1. Check if the HasPermissions trait is added to the User model
2. Clear permission cache: `$user->clearPermissionCache()`
3. Verify permission exists: `Permission::where('name', 'permission_name')->exists()`
4. Check audit logs for recent changes

### Permissions not appearing after adding new resource
Run `php artisan access:sync-permissions` to discover new resources

### Cannot delete permission group
Check if it's marked as `is_system`. System groups cannot be deleted.

## License

This package is proprietary software.