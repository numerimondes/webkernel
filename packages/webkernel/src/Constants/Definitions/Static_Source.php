<?php
namespace Webkernel\Constants\Definitions;

class Static_Source
{
    public const WEBKERNEL_VERSION                  = '0.0.24';
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

    public const WEBKERNEL_API_RATE_LIMIT_DEFAULT = '100,1';
    public const WEBKERNEL_API_RATE_LIMIT_STRICT  = '30,1';
    public const WEBKERNEL_API_TIMEOUT            = 30;
    
    public const WEBKERNEL_ASSET_VERSION_CACHE_TTL = 86400;
    public const WEBKERNEL_ASSET_MINIFY_ENABLED    = true;
    public const WEBKERNEL_ASSET_COMPRESSION_ENABLED = true;

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

    public const API_VERSION = '1.0.0';
    public const FEATURE_FLAGS = ['feature_a', 'feature_b'];
    public const DEBUG_MODE = false;

    public const WEBKERNEL_ALLOWED_CONFIG_FILE_EXTENSIONS   = ['php', 'json', 'yaml', 'yml', 'env'];
    public const WEBKERNEL_ALLOWED_UPLOAD_FILE_EXTENSIONS   = [
        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico', 'tiff', 'tif',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'rtf', 'txt', 'csv',
        'mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma',
        'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', 'm4v', '3gp',
        'zip', 'rar', '7z', 'tar', 'gz',
        'ttf', 'otf', 'woff', 'woff2', 'eot',
        'json', 'xml', 'yml', 'yaml',
        'psd', 'ai', 'sketch', 'fig', 'xd'
    ];
    public const WEBKERNEL_EXCLUDED_UPLOAD_FILE_EXTENSIONS  = [
        'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'msi', 'dll', 'app', 'deb', 'rpm', 'run',
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
        'asp', 'aspx', 'jsp', 'jspx', 'pl', 'py', 'rb', 'sh', 'bash', 'zsh', 'fish',
        'ps1', 'vbs', 'vb', 'js', 'ts', 'jsx', 'tsx', 'vue', 'svelte',
        'htaccess', 'htpasswd', 'conf', 'config', 'ini', 'cfg', 'toml',
        'nginx', 'apache', 'httpd', 'lighttpd',
        'env', 'environment', 'artisan',
        'sql', 'db', 'sqlite', 'sqlite3', 'mdb', 'accdb', 'dbf',
        'sys', 'tmp', 'temp', 'log', 'bak', 'old', 'orig', 'swp', 'swo',
        'swf', 'fla', 'as', 'actionscript',
        'docm', 'xlsm', 'pptm', 'dotm', 'xltm', 'potm',
        'c', 'cpp', 'cc', 'cxx', 'h', 'hpp', 'cs', 'java', 'class', 'jar',
        'go', 'rs', 'swift', 'kt', 'scala', 'clj', 'lisp', 'hs',
        'apk', 'ipa', 'xap', 'appx',
        'dmg', 'iso', 'img', 'bin', 'hex', 'raw',
        'torrent', 'lnk', 'url', 'desktop', 'reg', 'crt', 'key', 'pem', 'p12', 'pfx',
        'blade', 'stub', 'lock'
    ];
    
