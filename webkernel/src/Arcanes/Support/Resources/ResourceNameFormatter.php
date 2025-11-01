<?php declare(strict_types=1);

namespace Webkernel\Arcanes\Support\Resources;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Utility class for formatting resource and permission names
 * Provides consistent naming across the application
 */
class ResourceNameFormatter
{
  /**
   * Extract resource name from resource class
   *
   * @param string $resourceClass
   * @return string
   */
  public static function extractResourceName(string $resourceClass): string
  {
    if (!class_exists($resourceClass)) {
      return static::formatFromClassName($resourceClass);
    }

    try {
      $reflection = new ReflectionClass($resourceClass);
      $shortName = $reflection->getShortName();
      $name = preg_replace('/Resource$/', '', $shortName);

      return Str::snake((string) $name);
    } catch (\Exception $e) {
      return static::formatFromClassName($resourceClass);
    }
  }

  /**
   * Format resource name from class name string
   *
   * @param string $className
   * @return string
   */
  private static function formatFromClassName(string $className): string
  {
    $baseName = basename(str_replace('\\', '/', $className));
    $name = preg_replace('/Resource$/', '', $baseName);

    return Str::snake($name);
  }

  /**
   * Format resource title for display
   *
   * @param string $resourceName
   * @return string
   */
  public static function formatResourceTitle(string $resourceName): string
  {
    return Str::title(str_replace('_', ' ', $resourceName));
  }

  /**
   * Format permission label for display
   *
   * @param string $permission
   * @return string
   */
  public static function formatPermissionLabel(string $permission): string
  {
    return match ($permission) {
      'viewAny' => 'View Any',
      'view' => 'View',
      'create' => 'Create',
      'update' => 'Update',
      'delete' => 'Delete',
      'restore' => 'Restore',
      'forceDelete' => 'Force Delete',
      'replicate' => 'Replicate',
      'reorder' => 'Reorder',
      default => Str::title(str_replace('_', ' ', $permission)),
    };
  }

  /**
   * Generate privilege key
   *
   * @param string $resourceName
   * @param string $permission
   * @return string
   */
  public static function generatePrivilegeKey(string $resourceName, string $permission): string
  {
    return sprintf('resource.%s.%s', $resourceName, $permission);
  }

  /**
   * Generate privilege description
   *
   * @param string $resourceName
   * @param string $permission
   * @param string $moduleName
   * @return string
   */
  public static function generatePrivilegeDescription(
    string $resourceName,
    string $permission,
    string $moduleName,
  ): string {
    return sprintf(
      '%s %s (%s)',
      static::formatPermissionLabel($permission),
      static::formatResourceTitle($resourceName),
      $moduleName,
    );
  }

  /**
   * Parse privilege key into components
   *
   * @param string $privilegeKey
   * @return array{type: string, resource: string, permission: string}|null
   */
  public static function parsePrivilegeKey(string $privilegeKey): ?array
  {
    if (!preg_match('/^([^.]+)\.([^.]+)\.([^.]+)$/', $privilegeKey, $matches)) {
      return null;
    }

    return [
      'type' => $matches[1],
      'resource' => $matches[2],
      'permission' => $matches[3],
    ];
  }
}
