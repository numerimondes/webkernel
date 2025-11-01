# User Model Extension System

## Overview

The User Model Extension System provides a modular architecture for extending Laravel's User model without modifying the core model file. This system automatically discovers and merges methods and relationships from module-specific `UserModel.php` files across all registered modules.

## Architecture

### Core Components

1. **MergeUserMethodsFromModules Trait**
   - Located: `webkernel/src/Aptitudes/Users/Traits/MergeUserMethodsFromModules.php`
   - Handles dynamic method/relationship discovery and routing
   - Manages caching and conflict resolution
   - Provides runtime method delegation to extension classes

2. **GenerateUserIdeHelperCommand**
   - Located: `webkernel/src/Aptitudes/Users/Commands/GenerateUserIdeHelperCommand.php`
   - Generates PHPDoc annotations for IDE autocomplete
   - Documents all methods, relationships, and conflicts
   - Creates backups and validates syntax

3. **App\Models\User**
   - Located: `app/Models/User.php`
   - Immutable proxy class that extends base User
   - Auto-generated PHPDoc for IDE support
   - Should never contain custom code

## How It Works

### Discovery Process

```
┌─────────────────────────────────────────────────────────┐
│ 1. Module Registration (via QueryModules)               │
│    - Scans all registered modules                       │
│    - Reads namespace, basePath, and priority            │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│ 2. UserModel Discovery                                  │
│    - Looks for {basePath}/User/UserModel.php            │
│    - Requires and reflects the class                    │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│ 3. Method Analysis                                      │
│    - Extracts all public methods                        │
│    - Detects relationships via return type              │
│    - Captures parameters and metadata                   │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│ 4. Conflict Resolution                                  │
│    - Priority-based resolution (higher wins)            │
│    - Alphabetical fallback for same priority            │
│    - Logs all conflicts for awareness                   │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│ 5. Caching                                              │
│    - Production: 3600s TTL                              │
│    - Development: 0s (disabled)                         │
└─────────────────────────────────────────────────────────┘
```

### Runtime Resolution

```
User Method Call → __call() → Check relationships first
                               ↓
                          Found? → Build relationship → Return Relation
                               ↓
                          No → Check methods
                               ↓
                          Found? → Get/create instance → Delegate call
                               ↓
                          No → parent::__call()
```

## Creating Module Extensions

### Basic Structure

```
YourModule/
├── User/
│   └── UserModel.php
└── ...
```

### Example: Simple Method Extension

```php
<?php
declare(strict_types=1);

namespace YourModule\User;

class UserModel
{
  private $user;

  /**
   * Set the user instance
   * This method is automatically called by the trait
   */
  public function setUser($user): void
  {
    $this->user = $user;
  }

  /**
   * Calculate user's total points
   *
   * @return int
   */
  public function getTotalPoints(): int
  {
    return $this->user->activities()->sum('points');
  }

  /**
   * Check if user is premium
   *
   * @return bool
   */
  public function isPremium(): bool
  {
    return $this->user->subscription_level === 'premium';
  }
}
```

Usage:
```php
$user = User::find(1);
$points = $user->getTotalPoints(); // Automatically delegated
$premium = $user->isPremium();
```

### Example: Relationship Extension

```php
<?php
declare(strict_types=1);

namespace YourModule\User;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use YourModule\Models\Post;
use YourModule\Models\Badge;

class UserModel
{
  private $user;

  public function setUser($user): void
  {
    $this->user = $user;
  }

  /**
   * User's posts relationship
   *
   * @return HasMany
   */
  public function posts(): HasMany
  {
    return $this->user->hasMany(Post::class);
  }

  /**
   * User's badges relationship
   *
   * @return BelongsToMany
   */
  public function badges(): BelongsToMany
  {
    return $this->user->belongsToMany(Badge::class)
      ->withTimestamps()
      ->withPivot('earned_at');
  }
}
```

Usage:
```php
$user = User::find(1);

// As method (returns Relation)
$postsQuery = $user->posts();

// As property (executes query)
$posts = $user->posts;

// Chain with query builder
$recentPosts = $user->posts()->where('created_at', '>', now()->subDays(7))->get();
```

