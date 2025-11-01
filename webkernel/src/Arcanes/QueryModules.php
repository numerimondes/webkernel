<?php

declare(strict_types=1);

namespace Webkernel\Arcanes;
use Webkernel\Arcanes\Runtime\WebkernelManager;
/**
 * Ultra-fluent QueryBuilder for modules with chained methods
 *
 * Examples:
 *
 * // Get langpath values as array
 * $lang_paths = array_column(
 *     QueryModules::make()
 *         ->select(['langpath'])
 *         ->where('langpath')->isNotNull()
 *         ->get(),
 *     'langpath'
 * );
 *
 * // Merge langpath values into a comma-separated string
 * $langpath_string = QueryModules::make()
 *     ->select(['langpath'])
 *     ->where('langpath')->isNotNull()
 *     ->merge(',');
 *
 * // Get unique langpath values as array
 * $unique_langpaths = QueryModules::make()
 *     ->select(['langpath'])
 *     ->where('langpath')->isNotNull()
 *     ->unique();
 *
 * // Get unique langpath values as comma-separated string (RECOMMENDED)
 * $unique_langpath_string = QueryModules::make()
 *     ->select(['langpath'])
 *     ->where('langpath')->isNotNull()
 *     ->uniqueValues(',');
 */
class QueryModules
{
  private array $modules;

  private array $selectedFields = ['*'];

  private array $sortFields = [];

  private array $whereConditions = [];

  private ?int $limit = null;

  private int $offset = 0;

  public function __construct(array $modules)
  {
    $this->modules = $modules;
  }

  /**
   * Create a new QueryModules instance
   */
  public static function make(?array $modules = null): self
  {
    if ($modules === null) {
      // Cache modules to avoid repeated calls to WebkernelManager
      static $cachedModules = null;
      if ($cachedModules === null) {
        $manager = app(WebkernelManager::class);
        $cachedModules = $manager->getModules();
      }
      $modules = $cachedModules;
    }

    return new self($modules);
  }

  /**
   * Select specific fields
   */
  public function select(array $fields): self
  {
    $this->selectedFields = $fields;

    return $this;
  }

  /**
   * Sort by multiple fields
   */
  public function sortBy(array $fields): self
  {
    $this->sortFields = $fields;

    return $this;
  }

  /**
   * Start a where condition
   */
  public function where(string $field): WhereCondition
  {
    $condition = new WhereCondition($field, $this);
    $this->whereConditions[] = $condition;

    return $condition;
  }

  /**
   * Limit results
   */
  public function limit(int $limit): self
  {
    $this->limit = $limit;

    return $this;
  }

  /**
   * Limit per take (alias for limit)
   */
  public function limitPerTake(int $limit): self
  {
    return $this->limit($limit);
  }

  /**
   * Skip results
   */
  public function skip(int $offset): self
  {
    $this->offset = $offset;

    return $this;
  }

  /**
   * Get results
   */
  public function get(): array
  {
    $filtered = $this->modules;

    // Apply where conditions
    foreach ($this->whereConditions as $condition) {
      $filtered = array_filter($filtered, function ($module) use ($condition) {
        return $condition->apply($module);
      });
    }

    // Apply sorting
    if (!empty($this->sortFields)) {
      usort($filtered, function ($a, $b) {
        foreach ($this->sortFields as $field) {
          $aVal = $a[$field] ?? '';
          $bVal = $b[$field] ?? '';

          $result = strcmp($aVal, $bVal);
          if ($result !== 0) {
            return $result;
          }
        }

        return 0;
      });
    }

    // Apply pagination
    if ($this->offset > 0) {
      $filtered = array_slice($filtered, $this->offset);
    }

    if ($this->limit !== null) {
      $filtered = array_slice($filtered, 0, $this->limit);
    }

    // Apply field selection
    if (!in_array('*', $this->selectedFields)) {
      $filtered = array_map(function ($module) {
        $result = [];
        foreach ($this->selectedFields as $field) {
          if (isset($module[$field])) {
            $result[$field] = $module[$field];
          }
        }

        return $result;
      }, $filtered);
    }

    return array_values($filtered);
  }

  /**
   * Count results
   */
  public function count(): int
  {
    $filtered = $this->modules;

    foreach ($this->whereConditions as $condition) {
      $filtered = array_filter($filtered, function ($module) use ($condition) {
        return $condition->apply($module);
      });
    }

    return count($filtered);
  }

  /**
   * Get first result
   */
  public function first(): ?array
  {
    $results = $this->get();

    return $results[0] ?? null;
  }

  /**
   * Check if results exist
   */
  public function exists(): bool
  {
    return $this->count() > 0;
  }

  /**
   * Merge results into a single string with separator
   */
  public function merge(string $separator = ','): string
  {
    $results = $this->get();

    if (empty($results)) {
      return '';
    }

    // If only one field is selected, merge those values
    if (count($this->selectedFields) === 1 && !in_array('*', $this->selectedFields)) {
      $field = $this->selectedFields[0];
      $values = array_column($results, $field);

      return implode($separator, array_filter($values, fn($value) => $value !== null));
    }

    // If multiple fields or all fields, serialize each result
    $strings = array_map(function ($result) {
      return is_array($result) ? json_encode($result) : (string) $result;
    }, $results);

    return implode($separator, $strings);
  }

