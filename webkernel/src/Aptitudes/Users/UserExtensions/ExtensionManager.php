<?php
namespace Webkernel\Aptitudes\Users\UserExtensions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Webkernel\Arcanes\QueryModules;

class ExtensionManager
{
    /**
     * The user model instance that extensions belong to.
     *
     * @var Model
     */
    protected Model $user;

    /**
     * Mapping of extension class names to their handled attributes.
     * Built dynamically from ArcaneBuildModule configuration.
     *
     * @var array<class-string, array<string>>
     */
    protected array $extensionMap;

    /**
     * Mapping of relationship names to extension class names.
     * Supports multiple naming conventions for flexible property access.
     *
     * @var array<string, class-string>
     */
    protected array $relationshipMap;

    /**
     * Create a new extension manager instance.
     *
     * @param Model $user The user model that extensions will be associated with
     */
    public function __construct(Model $user)
    {
        $this->user = $user;
        $this->extensionMap = $this->buildExtensionMap();
        $this->relationshipMap = $this->buildRelationshipMap();
    }

    /**
     * Create a new extension record using the appropriate extension model.
     * Automatically determines the correct extension model based on provided attributes.
     *
     * @param array<string, mixed> $attributes Data to store in the extension model
     * @return Model The created extension model instance
     * @throws InvalidArgumentException When no suitable extension model is found
     */
    public function create(array $attributes): Model
    {
        $extensionClass = $this->findExtensionByAttributes($attributes);

        if ($extensionClass === null) {
            Log::warning('Extension creation failed: no suitable model found', [
                'attributes' => array_keys($attributes),
                'user_id' => $this->user->getKey(),
                'available_extensions' => array_keys($this->extensionMap)
            ]);

            throw new InvalidArgumentException(
                'No extension model found for attributes: ' . implode(', ', array_keys($attributes)) . '. ' .
                'Available extensions handle: ' . implode(', ', array_merge(...array_values($this->extensionMap)))
            );
        }

        /**
         * Ensure the user_id is set to maintain relationship integrity.
         * This prevents orphaned extension records and ensures proper association.
         */
        $attributes['user_id'] = $this->user->getKey();

        return $this->user->hasOne($extensionClass, 'user_id')->create($attributes);
    }

    /**
     * Update an existing extension record or create a new one if it doesn't exist.
     * Uses the same attribute-based model detection as the create method.
     *
     * @param array<string, mixed> $attributes Data to store or update in the extension model
     * @return Model The updated or created extension model instance
     * @throws InvalidArgumentException When no suitable extension model is found
     */
    public function updateOrCreate(array $attributes): Model
    {
        $extensionClass = $this->findExtensionByAttributes($attributes);

        if ($extensionClass === null) {
            throw new InvalidArgumentException(
                'No extension model found for attributes: ' . implode(', ', array_keys($attributes)) . '. ' .
                'Verify that an extension model handles these attributes.'
            );
        }

        return $this->user->hasOne($extensionClass, 'user_id')->updateOrCreate(
            ['user_id' => $this->user->getKey()],
            $attributes
        );
    }

    /**
     * Find the appropriate extension model class by relationship name.
     * Supports multiple naming conventions including snake_case, camelCase, and lowercase.
     *
     * @param string $relationshipName The property name used to access the extension
     * @return class-string|null The extension model class name or null if not found
     */
    public function findExtensionByRelationshipName(string $relationshipName): ?string
    {
        /**
         * Check direct match first for optimal performance.
         * This handles the most common case where the relationship name
         * exactly matches a defined mapping.
         */
        if (isset($this->relationshipMap[$relationshipName])) {
            return $this->relationshipMap[$relationshipName];
        }

        /**
         * Attempt to resolve through case conversion.
         * This provides flexibility for different coding style preferences
         * while maintaining predictable behavior.
         */
        $studlyName = Str::studly($relationshipName);
        $snakeName = Str::snake($studlyName);

        if (isset($this->relationshipMap[$snakeName])) {
            return $this->relationshipMap[$snakeName];
        }

        /**
         * Try camelCase conversion as final fallback.
         * This ensures maximum compatibility with various naming conventions.
         */
        $camelName = Str::camel($relationshipName);
        if (isset($this->relationshipMap[$camelName])) {
            return $this->relationshipMap[$camelName];
        }

        return null;
    }

    /**
     * Determine which extension model should handle the provided attributes.
     * Returns the first extension model that can handle any of the given attributes.
     *
     * @param array<string, mixed> $attributes The attributes to match against extension capabilities
     * @return class-string|null The matching extension class or null if none found
     */
    protected function findExtensionByAttributes(array $attributes): ?string
    {
        $attributeKeys = array_keys($attributes);

        /**
         * Filter out the user_id attribute as it's not relevant for extension selection.
         * The user_id is a relationship key rather than extension-specific data.
         */
        $attributeKeys = array_filter($attributeKeys, function ($key) {
            return $key !== 'user_id';
        });

        if (empty($attributeKeys)) {
            return null;
        }

        foreach ($this->extensionMap as $class => $handledAttributes) {
            if (array_intersect($attributeKeys, $handledAttributes)) {
                return $class;
            }
        }

        return null;
    }