## Filament Panel Integration

### Multi-Panel Tenant Logic

The system supports per-panel tenant implementations, allowing different tenant access logic for different Filament panels.

#### Example: Team Panel

```php
<?php
declare(strict_types=1);

namespace Platform\Teams\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Filament\Panel;

class UserModel
{
  private $user;

  public function setUser($user): void
  {
    $this->user = $user;
  }

  /**
   * Get teams for the teams panel
   *
   * @param Panel $panel
   * @return Collection
   */
  public function getTenants(Panel $panel): Collection
  {
    if ($panel->getId() === 'teams') {
      return $this->user->teams;
    }

    return collect();
  }

  /**
   * Check if user can access a team
   *
   * @param Model $tenant
   * @return bool
   */
  public function canAccessTenant(Model $tenant): bool
  {
    return $this->user->teams->contains($tenant);
  }
}
```

#### Example: Organization Panel

```php
<?php
declare(strict_types=1);

namespace Platform\Organizations\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Filament\Panel;

class UserModel
{
  private $user;

  public function setUser($user): void
  {
    $this->user = $user;
  }

  /**
   * Get organizations for the organizations panel
   *
   * @param Panel $panel
   * @return Collection
   */
  public function getTenants(Panel $panel): Collection
  {
    if ($panel->getId() === 'organizations') {
      return $this->user->organizations;
    }

    return collect();
  }

  /**
   * Check if user can access an organization
   *
   * @param Model $tenant
   * @return bool
   */
  public function canAccessTenant(Model $tenant): bool
  {
    return $this->user->organizations()
      ->where('organization_id', $tenant->id)
      ->where('status', 'active')
      ->exists();
  }
}
```

### Understanding Filament Conflicts

When multiple modules define `getTenants` or `canAccessTenant`, this creates a "conflict" that is resolved by module priority. This is **intentional and expected** for multi-panel architectures.

**Key Points:**
- Conflicts are NOT errors
- Priority determines which implementation is used
- Higher priority = winner
- Same priority = alphabetical module name wins
- All conflicts are logged for transparency

**Example Conflict Resolution:**
```
Platform\Teams (priority: 10) vs Platform\Organizations (priority: 5)
→ Winner: Platform\Teams

Platform\Accounting (priority: 10) vs Platform\Billing (priority: 10)
→ Winner: Platform\Accounting (alphabetical)
```

## Conflict Resolution

### Priority System

Modules can specify priority in their registration:

```php
QueryModules::register([
  'namespace' => 'CriticalModule',
  'basePath' => base_path('modules/critical'),
  'priority' => 100, // Highest priority
]);

QueryModules::register([
  'namespace' => 'StandardModule',
  'basePath' => base_path('modules/standard'),
  'priority' => 10, // Normal priority
]);
```

### Conflict Detection

The system automatically detects conflicts when:
- Multiple modules define the same method name
- Multiple modules define the same relationship name

### Resolution Rules

1. **Higher priority wins**
   ```
   ModuleA (priority: 20) vs ModuleB (priority: 10)
   → Winner: ModuleA
   ```

2. **Same priority: Alphabetical**
   ```
   ModuleA (priority: 10) vs ModuleB (priority: 10)
   → Winner: ModuleA
   ```

3. **All conflicts logged**
   ```php
   Log::warning('User model extension conflicts detected', [
     'conflicts' => [...],
   ]);
   ```

## IDE Helper Generation

### Command Usage

```bash
# Generate IDE helper
php artisan user:generate-ide-helper

# Show conflict report
php artisan user:generate-ide-helper --show-conflicts

# Skip backup creation
php artisan user:generate-ide-helper --no-backup
```

### What Gets Generated

The command generates comprehensive PHPDoc annotations including:

1. **Module Extension Methods**
   ```php
   /**
    * Methods from: YourModule
    * @method int getTotalPoints()
    * @method bool isPremium()
    */
   ```

2. **Module Extension Relationships**
   ```php
   /**
    * Relationships from: YourModule
    * @property-read HasMany $posts
    * @property-read BelongsToMany $badges
    */
   ```