  /**
   * Get unique results based on selected fields
   */
  public function unique(): self
  {
    $results = $this->get();

    if (empty($results)) {
      return new self([]);
    }

    // If only one field is selected, get unique values for that field
    if (count($this->selectedFields) === 1 && !in_array('*', $this->selectedFields)) {
      $field = $this->selectedFields[0];
      $values = array_column($results, $field);
      $uniqueValues = array_unique($values);

      // Return as array of single-field arrays
      $uniqueResults = array_map(function ($value) use ($field) {
        return [$field => $value];
      }, $uniqueValues);

      return new self($uniqueResults);
    }

    $unique = [];
    $seen = [];

    foreach ($results as $result) {
      $key = md5(serialize($result));
      if (!isset($seen[$key])) {
        $seen[$key] = true;
        $unique[] = $result;
      }
    }

    return new self($unique);
  }

  /**
   * Get unique values for a single field and merge them
   */
  public function uniqueValues(string $separator = ','): string
  {
    $results = $this->get();

    if (empty($results)) {
      return '';
    }

    // If only one field is selected, get unique values for that field
    if (count($this->selectedFields) === 1 && !in_array('*', $this->selectedFields)) {
      $field = $this->selectedFields[0];
      $values = array_column($results, $field);
      $uniqueValues = array_unique(array_filter($values, fn($value) => $value !== null));

      return implode($separator, $uniqueValues);
    }

    // For multiple fields, get unique combinations
    $unique = [];
    $seen = [];

    foreach ($results as $result) {
      $key = md5(serialize($result));
      if (!isset($seen[$key])) {
        $seen[$key] = true;
        $unique[] = is_array($result) ? json_encode($result) : (string) $result;
      }
    }

    return implode($separator, $unique);
  }

  /**
   * Scan directory for PHP files and create modules
   */
  public static function scanDirectory(string $directory, ?string $baseNamespace = null): self
  {
    if (!is_dir($directory)) {
      return new self([]);
    }

    $files = glob($directory . '/*.php') ?: [];

    // Auto-detect namespace from calling context if not provided
    if ($baseNamespace === null) {
      $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
      $callerClass = $trace[1]['class'] ?? null;
      if ($callerClass) {
        $baseNamespace = substr($callerClass, 0, strrpos($callerClass, '\\'));
      }
    }

    $modules = array_map(function ($file) use ($directory, $baseNamespace) {
      $className = basename($file, '.php');
      $relativePath = str_replace($directory . '/', '', $file);
      $subPath = dirname($relativePath);

      // Construire le namespace complet en incluant le chemin du dossier
      $namespace = $baseNamespace;
      if ($subPath !== '.') {
        $namespace .= '\\' . str_replace('/', '\\', $subPath);
      }

      // Ajouter le nom du dossier parent au namespace
      $parentDir = basename($directory);
      $namespace .= '\\' . $parentDir;

      $fullClassName = $namespace . '\\' . $className;

      return [
        'file' => $file,
        'name' => $className,
        'class' => $fullClassName,
        'namespace' => $namespace,
        'path' => $relativePath,
        'type' => 'php',
      ];
    }, $files);

    return new self($modules);
  }

  /**
   * Load providers from module by ID and provider type
   */
  public static function loadFromModule(string $moduleId, string $path = 'Boot', bool $recursive = true): void
  {
    $manager = app(WebkernelManager::class);
    $modules = $manager->getModules();

    $targetModule = null;
    foreach ($modules as $module) {
      if ($module['id'] === $moduleId) {
        $targetModule = $module;
        break;
      }
    }

    if (!$targetModule) {
      return;
    }

    $basePath = $targetModule['basePath'];
    $namespace = $targetModule['namespace'];
    $targetDirectory = $basePath . '/' . $path;

    if (!is_dir($targetDirectory)) {
      return;
    }

    if (!$recursive) {
      return;
    }

    $iterator = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($targetDirectory, \RecursiveDirectoryIterator::SKIP_DOTS),
      \RecursiveIteratorIterator::SELF_FIRST,
    );

    foreach ($iterator as $file) {
      if ($file->isFile() && $file->getExtension() === 'php') {
        // Build namespace from the full path relative to module base
        $relativePathFromModule = str_replace($basePath . '/', '', $file->getPathname());
        $pathParts = explode('/', dirname($relativePathFromModule));
        $classNamespace = $namespace;

        foreach ($pathParts as $part) {
          if ($part !== '.' && $part !== '') {
            $classNamespace .= '\\' . $part;
          }
        }

        $className = $classNamespace . '\\' . $file->getBasename('.php');

        if (class_exists($className)) {
          // Check if it's a ServiceProvider
          if (is_subclass_of($className, \Illuminate\Support\ServiceProvider::class)) {
            // Register ServiceProvider through Laravel's container
            app()->register($className);
          } else {
            // Handle regular classes with handle() method
            $instance = new $className();
            if (method_exists($instance, 'handle')) {
              $instance->handle();
            }
          }
        }
      }
    }
  }

  /**
   * Scan directory and instantiate all classes calling their handle() method
   */
  public static function loadFromDirectory(string $directory, ?string $baseNamespace = null): void
  {
    $modules = self::scanDirectory($directory, $baseNamespace)->get();

    foreach ($modules as $module) {
      if (class_exists($module['class'])) {
        $instance = new ($module['class'])();
        if (method_exists($instance, 'handle')) {
          $instance->handle();
        }
      }
    }
  }
}

