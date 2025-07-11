<?php
namespace Webkernel\Constants\Definitions\Webkernel\v_1_0_0;

class Api
{
    public const WEBKERNEL_API_RATE_LIMIT_DEFAULT = '100,1';
    public const WEBKERNEL_API_RATE_LIMIT_STRICT  = '30,1';
    public const WEBKERNEL_API_TIMEOUT            = 30;
    
    public const WEBKERNEL_ASSET_VERSION_CACHE_TTL = 86400;
    public const WEBKERNEL_ASSET_MINIFY_ENABLED    = true;
    public const WEBKERNEL_ASSET_COMPRESSION_ENABLED = true;
}