<?php
namespace Webkernel\PlatformConfig\ConfigFiles;

class WebkernelConfigCacheFile
{
    public const WEBKERNEL_CACHE_TTL_PACKAGES     = 3600;
    public const WEBKERNEL_CACHE_TTL_MODULES      = 1800;
    public const WEBKERNEL_CACHE_TTL_VERSIONS     = 7200;
    public const WEBKERNEL_CACHE_TTL_CONFIG       = 86400;
    public const WEBKERNEL_CACHE_TTL_BRANDING     = 43200;
    public const WEBKERNEL_CACHE_TTL_ASSETS       = 604800;

    public const WEBKERNEL_CACHE_KEY_PREFIX       = 'webkernel_';
    public const WEBKERNEL_CACHE_KEY_MODULES      = 'modules_';
    public const WEBKERNEL_CACHE_KEY_PACKAGES     = 'packages_';
    public const WEBKERNEL_CACHE_KEY_CONFIG       = 'config_';
    public const WEBKERNEL_CACHE_KEY_BRANDING     = 'branding_';
    public const WEBKERNEL_CACHE_KEY_VERSIONS     = 'versions_';
    
    public const WEBKERNEL_MAX_MB_WEBKERNEL_CACHE_SIZE         = 100;
    public const WEBKERNEL_MAX_LOG_SIZE           = 50;
    public const WEBKERNEL_MAX_MODULES_COUNT      = 100;
    public const WEBKERNEL_MAX_SUBMODULES_COUNT   = 50;
}