3. **Eloquent Methods**
   ```php
   /**
    * Eloquent Model Methods
    * @method bool save(array $options = [])
    * @method bool update(array $attributes = [])
    * @method bool delete()
    */
   ```

4. **Query Builder Methods**
   ```php
   /**
    * Query Builder Methods
    * @method static \Illuminate\Database\Eloquent\Builder<\App\Models\User> query()
    * @method static \Illuminate\Database\Eloquent\Builder<\App\Models\User> where($column, $operator = null, $value = null)
    * @method static \App\Models\User|null find(int|string $id, array $columns = ['*'])
    */
   ```

5. **Filament Contract Methods**
   ```php
   /**
    * Filament Contract Methods
    * @method bool canAccessPanel(\Filament\Panel $panel)
    * @method \Illuminate\Support\Collection getTenants(\Filament\Panel $panel)
    * @method bool canAccessTenant(\Illuminate\Database\Eloquent\Model $tenant)
    */
   ```

6. **Conflict Warnings**
   ```php
   /**
    * WARNING: CONFLICTS DETECTED:
    * - methods 'getTenants': Platform\Teams, Platform\Organizations
    *   Using: Platform\Teams
    */
   ```

### Backup Management

- Backups stored in: `storage/basix/backups/AppModelsUser/`
- Timestamped format: `User.php.2025-10-26_143022`
- Automatic cleanup: keeps last 10 backups
- Skip with `--no-backup` flag

### Syntax Validation

Before writing, the command validates PHP syntax:
```bash
php -l generated_file.php
```

If validation fails, the operation is aborted and original file remains unchanged.

## Performance Considerations

### Caching Strategy

```php
// Production: 1 hour cache
$cacheTtl = app()->environment('production') ? 3600 : 0;

// Cache key versioning
$cacheKey = 'merged_user_extensions_v3';
```

### Clear Cache

```php
// Programmatically
User::clearExtensionCache();

// Or clear all cache
php artisan cache:clear
```

### Instance Management

Extension class instances are created lazily and reused:
```php
// First call: creates instance
$user->getTotalPoints();

// Subsequent calls: reuses instance
$user->isPremium();
```

## Debugging

### Check Available Extensions

```php
$extensions = User::availableUserExtensions();

dd($extensions);
// Output:
// [
//   'methods' => [...],
//   'relationships' => [...],
//   'conflicts' => [...],
//   'metadata' => [...]
// ]
```

### Check Specific Extension

```php
$info = User::getExtensionInfo('getTotalPoints');

dd($info);
// Output:
// [
//   'type' => 'method',
//   'class' => 'YourModule\User\UserModel',
//   'module' => 'YourModule',
//   'priority' => 10,
//   'file' => '/path/to/UserModel.php',
//   'line' => 25,
//   'parameters' => [],
//   'return_type' => 'int'
// ]
```

### Check if Extension Exists

```php
if ($user->hasUserExtension('getTotalPoints')) {
  $points = $user->getTotalPoints();
}
```

### View All Conflicts

```php
$conflicts = User::getConflicts();

foreach ($conflicts as $conflict) {
  echo "{$conflict['type']}: {$conflict['name']}\n";
  foreach ($conflict['sources'] as $source) {
    echo "  - {$source['module']} (priority: {$source['priority']})\n";
  }
}
```

## Best Practices

### 1. Always Use Type Hints

```php
// Good
public function getTotalPoints(): int
{
  return $this->user->activities()->sum('points');
}

// Bad (no return type)
public function getTotalPoints()
{
  return $this->user->activities()->sum('points');
}
```

### 2. Document Your Methods

```php
/**
 * Calculate user's lifetime value based on all purchases
 *
 * @return float Total value in USD
 */
public function getLifetimeValue(): float
{
  return $this->user->orders()->sum('total');
}
```

### 3. Use Relationship Return Types

```php
// Good (auto-detected as relationship)
public function posts(): HasMany
{
  return $this->user->hasMany(Post::class);
}

// Bad (won't be detected as relationship)
public function posts()
{
  return $this->user->hasMany(Post::class);
}
```

### 4. Implement setUser Method

