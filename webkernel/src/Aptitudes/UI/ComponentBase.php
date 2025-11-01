<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\UI;

use Illuminate\Support\Str;

abstract class ComponentBase
{
  protected array $config = [];
  protected string $instanceId;
  protected ComponentSchema $schema;
  protected array $passedAttributes = [];

  public function __construct(array $attributes = [])
  {
    $this->instanceId = $this->generateId();
    $this->schema = new ComponentSchema($this->getName());
    $this->define($this->schema);
    $this->passedAttributes = $attributes;
    $this->config = $this->schema->resolve($attributes);
  }

  /**
   * Static factory method to create a component instance
   */
  public static function make(array $attributes = []): static
  {
    return new static($attributes);
  }

  /**
   * Magic method to handle fluent setters
   */
  public function __call(string $name, array $arguments): static
  {
    // Check if this is a known field in the schema
    $fieldNames = $this->schema->getFieldNames();

    if (in_array($name, $fieldNames)) {
      // Update the config with the new value
      $this->config[$name] = $arguments[0] ?? null;
      return $this;
    }

    throw new \BadMethodCallException(
      sprintf('Method %s::%s does not exist. Available fields: %s', static::class, $name, implode(', ', $fieldNames)),
    );
  }

  abstract protected function define(ComponentSchema $schema): void;

  protected function generateId(): string
  {
    return Str::kebab($this->getName()) . '_' . uniqid();
  }

  protected function getName(): string
  {
    return class_basename(static::class);
  }

  public function getConfig(): array
  {
    return $this->config;
  }

  public function getId(): string
  {
    return $this->instanceId;
  }

  public function getProcessedKeys(): array
  {
    return $this->schema->getFieldNames();
  }

  public function getClasses(): string
  {
    return $this->schema->getClasses($this->config);
  }

  public function getAttributes(): string
  {
    $attrs = collect([
      'class' => $this->getClasses(),
      'data-component' => Str::kebab($this->getName()),
      'data-builder' => $this->getBuilderData(),
    ]);

    $attrs = $attrs->merge($this->schema->getAttributes($this->config));

    // Merge with passed attributes
    $passedAttrs = collect($this->passedAttributes);

    // Handle class merging specially
    if (isset($passedAttrs['class'])) {
      $passedClass = $passedAttrs['class'];
      $generatedClass = $attrs['class'];
      // If override-css is set, use only the passed class
      if (isset($passedAttrs['override-css']) && $passedAttrs['override-css']) {
        $attrs['class'] = $passedClass;
      } else {
        // Otherwise, merge classes (passed classes are added to generated ones)
        $attrs['class'] = trim($generatedClass . ' ' . $passedClass);
      }
      // Remove override-css from final attributes
      $passedAttrs = $passedAttrs->except(['class', 'override-css']);
    }

    $attrs = $attrs->merge($passedAttrs);

    return $attrs
      ->filter(fn($v) => $v !== null && $v !== '')
      ->map(function ($v, $k) {
        $value = is_array($v) ? json_encode($v) : (string) $v;
        return $k === 'data-builder' ? "{$k}='{$value}'" : "{$k}=\"{$value}\"";
      })
      ->implode(' ');
  }

  protected function getBuilderData(): string
  {
    return json_encode(
      [
        'id' => $this->instanceId,
        'component' => $this->getName(),
        'schema' => $this->schema->getSchema(),
        'config' => $this->config,
      ],
      JSON_UNESCAPED_SLASHES,
    );
  }

  public function getSchema(): ComponentSchema
  {
    return $this->schema;
  }

  // Magic methods for better DX
  public function __get(string $name): mixed
  {
    return $this->config[$name] ?? null;
  }

  public function __isset(string $name): bool
  {
    return isset($this->config[$name]);
  }

  public function toArray(): array
  {
    return $this->config;
  }

  public function get(string $key, mixed $default = null): mixed
  {
    return $this->config[$key] ?? $default;
  }

  public function has(string $key): bool
  {
    return isset($this->config[$key]);
  }
}

