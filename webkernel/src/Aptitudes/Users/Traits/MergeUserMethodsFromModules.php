<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Webkernel\Arcanes\QueryModules;
use ReflectionClass;
use ReflectionMethod;
use BadMethodCallException;

/**
 * Merge User Methods From Modules
 *
 * Automatically discovers and merges methods and relationships from
 * module UserModel.php files across all registered modules.
 *
 * This trait enables a modular architecture where each module can extend
 * the User model without modifying the core User class. Methods and
 * relationships are discovered at runtime and cached for performance.
 *
 * Key Features:
 * - Automatic method and relationship discovery from module UserModel classes
 * - Priority-based conflict resolution (higher priority wins)
 * - Efficient caching with configurable TTL
 * - Relationship detection via return type analysis
 * - Support for per-panel tenant extensions (Filament integration)
 *
 * Conflict Resolution:
 * When multiple modules define the same method/relationship, resolution follows:
 * 1. Higher priority module wins
 * 2. If priorities are equal, alphabetically first module name wins
 * 3. All conflicts are logged for developer awareness
 *
 * Usage Example:
 * Create {YourModule}/User/UserModel.php:
 * ```php
 * namespace YourModule\User;
 * use Illuminate\Database\Eloquent\Relations\HasMany;
 *
 * class UserModel {
 *   private $user;
 *
 *   public function setUser($user) { $this->user = $user; }
 *
 *   public function posts(): HasMany {
 *     return $this->user->hasMany(Post::class);
 *   }
 * }
 * ```
 *
 * @package Webkernel\Aptitudes\Users\Traits
 * @author El Moumen Yassine, Numerimondes
 */
trait MergeUserMethodsFromModules
{
  /**
   * Cached extension metadata structure:
   * - methods: ['methodName' => ['class', 'module', 'priority', 'file', 'line', 'parameters', 'return_type']]
   * - relationships: ['relationName' => [...same structure]]
   * - conflicts: ['type:name' => ['name', 'type', 'sources' => [...]]]
   * - metadata: ['total_modules_scanned', 'total_files_loaded', 'scan_timestamp']
   *
   * @var array|null
   */
  protected static ?array $mergedUserExtensions = null;

  /**
   * Runtime instances of extension classes.
   * Lazily instantiated on first method call.
   *
   * @var array<string, object>
   */
  protected array $userExtensionInstances = [];

  /**
   * Handle dynamic method calls with prioritized resolution
   *
   * Resolution order:
   * 1. Relationships (return Relation instance)
   * 2. Regular methods (delegate to extension class)
   * 3. Parent class __call (fallback to Eloquent magic)
   *
   * @param string $method Method name
   * @param array $parameters Method arguments
   * @return mixed
   */
  public function __call($method, $parameters)
  {
    if (static::$mergedUserExtensions === null) {
      static::discoverUserExtensions();
    }

    // PRIORITY 1: Check relationships FIRST
    if (isset(static::$mergedUserExtensions['relationships'][$method])) {
      return $this->buildModuleRelationship($method, static::$mergedUserExtensions['relationships'][$method]);
    }

    // PRIORITY 2: Check regular methods
    if (isset(static::$mergedUserExtensions['methods'][$method])) {
      $config = static::$mergedUserExtensions['methods'][$method];
      $extensionClass = $config['class'];

      if (!isset($this->userExtensionInstances[$extensionClass])) {
        $this->userExtensionInstances[$extensionClass] = new $extensionClass();
        if (method_exists($this->userExtensionInstances[$extensionClass], 'setUser')) {
          $this->userExtensionInstances[$extensionClass]->setUser($this);
        }
      }

      return call_user_func_array([$this->userExtensionInstances[$extensionClass], $method], $parameters);
    }

    return parent::__call($method, $parameters);
  }

  /**
   * Handle dynamic property access for relationships
   *
   * When accessing $user->posts, this resolves to the relationship
   * and executes the query to return results.
   *
   * @param string $key Property name
   * @return mixed
   */
  public function __get($key)
  {
    if (static::$mergedUserExtensions === null) {
      static::discoverUserExtensions();
    }

    // Check if it's a relationship
    if (isset(static::$mergedUserExtensions['relationships'][$key])) {
      return $this->buildModuleRelationship($key, static::$mergedUserExtensions['relationships'][$key])->get();
    }

    return parent::__get($key);
  }