```php
class UserModel
{
  private $user;

  // Required for context
  public function setUser($user): void
  {
    $this->user = $user;
  }

  // Your methods can now access $this->user
  public function someMethod(): mixed
  {
    return $this->user->someProperty;
  }
}
```

### 5. Handle Null Cases

```php
public function getActiveSubscription(): ?Subscription
{
  return $this->user->subscriptions()
    ->where('status', 'active')
    ->first();
}

public function hasActiveSubscription(): bool
{
  return $this->getActiveSubscription() !== null;
}
```

### 6. Use Module Priority Wisely

```php
// High priority for core functionality
'priority' => 100, // Core authentication module

// Normal priority for features
'priority' => 10,  // Standard feature modules

// Low priority for optional features
'priority' => 1,   // Optional/experimental modules
```

### 7. Namespace Consistency

```php
// Good: Follows convention
namespace YourModule\User;

// Bad: Different location
namespace YourModule\Models\Extensions;
```

### 8. Avoid State in Extension Classes

```php
// Good: Stateless
class UserModel
{
  private $user;

  public function getTotalPoints(): int
  {
    return $this->user->activities()->sum('points');
  }
}

// Bad: Maintaining state
class UserModel
{
  private $user;
  private $cachedPoints; // Avoid this

  public function getTotalPoints(): int
  {
    if (!$this->cachedPoints) {
      $this->cachedPoints = $this->user->activities()->sum('points');
    }
    return $this->cachedPoints;
  }
}
```

### 9. Regenerate IDE Helper After Changes

```bash
# After adding/modifying UserModel classes
php artisan user:generate-ide-helper

# Check for conflicts
php artisan user:generate-ide-helper --show-conflicts
```

### 10. Test Your Extensions

```php
use Tests\TestCase;
use App\Models\User;

class UserExtensionTest extends TestCase
{
  public function test_get_total_points_returns_integer()
  {
    $user = User::factory()->create();

    $points = $user->getTotalPoints();

    $this->assertIsInt($points);
  }

  public function test_posts_relationship_exists()
  {
    $user = User::factory()->create();

    $this->assertTrue($user->hasUserExtension('posts'));
  }
}
```

## Common Patterns

### Pattern 1: Aggregation Methods

```php
/**
 * Get user statistics
 *
 * @return array
 */
public function getStatistics(): array
{
  return [
    'total_posts' => $this->user->posts()->count(),
    'total_comments' => $this->user->comments()->count(),
    'total_likes' => $this->user->likes()->count(),
    'member_since' => $this->user->created_at->diffForHumans(),
  ];
}
```

### Pattern 2: Computed Properties

```php
/**
 * Get user's display name
 *
 * @return string
 */
public function getDisplayName(): string
{
  if ($this->user->nickname) {
    return $this->user->nickname;
  }

  return $this->user->first_name . ' ' . $this->user->last_name;
}

/**
 * Get user's initials
 *
 * @return string
 */
public function getInitials(): string
{
  $firstInitial = substr($this->user->first_name, 0, 1);
  $lastInitial = substr($this->user->last_name, 0, 1);

  return strtoupper($firstInitial . $lastInitial);
}
```

### Pattern 3: Business Logic Methods

```php
/**
 * Check if user can perform action
 *
 * @param string $action
 * @return bool
 */
public function can(string $action): bool
{
  return $this->user->permissions()
    ->where('action', $action)
    ->where('granted', true)
    ->exists();
}

/**
 * Grant permission to user
 *
 * @param string $action
 * @return void
 */
public function grantPermission(string $action): void
{
  $this->user->permissions()->updateOrCreate(
    ['action' => $action],
    ['granted' => true, 'granted_at' => now()]
  );
}
```

### Pattern 4: Query Scopes

```php
/**
 * Get active posts query
 *
 * @return \Illuminate\Database\Eloquent\Builder
 */
public function activePosts()
{
  return $this->user->posts()
    ->where('status', 'published')
    ->where('deleted_at', null);
}

/**
 * Get featured posts
 *
 * @return \Illuminate\Database\Eloquent\Collection
 */
public function getFeaturedPosts()
{
  return $this->user->posts()
    ->where('featured', true)
    ->orderBy('featured_at', 'desc')
    ->limit(5)
    ->get();
}
```

