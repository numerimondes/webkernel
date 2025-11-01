<?php declare(strict_types=1);

namespace App\Models;

/**
 * Application User Model Proxy
 *
 * Strict, immutable wrapper around Webkernel\Aptitudes\Users\Models\User.
 * Automatically inherits methods and relationships from all User extension
 * models across registered modules.
 *
 * DO NOT MODIFY: Changes will break authentication, Filament integration,
 * and access control. All logic resides in the base Webkernel implementation
 * and dynamically loaded extension models.
 *
 * To extend the User model, create {YourModule}/User/UserModel.php.
 * See: webkernel/src/Aptitudes/Users/Traits/MergeUserMethodsFromModules.md
 *
 * Generation Info:
 * - Scanned: 22 modules
 * - Loaded: 2 UserModel files
 * - Timestamp: 2025-10-30T15:39:16+00:00
 *
 *
 * Methods from: Platform\EnjoyTheWorld
 * @method void setUser(App\Models\User $user)
 * @method bool EnjoyTW_isProvider()
 * @method bool EnjoyTW_isActiveProvider()
 * @method int EnjoyTW_activeServicesCount()
 * @method int EnjoyTW_featuredServicesCount()
 * @method Platform\EnjoyTheWorld\Models\Provider EnjoyTW_createOrUpdateProvider(array $data)
 * @method bool EnjoyTW_activateProvider()
 * @method bool EnjoyTW_deactivateProvider()
 * @method Illuminate\Support\Collection EnjoyTW_getActiveServices()
 * @method Illuminate\Support\Collection EnjoyTW_getFeaturedServices()
 *
 * Methods from: Platform\Numerimondes\Server
 * @method bool isOrganizationAdmin(?Platform\Numerimondes\Server\Models\Organization $organization = null)
 * @method ?Platform\Numerimondes\Server\Models\Organization currentOrganization()
 * @method Illuminate\Support\Collection getTenants(Filament\Panel $panel)
 * @method bool canAccessTenant(Illuminate\Database\Eloquent\Model $tenant)
 *
 * Relationships from: Platform\EnjoyTheWorld
 * @property-read Illuminate\Database\Eloquent\Relations\HasOne $EnjoyTW_provider
 * @property-read Illuminate\Database\Eloquent\Relations\HasManyThrough $EnjoyTW_services
 *
 * Relationships from: Platform\Numerimondes\Server
 * @property-read Illuminate\Database\Eloquent\Relations\BelongsToMany $organizations
 *
 * Eloquent Model Methods
 * @method mixed save(array $options = [])
 * @method mixed update(array $attributes = [], array $options = [])
 * @method mixed delete()
 * @method mixed forceDelete()
 * @method mixed fresh(mixed $with = [])
 * @method mixed refresh()
 * @method mixed fill(array $attributes)
 * @method mixed forceFill(array $attributes)
 * @method mixed replicate(?array $except = null)
 * @method mixed is(mixed $model)
 * @method mixed isNot(mixed $model)
 * @method mixed getKey()
 * @method mixed getKeyName()
 * @method mixed getKeyType()
 * @method mixed getIncrementing()
 * @method mixed getAttribute(mixed $key)
 * @method mixed setAttribute(mixed $key, mixed $value)
 * @method mixed getOriginal(mixed $key = null, mixed $default = null)
 * @method mixed getDirty()
 * @method mixed getChanges()
 * @method mixed isDirty(mixed $attributes = null)
 * @method mixed isClean(mixed $attributes = null)
 * @method mixed wasChanged(mixed $attributes = null)
 * @method mixed touch(mixed $attribute = null)
 * @method mixed push()
 * @method mixed toArray()
 * @method mixed toJson(mixed $options = 0)
 * @method mixed jsonSerialize()
 * @method mixed getTable()
 * @method mixed getConnectionName()
 * @method mixed setConnection(mixed $name)
 * @method mixed getAttributes()
 * @method mixed setRawAttributes(array $attributes, mixed $sync = false)
 * @method mixed syncOriginal()
 *
 * Query Builder Methods
 * @method static \Illuminate\Database\Eloquent\Builder<\App\Models\User> query()
 * @method static \Illuminate\Database\Eloquent\Builder<\App\Models\User> where(string|array|\Closure $column, mixed $operator = null, mixed $value = null)
 * @method static \App\Models\User|null find(int|string $id, array $columns = "[\'*\']")
 * @method static \App\Models\User findOrFail(int|string $id, array $columns = "[\'*\']")
 * @method static \App\Models\User create(array $attributes = '[]')
 * @method static \App\Models\User firstOrCreate(array $attributes, array $values = '[]')
 * @method static \App\Models\User updateOrCreate(array $attributes, array $values = '[]')
 * @method static \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> all(array $columns = "[\'*\']")
 * @method static \App\Models\User|null first()
 * @method static \App\Models\User firstOrFail()
 *
 * Filament Contract Methods
 * @method bool canAccessPanel(\Filament\Panel $panel)
 * @method \Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model> getTenants(\Filament\Panel $panel)
 * @method bool canAccessTenant(\Illuminate\Database\Eloquent\Model $tenant)
 *
 * HasTenancy Trait Methods
 * @method \Illuminate\Database\Eloquent\Model|null currentTenant()
 *
 * WARNING: CONFLICTS DETECTED:
 *
 * - methods 'setUser': Platform\EnjoyTheWorld, Platform\Numerimondes\Server
 *   Using: Platform\EnjoyTheWorld
 *
 * NOTE: Conflicts are resolved by priority. Higher priority wins.
 * For Filament panel methods (getTenants, canAccessTenant), this is expected
 * when multiple panels are registered with different tenant logic.
 *
 * @return \App\Models\User For PHPActor and Intelephense chaining support
 *
 * @author El Moumen Yassine, Numerimondes
 */
final class User extends \Webkernel\Aptitudes\Users\Models\User
{
  // This class is intentionally empty.
  // All functionality is inherited and auto-merged from module-defined UserModel classes.
  // Do not add methods or properties here.
}
