<?php declare(strict_types=1);

namespace Webkernel\Arcanes\Support\Resources;

use Illuminate\Support\Facades\Gate;
use ReflectionClass;
use ReflectionMethod;

/**
 * Extracts real permissions from resource classes
 * Uses multiple sources: can* methods, policies, and model traits
 */
class PermissionExtractor
{
  private array $extractionLog = [];
  private bool $enableLogging;

  /**
   * Create a new permission extractor
   *
   * @param bool $enableLogging Enable debug logging
   */
  public function __construct(bool $enableLogging = false)
  {
    $this->enableLogging = $enableLogging;
  }

  /**
   * Extract permissions from resource class
   * Priority: can* methods > policy methods > default permissions
   *
   * @param string $resourceClass
   * @return array<int, string>
   */
  public function extractPermissions(string $resourceClass): array
  {
    if (!class_exists($resourceClass)) {
      $this->log("error_{$resourceClass}", ['error' => 'class_not_found']);
      return [];
    }

    try {
      $reflection = new ReflectionClass($resourceClass);

      $canMethodPermissions = $this->extractFromCanMethods($reflection);
      if (!empty($canMethodPermissions)) {
        $this->log("success_{$resourceClass}", [
          'source' => 'can_methods',
          'permissions' => $canMethodPermissions,
        ]);
        return $canMethodPermissions;
      }

      if (method_exists($resourceClass, 'getModel')) {
        $modelClass = $resourceClass::getModel();

        $policyPermissions = $this->extractFromPolicy($modelClass);
        if (!empty($policyPermissions)) {
          $this->log("success_{$resourceClass}", [
            'source' => 'policy',
            'model' => $modelClass,
            'permissions' => $policyPermissions,
          ]);
          return $policyPermissions;
        }

        $defaultPermissions = $this->getDefaultPermissions($modelClass);
        $this->log("success_{$resourceClass}", [
          'source' => 'default_with_traits',
          'model' => $modelClass,
          'permissions' => $defaultPermissions,
        ]);
        return $defaultPermissions;
      }

      $basicPermissions = $this->getBasicPermissions();
      $this->log("success_{$resourceClass}", [
        'source' => 'basic_default',
        'permissions' => $basicPermissions,
      ]);
      return $basicPermissions;
    } catch (\Exception $e) {
      $this->log("error_{$resourceClass}", [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      return $this->getBasicPermissions();
    }
  }

  /**
   * Extract permissions from can* methods in resource class
   *
   * @param ReflectionClass $reflection
   * @return array<int, string>
   */
  private function extractFromCanMethods(ReflectionClass $reflection): array
  {
    $permissions = [];
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC);

    foreach ($methods as $method) {
      $methodName = $method->getName();

      if (preg_match('/^can([A-Z].+)$/', $methodName, $matches)) {
        $permission = lcfirst($matches[1]);
        $permissions[] = $permission;
      }
    }

    return array_unique($permissions);
  }

  /**
   * Extract permissions from model policy
   *
   * @param string $modelClass
   * @return array<int, string>
   */
  private function extractFromPolicy(string $modelClass): array
  {
    if (!class_exists($modelClass)) {
      return [];
    }

    try {
      $policy = Gate::getPolicyFor($modelClass);

      if (!$policy) {
        return [];
      }

      $reflection = new ReflectionClass($policy);
      $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
      $permissions = [];

      foreach ($methods as $method) {
        $methodName = $method->getName();

        if ($this->isValidPolicyMethod($methodName)) {
          $permissions[] = $methodName;
        }
      }

      return array_unique($permissions);
    } catch (\Exception $e) {
      $this->log("policy_error_{$modelClass}", [
        'error' => $e->getMessage(),
      ]);
      return [];
    }
  }

  /**
   * Check if method name is a valid policy method
   *
   * @param string $methodName
   * @return bool
   */
  private function isValidPolicyMethod(string $methodName): bool
  {
    if (str_starts_with($methodName, '__')) {
      return false;
    }

    $excludedMethods = ['before', 'after'];
    return !in_array($methodName, $excludedMethods, true);
  }

  /**
   * Get default permissions based on model traits
   *
   * @param string $modelClass
   * @return array<int, string>
   */
  private function getDefaultPermissions(string $modelClass): array
  {
    $permissions = $this->getBasicPermissions();

    if (!class_exists($modelClass)) {
      return $permissions;
    }

    try {
      if ($this->supportsSoftDeletes($modelClass)) {
        $permissions[] = 'restore';
        $permissions[] = 'forceDelete';
      }

      if ($this->supportsReplication($modelClass)) {
        $permissions[] = 'replicate';
      }

      if ($this->supportsReordering($modelClass)) {
        $permissions[] = 'reorder';
      }
    } catch (\Exception $e) {
      // Return basic permissions on error
    }

    return array_unique($permissions);
  }

  /**
   * Get basic CRUD permissions
   *
   * @return array<int, string>
   */
  private function getBasicPermissions(): array
  {
    return ['viewAny', 'view', 'create', 'update', 'delete'];
  }

  /**
   * Check if model supports soft deletes
   *
   * @param string $modelClass
   * @return bool
   */
  private function supportsSoftDeletes(string $modelClass): bool
  {
    try {
      return class_exists($modelClass) &&
        in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($modelClass), true);
    } catch (\Exception $e) {
      return false;
    }
  }

  /**
   * Check if model supports replication
   *
   * @param string $modelClass
   * @return bool
   */
  private function supportsReplication(string $modelClass): bool
  {
    try {
      return class_exists($modelClass) && method_exists($modelClass, 'replicate');
    } catch (\Exception $e) {
      return false;
    }
  }

  /**
   * Check if model supports reordering
   *
   * @param string $modelClass
   * @return bool
   */
  private function supportsReordering(string $modelClass): bool
  {
    try {
      if (!class_exists($modelClass)) {
        return false;
      }

      $traits = class_uses_recursive($modelClass);

      foreach ($traits as $trait) {
        if (str_contains($trait, 'Sortable') || str_contains($trait, 'Orderable')) {
          return true;
        }
      }

      return false;
    } catch (\Exception $e) {
      return false;
    }
  }

  /**
   * Get extraction log
   *
   * @return array<string, mixed>
   */
  public function getLog(): array
  {
    return $this->extractionLog;
  }

  /**
   * Log extraction information
   *
   * @param string $key
   * @param mixed $data
   * @return void
   */
  private function log(string $key, mixed $data): void
  {
    if (!$this->enableLogging) {
      return;
    }

    $this->extractionLog[$key] = $data;
  }
}
