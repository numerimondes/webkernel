<?php
namespace Webkernel\PlatformConfig;

class Main
{
    public function getWebkernelPackages(): array
    {
        return function_exists('platformConfigPackagesHelper') 
            ? platformConfigPackagesHelper() 
            : [];
    }
    
    public function generateWebkernelComposerArray(): array
    {
        return function_exists('platformConfigComposerHelper') 
            ? platformConfigComposerHelper() 
            : [];
    }
    
    public function exportWebkernelComposerJson(string $outputPath = null): bool
    {
        return function_exists('platformConfigComposerExportHelper') 
            ? platformConfigComposerExportHelper($outputPath) 
            : false;
    }
    
    public function getWebkernelBrandingConfig(): array
    {
        return function_exists('platformConfigLoaderHelper') 
            ? platformConfigLoaderHelper('webkernel') 
            : [];
    }
    
    public function getWebkernelModuleConfig(string $moduleName): array
    {
        return function_exists('platformConfigLoaderHelper') 
            ? platformConfigLoaderHelper($moduleName) 
            : [];
    }
    
    public function generateWebkernelCacheKey(string $type, string $identifier = ''): string
    {
        $prefix = CACHE_KEY_PREFIX;
        $typeKey = constant("CACHE_KEY_" . strtoupper($type)) ?? $type . '_';
        
        return $prefix . $typeKey . ($identifier ? $identifier . '_' : '') . md5(app()->environment());
    }
    
    public function getWebkernelCacheTtl(string $type): int
    {
        $constant = "CACHE_TTL_" . strtoupper($type);
        return defined($constant) ? constant($constant) : WEBKERNEL_CACHE_TTL_CONFIG;
    }
    
    public function checkWebkernelProductionEnvironment(): bool
    {
        return app()->environment() === WEBKERNEL_PRODUCTION_ENVIRONMENT;
    }
    
    public function getWebkernelSupportedModuleTypes(): array
    {
        return array_values(WEBKERNEL_MODULE_TYPES);
    }
    
    public function validateWebkernelModuleType(string $type): bool
    {
        return in_array($type, $this->getWebkernelSupportedModuleTypes(), true);
    }
    
    public function buildWebkernelVersionFilePath(string $moduleName, string $version, bool $isSubmodule = false): string
    {
        $versionFile = str_replace(['.', '-'], '_', $version);
        $basePath = $isSubmodule ? WEBKERNEL_MODULES_BASE_PATH : WEBKERNEL_BASE_PATH;
        
        return base_path("{$basePath}/{$moduleName}/" . WEBKERNEL_VERSION_FILE_PREFIX . $versionFile . WEBKERNEL_VERSION_FILE_EXTENSION);
    }
    
    public function buildWebkernelModuleFilePath(string $moduleName, bool $isSubmodule = false): string
    {
        $basePath = $isSubmodule ? WEBKERNEL_MODULES_BASE_PATH : WEBKERNEL_BASE_PATH;
        $configFile = $isSubmodule ? WEBKERNEL_SUBMODULE_CONFIG_FILE : WEBKERNEL_MODULE_CONFIG_FILE;
        
        return base_path("{$basePath}/{$moduleName}/{$configFile}");
    }
    
    public function checkWebkernelVersionSupported(string $version): bool
    {
        return version_compare($version, WEBKERNEL_MIN_VERSION, '>=') 
            && version_compare($version, WEBKERNEL_REMOTE_STABLE_VERSION, '<=');
    }
    
    public function generateWebkernelConfigHash(array $config): string
    {
        return hash(WEBKERNEL_SECURITY_HASH_ALGORITHM, serialize($config));
    }
    
    public function validateWebkernelConfigIntegrity(array $config, string $expectedHash): bool
    {
        return hash_equals($expectedHash, $this->generateWebkernelConfigHash($config));
    }
    
    public function validateWebkernelEnvironment(string $environment): bool
    {
        return in_array($environment, WEBKERNEL_SUPPORTED_ENVIRONMENTS, true);
    }
    
