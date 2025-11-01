<?php
declare(strict_types=1);

namespace Platform\Numerimondes\Server\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Platform\Numerimondes\Server\Models\Organization;
use Filament\Panel;

/**
 * Numerimondes User Extension
 *
 * Automatically discovered and merged into User model via MergeUserMethodsFromModules
 */
class UserModel
{
  protected User $user;

  /**
   * Set user instance
   *
   * @param User $user
   * @return void
   */
  public function setUser(User $user): void
  {
    $this->user = $user;
  }

  /**
   * User organizations
   *
   * @return BelongsToMany
   */
  public function organizations(): BelongsToMany
  {
    return $this->user
      ->belongsToMany(Organization::class, 'organization_user', 'user_id', 'organization_id')
      ->withTimestamps();
  }

  /**
   * Check if user is admin of organization
   *
   * @param Organization|null $organization
   * @return bool
   */
  public function isOrganizationAdmin(?Organization $organization = null): bool
  {
    $org = $organization ?? $this->currentOrganization();

    if (!$org) {
      return false;
    }

    return $this->organizations()->where('organization_id', $org->id)->wherePivot('role', 'admin')->exists();
  }

  /**
   * Get current active organization from Filament
   *
   * @return Organization|null
   */
  public function currentOrganization(): ?Organization
  {
    if (!function_exists('filament')) {
      return null;
    }

    $tenant = filament()->getTenant();

    return $tenant instanceof Organization ? $tenant : null;
  }

  /**
   * Get tenant list for Filament panel
   *
   * @param Panel $panel
   * @return Collection
   */
  public function getTenants(Panel $panel): Collection
  {
    return $this->organizations()->get();
  }

  /**
   * Check if user can access given tenant
   *
   * @param Model $tenant
   * @return bool
   */
  public function canAccessTenant(Model $tenant): bool
  {
    return $this->organizations()->whereKey($tenant->getKey())->exists();
  }
}
