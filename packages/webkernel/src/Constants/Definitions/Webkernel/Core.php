<?php

namespace Webkernel\Constants\Definitions\Webkernel;

class Core
{
    public const WEBKERNEL_VERSION                  = '0.0.26';
    public const WEBKERNEL_REMOTE_STABLE_VERSION    = '0.0.26';
    public const WEBKERNEL_BRANDING_VERSION_OR_LESS = '1.0.0'; 
    public const WEBKERNEL_NAME                     = 'Webkernel';
    public const WEBKERNEL_LICENCE                  = 'MPL-2.0';
    public const WEBKERNEL_PLATFORM_NAME            = '';
    public const WEBKERNEL_API_VERSION              = 'v1';
    public const WEBKERNEL_MODULES_BASE_PATH        = 'platform/';
    public const WEBKERNEL_MODULES_NAMESPACE        = 'Numerimondes\\';
    public const WEBKERNEL_BASE_PATH                = 'packages/webkernel/src';
    public const WEBKERNEL_CORE_PATH                = 'packages/webkernel/src/Core';
    public const WEBKERNEL_ROUTE_PATH               = 'packages/webkernel/src/Core/routes';
    public const WEBKERNEL_NAMESPACE                = 'Webkernel\\';
    public const WEBKERNEL_MAIN_HELPER_FILE         = 'packages/webkernel/src/Core/Helpers/helpers.php';
    public const WEBKERNEL_PROVIDERS_BASE_PATH      = 'packages/webkernel/src/PlatformConfig/Providers';
    public const WEBKERNEL_PRIVATE_ASSETS_PATH      = 'packages/webkernel/src/Core/Resources/repo-assets';
    public const WEBKERNEL_PRIVATE_RESOURCE_PATH      = 'packages/webkernel/src/Core/Resources';
    public const WEBKERNEL_PUBLIC_ASSETS_PATH       = 'public/webkernel';
    public const WEBKERNEL_THEMES_BASE_PATH         = 'packages/webkernel/src/Core/Resources/Themes';
    public const WEBKERNEL_VIEWS_BASE_PATH          = 'packages/webkernel/src/Core/Resources/Views';
    public const WEBKERNEL_LANG_BASE_PATH           = 'packages/webkernel/src/Core/lang';
    public const WEBKERNEL_SUPPORTED_ENVIRONMENTS   = ['local', 'development', 'staging', 'production'];
    public const WEBKERNEL_DEFAULT_ENVIRONMENT      = 'local';
    public const WEBKERNEL_PRODUCTION_ENVIRONMENT   = 'production';
    public const WEBKERNEL_MODULE_TYPES =
    [
        'CORE'          => 'core',
        'PLATFORM'      => 'platform',
        'PLUGIN'        => 'plugin',
        'THEME'         => 'theme',
        'INTEGRATION'   => 'integration',
        'UTILITY'       => 'utility',
        'SUBMODULE'     => 'submodule',
    ];    
    public const WEBKERNEL_PRIORITY_CRITICAL  = 1;
    public const WEBKERNEL_PRIORITY_HIGH      = 5;
    public const WEBKERNEL_PRIORITY_NORMAL    = 10;
    public const WEBKERNEL_PRIORITY_LOW       = 15;
    public const WEBKERNEL_PRIORITY_OPTIONAL  = 20;    
    public const WEBKERNEL_COMPOSER_GENERATED_OUTPUT     = 'generated.composer.json';
    public const WEBKERNEL_MODULE_CONFIG_FILE            = 'Module.php';
    public const WEBKERNEL_SUBMODULE_CONFIG_FILE         = 'SubModule.php';
    public const WEBKERNEL_VERSION_FILE_PREFIX           = 'v_'; //v_11_10_97 => v11.10.97
    public const WEBKERNEL_VERSION_FILE_EXTENSION        = '.php';
    public const WEBKERNEL_PACKAGE_CONFIG_FILE           = 'composer.json';
}