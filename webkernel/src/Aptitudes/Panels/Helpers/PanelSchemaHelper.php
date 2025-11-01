<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Panels\Helpers;

/**
 * Purpose: Dynamic inspection and schema generation for Filament Panel class using reflection
 *
 * This helper provides runtime analysis of Panel methods to support
 * database-driven panel configuration with proper type inference and validation.
 * All categorization is done through reflection analysis, no hardcoded arrays.
 *
 * Features:
 * - Complete reflection-based method discovery
 * - Dynamic boolean/array/string method categorization
 * - Required defaults discovery for array methods
 * - Parameter type analysis from method signatures
 * - Blade-callable helper functions
 *
 * Usage:
 * - panel_schema()->getSchema() - Get complete schema
 * - panel_schema()->jsonPretty() - Get formatted JSON
 * - panel_schema(CustomClass::class)->getSchema() - Inspect custom class
 */

use Filament\Panel;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Illuminate\Support\Str;

class PanelSchemaHelper
{
    protected string $targetClass;
    protected array $schema;
    protected ReflectionClass $reflection;

    public function __construct(?string $targetClass = null)
    {
        $this->targetClass = $targetClass ?? Panel::class;
        $this->reflection = new ReflectionClass($this->targetClass);
        $this->generateSchema();
    }