  /**
   * Build a relationship from module extension
   *
   * Creates the extension instance if needed, sets the user context,
   * and returns the Relation object.
   *
   * @param string $method Relationship method name
   * @param array $config Method configuration
   * @return Relation
   */
  protected function buildModuleRelationship(string $method, array $config): Relation
  {
    $extensionClass = $config['class'];

    if (!isset($this->userExtensionInstances[$extensionClass])) {
      $this->userExtensionInstances[$extensionClass] = new $extensionClass();
      if (method_exists($this->userExtensionInstances[$extensionClass], 'setUser')) {
        $this->userExtensionInstances[$extensionClass]->setUser($this);
      }
    }

    return $this->userExtensionInstances[$extensionClass]->$method();
  }

  /**
   * Discover all user extensions from modules
   *
   * Scans all registered modules for User/UserModel.php files,
   * extracts public methods, detects relationships via return types,
   * and builds a cached registry with conflict detection.
   *
   * Cache Strategy:
   * - Production: 3600s (1 hour) TTL
   * - Development: 0s (disabled) for hot-reload
   *
   * @return void
   */
  protected static function discoverUserExtensions(): void
  {
    $cacheKey = 'merged_user_extensions_v3';
    $cacheTtl = app()->environment('production') ? 3600 : 0;

    static::$mergedUserExtensions = Cache::remember($cacheKey, $cacheTtl, function () {
      $extensions = [
        'methods' => [],
        'relationships' => [],
        'conflicts' => [],
        'metadata' => [
          'total_modules_scanned' => 0,
          'total_files_loaded' => 0,
          'scan_timestamp' => now()->toIso8601String(),
        ],
      ];

      $modules = QueryModules::make()
        ->select(['namespace', 'basePath', 'priority'])
        ->unique()
        ->get();

      $extensions['metadata']['total_modules_scanned'] = count($modules);

      foreach ($modules as $module) {
        $namespace = $module['namespace'] ?? null;
        $basePath = $module['basePath'] ?? null;
        $priority = $module['priority'] ?? 0;

        if (!$namespace || !$basePath) {
          continue;
        }

        $userModelPath = $basePath . '/User/UserModel.php';

        if (!file_exists($userModelPath)) {
          continue;
        }

        try {
          static::loadModuleUserExtension($extensions, $userModelPath, $namespace, $priority);
          $extensions['metadata']['total_files_loaded']++;
        } catch (\Throwable $e) {
          Log::error("Failed to load UserModel from {$namespace}: {$e->getMessage()}");
        }
      }

      if (!empty($extensions['conflicts'])) {
        Log::warning('User model extension conflicts detected', [
          'conflicts' => $extensions['conflicts'],
        ]);
      }

      return $extensions;
    });
  }

  /**
   * Load user extension from a module
   *
   * Analyzes a UserModel class and extracts:
   * - All public methods (excluding magic methods and inherited methods)
   * - Return types to detect relationships
   * - Method parameters for documentation
   *
   * @param array $extensions Reference to extensions registry
   * @param string $filePath Path to UserModel.php
   * @param string $namespace Module namespace
   * @param int $priority Module priority (default: 0)
   * @return void
   */
  protected static function loadModuleUserExtension(
    array &$extensions,
    string $filePath,
    string $namespace,
    int $priority = 0,
  ): void {
    $className = basename($filePath, '.php');
    $fullClassName = $namespace . '\\User\\' . $className;

    if (!class_exists($fullClassName)) {
      require_once $filePath;
    }

    if (!class_exists($fullClassName)) {
      return;
    }

    $reflection = new ReflectionClass($fullClassName);

    if ($reflection->isAbstract() || $reflection->isTrait()) {
      return;
    }

    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

    foreach ($methods as $method) {
      // Skip magic methods
      if (strpos($method->name, '__') === 0) {
        continue;
      }

      // Skip inherited methods
      if ($method->class !== $fullClassName) {
        continue;
      }

      $returnType = $method->getReturnType();
      $isRelationship = false;

      // Detect relationships via return type
      if ($returnType) {
        $returnTypeName = $returnType instanceof \ReflectionNamedType ? $returnType->getName() : (string) $returnType;
        $isRelationship = is_subclass_of($returnTypeName, Relation::class);
      }

      $methodInfo = [
        'class' => $fullClassName,
        'module' => $namespace,
        'priority' => $priority,
        'file' => $filePath,
        'line' => $method->getStartLine(),
        'parameters' => static::extractMethodParameters($method),
        'return_type' => $returnType ? (string) $returnType : 'mixed',
      ];

      if ($isRelationship) {
        static::registerExtension($extensions, 'relationships', $method->name, $methodInfo);
      } else {
        static::registerExtension($extensions, 'methods', $method->name, $methodInfo);
      }
    }
  }