### Pattern 5: Conditional Relationships

```php
/**
 * Get user's team (if any)
 *
 * @return BelongsTo
 */
public function team(): BelongsTo
{
  return $this->user->belongsTo(Team::class);
}

/**
 * Check if user is in a team
 *
 * @return bool
 */
public function isInTeam(): bool
{
  return $this->user->team_id !== null;
}

/**
 * Get team members (including self)
 *
 * @return \Illuminate\Database\Eloquent\Collection
 */
public function getTeamMembers()
{
  if (!$this->isInTeam()) {
    return collect([$this->user]);
  }

  return $this->user->team->members;
}
```

## Advanced Use Cases

### Use Case 1: Multi-Tenant Scoping

```php
namespace Platform\Tenancy\User;

use Illuminate\Database\Eloquent\Builder;

class UserModel
{
  private $user;

  public function setUser($user): void
  {
    $this->user = $user;
  }

  /**
   * Scope query to current tenant
   *
   * @param Builder $query
   * @return Builder
   */
  public function scopeTenant(Builder $query): Builder
  {
    $tenantId = $this->user->current_tenant_id;

    return $query->where('tenant_id', $tenantId);
  }

  /**
   * Get resources accessible in current tenant
   *
   * @param string $resourceClass
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function getTenantResources(string $resourceClass)
  {
    return $resourceClass::query()
      ->where('tenant_id', $this->user->current_tenant_id)
      ->where('user_id', $this->user->id)
      ->get();
  }
}
```

### Use Case 2: Audit Trail

```php
namespace Platform\Audit\User;

use Platform\Audit\Models\AuditLog;

class UserModel
{
  private $user;

  public function setUser($user): void
  {
    $this->user = $user;
  }

  /**
   * Log user activity
   *
   * @param string $action
   * @param array $data
   * @return void
   */
  public function logActivity(string $action, array $data = []): void
  {
    AuditLog::create([
      'user_id' => $this->user->id,
      'action' => $action,
      'data' => $data,
      'ip_address' => request()->ip(),
      'user_agent' => request()->userAgent(),
      'created_at' => now(),
    ]);
  }

  /**
   * Get user's activity logs
   *
   * @return HasMany
   */
  public function activityLogs(): HasMany
  {
    return $this->user->hasMany(AuditLog::class);
  }
}
```

### Use Case 3: Notification Preferences

```php
namespace Platform\Notifications\User;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Platform\Notifications\Models\NotificationPreference;

class UserModel
{
  private $user;

  public function setUser($user): void
  {
    $this->user = $user;
  }

  /**
   * Notification preferences relationship
   *
   * @return HasOne
   */
  public function notificationPreferences(): HasOne
  {
    return $this->user->hasOne(NotificationPreference::class);
  }

  /**
   * Check if user wants email notifications
   *
   * @param string $type
   * @return bool
   */
  public function wantsEmailNotification(string $type): bool
  {
    $prefs = $this->user->notificationPreferences;

    if (!$prefs) {
      return true; // Default to enabled
    }

    return $prefs->{"email_{$type}"} ?? true;
  }

  /**
   * Update notification preference
   *
   * @param string $type
   * @param bool $enabled
   * @return void
   */
  public function setEmailNotification(string $type, bool $enabled): void
  {
    $this->user->notificationPreferences()->updateOrCreate(
      ['user_id' => $this->user->id],
      ["email_{$type}" => $enabled]
    );
  }
}
```

### Use Case 4: Role-Based Access

```php
namespace Platform\Roles\User;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Platform\Roles\Models\Role;

class UserModel
{
  private $user;

  public function setUser($user): void
  {
    $this->user = $user;
  }

  /**
   * User roles relationship
   *
   * @return BelongsToMany
   */
  public function roles(): BelongsToMany
  {
    return $this->user->belongsToMany(Role::class)
      ->withTimestamps();
  }

  /**
   * Check if user has role
   *
   * @param string $roleName
   * @return bool
   */
  public function hasRole(string $roleName): bool
  {
    return $this->user->roles()
      ->where('name', $roleName)
      ->exists();
  }

  /**
   * Check if user has any of the given roles
   *
   * @param array $roleNames
   * @return bool
   */
  public function hasAnyRole(array $roleNames): bool
  {
    return $this->user->roles()
      ->whereIn('name', $roleNames)
      ->exists();
  }

  /**
   * Assign role to user
   *
   * @param string $roleName
   * @return void
   */
  public function assignRole(string $roleName): void
  {
    $role = Role::where('name', $roleName)->firstOrFail();

    $this->user->roles()->syncWithoutDetaching([$role->id]);
  }
}
```