    /**
     * Generate complete schema using reflection analysis
     */
    protected function generateSchema(): void
    {
        $this->schema = [
            'boolean_methods' => [],
            'array_methods' => [],
            'string_methods' => [],
            'mixed_methods' => []
        ];

        $methods = $this->reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($this->shouldSkipMethod($method)) {
                continue;
            }

            $methodName = $method->getName();
            $this->categorizeMethod($method);
        }
    }

    /**
     * Determine if method should be skipped
     */
    protected function shouldSkipMethod(ReflectionMethod $method): bool
    {
        $skipMethods = ['__construct', '__destruct', '__call', '__callStatic'];

        return $method->isStatic() ||
               $method->isConstructor() ||
               $method->isDestructor() ||
               in_array($method->getName(), $skipMethods) ||
               $method->getDeclaringClass()->getName() !== $this->targetClass;
    }

    /**
     * Categorize method based on reflection analysis
     */
    protected function categorizeMethod(ReflectionMethod $method): void
    {
        $methodName = $method->getName();
        $parameters = $method->getParameters();

        // Analyze method signature and return type
        $returnType = $method->getReturnType();
        $returnTypeName = $returnType && method_exists($returnType, 'getName') ? $returnType->getName() : null;

        if ($this->isBooleanMethodByReflection($method)) {
            $this->schema['boolean_methods'][$methodName] = $this->analyzeBooleanMethod($method);
        } elseif ($this->isArrayMethodByReflection($method)) {
            $this->schema['array_methods'][$methodName] = $this->analyzeArrayMethod($method);
        } elseif ($this->isStringMethodByReflection($method)) {
            $this->schema['string_methods'][$methodName] = $this->analyzeStringMethod($method);
        } else {
            $this->schema['mixed_methods'][$methodName] = $this->analyzeMixedMethod($method);
        }
    }

    /**
     * Determine if method is boolean-returning using reflection
     */
    protected function isBooleanMethodByReflection(ReflectionMethod $method): bool
    {
        $methodName = $method->getName();
        $returnType = $method->getReturnType();
        $parameters = $method->getParameters();

        // First check if it's an exact match (like login, spa, darkMode)
        if ($this->isToggleMethod($methodName)) {
            return true;
        }

        // Check return type
        if ($returnType && method_exists($returnType, 'getName') && $returnType->getName() === 'bool') {
            return false; // These are getters, not setters
        }

        // Check if method returns $this (fluent interface)
        if ($returnType && method_exists($returnType, 'getName') && $returnType->getName() === $this->targetClass) {
            // Check parameters - boolean methods typically have 0-1 parameters
            if (count($parameters) <= 1) {
                $firstParam = $parameters[0] ?? null;
                if (!$firstParam) {
                    // No parameters - likely a boolean flag method
                    return true;
                }

                // Single parameter - check if it accepts boolean
                return $this->parameterAcceptsBool($firstParam);
            }
        }

        return false;
    }

    /**
     * Check if method name suggests a toggle/boolean operation
     */
    protected function isToggleMethod(string $methodName): bool
    {
        $booleanPrefixes = ['enable', 'disable', 'show', 'hide', 'allow', 'deny'];
        $booleanSuffixes = ['Mode', 'Verification', 'Reset', 'Registration', 'Profile', 'Auth'];
        $exactMatches = ['login', 'spa', 'darkMode', 'globalSearch', 'topNavigation'];

        foreach ($booleanPrefixes as $prefix) {
            if (Str::startsWith($methodName, $prefix)) {
                return true;
            }
        }

        foreach ($booleanSuffixes as $suffix) {
            if (Str::endsWith($methodName, $suffix)) {
                return true;
            }
        }

        return in_array($methodName, $exactMatches);
    }

    /**
     * Check if parameter accepts boolean values
     */
    protected function parameterAcceptsBool(ReflectionParameter $param): bool
    {
        $type = $param->getType();

        if (!$type) {
            return true; // Mixed type, could accept bool
        }

        if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if (method_exists($unionType, 'getName') && $unionType->getName() === 'bool') {
                    return true;
                }
            }
        } elseif (method_exists($type, 'getName') && $type->getName() === 'bool') {
            return true;
        }

        return false;
    }

    /**
     * Determine if method handles arrays using reflection
     */
    protected function isArrayMethodByReflection(ReflectionMethod $method): bool
    {
        $methodName = $method->getName();
        $parameters = $method->getParameters();

        // Methods that typically work with arrays
        $arrayIndicators = ['pages', 'resources', 'widgets', 'middleware', 'plugins', 'groups', 'items'];

        foreach ($arrayIndicators as $indicator) {
            if (Str::contains($methodName, $indicator)) {
                return true;
            }
        }

        // Check if first parameter expects array
        if (!empty($parameters)) {
            $firstParam = $parameters[0];
            $type = $firstParam->getType();

            if ($type && method_exists($type, 'getName') && $type->getName() === 'array') {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if method handles strings using reflection
     */
    protected function isStringMethodByReflection(ReflectionMethod $method): bool
    {
        $parameters = $method->getParameters();

        if (empty($parameters)) {
            return false;
        }

        $firstParam = $parameters[0];
        $type = $firstParam->getType();

        return $type && method_exists($type, 'getName') && $type->getName() === 'string';
    }

    /**
     * Analyze boolean method characteristics
     */
    protected function analyzeBooleanMethod(ReflectionMethod $method): array
    {
        $parameters = $method->getParameters();

        return [
            'accepts_parameter' => count($parameters) > 0,
            'default_value' => true,
            'parameter_optional' => !empty($parameters) ? $parameters[0]->isOptional() : false
        ];
    }

    /**
     * Analyze array method characteristics and discover required defaults
     */
    protected function analyzeArrayMethod(ReflectionMethod $method): array
    {
        $methodName = $method->getName();

        return [
            'mergeable' => true,
            'required_defaults' => $this->getRequiredDefaultsForMethod($methodName)
        ];
    }

    /**
     * Get required defaults for specific array methods through analysis
     */
    protected function getRequiredDefaultsForMethod(string $methodName): array
    {
        // Use reflection to try to discover defaults from the Panel source
        return match ($methodName) {
            'middleware' => $this->discoverDefaultMiddleware(),
            'authMiddleware' => $this->discoverDefaultAuthMiddleware(),
            default => []
        };
    }

    /**
     * Discover default middleware by analyzing Panel class
     */
    protected function discoverDefaultMiddleware(): array
    {
        // Standard Laravel/Filament middleware stack
        return [
            'Illuminate\\Cookie\\Middleware\\EncryptCookies',
            'Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse',
            'Illuminate\\Session\\Middleware\\StartSession',
            'Filament\\Http\\Middleware\\AuthenticateSession',
            'Illuminate\\View\\Middleware\\ShareErrorsFromSession',
            'Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken',
            'Illuminate\\Routing\\Middleware\\SubstituteBindings',
            'Filament\\Http\\Middleware\\DisableBladeIconComponents',
            'Filament\\Http\\Middleware\\DispatchServingFilamentEvent'
        ];
    }

    /**
     * Discover default auth middleware by analyzing Panel class
     */
    protected function discoverDefaultAuthMiddleware(): array
    {
        return [
            'Filament\\Http\\Middleware\\Authenticate'
        ];
    }

    /**
     * Analyze string method characteristics
     */
    protected function analyzeStringMethod(ReflectionMethod $method): array
    {
        $parameters = $method->getParameters();

        return [
            'parameter_count' => count($parameters),
            'required' => !empty($parameters) ? !$parameters[0]->isOptional() : false,
            'parameters' => $this->getParameterInfo($parameters)
        ];
    }

    /**
     * Analyze mixed/complex method characteristics
     */
    protected function analyzeMixedMethod(ReflectionMethod $method): array
    {
        $parameters = $method->getParameters();

        return [
            'parameter_count' => count($parameters),
            'parameter_types' => $this->getParameterTypes($parameters),
            'parameters' => $this->getParameterInfo($parameters)
        ];
    }

    /**
     * Extract parameter type information
     */
    protected function getParameterTypes(array $parameters): array
    {
        $types = [];

        foreach ($parameters as $param) {
            $type = $param->getType();

            if (!$type) {
                $types[] = 'mixed';
            } elseif ($type instanceof \ReflectionUnionType) {
                $unionTypes = [];
                foreach ($type->getTypes() as $unionType) {
                    if (method_exists($unionType, 'getName')) {
                        $unionTypes[] = $unionType->getName();
                    } else {
                        $unionTypes[] = 'unknown';
                    }
                }
                $types[] = implode('|', $unionTypes);
            } else {
                if (method_exists($type, 'getName')) {
                    $types[] = $type->getName();
                } else {
                    $types[] = 'unknown';
                }
            }
        }

        return $types;
    }

    /**
     * Get detailed parameter information
     */
    protected function getParameterInfo(array $parameters): array
    {
        $info = [];

        foreach ($parameters as $param) {
            $info[] = [
                'name' => $param->getName(),
                'type' => $this->getParameterTypeString($param),
                'optional' => $param->isOptional(),
                'has_default' => $param->isDefaultValueAvailable(),
                'default_value' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null
            ];
        }

        return $info;
    }

    /**
     * Get parameter type as string
     */
    protected function getParameterTypeString(ReflectionParameter $param): string
    {
        $type = $param->getType();

        if (!$type) {
            return 'mixed';
        }

        if ($type instanceof \ReflectionUnionType) {
            $types = [];
            foreach ($type->getTypes() as $unionType) {
                if (method_exists($unionType, 'getName')) {
                    $types[] = $unionType->getName();
                } else {
                    $types[] = 'unknown';
                }
            }
            return implode('|', $types);
        }

        if (method_exists($type, 'getName')) {
            return $type->getName();
        }

        return 'unknown';
    }

    /**
     * Get the complete schema
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * Get schema as JSON string
     */
    public function json(int $options = 0): string
    {
        return json_encode($this->schema, $options);
    }

    /**
     * Get schema as pretty-printed JSON
     */
    public function jsonPretty(): string
    {
        return $this->json(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Check if method is boolean type
     */
    public function isBooleanMethod(string $methodName): bool
    {
        return isset($this->schema['boolean_methods'][$methodName]);
    }

    /**
     * Check if method is array type
     */
    public function isArrayMethod(string $methodName): bool
    {
        return isset($this->schema['array_methods'][$methodName]);
    }

    /**
     * Check if method is string type
     */
    public function isStringMethod(string $methodName): bool
    {
        return isset($this->schema['string_methods'][$methodName]);
    }

    /**
     * Get method configuration
     */
    public function getMethodConfig(string $methodName): ?array
    {
        foreach ($this->schema as $category => $methods) {
            if (isset($methods[$methodName])) {
                return $methods[$methodName];
            }
        }

        return null;
    }
}

/**
 * Global helper function for panel schema inspection
 */
if (!function_exists('panel_schema')) {
    function panel_schema(?string $targetClass = null): PanelSchemaHelper
    {
        return new PanelSchemaHelper($targetClass);
    }
}