class ComponentSchema
{
  protected string $name;
  protected array $fields = [];
  protected ?string $lastField = null;
  protected array $computedFields = [];
  protected array $baseClasses = [];
  protected array $variantClasses = [];
  protected array $attributeRules = [];
  protected array $conditionalClasses = [];
  protected array $dynamicClasses = [];

  public function __construct(string $name)
  {
    $this->name = $name;

    // Add default properties available in all components
    $this->string('customColor')->nullable();
    $this->string('customStyle')->nullable();
    $this->string('customClass')->nullable();
  }

  // Field definition methods
  public function string(string $name): self
  {
    return $this->addField($name, 'string');
  }

  public function mixed(string $name): self
  {
    return $this->addField($name, 'mixed');
  }

  public function boolean(string $name): self
  {
    return $this->addField($name, 'boolean');
  }

  public function number(string $name): self
  {
    return $this->addField($name, 'number');
  }

  public function float(string $name): self
  {
    // Alias of number() for readability
    return $this->addField($name, 'number');
  }

  public function enum(string $name): self
  {
    return $this->addField($name, 'enum');
  }

  public function url(string $name): self
  {
    return $this->addField($name, 'url');
  }

  public function icon(string $name): self
  {
    return $this->addField($name, 'icon');
  }

  public function integer(string $name): self
  {
    return $this->addField($name, 'integer');
  }

  public function array(string $name): self
  {
    return $this->addField($name, 'array');
  }

  public function sound(string $name): self
  {
    return $this->addField($name, 'sound');
  }

  // NEW: Semantic color field that maps to theme colors
  public function color(string $name): self
  {
    return $this->addField($name, 'color');
  }

  protected function addField(string $name, string $type): self
  {
    $this->fields[$name] = [
      'type' => $type,
      'default' => null,
      'options' => null,
      'label' => ucfirst(str_replace('_', ' ', $name)),
      'nullable' => false,
      'help' => null,
      'placeholder' => null,
      'condition' => null,
      'classes' => null,
    ];
    $this->lastField = $name;
    return $this;
  }

  // Field modifier methods
  public function default(mixed $value): self
  {
    if ($this->lastField) {
      $this->fields[$this->lastField]['default'] = $value;
    }
    return $this;
  }

  public function options(array $options): self
  {
    if ($this->lastField) {
      $this->fields[$this->lastField]['options'] = $options;
    }
    return $this;
  }

  public function label(string $label): self
  {
    if ($this->lastField) {
      $this->fields[$this->lastField]['label'] = $label;
    }
    return $this;
  }

  public function help(string $help): self
  {
    if ($this->lastField) {
      $this->fields[$this->lastField]['help'] = $help;
    }
    return $this;
  }

  public function placeholder(string $placeholder): self
  {
    if ($this->lastField) {
      $this->fields[$this->lastField]['placeholder'] = $placeholder;
    }
    return $this;
  }

  public function nullable(): self
  {
    if ($this->lastField) {
      $this->fields[$this->lastField]['nullable'] = true;
    }
    return $this;
  }

  public function when(string $condition): self
  {
    if ($this->lastField) {
      $this->fields[$this->lastField]['condition'] = $condition;
    }
    return $this;
  }

  // Computed fields
  public function compute(string $name, callable $callback): self
  {
    $this->computedFields[$name] = $callback;
    return $this;
  }

  // Styling methods
  public function baseClasses(array $classes): self
  {
    $this->baseClasses = array_merge($this->baseClasses, $classes);
    return $this;
  }

  // NEW: Color-aware variant classes that use semantic colors
  public function colorVariant(string $variant, array $colorClasses): self
  {
    $this->variantClasses['color'][$variant] = $colorClasses;
    return $this;
  }

  public function variantClass(string $field, string $value, string|callable $classes): self
  {
    $this->variantClasses[$field][$value] = $classes;
    return $this;
  }

  public function variantClasses(string $field, array $classMap): self
  {
    $this->variantClasses[$field] = array_merge($this->variantClasses[$field] ?? [], $classMap);
    return $this;
  }

  public function conditionalClass(string $condition, string|callable $classes): self
  {
    $this->conditionalClasses[$condition] = $classes;
    return $this;
  }

  public function dynamicClass(string $name, callable $callback): self
  {
    $this->dynamicClasses[$name] = $callback;
    return $this;
  }

