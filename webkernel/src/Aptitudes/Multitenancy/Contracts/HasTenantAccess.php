<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Multitenancy\Contracts;

/**
 * Contract for tenant models
 *
 * Any model implementing this interface can be used as a tenant
 * in Filament panels without additional configuration.
 */
interface HasTenantAccess
{
  /**
   * Get unique tenant identifier
   *
   * @return string
   */
  public function getTenantId(): string;

  /**
   * Get tenant type for polymorphic support
   *
   * @return string
   */
  public function getTenantType(): string;

  /**
   * Get tenant display name
   *
   * @return string
   */
  public function getTenantName(): string;

  /**
   * Check if user can access this tenant
   *
   * @param mixed $user
   * @return bool
   */
  public function canBeAccessedBy($user): bool;
}