  /**
   * Register an extension with conflict detection
   *
   * Conflict Resolution Rules:
   * 1. Higher priority wins
   * 2. Same priority: alphabetically first module wins
   * 3. All conflicts logged for developer awareness
   *
   * @param array $extensions Reference to extensions registry
   * @param string $type 'methods' or 'relationships'
   * @param string $name Method/relationship name
   * @param array $info Method metadata
   * @return void
   */
  protected static function registerExtension(array &$extensions, string $type, string $name, array $info): void
  {
    if (isset($extensions[$type][$name])) {
      $existing = $extensions[$type][$name];

      $conflictKey = "{$type}:{$name}";
      if (!isset($extensions['conflicts'][$conflictKey])) {
        $extensions['conflicts'][$conflictKey] = [
          'name' => $name,
          'type' => $type,
          'sources' => [$existing],
        ];
      }
      $extensions['conflicts'][$conflictKey]['sources'][] = $info;

      // Priority-based resolution
      if ($info['priority'] > $existing['priority']) {
        $extensions[$type][$name] = $info;
      } elseif ($info['priority'] === $existing['priority']) {
        // Alphabetical fallback for same priority
        if ($info['module'] < $existing['module']) {
          $extensions[$type][$name] = $info;
        }
      }
    } else {
      $extensions[$type][$name] = $info;
    }
  }

  /**
   * Extract method parameters for documentation
   *
   * @param ReflectionMethod $method
   * @return array<int, array{name: string, type: string, optional: bool, default?: mixed}>
   */
  protected static function extractMethodParameters(ReflectionMethod $method): array
  {
    $params = [];
    foreach ($method->getParameters() as $param) {
      $paramInfo = [
        'name' => $param->getName(),
        'type' => $param->getType() ? (string) $param->getType() : 'mixed',
        'optional' => $param->isOptional(),
      ];
      if ($param->isOptional() && $param->isDefaultValueAvailable()) {
        $paramInfo['default'] = $param->getDefaultValue();
      }
      $params[] = $paramInfo;
    }
    return $params;
  }

  /**
   * Check if a method or relationship exists
   *
   * @param string $name Method/relationship name
   * @return bool
   */
  public function hasUserExtension(string $name): bool
  {
    if (static::$mergedUserExtensions === null) {
      static::discoverUserExtensions();
    }

    return isset(static::$mergedUserExtensions['methods'][$name]) ||
      isset(static::$mergedUserExtensions['relationships'][$name]);
  }

  /**
   * Get all available extensions with metadata
   *
   * Returns complete registry including conflicts and metadata
   *
   * @return array
   */
  public static function availableUserExtensions(): array
  {
    if (static::$mergedUserExtensions === null) {
      static::discoverUserExtensions();
    }

    return static::$mergedUserExtensions;
  }

  /**
   * Get detailed information about a specific extension
   *
   * @param string $name Method/relationship name
   * @return array|null Metadata with 'type' key, or null if not found
   */
  public static function getExtensionInfo(string $name): ?array
  {
    if (static::$mergedUserExtensions === null) {
      static::discoverUserExtensions();
    }

    if (isset(static::$mergedUserExtensions['methods'][$name])) {
      return array_merge(['type' => 'method'], static::$mergedUserExtensions['methods'][$name]);
    }

    if (isset(static::$mergedUserExtensions['relationships'][$name])) {
      return array_merge(['type' => 'relationship'], static::$mergedUserExtensions['relationships'][$name]);
    }

    return null;
  }

  /**
   * Get all conflicts detected
   *
   * @return array
   */
  public static function getConflicts(): array
  {
    if (static::$mergedUserExtensions === null) {
      static::discoverUserExtensions();
    }

    return static::$mergedUserExtensions['conflicts'] ?? [];
  }

  /**
   * Clear the extension cache
   *
   * Forces re-discovery on next access.
   * Useful after module installation/removal.
   *
   * @return void
   */
  public static function clearExtensionCache(): void
  {
    Cache::forget('merged_user_extensions_v3');
    static::$mergedUserExtensions = null;
  }
}