  // Attribute methods
  public function attribute(string $name, callable|string|array $rule): self
  {
    $this->attributeRules[$name] = $rule;
    return $this;
  }

  public function conditionalAttribute(string $conditionOrAttribute, mixed $attributeOrValue, mixed $value = null): self
  {
    // Support 2-argument form: (attribute, callable)
    if (is_callable($attributeOrValue) && $value === null) {
      $attribute = $conditionOrAttribute;
      $this->attributeRules[$attribute] = $attributeOrValue;
      return $this;
    }

    // Support original 3-argument form: (condition, attribute, value)
    $condition = $conditionOrAttribute;
    $attribute = is_string($attributeOrValue) ? $attributeOrValue : (string) $attributeOrValue;
    $fixedValue = $value;

    $this->attributeRules[$attribute] = function ($config) use ($condition, $fixedValue) {
      $passes = $config[$condition] ?? false;
      if (!$passes) {
        return null;
      }
      return is_callable($fixedValue) ? $fixedValue($config) : $fixedValue;
    };

    return $this;
  }

  public function dynamicAttribute(string $attribute, callable $callback): self
  {
    $this->attributeRules[$attribute] = $callback;
    return $this;
  }

  // Resolution methods
  public function resolve(array $attributes): array
  {
    $resolved = [];

    // Resolve regular fields
    foreach ($this->fields as $name => $field) {
      $value = $attributes[$name] ?? $field['default'];

      if ($field['type'] === 'boolean') {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
      } elseif ($field['type'] === 'number') {
        $value = is_numeric($value) ? (float) $value : null;
      } elseif ($field['type'] === 'sound') {
        $value = $this->validateSoundValue($value);
      } elseif ($field['type'] === 'color') {
        // Validate semantic color
        $value = $this->validateSemanticColor($value);
      }

      $resolved[$name] = $value;
    }

    // Resolve computed fields
    foreach ($this->computedFields as $name => $callback) {
      $resolved[$name] = $callback($resolved);
    }

    return $resolved;
  }

  public function getClasses(array $config): string
  {
    if ($config['remove_styling'] ?? false) {
      return $config['custom_class'] ?? '';
    }

    $classes = collect();

    // Base classes
    $classes = $classes->merge($this->baseClasses);

    // Handle color variants - the magic happens here
    if (isset($config['color'])) {
      $colorValue = $config['color'];

      // Standard colors that follow the pattern: bg-{color} text-background hover:bg-{color}/90 focus:ring-{color}
      $standardColors = ['primary', 'secondary', 'accent', 'success', 'warning', 'danger', 'info', 'black'];

      if (in_array($colorValue, $standardColors)) {
        // Auto-generate standard pattern
        $classes->push("bg-{$colorValue} text-background hover:bg-{$colorValue}/90 focus:ring-{$colorValue}");
      } else {
        // Check for custom variants (ghost, outline, etc.)
        if (isset($this->variantClasses['color'])) {
          $colorVariants = $this->variantClasses['color'];

          foreach ($colorVariants as $variant => $colorClasses) {
            if ($variant === $colorValue) {
              if (is_callable($colorClasses)) {
                $classes->push($colorClasses($config));
              } else {
                // Use semantic color classes
                if (is_string($colorClasses)) {
                  // Direct string classes - just add them
                  $classes->push($colorClasses);
                } else {
                  // Array of classes - resolve semantic colors
                  $classes = $classes->merge($this->resolveSemanticColorClasses($colorClasses, $colorValue));
                }
              }
              break;
            }
          }
        } else {
          // Fallback to primary if color not found
          $classes->push('bg-primary text-background hover:bg-primary/90 focus:ring-primary');
        }
      }
    }

    // Regular variant classes (non-color)
    foreach ($this->variantClasses as $field => $variants) {
      if ($field === 'color' || str_starts_with($field, '_')) {
        continue;
      }

      $value = $config[$field] ?? null;
      if ($value && isset($variants[$value])) {
        $variant = $variants[$value];
        if (is_callable($variant)) {
          $classes->push($variant($config));
        } else {
          $classes->push($variant);
        }
      }
    }

    // Conditional classes
    foreach ($this->conditionalClasses as $condition => $classValue) {
      if ($this->evaluateCondition($condition, $config)) {
        if (is_callable($classValue)) {
          $classes->push($classValue($config));
        } else {
          $classes->push($classValue);
        }
      }
    }

    // Dynamic classes
    foreach ($this->dynamicClasses as $name => $callback) {
      $result = $callback($config);
      if ($result) {
        $classes->push($result);
      }
    }

    // Custom class
    if (!empty($config['custom_class'])) {
      $classes->push($config['custom_class']);
    }

    return $classes->filter()->unique()->implode(' ');
  }