    /**
     * Build the extension map from configured extension classes.
     * Creates a mapping of extension class names to their handled attributes.
     *
     * @return array<class-string, array<string>>
     */
    protected function buildExtensionMap(): array
    {
        try {
            // Use QueryModules to get user extensions from available modules
            $extensions = QueryModules::make()
                ->select(['namespace'])
                ->where('namespace')->isNotNull()
                ->get();

            // Extract extension classes from modules
            $extensionClasses = [];
            foreach ($extensions as $module) {
                if (isset($module['namespace'])) {
                    $namespace = $module['namespace'];

                    // Look for UserExtension classes in the module's namespace
                    $possibleExtensions = [
                        $namespace . '\\Models\\UserExtension',
                        $namespace . '\\UserExtensions\\UserExtension',
                        $namespace . '\\Extensions\\UserExtension',
                    ];

                    foreach ($possibleExtensions as $extensionClass) {
                        if (class_exists($extensionClass)) {
                            $extensionClasses[] = $extensionClass;
                        }
                    }
                }
            }

            $extensions = $extensionClasses;
        } catch (\Exception $e) {
            Log::error('Failed to load user extensions from modules', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }

        $map = [];

        foreach ($extensions as $class) {
            if (!class_exists($class)) {
                Log::warning('Extension class does not exist', [
                    'class' => $class
                ]);
                continue;
            }

            if (!method_exists($class, 'getHandledAttributes')) {
                Log::warning('Extension class lacks required getHandledAttributes method', [
                    'class' => $class
                ]);
                continue;
            }

            // SECURITY CHECK: Validate secure extensions
            if (!$this->validateSecureExtension($class)) {
                Log::warning('Extension failed security validation', [
                    'class' => $class
                ]);
                continue;
            }

            try {
                $handledAttributes = $class::getHandledAttributes();

                if (!is_array($handledAttributes)) {
                    Log::warning('Extension getHandledAttributes must return array', [
                        'class' => $class,
                        'returned_type' => gettype($handledAttributes)
                    ]);
                    continue;
                }

                $map[$class] = $handledAttributes;
            } catch (\Exception $e) {
                Log::warning('Failed to get handled attributes from extension', [
                    'class' => $class,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $map;
    }

    /**
     * Validate secure extensions to prevent unauthorized access
     *
     * @param string $class Extension class name
     * @return bool True if extension is authorized
     */
    protected function validateSecureExtension(string $class): bool
    {
        // Check if extension implements SecureExtension interface
        if (!is_subclass_of($class, \Webkernel\Aptitudes\Users\UserExtensions\Contracts\SecureExtension::class)) {
            // Non-secure extensions are allowed (backward compatibility)
            return true;
        }

        try {
            // Validate security level
            $securityLevel = $class::getSecurityLevel();
            if ($securityLevel < 0 || $securityLevel > 100) {
                Log::error('Invalid security level for extension', [
                    'class' => $class,
                    'security_level' => $securityLevel
                ]);
                return false;
            }

            // Only allow RBAC extensions with security level 100
            if ($securityLevel === 100) {
                $isRbacExtension = str_contains($class, 'RBAC') || str_contains($class, 'Rbac');
                if (!$isRbacExtension) {
                    Log::error('Non-RBAC extension attempted to use maximum security level', [
                        'class' => $class,
                        'security_level' => $securityLevel
                    ]);
                    return false;
                }
            }

            // Validate access permissions
            if (!$class::validateAccess()) {
                Log::warning('Extension access validation failed', [
                    'class' => $class
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Security validation failed for extension', [
                'class' => $class,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Build the relationship name mapping for property access.
     * Creates multiple naming convention variants for each extension model.
     *
     * @return array<string, class-string>
     */
    protected function buildRelationshipMap(): array
    {
        $map = [];

        foreach (array_keys($this->extensionMap) as $class) {
            /**
             * Extract the base class name from the fully qualified class name.
             * This allows relationship names to be independent of namespace structure.
             */
            $className = class_basename($class);

            /**
             * Generate multiple relationship name variants to support different
             * coding style preferences and conventions.
             */
            $variants = [
                Str::snake($className),           // user_profile
                Str::camel($className),           // userProfile
                strtolower($className),          // userprofile
                Str::kebab($className),           // user-profile
            ];

            /**
             * Register each variant in the mapping, with later variants
             * taking precedence in case of conflicts.
             */
            foreach ($variants as $variant) {
                if ($variant !== '') {
                    $map[$variant] = $class;
                }
            }
        }

        return $map;
    }

    /**
     * Get all available extension models and their handled attributes.
     * Useful for debugging and system introspection.
     *
     * @return array<class-string, array<string>>
     */
    public function getAvailableExtensions(): array
    {
        return $this->extensionMap;
    }

    /**
     * Get the complete relationship name mapping.
     * Useful for debugging relationship resolution issues.
     *
     * @return array<string, class-string>
     */
    public function getRelationshipMap(): array
    {
        return $this->relationshipMap;
    }

    /**
     * Check if any extension model can handle the provided attributes.
     * Useful for validation and pre-flight checks.
     *
     * @param array<string, mixed> $attributes The attributes to check
     * @return bool True if an extension model can handle the attributes
     */
    public function canHandle(array $attributes): bool
    {
        return $this->findExtensionByAttributes($attributes) !== null;
    }

    /**
     * Get all extension models that can handle any of the provided attributes.
     * Returns multiple matches when attributes span multiple extensions.
     *
     * @param array<string, mixed> $attributes The attributes to match
     * @return array<class-string> Array of matching extension class names
     */
    public function getAllMatchingExtensions(array $attributes): array
    {
        $attributeKeys = array_filter(array_keys($attributes), function ($key) {
            return $key !== 'user_id';
        });

        if (empty($attributeKeys)) {
            return [];
        }

        $matches = [];

        foreach ($this->extensionMap as $class => $handledAttributes) {
            if (array_intersect($attributeKeys, $handledAttributes)) {
                $matches[] = $class;
            }
        }

        return $matches;
    }
}