## Troubleshooting

### Problem: Method Not Found

**Symptom:**
```
BadMethodCallException: Method methodName does not exist
```

**Solutions:**
1. Check if UserModel.php exists in correct location
2. Verify method is public
3. Clear extension cache: `User::clearExtensionCache()`
4. Regenerate IDE helper: `php artisan user:generate-ide-helper`

### Problem: Wrong Method Being Called

**Symptom:** Unexpected behavior from a method

**Solutions:**
1. Check for conflicts: `php artisan user:generate-ide-helper --show-conflicts`
2. Review module priorities
3. Check conflict resolution logs in `storage/logs/laravel.log`

### Problem: IDE Not Recognizing Methods

**Symptom:** No autocomplete in IDE

**Solutions:**
1. Regenerate IDE helper: `php artisan user:generate-ide-helper`
2. Restart IDE
3. Rebuild IDE index (PhpStorm: File → Invalidate Caches)
4. Check that `app/Models/User.php` has PHPDoc annotations

### Problem: Relationship Not Working

**Symptom:**
```
Property [relationName] does not exist on this collection instance
```

**Solutions:**
1. Verify return type is correct: `public function posts(): HasMany`
2. Import relationship class: `use Illuminate\Database\Eloquent\Relations\HasMany;`
3. Check that relationship method returns a Relation instance
4. Clear cache and regenerate helper

### Problem: Extension Cache Not Clearing

**Symptom:** Changes not reflected after modifying UserModel

**Solutions:**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear

# Or programmatically
User::clearExtensionCache();

# Set development environment
APP_ENV=local  # Disables caching
```

### Problem: Syntax Error in Generated File

**Symptom:** Command fails with syntax validation error

**Solutions:**
1. Check for invalid characters in PHPDoc
2. Verify all UserModel classes are valid PHP
3. Check return type declarations
4. Review last backup before failed generation

## Performance Optimization

### 1. Eager Loading Extensions

```php
// Bad: N+1 queries
$users = User::all();
foreach ($users as $user) {
  $posts = $user->posts; // Query per user
}

// Good: Eager load
$users = User::with('posts')->get();
foreach ($users as $user) {
  $posts = $user->posts; // No additional query
}
```

### 2. Caching Results

```php
use Illuminate\Support\Facades\Cache;

class UserModel
{
  private $user;

  public function setUser($user): void
  {
    $this->user = $user;
  }

  public function getExpensiveCalculation(): float
  {
    $cacheKey = "user.{$this->user->id}.expensive_calc";

    return Cache::remember($cacheKey, 3600, function () {
      // Expensive calculation here
      return $this->user->orders()->sum('total') * 1.1;
    });
  }
}
```

### 3. Lazy Loading

```php
class UserModel
{
  private $user;
  private $statistics = null;

  public function setUser($user): void
  {
    $this->user = $user;
  }

  private function loadStatistics(): array
  {
    if ($this->statistics === null) {
      $this->statistics = [
        'posts' => $this->user->posts()->count(),
        'comments' => $this->user->comments()->count(),
      ];
    }

    return $this->statistics;
  }

  public function getTotalPosts(): int
  {
    return $this->loadStatistics()['posts'];
  }
}
```

## Migration Guide

### From Traits to UserModel

**Before (using traits):**
```php
namespace App\Models;

use YourModule\Traits\HasPosts;
use YourModule\Traits\HasBadges;

class User extends Authenticatable
{
  use HasPosts, HasBadges;
}
```

**After (using UserModel):**
```php
// app/Models/User.php remains clean
final class User extends \Webkernel\Aptitudes\Users\Models\User
{
  // Intentionally empty
}

