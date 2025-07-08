<?php
namespace Webkernel\Constants\Definitions\Webkernel;

class Core
{
    public const WEBKERNEL_VERSION                  = '0.0.26';
    public const WEBKERNEL_VERSION_STABLE           = '0.0.21';
    public const DEFAULT_TIMEOUT = 30;
    public const MAX_RETRIES = 3;
    public const CACHE_TTL = 3600;
    public const WEBKERNEL_ROUTE_PATH = "packages/webkernel/src/Core/routes";
    public const WEBKERNEL_MODULES_NAMESPACE = "Numerimondes\\";
    public const WEBKERNEL_REMOTE_STABLE_VERSION    = '0.0.26';
    public const WEBKERNEL_BRANDING_VERSION_OR_LESS = '1.0.0'; 
    public const WEBKERNEL_NAME                     = 'Webkernel';
    public const WEBKERNEL_LICENCE                  = 'MPL-2.0';
    public const WEBKERNEL_PLATFORM_NAME            = '';
    public const WEBKERNEL_API_VERSION              = 'v1';
    public const WEBKERNEL_MODULES_BASE_PATH        = 'platform/';
    public const WEBKERNEL_BASE_PATH                = 'packages/webkernel/src';
    public const WEBKERNEL_CORE_PATH                = 'packages/webkernel/src/Core';
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

    /*
    |--------------------------------------------------------------------------
    | Webkernel Constant path and Namespaces
    |--------------------------------------------------------------------------
    |
    | Author: El Moumen Yassine
    | Email: yassine@numerimondes.com
    | Phone/WhatsApp: +212 6 20 99 06 92
    | Website: www.numerimondes.com
    |
    | License: Mozilla Public License (MPL)
    |
    | This configuration file contains all the NOO (NO OVERRIDE) 
    | for the Webkernel system.
    |
    | Webkernel is a comprehensive Laravel framework extension that provides
    | robust translation management, widget systems, and advanced features
    | for modern web applications.
    |
    */

    public const WEBKERNEL__LANGUAGE__MODEL__CLASS_ESCAPED     = 'Webkernel\\Core\\Models\\Language';
    public const WEBKERNEL__LANGUAGE__MODEL__CLASS_ALIAS_SIMPLE     = 'Webkernel\Core\Models\Language';
    public const WEBKERNEL__LANGUAGE__TRANSLATION__MODEL_CLASS_ALIAS_SIMPLE     = 'Webkernel\Core\Models\LanguageTranslation';
    public const WEBKERNEL__LANGUAGE__TRANSLATION__MODEL_CLASS_ESCAPED     = 'Webkernel\\Core\\Models\\LanguageTranslation';
    public const WEBKERNEL__LANGUAGE__MIDDLEWARE__CLASS_ALIAS_SIMPLE     = 'Webkernel\Core\Http\Middleware\SetLang';
    public const WEBKERNEL__LANGUAGE__MIDDLEWARE__CLASS_ESCAPED     = 'Webkernel\\Core\\Http\\Middleware\\SetLang';
    public const WEBKERNEL__CORE__CLUSTERS__PATH = 'packages/webkernel/src/Filament/Clusters';
    public const WEBKERNEL__CORE__CLUSTERS__CLASS_ALIAS_SIMPLE= 'Webkernel\\Filament\\Clusters';
    public const WEBKERNEL__CORE__RESOURCES__PATH = 'packages/webkernel/src/Filament/Resources';
    public const WEBKERNEL__CORE__RESOURCES__CLASS_ALIAS_SIMPLE= 'Webkernel\\Filament\\Resources';
    public const WEBKERNEL__CORE__PAGES__PATH = 'packages/webkernel/src/Filament/Pages';
    public const WEBKERNEL__CORE__PAGES__CLASS_ALIAS_SIMPLE= 'Webkernel\\Filament\\Pages';
    public const WEBKERNEL__CORE__WIDGETS__PATH = 'packages/webkernel/src/Filament/Widgets';
    public const WEBKERNEL__CORE__WIDGETS__CLASS_ALIAS_SIMPLE= 'Webkernel\\Filament\\Widgets';
}
