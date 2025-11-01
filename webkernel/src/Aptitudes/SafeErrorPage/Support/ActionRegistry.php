<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\SafeErrorPage\Support;

use Closure;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

/**
 * Registry for error page action handlers.
 *
 * Actions are identified by string keys and resolved at execution time.
 * This avoids closure serialization issues with sessions/cache.
 */
class ActionRegistry
{
  /**
   * Registered action handlers.
   *
   * @var array<string, Closure>
   */
  private static array $actions = [];

  /**
   * Whitelist of allowed action names (security measure).
   *
   * @var array<string>
   */
  private static array $allowedActions = [
    'cache-clear',
    'config-clear',
    'route-clear',
    'view-clear',
    'optimize-clear',
    'emergency-cache-reset',
    'run-migrations',
    'seed-database',
    'restart-queue',
    'storage-link',
  ];

  /**
   * Register a new action handler.
   *
   * @param string $name Action identifier
   * @param Closure $handler Action execution logic
   * @return void
   */
  public static function register(string $name, Closure $handler): void
  {
    self::$actions[$name] = $handler;

    // Auto-add to whitelist if not present
    if (!in_array($name, self::$allowedActions, true)) {
      self::$allowedActions[] = $name;
    }
  }

  /**
   * Execute a registered action.
   *
   * @param string $name Action identifier
   * @return mixed Action result
   * @throws RuntimeException If action not found or not allowed
   */
  public static function execute(string $name): mixed
  {
    if (!in_array($name, self::$allowedActions, true)) {
      throw new RuntimeException("Action '{$name}' is not in the whitelist");
    }

    if (!isset(self::$actions[$name])) {
      throw new RuntimeException("Action '{$name}' not found in registry");
    }

    return self::$actions[$name]();
  }

  /**
   * Check if an action is registered.
   *
   * @param string $name Action identifier
   * @return bool
   */
  public static function has(string $name): bool
  {
    return isset(self::$actions[$name]);
  }

  /**
   * Get all registered action names.
   *
   * @return array<string>
   */
  public static function all(): array
  {
    return array_keys(self::$actions);
  }

  /**
   * Clear all registered actions.
   *
   * @return void
   */
  public static function clear(): void
  {
    self::$actions = [];
  }

  /**
   * Register default system actions.
   *
   * @return void
   */
  public static function registerDefaults(): void
  {
    self::register('cache-clear', function (): string {
      Artisan::call('cache:clear');
      return 'Cache cleared successfully';
    });

    self::register('config-clear', function (): string {
      Artisan::call('config:clear');
      return 'Configuration cache cleared';
    });

    self::register('route-clear', function (): string {
      Artisan::call('route:clear');
      return 'Route cache cleared';
    });

    self::register('view-clear', function (): string {
      Artisan::call('view:clear');
      return 'View cache cleared';
    });

    self::register('optimize-clear', function (): string {
      Artisan::call('optimize:clear');
      return 'All optimization caches cleared';
    });

    self::register('emergency-cache-reset', function (): string {
      Artisan::call('cache:clear');
      Artisan::call('config:clear');
      Artisan::call('route:clear');
      Artisan::call('view:clear');
      return 'Emergency cache reset completed';
    });
  }
}