  public function getAttributes(array $config): array
  {
    $attrs = [];

    foreach ($this->attributeRules as $name => $rule) {
      if (is_callable($rule)) {
        $value = $rule($config);
        if ($value !== null) {
          $attrs[$name] = $value;
        }
      } elseif (is_string($rule) && isset($config[$rule])) {
        $attrs[$name] = $config[$rule];
      } elseif (is_array($rule)) {
        foreach ($rule as $condition => $value) {
          if (isset($config[$condition]) && $config[$condition]) {
            $attrs[$name] = is_callable($value) ? $value($config) : $value;
            break;
          }
        }
      }
    }

    return $attrs;
  }

  protected function evaluateCondition(string $condition, array $config): bool
  {
    if (isset($config[$condition])) {
      return (bool) $config[$condition];
    }

    if (str_contains($condition, ':')) {
      [$field, $value] = explode(':', $condition, 2);
      return ($config[$field] ?? null) === $value;
    }

    return false;
  }

  // NEW: Resolve semantic color classes to actual Tailwind classes
  protected function resolveSemanticColorClasses(array $colorClasses, string $semanticColor): array
  {
    $resolvedClasses = [];

    foreach ($colorClasses as $class) {
      // Replace semantic placeholders with actual color names
      $resolvedClass = str_replace(
        ['{color}', '{semantic}'],
        [$this->getActualColorName($semanticColor), $semanticColor],
        $class,
      );

      $resolvedClasses[] = $resolvedClass;
    }

    return $resolvedClasses;
  }

  // NEW: Get actual color name from semantic color
  protected function getActualColorName(string $semanticColor): string
  {
    // Get from theme helper if available
    if (function_exists('theme_color')) {
      return theme_color($semanticColor);
    }

    // Fallback mapping
    $colorMapping = [
      'primary' => 'blue',
      'secondary' => 'gray',
      'success' => 'green',
      'warning' => 'amber',
      'danger' => 'red',
      'error' => 'red',
      'info' => 'blue',
    ];

    return $colorMapping[$semanticColor] ?? 'gray';
  }

  // NEW: Validate semantic color
  protected function validateSemanticColor(mixed $value): ?string
  {
    if ($value === null || $value === '') {
      return null;
    }

    $validSemanticColors = ['primary', 'secondary', 'success', 'warning', 'danger', 'error', 'info'];

    return in_array($value, $validSemanticColors) ? $value : 'primary';
  }

  // Getter methods
  public function getSchema(): array
  {
    return $this->fields;
  }

  public function getFieldNames(): array
  {
    return array_keys($this->fields);
  }

  public function getName(): string
  {
    return $this->name;
  }

  protected function validateSoundValue(mixed $value): ?string
  {
    if ($value === null || $value === '') {
      return null;
    }

    $availableSounds = $this->getAvailableSounds();
    return in_array($value, $availableSounds) ? $value : null;
  }

  public function getAvailableSounds(): array
  {
    static $sounds = null;

    if ($sounds === null) {
      $soundsPath = __DIR__ . '/Resources/js/sounds/lib';
      $sounds = [];

      if (is_dir($soundsPath)) {
        $files = scandir($soundsPath);
        $allowedExtensions = ['wav', 'mp3', 'ogg', 'm4a'];

        foreach ($files as $file) {
          $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
          if (in_array($extension, $allowedExtensions) && !str_starts_with($file, '.')) {
            $soundName = pathinfo($file, PATHINFO_FILENAME);
            $soundName = str_replace('_', '-', $soundName);
            $sounds[] = $soundName;
          }
        }
      }
    }

    return $sounds;
  }
}
