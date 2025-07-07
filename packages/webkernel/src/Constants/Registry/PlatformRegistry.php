<?php

declare(strict_types=1);

namespace Webkernel\Constants\Registry;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class PlatformRegistry
{
    private static ?string $tenantId = null;
    private static array $cache = [];
    private static array $definitions = [];
    private static bool $initialized = false;

    public static function setTenantId(?string $tenantId): void
    {
        if (static::$tenantId !== $tenantId) {
            static::$tenantId = $tenantId;
            static::$cache = [];
        }
    }

    public static function get(?string $key = null, ?string $context = null): mixed
    {
        static::initialize();
        $cacheKey = $context ? "{$context}.{$key}" : ($key ?? '_all');

        if (isset(static::$cache[$cacheKey])) {
            return static::$cache[$cacheKey];
        }

        $value = $key === null ? static::getAllContextConstants($context) : static::resolve($key, $context);
        static::$cache[$cacheKey] = $value;
        return $value;
    }

    private static function initialize(): void
    {
        if (static::$initialized) {
            return;
        }

        static::loadDefinitions();
        static::$initialized = true;
    }

    private static function loadDefinitions(): void
    {
        $definitionsPath = __DIR__ . '/../Definitions';
        static::$definitions['webkernel'] = static::loadDefinition($definitionsPath . '/WebkernelDefinition.php');
        static::$definitions['branding'] = static::loadDefinition($definitionsPath . '/BrandingDefinition.php');
        static::loadModuleDefinitions();
    }

    private static function loadDefinition(string $path): array
    {
        return file_exists($path) && is_array($definition = include $path) ? $definition : [];
    }

    private static function loadModuleDefinitions(): void
    {
        $modulesPath = __DIR__ . '/../Definitions/Modules';
        if (!is_dir($modulesPath)) {
            return;
        }

        $modules = array_diff(scandir($modulesPath), ['.', '..']);
        foreach ($modules as $module) {
            $modulePath = $modulesPath . '/' . $module;
            if (!is_dir($modulePath)) {
                continue;
            }

            $moduleDefinitionPath = $modulePath . '/ModuleDefinition.php';
            $coreDefinitionPath = $modulePath . '/CoreDefinition.php';

            if (file_exists($moduleDefinitionPath)) {
                static::$definitions['modules'][$module] = static::loadDefinition($moduleDefinitionPath);
            }
            if (file_exists($coreDefinitionPath)) {
                static::$definitions['modules'][$module]['core'] = static::loadDefinition($coreDefinitionPath);
            }
        }
    }

    private static function resolve(string $key, ?string $context): mixed
    {
        if ($context === 'branding') {
            return static::resolveBranding($key);
        }
        if ($context && isset(static::$definitions['modules'][$context])) {
            $moduleValue = static::resolveFromModule($key, $context);
            if ($moduleValue !== null) {
                return $moduleValue;
            }
        }
        return static::resolveFromWebkernel($key);
    }

    private static function resolveBranding(string $key): mixed
    {
        if (static::$tenantId) {
            $tenantBranding = static::getTenantBranding();
            if (isset($tenantBranding[$key])) {
                return $tenantBranding[$key];
            }
        }

        $brandingDef = static::$definitions['branding'] ?? [];
        $version = static::resolveFromWebkernel('WEBKERNEL_VERSION') ?? 'default';
        if (isset($brandingDef['versions'][$version][$key])) {
            return $brandingDef['versions'][$version][$key];
        }
        if (isset($brandingDef['default'][$key])) {
            return $brandingDef['default'][$key];
        }
        return static::resolveFromWebkernel($key);
    }

    private static function getTenantBranding(): array
    {
        if (!static::$tenantId) {
            return [];
        }

        $cacheKey = "webkernel.tenant.branding." . static::$tenantId;
        return Cache::remember($cacheKey, 3600, function () {
            try {
                $tenant = DB::table('webkernel_tenants')->where('tenant_id', static::$tenantId)->first();
                if ($tenant && $tenant->branding) {
                    $branding = json_decode($tenant->branding, true);
                    return is_array($branding) ? $branding : [];
                }
            } catch (\Exception $e) {
                return [];
            }
            return [];
        });
    }

