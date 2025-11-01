<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Multitenancy;

use App\Models\User;
use Filament\Panel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use Webkernel\Aptitudes\Multitenancy\Contracts\HasTenantAccess;

/**
 * TenantResolver
 *
 * Automatically discovers tenant relationships from UserModel extensions
 */
class TenantResolver
{
  /**
   * Resolve all tenants for user
   *
   * @param User $user
   * @param Panel|null $panel
   * @return Collection<int, HasTenantAccess>
   */
  public static function resolveFor(User $user, ?Panel $panel = null): Collection
  {
    $relationships = static::discoverTenantRelationships();

    return collect($relationships)
      ->flatMap(fn($method) => static::callRelationship($user, $method))
      ->filter(fn($item) => $item instanceof HasTenantAccess)
      ->values();
  }

  /**
   * Discover tenant relationships from UserModel extensions
   *
   * @return array<int, string>
   */
  protected static function discoverTenantRelationships(): array
  {
    return Cache::remember('tenant_relationships', 3600, function () {
      if (!class_exists(User::class)) {
        return [];
      }

      if (!method_exists(User::class, 'availableUserExtensions')) {
        return [];
      }

      $extensions = User::availableUserExtensions();
      $relationships = [];

      foreach ($extensions['relationships'] ?? [] as $name => $info) {
        if (static::isTenantRelationship($name, $info)) {
          $relationships[] = $name;
        }
      }

      return $relationships;
    });
  }

  /**
   * Check if relationship returns HasTenantAccess models
   *
   * @param array<string, mixed> $info
   * @return bool
   */
  protected static function isTenantRelationship(string $name, array $info): bool
  {
    $returnType = $info['return_type'] ?? null;

    if (!$returnType || !is_string($returnType)) {
      return false;
    }

    // Match any Eloquent relation with a generic model type
    if (preg_match('/(?:BelongsToMany|HasMany|HasOne|MorphToMany|MorphMany|MorphOne)<(.+?)>/', $returnType, $matches)) {
      $modelClass = trim($matches[1]);

      if (class_exists($modelClass)) {
        try {
          $reflection = new \ReflectionClass($modelClass);
          return $reflection->implementsInterface(HasTenantAccess::class);
        } catch (\ReflectionException $e) {
          Log::warning("Failed to reflect on {$modelClass}: {$e->getMessage()}");
          return false;
        }
      }
    }

    return false;
  }

  /**
   * Call relationship method on user
   *
   * @param User $user
   * @param string $method
   * @return Collection<int, mixed>
   */
  protected static function callRelationship(User $user, string $method): Collection
  {
    try {
      $result = $user->__get($method);
      return $result instanceof Collection ? $result : collect([$result])->filter();
    } catch (\Throwable $e) {
      Log::debug("Failed to call relationship {$method}: {$e->getMessage()}");
      return collect();
    }
  }

  /**
   * Clear tenant resolution cache
   *
   * @return void
   */
  public static function clearCache(): void
  {
    Cache::forget('tenant_relationships');
  }
}
