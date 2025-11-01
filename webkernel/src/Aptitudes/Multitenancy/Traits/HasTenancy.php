<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Multitenancy\Traits;

use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Webkernel\Aptitudes\Multitenancy\TenantResolver;
use Webkernel\Aptitudes\Multitenancy\Contracts\HasTenantAccess;

/**
 * HasTenancy Trait
 *
 * Provides automatic tenant discovery and access control.
 * Works with MergeUserMethodsFromModules to discover tenant relationships.
 */
trait HasTenancy
{
  /**
   * Check if user can access given tenant
   *
   * @param Model $tenant
   * @return bool
   */
  public function canAccessTenant(Model $tenant): bool
  {
    if (!$tenant instanceof HasTenantAccess) {
      return false;
    }

    return $tenant->canBeAccessedBy($this);
  }

  /**
   * Get all tenants for user across all modules
   *
   * @param Panel|null $panel
   * @return Collection<int, Model&HasTenantAccess>
   */
  public function getTenants(?Panel $panel = null): Collection
  {
    return TenantResolver::resolveFor($this, $panel);
  }

  /**
   * Get current active tenant from Filament
   *
   * @return Model|null
   */
  public function currentTenant(): ?Model
  {
    if (!function_exists('filament')) {
      return null;
    }

    $filament = filament();

    /**
     * @method Model|null getTenant()
     */
    $filament;

    return $filament->getTenant();
  }
}