    private static function resolveFromModule(string $key, string $moduleName): mixed
    {
        $moduleDef = static::$definitions['modules'][$moduleName] ?? [];
        if (isset($moduleDef['core'][$key])) {
            return static::evaluateValue($moduleDef['core'][$key]);
        }
        if (isset($moduleDef[$key])) {
            return static::evaluateValue($moduleDef[$key]);
        }
        return null;
    }

    private static function resolveFromWebkernel(string $key): mixed
    {
        $webkernelDef = static::$definitions['webkernel'] ?? [];
        return isset($webkernelDef[$key]) ? static::evaluateValue($webkernelDef[$key]) : null;
    }

    private static function evaluateValue(mixed $value): mixed
    {
        if (is_callable($value)) {
            return $value();
        }
        if (is_string($value) && str_starts_with($value, '@dynamic:')) {
            return static::evaluateDynamicValue(substr($value, 9));
        }
        return $value;
    }

    private static function evaluateDynamicValue(string $expression): mixed
    {
        return match ($expression) {
            'webkernel_version' => static::resolveFromWebkernel('WEBKERNEL_VERSION'),
            'current_timestamp' => now()->timestamp,
            'current_date' => now()->format('Y-m-d'),
            'platform_path' => base_path('platform'),
            'webkernel_path' => base_path('packages/webkernel'),
            default => $expression,
        };
    }

    private static function getAllContextConstants(?string $context): array
    {
        if (!$context) {
            $constants = [];
            foreach (static::$definitions['webkernel'] ?? [] as $key => $value) {
                $constants[$key] = static::evaluateValue($value);
            }
            return $constants;
        }
        if ($context === 'branding') {
            $constants = [];
            $brandingDef = static::$definitions['branding'] ?? [];
            if (isset($brandingDef['default'])) {
                foreach ($brandingDef['default'] as $key => $value) {
                    $constants[$key] = static::evaluateValue($value);
                }
            }
            $version = static::resolveFromWebkernel('WEBKERNEL_VERSION');
            if ($version && isset($brandingDef['versions'][$version])) {
                foreach ($brandingDef['versions'][$version] as $key => $value) {
                    $constants[$key] = static::evaluateValue($value);
                }
            }
            if (static::$tenantId) {
                $tenantBranding = static::getTenantBranding();
                foreach ($tenantBranding as $key => $value) {
                    $constants[$key] = $value;
                }
            }
            return $constants;
        }
        if (isset(static::$definitions['modules'][$context])) {
            $constants = [];
            $moduleDef = static::$definitions['modules'][$context] ?? [];
            foreach ($moduleDef as $key => $value) {
                if ($key === 'core') {
                    continue;
                }
                $constants[$key] = static::evaluateValue($value);
            }
            if (isset($moduleDef['core'])) {
                foreach ($moduleDef['core'] as $key => $value) {
                    $constants[$key] = static::evaluateValue($value);
                }
            }
            return $constants;
        }
        return [];
    }

    public static function dumpDefinitions(): void
    {
        static::initialize();
        print_r(static::$definitions);
    }
}

if (__FILE__ === realpath($_SERVER['SCRIPT_FILENAME'])) {
    $autoloadPaths = [
        __DIR__ . '/../../../../../vendor/autoload.php',
    ];

    $autoloadFound = false;
    foreach ($autoloadPaths as $autoloadPath) {
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
            $autoloadFound = true;
            break;
        }
    }

    if (!$autoloadFound) {
        echo "Error: Could not find vendor/autoload.php\n";
        exit(1);
    }

    PlatformRegistry::dumpDefinitions();

    echo "\n===== PlatformRegistry::\$definitions =====\n";
    print_r((new \ReflectionClass(PlatformRegistry::class))
        ->getStaticProperties()['definitions']);
}