    public function buildWebkernelFullCacheKey(string $type, string $identifier = '', string $environment = ''): string
    {
        $env = $environment ?: app()->environment();
        return $this->generateWebkernelCacheKey($type, $identifier) . '_' . $env;
    }
    
    public function validateWebkernelConfigFileExtension(string $filename): bool
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return in_array($extension, ALLOWED_CONFIG_FILE_EXTENSIONS, true);
    }
    
    public function validateWebkernelUploadFileExtension(string $filename): bool
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (empty(ALLOWED_UPLOAD_FILE_EXTENSIONS)) {
            return !in_array($extension, EXCLUDED_UPLOAD_FILE_EXTENSIONS, true);
        }
        
        return in_array($extension, ALLOWED_UPLOAD_FILE_EXTENSIONS, true);
    }
    
    public function checkWebkernelDevelopmentEnvironment(): bool
    {
        return in_array(app()->environment(), ['local', 'development'], true);
    }
    
    public function checkWebkernelStagingEnvironment(): bool
    {
        return app()->environment() === 'staging';
    }
    
    public function getWebkernelPriorityLevel(string $level): int
    {
        $constant = "PRIORITY_" . strtoupper($level);
        return defined($constant) ? constant($constant) : PRIORITY_NORMAL;
    }
    
    public function getWebkernelAllPriorityLevels(): array
    {
        return [
            'CRITICAL' => PRIORITY_CRITICAL,
            'HIGH' => PRIORITY_HIGH,
            'NORMAL' => PRIORITY_NORMAL,
            'LOW' => PRIORITY_LOW,
            'OPTIONAL' => PRIORITY_OPTIONAL,
        ];
    }
    
    public function getWebkernelAllCommands(): array
    {
        return [
            'updater' => WEBKERNEL_UPDATER,
            'modules_updater' => WEBKERNEL_MODULES_UPDATER,
            'installer' => WEBKERNEL_INSTALLER,
            'composer_generator' => WEBKERNEL_COMPOSER_GENERATOR,
            'cache_clear' => WEBKERNEL_CACHE_CLEAR,
            'config_cache' => WEBKERNEL_CONFIG_CACHE,
            'artisan_cmd_updater' => WEBKERNEL_ARTISAN_CMD_UPDATER,
        ];
    }
    
    public function getWebkernelCommand(string $commandName): string
    {
        $commands = $this->getWebkernelAllCommands();
        return $commands[$commandName] ?? '';
    }
    
    public function getWebkernelAllCacheTtl(): array
    {
        return [
            'packages' => CACHE_TTL_PACKAGES,
            'modules' => CACHE_TTL_MODULES,
            'versions' => CACHE_TTL_VERSIONS,
            'config' => CACHE_TTL_CONFIG,
            'branding' => CACHE_TTL_BRANDING,
            'assets' => CACHE_TTL_ASSETS,
        ];
    }
    
    public function getWebkernelAllCacheKeyPrefixes(): array
    {
        return [
            'main' => CACHE_KEY_PREFIX,
            'modules' => CACHE_KEY_MODULES,
            'packages' => CACHE_KEY_PACKAGES,
            'config' => CACHE_KEY_CONFIG,
            'branding' => CACHE_KEY_BRANDING,
            'versions' => CACHE_KEY_VERSIONS,
        ];
    }
    
    public function getWebkernelConfigurationSummary(): array
    {
        return [
            'version' => WEBKERNEL_VERSION,
            'environment' => app()->environment(),
            'is_production' => $this->checkWebkernelProductionEnvironment(),
            'api_version' => WEBKERNEL_API_VERSION,
            'supported_environments' => SUPPORTED_ENVIRONMENTS,
            'supported_module_types' => $this->getWebkernelSupportedModuleTypes(),
            'cache_ttl' => $this->getWebkernelAllCacheTtl(),
            'priority_levels' => $this->getWebkernelAllPriorityLevels(),
            'commands' => $this->getWebkernelAllCommands(),
        ];
    }
}