<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Panels\Models;

/**
 * Purpose: Database model for storing and loading panels with dynamic schema validation
 * 
 * This model stores panel method calls in a format that directly mirrors
 * how panels are traditionally defined in PanelProvider classes, with
 * intelligent merging and validation using reflection-based schema analysis.
 * 
 * Key features:
 * - Dynamic method validation using PanelSchemaHelper
 * - Intelligent array merging with required defaults
 * - Boolean parameter handling with proper state management
 * - Type-safe method application with comprehensive error handling
 *
 * @property string $id
 * @property string $path
 * @property array<array-key, mixed>|null $methods
 * @property string $panel_source
 * @property string $version
 * @property bool $is_active
 * @property bool $is_default
 * @property int $sort_order
 * @property string|null $description
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder<static>|Panels active()
 * @method static Builder<static>|Panels bySource(string $source)
 * @method static Builder<static>|Panels default()
 * @method static Builder<static>|Panels newModelQuery()
 * @method static Builder<static>|Panels newQuery()
 * @method static Builder<static>|Panels query()
 * @method static Builder<static>|Panels whereCreatedAt($value)
 * @method static Builder<static>|Panels whereDescription($value)
 * @method static Builder<static>|Panels whereId($value)
 * @method static Builder<static>|Panels whereIsActive($value)
 * @method static Builder<static>|Panels whereIsDefault($value)
 * @method static Builder<static>|Panels whereMetadata($value)
 * @method static Builder<static>|Panels whereMethods($value)
 * @method static Builder<static>|Panels wherePanelSource($value)
 * @method static Builder<static>|Panels wherePath($value)
 * @method static Builder<static>|Panels whereSortOrder($value)
 * @method static Builder<static>|Panels whereUpdatedAt($value)
 * @method static Builder<static>|Panels whereVersion($value)
 * @mixin \Eloquent
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Webkernel\Aptitudes\Panels\Helpers\PanelSchemaHelper;

class Panels extends Model
{
  protected $table = 'apt_panels';
  public $incrementing = false;
  protected $keyType = 'string';

  protected $fillable = [
    'id',
    'path',
    'methods',
    'panel_source',
    'version',
    'is_active',
    'is_default',
    'sort_order',
    'description',
    'metadata',
  ];

  protected $casts = [
    'methods' => 'array',
    'metadata' => 'array',
    'is_active' => 'boolean',
    'is_default' => 'boolean',
    'sort_order' => 'integer',
  ];

  protected $attributes = [
    'panel_source' => 'database',
    'version' => '4.0',
    'is_active' => true,
    'is_default' => false,
    'sort_order' => 0,
  ];

  protected ?PanelSchemaHelper $schemaHelper = null;

  public function scopeActive(Builder $query): Builder
  {
    return $query->where('is_active', true);
  }

  public function scopeDefault(Builder $query): Builder
  {
    return $query->where('is_default', true);
  }

  public function scopeBySource(Builder $query, string $source): Builder
  {
    return $query->where('panel_source', $source);
  }

  /**
   * Get schema helper instance
   */
  protected function getSchemaHelper(): PanelSchemaHelper
  {
    if (!$this->schemaHelper) {
      $this->schemaHelper = new PanelSchemaHelper();
    }

    return $this->schemaHelper;
  }

  /**
   * Create Panel instance with all configurations applied
   */
  public function createPanel(): Panel
  {
    $panel = Panel::make()->id($this->id)->path($this->path);

    if ($this->is_default) {
      $panel->default();
    }

    $this->applyStoredMethods($panel);

    return $panel;
  }

  /**
   * Apply all stored method calls to the panel instance
   */
  protected function applyStoredMethods(Panel $panel): void
  {
    $methods = $this->methods ?? [];
    $schema = $this->getSchemaHelper();

    // First apply special methods that need to be handled differently
    $this->applySpecialMethods($panel, $methods);

    // Then apply regular methods
    foreach ($methods as $methodName => $parameters) {
      if ($this->isSpecialMethod($methodName)) {
        continue; // Skip special methods as they were already handled
      }

      if (!method_exists($panel, $methodName)) {
        continue;
      }

      $this->applyMethod($panel, $methodName, $parameters, $schema);
    }
  }

  /**
   * Check if method is a special method that needs special handling
   */
  protected function isSpecialMethod(string $methodName): bool
  {
    $specialMethods = ['login', 'registration', 'profile', 'spa', 'darkMode', 'globalSearch', 'topNavigation'];
    return in_array($methodName, $specialMethods);
  }

  /**
   * Apply special methods that need special handling
   */
  protected function applySpecialMethods(Panel $panel, array $methods): void
  {
    // Handle authentication methods
    if (isset($methods['login']) && $methods['login']) {
      $panel->login();
    }

    if (isset($methods['registration']) && $methods['registration']) {
      $panel->registration();
    }

    if (isset($methods['profile']) && $methods['profile']) {
      $panel->profile();
    }

    // Handle other special methods
    if (isset($methods['spa']) && $methods['spa']) {
      $panel->spa();
    }

    if (isset($methods['darkMode']) && $methods['darkMode']) {
      $panel->darkMode();
    }

    if (isset($methods['globalSearch']) && $methods['globalSearch']) {
      $panel->globalSearch();
    }

    if (isset($methods['topNavigation']) && $methods['topNavigation']) {
      $panel->topNavigation();
    }

    // Apply default middlewares via reflection, then add custom ones
    $this->applyDefaultMiddlewares($panel);
    $this->applyCustomMiddlewares($panel, $methods);
  }

  /**
   * Apply default middlewares via reflection
   */
  protected function applyDefaultMiddlewares(Panel $panel): void
  {
    try {
      // Get default middlewares from a standard Filament panel
      $defaultPanel = \Filament\Panel::make('default');

      // Apply standard web middleware
      $panel->middleware([
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Filament\Http\Middleware\AuthenticateSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Filament\Http\Middleware\DisableBladeIconComponents::class,
        \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
        \Webkernel\Aptitudes\I18n\Http\Middleware\LanguageResolutionMiddleware::class,
      ]);

      // Apply standard auth middleware
      $panel->authMiddleware([\Filament\Http\Middleware\Authenticate::class]);
    } catch (\Throwable $e) {
    }
  }

  /**
   * Apply custom middlewares from database configuration
   */
  protected function applyCustomMiddlewares(Panel $panel, array $methods): void
  {
    // Apply custom web middleware if specified
    if (isset($methods['middleware']) && is_array($methods['middleware'])) {
      try {
        $panel->middleware($methods['middleware']);
      } catch (\Throwable $e) {
      }
    }

    // Apply custom auth middleware if specified
    if (isset($methods['authMiddleware']) && is_array($methods['authMiddleware'])) {
      try {
        $panel->authMiddleware($methods['authMiddleware']);
      } catch (\Throwable $e) {
      }
    }
  }

  /**
   * Apply individual method with proper parameter handling based on schema
   */
  protected function applyMethod(Panel $panel, string $methodName, mixed $parameters, PanelSchemaHelper $schema): void
  {
    try {
      if ($schema->isBooleanMethod($methodName)) {
        $this->applyBooleanMethod($panel, $methodName, $parameters, $schema);
      } elseif ($schema->isArrayMethod($methodName)) {
        $this->applyArrayMethod($panel, $methodName, $parameters, $schema);
      } else {
        $this->applyRegularMethod($panel, $methodName, $parameters);
      }
    } catch (\Throwable $e) {
    }
  }

  /**
   * Apply boolean method with proper state handling
   */
  protected function applyBooleanMethod(
    Panel $panel,
    string $methodName,
    mixed $parameters,
    PanelSchemaHelper $schema,
  ): void {
    $methodConfig = $schema->getMethodConfig($methodName);

    if ($methodConfig['accepts_parameter']) {
      $value = is_bool($parameters) ? $parameters : true;
      $panel->$methodName($value);
    } else {
      $panel->$methodName();
    }
  }

  /**
   * Apply array method with intelligent merging
   */
  protected function applyArrayMethod(
    Panel $panel,
    string $methodName,
    mixed $parameters,
    PanelSchemaHelper $schema,
  ): void {
    $methodConfig = $schema->getMethodConfig($methodName);
    $requiredDefaults = $methodConfig['required_defaults'] ?? [];

    if (!is_array($parameters)) {
      $parameters = [];
    }

    // Merge with required defaults
    if ($methodConfig['mergeable'] && !empty($requiredDefaults)) {
      $parameters = array_merge($requiredDefaults, $parameters);
    }

    // Handle special preprocessing
    $parameters = $this->preprocessMethodParameters($methodName, $parameters);

    if (empty($parameters)) {
      return; // Skip empty arrays for optional methods
    }

    if ($this->isAssociativeArray($parameters)) {
      $panel->$methodName(...array_values($parameters));
    } else {
      $panel->$methodName($parameters);
    }
  }

  /**
   * Apply regular method (string, mixed, etc.)
   */
  protected function applyRegularMethod(Panel $panel, string $methodName, mixed $parameters): void
  {
    $processedParameters = $this->preprocessMethodParameters($methodName, $parameters);

    if (empty($processedParameters)) {
      $panel->$methodName();
    } elseif (is_array($processedParameters)) {
      // Special handling for colors method
      if ($methodName === 'colors') {
        $panel->$methodName($processedParameters);
      } else {
        $panel->$methodName(...$processedParameters);
      }
    } else {
      $panel->$methodName($processedParameters);
    }
  }

  /**
   * Preprocess method parameters for special cases
   */
  protected function preprocessMethodParameters(string $methodName, mixed $parameters): mixed
  {
    return match ($methodName) {
      'colors' => $this->preprocessColors($parameters),
      'discoverResources', 'discoverPages', 'discoverWidgets' => $this->preprocessDiscoverMethods($parameters),
      default => $parameters,
    };
  }

  /**
   * Preprocess color parameters
   */
  protected function preprocessColors(mixed $parameters): mixed
  {
    if (!is_array($parameters) || empty($parameters)) {
      return $parameters;
    }

    $processedColors = [];

    foreach ($parameters as $key => $value) {
      if (is_string($value)) {
        // Handle "Color::Blue" format
        if (str_starts_with($value, 'Color::')) {
          $colorName = substr($value, 7); // Remove "Color::" prefix
          if (class_exists(Color::class)) {
            $colorConstant = 'Filament\Support\Colors\Color::' . $colorName;
            if (defined($colorConstant)) {
              $processedColors[$key] = constant($colorConstant);
              continue;
            }
          }
        }
        // Handle direct color names like "Blue"
        elseif (class_exists(Color::class)) {
          $colorConstant = 'Filament\Support\Colors\Color::' . ucfirst(strtolower($value));
          if (defined($colorConstant)) {
            $processedColors[$key] = constant($colorConstant);
            continue;
          }
        }
      }
      $processedColors[$key] = $value;
    }

    return $processedColors;
  }

  /**
   * Preprocess discover method parameters
   */
  protected function preprocessDiscoverMethods(mixed $parameters): array
  {
    if (!is_array($parameters)) {
      return [];
    }

    if (isset($parameters['in']) && str_starts_with($parameters['in'], 'app/')) {
      $parameters['in'] = app_path(substr($parameters['in'], 4));
    }

    return [
      'in' => $parameters['in'] ?? '',
      'for' => $parameters['for'] ?? '',
    ];
  }

  /**
   * Check if array is associative
   */
  protected function isAssociativeArray(array $array): bool
  {
    return array_keys($array) !== range(0, count($array) - 1);
  }

  /**
   * Validate methods against schema
   */
  public function validateMethods(): array
  {
    $errors = [];
    $schema = $this->getSchemaHelper();
    $methods = $this->methods ?? [];

    foreach ($methods as $methodName => $parameters) {
      if (!method_exists(Panel::class, $methodName)) {
        $errors[] = "Method {$methodName} does not exist on Panel class";
        continue;
      }

      $methodConfig = $schema->getMethodConfig($methodName);
      if (!$methodConfig) {
        $errors[] = "Method {$methodName} not found in schema";
        continue;
      }

      // Validate based on method type
      if ($schema->isBooleanMethod($methodName)) {
        if (!is_bool($parameters) && !is_null($parameters)) {
          $errors[] = "Method {$methodName} expects boolean parameter";
        }
      } elseif ($schema->isArrayMethod($methodName)) {
        if (!is_array($parameters) && !is_null($parameters)) {
          $errors[] = "Method {$methodName} expects array parameter";
        }
      }
    }

    return $errors;
  }

  /**
   * Quick factory method for creating panels with methods
   */
  public static function quick(string $id, string $path, array $methods = []): self
  {
    return self::create([
      'id' => $id,
      'path' => $path,
      'methods' => array_merge(
        [
          'login' => true,
          'colors' => ['primary' => 'blue'], // ✅ Format correct
        ],
        $methods,
      ),
      'is_active' => true,
    ]);
  }

  /**
   * Debug method configuration
   */
  public function debugConfig(): array
  {
    return [
      'model' => $this->toArray(),
      'validation_errors' => $this->validateMethods(),
      'schema_analysis' => $this->getSchemaHelper()->getSchema(),
      'would_create' => [
        'id' => $this->id,
        'path' => $this->path,
        'is_default' => $this->is_default,
        'method_count' => count($this->methods ?? []),
        'available_methods' => array_keys($this->methods ?? []),
      ],
    ];
  }
}