    public const WEBKERNEL_SECURITY_HASH_ALGORITHM          = 'sha256';
    public const WEBKERNEL_SECURITY_ENCRYPTION_METHOD       = 'AES-256-CBC';
    public const WEBKERNEL_SECURITY_TOKEN_LENGTH            = 64;
    public const WEBKERNEL_SECURITY_PASSWORD_MIN_LENGTH     = 12;
    public const WEBKERNEL_SECURITY_PASSWORD_MAX_LENGTH     = 128;
    public const WEBKERNEL_SECURITY_SESSION_LIFETIME        = 7200;
    public const WEBKERNEL_SECURITY_CSRF_TOKEN_LIFETIME     = 3600;
    public const WEBKERNEL_SECURITY_API_TOKEN_LIFETIME      = 86400;
    public const WEBKERNEL_SECURITY_REFRESH_TOKEN_LIFETIME  = 604800;
    public const WEBKERNEL_SECURITY_RESET_TOKEN_LIFETIME    = 900;
    public const WEBKERNEL_SECURITY_VERIFICATION_TOKEN_LIFETIME = 3600;
    public const WEBKERNEL_SECURITY_MAX_LOGIN_ATTEMPTS      = 5;
    public const WEBKERNEL_SECURITY_LOGIN_THROTTLE_MINUTES  = 15;
    public const WEBKERNEL_SECURITY_PASSWORD_HISTORY_COUNT  = 12;
    public const WEBKERNEL_SECURITY_FORCE_HTTPS             = true;
    public const WEBKERNEL_SECURITY_SECURE_COOKIES          = true;
    public const WEBKERNEL_SECURITY_SAME_SITE_COOKIES       = 'strict';
    public const WEBKERNEL_SECURITY_CONTENT_SECURITY_POLICY = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'none';";
    public const WEBKERNEL_SECURITY_X_FRAME_OPTIONS         = 'DENY';
    public const WEBKERNEL_SECURITY_X_CONTENT_TYPE_OPTIONS  = 'nosniff';
    public const WEBKERNEL_SECURITY_X_XSS_PROTECTION        = '1; mode=block';
    public const WEBKERNEL_SECURITY_REFERRER_POLICY         = 'strict-origin-when-cross-origin';
    public const WEBKERNEL_SECURITY_PERMISSION_POLICY       = 'camera=(), microphone=(), geolocation=(), interest-cohort=()';
    public const WEBKERNEL_SECURITY_HSTS_MAX_AGE            = 31536000;
    public const WEBKERNEL_SECURITY_HSTS_INCLUDE_SUBDOMAINS = true;
    public const WEBKERNEL_SECURITY_HSTS_PRELOAD            = true;
    public const WEBKERNEL_SECURITY_FILE_MAX_SIZE_MB        = 50;
    public const WEBKERNEL_SECURITY_UPLOAD_MAX_FILES        = 10;
    public const WEBKERNEL_SECURITY_SCAN_UPLOADED_FILES     = true;
    public const WEBKERNEL_SECURITY_QUARANTINE_SUSPICIOUS_FILES = true;
    public const WEBKERNEL_SECURITY_LOG_SECURITY_EVENTS     = true;
    public const WEBKERNEL_SECURITY_ENABLE_AUDIT_LOG        = true;
    public const WEBKERNEL_SECURITY_SANITIZE_FILENAMES      = true;
    public const WEBKERNEL_SECURITY_VALIDATE_FILE_CONTENT   = true;
    public const WEBKERNEL_SECURITY_ALLOWED_MIME_TYPES      = [
        'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 
        'image/svg+xml', 'image/x-icon', 'image/tiff',
        'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.presentation', 'application/rtf', 'text/plain', 'text/csv',
        'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/flac', 'audio/aac', 'audio/x-m4a', 'audio/x-ms-wma',
        'video/mp4', 'video/avi', 'video/quicktime', 'video/x-ms-wmv', 'video/x-flv', 'video/webm',
        'video/x-matroska', 'video/x-m4v', 'video/3gpp',
        'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed',
        'application/x-tar', 'application/gzip',
        'font/ttf', 'font/otf', 'font/woff', 'font/woff2', 'application/vnd.ms-fontobject',
        'application/json', 'application/xml', 'text/xml', 'application/x-yaml', 'text/yaml'
    ];
    public const WEBKERNEL_SECURITY_BLOCKED_MIME_TYPES      = [
        'application/x-php', 'application/x-httpd-php', 'application/php', 'text/x-php',
        'application/x-executable', 'application/x-msdownload', 'application/x-msdos-program',
        'application/x-sh', 'application/x-shellscript', 'text/x-shellscript',
        'application/javascript', 'text/javascript', 'application/x-javascript',
        'application/x-asp', 'application/x-aspx', 'application/x-jsp'
    ];

    public const WEBKERNEL_DEFAULT_LOCAL_PATH                  = 'packages/webkernel';
    public const WEBKERNEL_DEFAULT_GITHUB_REMOTE_REPO          = 'https://github.com/numerimondes/webkernel';
    public const WEBKERNEL_DEFAULT_ISSUES_LINK          = 'https://github.com/numerimondes/webkernel/issues';
    public const NUMERIMONDES_DOT_COM          = 'https://numerimondes.com';
    public const WEBKERNEL_GITHUB_MAIN_BRANCH_NAME             = 'main';
    public const WEBKERNEL_GITHUB_URL_ARGUMENTS                = '';
    public const WEBKERNEL_DEFAULT_NUMERIMONDES_REMOTE_REPO    = '';
    public const WEBKERNEL_DEFAULT_NUMERIMONDES_URL_ARGUMENTS  = '';
    public const WEBKERNEL_IS_NUMERIMONDES_REPO_USABLE         = false;
    public const WEBKERNEL_REMOTE_PACKAGES_PATH = 'packages/webkernel/src/PlatformConfig/WebkernelConfigCoreFile.php';
    public const WEBKERNEL_UPDATER                = 'webkernel:update';
    public const WEBKERNEL_MODULES_UPDATER        = 'webkernel:platform-update';
    public const WEBKERNEL_INSTALLER              = 'webkernel:install';
    public const WEBKERNEL_COMPOSER_GENERATOR     = 'webkernel:composer-generate';
    public const WEBKERNEL_CACHE_CLEAR            = 'webkernel:cache-clear';
    public const WEBKERNEL_CONFIG_CACHE           = 'webkernel:config-cache';
    public const WEBKERNEL_ARTISAN_CMD_UPDATER    = 'php artisan webkernel:update';
    
    public static function getCloneCommand(string $remoteRepo): string
    {
        return "git clone --filter=blob:none --sparse " . escapeshellarg($remoteRepo);
    }
    
    public static function getRemoteFileUrl(string $remoteRepo): string
    {
        return rtrim($remoteRepo, '/') . '/raw/main/' . self::WEBKERNEL_REMOTE_PACKAGES_PATH;
    }
}