/**
 * Where condition builder for chained methods
 */
class WhereCondition
{
  private string $field;

  private QueryModules $query;

  private array $conditions = [];

  public function __construct(string $field, QueryModules $query)
  {
    $this->field = $field;
    $this->query = $query;
  }

  /**
   * Field contains value
   */
  public function contains(string $value): self
  {
    $this->conditions[] = ['operator' => 'contains', 'value' => $value];

    return $this;
  }

  /**
   * Field does not contain value
   */
  public function notContains(string $value): self
  {
    $this->conditions[] = ['operator' => 'not_contains', 'value' => $value];

    return $this;
  }

  /**
   * Field equals value
   */
  public function is($value): self
  {
    $this->conditions[] = ['operator' => 'equals', 'value' => $value];

    return $this;
  }

  /**
   * Field is not equal to value
   */
  public function isNot($value): self
  {
    $this->conditions[] = ['operator' => 'not_equals', 'value' => $value];

    return $this;
  }

  /**
   * Field starts with value
   */
  public function startsWith(string $value): self
  {
    $this->conditions[] = ['operator' => 'starts_with', 'value' => $value];

    return $this;
  }

  /**
   * Field ends with value
   */
  public function endsWith(string $value): self
  {
    $this->conditions[] = ['operator' => 'ends_with', 'value' => $value];

    return $this;
  }

  /**
   * Field is in array of values
   */
  public function in(array $values): self
  {
    $this->conditions[] = ['operator' => 'in', 'value' => $values];

    return $this;
  }

  /**
   * Field is null
   */
  public function isNull(): self
  {
    $this->conditions[] = ['operator' => 'null', 'value' => null];

    return $this;
  }

  /**
   * Field is not null
   */
  public function isNotNull(): self
  {
    $this->conditions[] = ['operator' => 'not_null', 'value' => null];

    return $this;
  }

  /**
   * Return to the main query builder
   */
  public function endWhere(): QueryModules
  {
    return $this->query;
  }

  // === DELEGATE ALL QueryModules METHODS ===

  public function select(array $fields): QueryModules
  {
    return $this->query->select($fields);
  }

  public function sortBy(array $fields): QueryModules
  {
    return $this->query->sortBy($fields);
  }

  public function where(string $field): WhereCondition
  {
    return $this->query->where($field);
  }

  public function limit(int $limit): QueryModules
  {
    return $this->query->limit($limit);
  }

  public function limitPerTake(int $limit): QueryModules
  {
    return $this->query->limitPerTake($limit);
  }

  public function skip(int $offset): QueryModules
  {
    return $this->query->skip($offset);
  }

  public function get(): array
  {
    return $this->query->get();
  }

  public function count(): int
  {
    return $this->query->count();
  }

  public function first(): ?array
  {
    return $this->query->first();
  }

  public function exists(): bool
  {
    return $this->query->exists();
  }

  public function merge(string $separator = ','): string
  {
    return $this->query->merge($separator);
  }

  public function unique(): QueryModules
  {
    return $this->query->unique();
  }

  public function uniqueValues(string $separator = ','): string
  {
    return $this->query->uniqueValues($separator);
  }

  /**
   * Apply all conditions for this field
   */
  public function apply(array $module): bool
  {
    if (!isset($module[$this->field])) {
      return false;
    }

    $moduleValue = $module[$this->field];

    // All conditions must be true (AND logic)
    foreach ($this->conditions as $condition) {
      if (!$this->applyCondition($moduleValue, $condition)) {
        return false;
      }
    }

    return true;
  }

  /**
   * Apply individual condition
   */
  private function applyCondition($moduleValue, array $condition): bool
  {
    $operator = $condition['operator'];
    $value = $condition['value'];

    return match ($operator) {
      'equals' => $moduleValue === $value,
      'not_equals' => $moduleValue !== $value,
      'contains' => str_contains(strtolower($moduleValue), strtolower($value)),
      'not_contains' => !str_contains(strtolower($moduleValue), strtolower($value)),
      'starts_with' => str_starts_with(strtolower($moduleValue), strtolower($value)),
      'ends_with' => str_ends_with(strtolower($moduleValue), strtolower($value)),
      'in' => in_array($moduleValue, $value),
      'null' => $moduleValue === null,
      'not_null' => $moduleValue !== null,
      default => false,
    };
  }
}