// Create YourModule/User/UserModel.php
namespace YourModule\User;

class UserModel
{
  private $user;

  public function setUser($user): void
  {
    $this->user = $user;
  }

  // Move trait methods here
  public function posts(): HasMany
  {
    return $this->user->hasMany(Post::class);
  }

  public function badges(): BelongsToMany
  {
    return $this->user->belongsToMany(Badge::class);
  }
}
```

### Benefits of Migration

1. **Cleaner User Model:** No trait imports
2. **Better IDE Support:** Auto-generated PHPDoc
3. **Conflict Resolution:** Automatic priority handling
4. **Module Isolation:** Each module owns its extensions
5. **Dynamic Discovery:** No manual registration needed

## API Reference

### MergeUserMethodsFromModules

#### Methods

##### `availableUserExtensions(): array`
Returns all discovered extensions with metadata.

```php
$extensions = User::availableUserExtensions();
// Returns: ['methods' => [...], 'relationships' => [...], 'conflicts' => [...], 'metadata' => [...]]
```

##### `getExtensionInfo(string $name): ?array`
Get detailed information about a specific extension.

```php
$info = User::getExtensionInfo('getTotalPoints');
// Returns: ['type' => 'method', 'class' => '...', 'module' => '...', ...]
```

##### `hasUserExtension(string $name): bool`
Check if an extension exists.

```php
if ($user->hasUserExtension('posts')) {
  $posts = $user->posts;
}
```

##### `getConflicts(): array`
Get all detected conflicts.

```php
$conflicts = User::getConflicts();
// Returns: ['type:name' => ['name' => '...', 'type' => '...', 'sources' => [...]]]
```

##### `clearExtensionCache(): void`
Clear the extension cache.

```php
User::clearExtensionCache();
```

### GenerateUserIdeHelperCommand

#### Command Options

```bash
php artisan user:generate-ide-helper [options]

Options:
  --show-conflicts    Display detailed conflicts report
  --no-backup        Skip backup creation
```

#### Exit Codes

- `0` (SUCCESS): IDE helper generated successfully
- `1` (FAILURE): File not found or syntax validation failed

## Configuration

### Cache Settings

Edit `config/cache.php` if needed:

```php
'stores' => [
  'file' => [
    'driver' => 'file',
    'path' => storage_path('framework/cache/data'),
  ],
],

'default' => env('CACHE_DRIVER', 'file'),
```

### Module Registration

Modules should be registered via `QueryModules`:

```php
use Webkernel\Arcanes\QueryModules;

QueryModules::register([
  'namespace' => 'YourModule',
  'basePath' => base_path('modules/your-module'),
  'priority' => 10,
]);
```

## Security Considerations

### 1. Input Validation

```php
public function updateProfile(array $data): bool
{
  $validated = validator($data, [
    'bio' => 'string|max:500',
    'website' => 'url|nullable',
  ])->validate();

  return $this->user->update($validated);
}
```

### 2. Authorization Checks

```php
public function deletePost(Post $post): bool
{
  if ($this->user->id !== $post->user_id) {
    throw new AuthorizationException('Cannot delete post');
  }

  return $post->delete();
}
```

### 3. Avoid Exposing Sensitive Data

```php
// Bad
public function getSensitiveData(): array
{
  return [
    'password' => $this->user->password,
    'api_token' => $this->user->api_token,
  ];
}

// Good
public function getPublicProfile(): array
{
  return [
    'name' => $this->user->name,
    'bio' => $this->user->bio,
    'avatar' => $this->user->avatar_url,
  ];
}
```

## Conclusion

The User Model Extension System provides a powerful, modular approach to extending Laravel's User model. By following the patterns and best practices outlined in this documentation, you can:

- Keep your User model clean and maintainable
- Organize functionality by module
- Avoid conflicts through priority-based resolution
- Maintain excellent IDE support through auto-generated PHPDoc
- Scale your application architecture without technical debt

For questions or issues, please refer to the troubleshooting section or contact the development team.

---

**Version:** 3.0
**Last Updated:** October 26, 2025
**Author:** El Moumen Yassine, Numerimondes
