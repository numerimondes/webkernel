<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Base\Services;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Collection;

/**
 * Central registry for managing render hooks across all modules
 *
 * This service provides a centralized way to register, manage, and coordinate
 * render hooks from different modules while maintaining clean separation.
 */
class RenderHookRegistry
{
  private static array $registeredHooks = [];
  private static array $hookProviders = [];

  /**
   * Register a render hook with metadata
   */
  public static function register(
    string $hook,
    callable $callback,
    string $moduleId,
    int $priority = 0,
    array $conditions = [],
  ): void {
    $hookData = [
      'callback' => $callback,
      'module_id' => $moduleId,
      'priority' => $priority,
      'conditions' => $conditions,
      'registered_at' => now(),
    ];

    self::$registeredHooks[$hook][] = $hookData;

    // Sort by priority (higher priority first)
    usort(self::$registeredHooks[$hook], fn($a, $b) => $b['priority'] <=> $a['priority']);

    // Register with Filament
    FilamentView::registerRenderHook($hook, function () use ($callback, $conditions) {
      // Check conditions before rendering
      if (!empty($conditions)) {
        foreach ($conditions as $condition => $value) {
          if (!self::checkCondition($condition, $value)) {
            return '';
          }
        }
      }

      return call_user_func($callback);
    });
  }

  /**
   * Register multiple hooks from a module
   */
  public static function registerFromModule(string $moduleId, array $hooks): void
  {
    self::$hookProviders[$moduleId] = $hooks;

    foreach ($hooks as $hookConfig) {
      $callback = $hookConfig['callback'];

      // Convert array callback to proper callable
      if (is_array($callback) && count($callback) === 2) {
        $callback = function () use ($callback) {
          return call_user_func($callback);
        };
      }

      self::register(
        $hookConfig['hook'],
        $callback,
        $moduleId,
        $hookConfig['priority'] ?? 0,
        $hookConfig['conditions'] ?? [],
      );
    }
  }

  /**
   * Get all registered hooks for a specific hook point
   */
  public static function getHooksFor(string $hook): array
  {
    return self::$registeredHooks[$hook] ?? [];
  }

  /**
   * Get all hooks from a specific module
   */
  public static function getModuleHooks(string $moduleId): array
  {
    return self::$hookProviders[$moduleId] ?? [];
  }

  /**
   * Get statistics about registered hooks
   */
  public static function getStats(): array
  {
    $totalHooks = array_sum(array_map('count', self::$registeredHooks));
    $hookPoints = count(self::$registeredHooks);
    $modules = count(self::$hookProviders);

    return [
      'total_hooks' => $totalHooks,
      'hook_points' => $hookPoints,
      'modules_with_hooks' => $modules,
      'hooks_by_point' => array_map('count', self::$registeredHooks),
      'hooks_by_module' => array_map('count', self::$hookProviders),
    ];
  }

  /**
   * Check if a condition is met
   */
  private static function checkCondition(string $condition, $value): bool
  {
    return match ($condition) {
      'auth' => auth()->check() === $value,
      'guest' => auth()->guest() === $value,
      'panel' => request()->route()?->getPrefix() === $value,
      'route' => request()->routeIs($value),
      'user_role' => auth()->check() && auth()->user()->hasRole($value),
      'user_permission' => auth()->check() && auth()->user()->can($value),
      default => true,
    };
  }

  /**
   * Clear all hooks (useful for testing)
   */
  public static function clear(): void
  {
    self::$registeredHooks = [];
    self::$hookProviders = [];
  }
}
