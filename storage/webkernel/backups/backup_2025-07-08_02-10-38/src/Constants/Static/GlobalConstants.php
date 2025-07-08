<?php
/**
 * Auto-generated constants - DO NOT EDIT
 * Generated: 2025-07-08 02:04:21
 * Generation time: 0.79ms
 * Total constants: 143
 * Total classes: 10
 */

// Webkernel\Constants\Definitions\ReamMar\Core\v_1_0_0\Core
// Source: Definitions/ReamMar/Core/v_1_0_0/Core.php
if (!defined('PLATFORM_NAME')) define('PLATFORM_NAME', 'ReamMar');
if (!defined('PLATFORM_LOGO')) define('PLATFORM_LOGO', 'logo.png');
if (!defined('PLATFORM_URL')) define('PLATFORM_URL', 'https://reammar.com');

// Webkernel\Constants\Definitions\Webkernel\Core
// Source: Definitions/Webkernel/Core.php
if (!defined('WEBKERNEL_VERSION')) define('WEBKERNEL_VERSION', '0.0.26');
if (!defined('DEFAULT_TIMEOUT')) define('DEFAULT_TIMEOUT', 30);
if (!defined('MAX_RETRIES')) define('MAX_RETRIES', 3);
if (!defined('CACHE_TTL')) define('CACHE_TTL', 3600);
if (!defined('WEBKERNEL_ROUTE_PATH')) define('WEBKERNEL_ROUTE_PATH', 'packages/webkernel/src/Core/routes');
if (!defined('WEBKERNEL_MODULES_NAMESPACE')) define('WEBKERNEL_MODULES_NAMESPACE', 'Numerimondes\\');
if (!defined('WEBKERNEL_REMOTE_STABLE_VERSION')) define('WEBKERNEL_REMOTE_STABLE_VERSION', '0.0.26');
if (!defined('WEBKERNEL_BRANDING_VERSION_OR_LESS')) define('WEBKERNEL_BRANDING_VERSION_OR_LESS', '1.0.0');
if (!defined('WEBKERNEL_NAME')) define('WEBKERNEL_NAME', 'Webkernel');
if (!defined('WEBKERNEL_LICENCE')) define('WEBKERNEL_LICENCE', 'MPL-2.0');
if (!defined('WEBKERNEL_PLATFORM_NAME')) define('WEBKERNEL_PLATFORM_NAME', '');
if (!defined('WEBKERNEL_API_VERSION')) define('WEBKERNEL_API_VERSION', 'v1');
if (!defined('WEBKERNEL_MODULES_BASE_PATH')) define('WEBKERNEL_MODULES_BASE_PATH', 'platform/');
if (!defined('WEBKERNEL_BASE_PATH')) define('WEBKERNEL_BASE_PATH', 'packages/webkernel/src');
if (!defined('WEBKERNEL_CORE_PATH')) define('WEBKERNEL_CORE_PATH', 'packages/webkernel/src/Core');
if (!defined('WEBKERNEL_NAMESPACE')) define('WEBKERNEL_NAMESPACE', 'Webkernel\\');
if (!defined('WEBKERNEL_MAIN_HELPER_FILE')) define('WEBKERNEL_MAIN_HELPER_FILE', 'packages/webkernel/src/Core/Helpers/helpers.php');
if (!defined('WEBKERNEL_PROVIDERS_BASE_PATH')) define('WEBKERNEL_PROVIDERS_BASE_PATH', 'packages/webkernel/src/PlatformConfig/Providers');
if (!defined('WEBKERNEL_PRIVATE_ASSETS_PATH')) define('WEBKERNEL_PRIVATE_ASSETS_PATH', 'packages/webkernel/src/Core/Resources/repo-assets');
if (!defined('WEBKERNEL_PRIVATE_RESOURCE_PATH')) define('WEBKERNEL_PRIVATE_RESOURCE_PATH', 'packages/webkernel/src/Core/Resources');
if (!defined('WEBKERNEL_PUBLIC_ASSETS_PATH')) define('WEBKERNEL_PUBLIC_ASSETS_PATH', 'public/webkernel');
if (!defined('WEBKERNEL_THEMES_BASE_PATH')) define('WEBKERNEL_THEMES_BASE_PATH', 'packages/webkernel/src/Core/Resources/Themes');
if (!defined('WEBKERNEL_VIEWS_BASE_PATH')) define('WEBKERNEL_VIEWS_BASE_PATH', 'packages/webkernel/src/Core/Resources/Views');
if (!defined('WEBKERNEL_LANG_BASE_PATH')) define('WEBKERNEL_LANG_BASE_PATH', 'packages/webkernel/src/Core/lang');
if (!defined('WEBKERNEL_SUPPORTED_ENVIRONMENTS')) define('WEBKERNEL_SUPPORTED_ENVIRONMENTS', '["local","development","staging","production"]');
if (!defined('WEBKERNEL_DEFAULT_ENVIRONMENT')) define('WEBKERNEL_DEFAULT_ENVIRONMENT', 'local');
if (!defined('WEBKERNEL_PRODUCTION_ENVIRONMENT')) define('WEBKERNEL_PRODUCTION_ENVIRONMENT', 'production');
if (!defined('WEBKERNEL_MODULE_TYPES')) define('WEBKERNEL_MODULE_TYPES', '{"CORE":"core","PLATFORM":"platform","PLUGIN":"plugin","THEME":"theme","INTEGRATION":"integration","UTILITY":"utility","SUBMODULE":"submodule"}');
if (!defined('WEBKERNEL_PRIORITY_CRITICAL')) define('WEBKERNEL_PRIORITY_CRITICAL', 1);
if (!defined('WEBKERNEL_PRIORITY_HIGH')) define('WEBKERNEL_PRIORITY_HIGH', 5);
if (!defined('WEBKERNEL_PRIORITY_NORMAL')) define('WEBKERNEL_PRIORITY_NORMAL', 10);
if (!defined('WEBKERNEL_PRIORITY_LOW')) define('WEBKERNEL_PRIORITY_LOW', 15);
if (!defined('WEBKERNEL_PRIORITY_OPTIONAL')) define('WEBKERNEL_PRIORITY_OPTIONAL', 20);
if (!defined('WEBKERNEL_COMPOSER_GENERATED_OUTPUT')) define('WEBKERNEL_COMPOSER_GENERATED_OUTPUT', 'generated.composer.json');
if (!defined('WEBKERNEL_MODULE_CONFIG_FILE')) define('WEBKERNEL_MODULE_CONFIG_FILE', 'Module.php');
if (!defined('WEBKERNEL_SUBMODULE_CONFIG_FILE')) define('WEBKERNEL_SUBMODULE_CONFIG_FILE', 'SubModule.php');
if (!defined('WEBKERNEL_VERSION_FILE_PREFIX')) define('WEBKERNEL_VERSION_FILE_PREFIX', 'v_');
if (!defined('WEBKERNEL_VERSION_FILE_EXTENSION')) define('WEBKERNEL_VERSION_FILE_EXTENSION', '.php');
if (!defined('WEBKERNEL_PACKAGE_CONFIG_FILE')) define('WEBKERNEL_PACKAGE_CONFIG_FILE', 'composer.json');
if (!defined('WEBKERNEL__LANGUAGE__MODEL__CLASS_ESCAPED')) define('WEBKERNEL__LANGUAGE__MODEL__CLASS_ESCAPED', 'Webkernel\\Core\\Models\\Language');
if (!defined('WEBKERNEL__LANGUAGE__MODEL__CLASS_ALIAS_SIMPLE')) define('WEBKERNEL__LANGUAGE__MODEL__CLASS_ALIAS_SIMPLE', 'Webkernel\\Core\\Models\\Language');
if (!defined('WEBKERNEL__LANGUAGE__TRANSLATION__MODEL_CLASS_ALIAS_SIMPLE')) define('WEBKERNEL__LANGUAGE__TRANSLATION__MODEL_CLASS_ALIAS_SIMPLE', 'Webkernel\\Core\\Models\\LanguageTranslation');
if (!defined('WEBKERNEL__LANGUAGE__TRANSLATION__MODEL_CLASS_ESCAPED')) define('WEBKERNEL__LANGUAGE__TRANSLATION__MODEL_CLASS_ESCAPED', 'Webkernel\\Core\\Models\\LanguageTranslation');
if (!defined('WEBKERNEL__LANGUAGE__MIDDLEWARE__CLASS_ALIAS_SIMPLE')) define('WEBKERNEL__LANGUAGE__MIDDLEWARE__CLASS_ALIAS_SIMPLE', 'Webkernel\\Core\\Http\\Middleware\\SetLang');
if (!defined('WEBKERNEL__LANGUAGE__MIDDLEWARE__CLASS_ESCAPED')) define('WEBKERNEL__LANGUAGE__MIDDLEWARE__CLASS_ESCAPED', 'Webkernel\\Core\\Http\\Middleware\\SetLang');
if (!defined('WEBKERNEL__CORE__CLUSTERS__PATH')) define('WEBKERNEL__CORE__CLUSTERS__PATH', 'packages/webkernel/src/Filament/Clusters');
if (!defined('WEBKERNEL__CORE__CLUSTERS__CLASS_ALIAS_SIMPLE')) define('WEBKERNEL__CORE__CLUSTERS__CLASS_ALIAS_SIMPLE', 'Webkernel\\Filament\\Clusters');
if (!defined('WEBKERNEL__CORE__RESOURCES__PATH')) define('WEBKERNEL__CORE__RESOURCES__PATH', 'packages/webkernel/src/Filament/Resources');
if (!defined('WEBKERNEL__CORE__RESOURCES__CLASS_ALIAS_SIMPLE')) define('WEBKERNEL__CORE__RESOURCES__CLASS_ALIAS_SIMPLE', 'Webkernel\\Filament\\Resources');
if (!defined('WEBKERNEL__CORE__PAGES__PATH')) define('WEBKERNEL__CORE__PAGES__PATH', 'packages/webkernel/src/Filament/Pages');
if (!defined('WEBKERNEL__CORE__PAGES__CLASS_ALIAS_SIMPLE')) define('WEBKERNEL__CORE__PAGES__CLASS_ALIAS_SIMPLE', 'Webkernel\\Filament\\Pages');
if (!defined('WEBKERNEL__CORE__WIDGETS__PATH')) define('WEBKERNEL__CORE__WIDGETS__PATH', 'packages/webkernel/src/Filament/Widgets');
if (!defined('WEBKERNEL__CORE__WIDGETS__CLASS_ALIAS_SIMPLE')) define('WEBKERNEL__CORE__WIDGETS__CLASS_ALIAS_SIMPLE', 'Webkernel\\Filament\\Widgets');

// Webkernel\Constants\Definitions\Webkernel\v_1_0_0\Core
// Source: Definitions/Webkernel/v_1_0_0/Core.php
if (!defined('API_VERSION')) define('API_VERSION', '1.0.0');
if (!defined('FEATURE_FLAGS')) define('FEATURE_FLAGS', '["feature_a","feature_b"]');
if (!defined('DEBUG_MODE')) define('DEBUG_MODE', false);

// Webkernel\Constants\Definitions\Webkernel\v_1_0_0\Api
// Source: Definitions/Webkernel/v_1_0_0/Api.php
if (!defined('WEBKERNEL_API_RATE_LIMIT_DEFAULT')) define('WEBKERNEL_API_RATE_LIMIT_DEFAULT', '100,1');
if (!defined('WEBKERNEL_API_RATE_LIMIT_STRICT')) define('WEBKERNEL_API_RATE_LIMIT_STRICT', '30,1');
if (!defined('WEBKERNEL_API_TIMEOUT')) define('WEBKERNEL_API_TIMEOUT', 30);
if (!defined('WEBKERNEL_ASSET_VERSION_CACHE_TTL')) define('WEBKERNEL_ASSET_VERSION_CACHE_TTL', 86400);
if (!defined('WEBKERNEL_ASSET_MINIFY_ENABLED')) define('WEBKERNEL_ASSET_MINIFY_ENABLED', true);
if (!defined('WEBKERNEL_ASSET_COMPRESSION_ENABLED')) define('WEBKERNEL_ASSET_COMPRESSION_ENABLED', true);

// Webkernel\Constants\Definitions\Webkernel\v_1_0_0\Cache
// Source: Definitions/Webkernel/v_1_0_0/Cache.php
if (!defined('WEBKERNEL_CACHE_TTL_PACKAGES')) define('WEBKERNEL_CACHE_TTL_PACKAGES', 3600);
if (!defined('WEBKERNEL_CACHE_TTL_MODULES')) define('WEBKERNEL_CACHE_TTL_MODULES', 1800);
if (!defined('WEBKERNEL_CACHE_TTL_VERSIONS')) define('WEBKERNEL_CACHE_TTL_VERSIONS', 7200);
if (!defined('WEBKERNEL_CACHE_TTL_CONFIG')) define('WEBKERNEL_CACHE_TTL_CONFIG', 86400);
if (!defined('WEBKERNEL_CACHE_TTL_BRANDING')) define('WEBKERNEL_CACHE_TTL_BRANDING', 43200);
if (!defined('WEBKERNEL_CACHE_TTL_ASSETS')) define('WEBKERNEL_CACHE_TTL_ASSETS', 604800);
if (!defined('WEBKERNEL_CACHE_KEY_PREFIX')) define('WEBKERNEL_CACHE_KEY_PREFIX', 'webkernel_');
if (!defined('WEBKERNEL_CACHE_KEY_MODULES')) define('WEBKERNEL_CACHE_KEY_MODULES', 'modules_');
if (!defined('WEBKERNEL_CACHE_KEY_PACKAGES')) define('WEBKERNEL_CACHE_KEY_PACKAGES', 'packages_');
if (!defined('WEBKERNEL_CACHE_KEY_CONFIG')) define('WEBKERNEL_CACHE_KEY_CONFIG', 'config_');
if (!defined('WEBKERNEL_CACHE_KEY_BRANDING')) define('WEBKERNEL_CACHE_KEY_BRANDING', 'branding_');
if (!defined('WEBKERNEL_CACHE_KEY_VERSIONS')) define('WEBKERNEL_CACHE_KEY_VERSIONS', 'versions_');
if (!defined('WEBKERNEL_MAX_MB_WEBKERNEL_CACHE_SIZE')) define('WEBKERNEL_MAX_MB_WEBKERNEL_CACHE_SIZE', 100);
if (!defined('WEBKERNEL_MAX_LOG_SIZE')) define('WEBKERNEL_MAX_LOG_SIZE', 50);
if (!defined('WEBKERNEL_MAX_MODULES_COUNT')) define('WEBKERNEL_MAX_MODULES_COUNT', 100);
if (!defined('WEBKERNEL_MAX_SUBMODULES_COUNT')) define('WEBKERNEL_MAX_SUBMODULES_COUNT', 50);

// Webkernel\Constants\Definitions\Webkernel\v_1_0_0\Security
// Source: Definitions/Webkernel/v_1_0_0/Security.php
if (!defined('WEBKERNEL_ALLOWED_CONFIG_FILE_EXTENSIONS')) define('WEBKERNEL_ALLOWED_CONFIG_FILE_EXTENSIONS', '["php","json","yaml","yml","env"]');
if (!defined('WEBKERNEL_ALLOWED_UPLOAD_FILE_EXTENSIONS')) define('WEBKERNEL_ALLOWED_UPLOAD_FILE_EXTENSIONS', '["jpg","jpeg","png","gif","bmp","webp","svg","ico","tiff","tif","pdf","doc","docx","xls","xlsx","ppt","pptx","odt","ods","odp","rtf","txt","csv","mp3","wav","ogg","flac","aac","m4a","wma","mp4","avi","mov","wmv","flv","webm","mkv","m4v","3gp","zip","rar","7z","tar","gz","ttf","otf","woff","woff2","eot","json","xml","yml","yaml","psd","ai","sketch","fig","xd"]');
if (!defined('WEBKERNEL_EXCLUDED_UPLOAD_FILE_EXTENSIONS')) define('WEBKERNEL_EXCLUDED_UPLOAD_FILE_EXTENSIONS', '["exe","bat","cmd","com","pif","scr","msi","dll","app","deb","rpm","run","php","php3","php4","php5","php7","php8","phtml","phar","asp","aspx","jsp","jspx","pl","py","rb","sh","bash","zsh","fish","ps1","vbs","vb","js","ts","jsx","tsx","vue","svelte","htaccess","htpasswd","conf","config","ini","cfg","toml","nginx","apache","httpd","lighttpd","env","environment","artisan","sql","db","sqlite","sqlite3","mdb","accdb","dbf","sys","tmp","temp","log","bak","old","orig","swp","swo","swf","fla","as","actionscript","docm","xlsm","pptm","dotm","xltm","potm","c","cpp","cc","cxx","h","hpp","cs","java","class","jar","go","rs","swift","kt","scala","clj","lisp","hs","apk","ipa","xap","appx","dmg","iso","img","bin","hex","raw","torrent","lnk","url","desktop","reg","crt","key","pem","p12","pfx","blade","stub","lock"]');
if (!defined('WEBKERNEL_SECURITY_HASH_ALGORITHM')) define('WEBKERNEL_SECURITY_HASH_ALGORITHM', 'sha256');
if (!defined('WEBKERNEL_SECURITY_ENCRYPTION_METHOD')) define('WEBKERNEL_SECURITY_ENCRYPTION_METHOD', 'AES-256-CBC');
if (!defined('WEBKERNEL_SECURITY_TOKEN_LENGTH')) define('WEBKERNEL_SECURITY_TOKEN_LENGTH', 64);
if (!defined('WEBKERNEL_SECURITY_PASSWORD_MIN_LENGTH')) define('WEBKERNEL_SECURITY_PASSWORD_MIN_LENGTH', 12);
if (!defined('WEBKERNEL_SECURITY_PASSWORD_MAX_LENGTH')) define('WEBKERNEL_SECURITY_PASSWORD_MAX_LENGTH', 128);
if (!defined('WEBKERNEL_SECURITY_SESSION_LIFETIME')) define('WEBKERNEL_SECURITY_SESSION_LIFETIME', 7200);
if (!defined('WEBKERNEL_SECURITY_CSRF_TOKEN_LIFETIME')) define('WEBKERNEL_SECURITY_CSRF_TOKEN_LIFETIME', 3600);
if (!defined('WEBKERNEL_SECURITY_API_TOKEN_LIFETIME')) define('WEBKERNEL_SECURITY_API_TOKEN_LIFETIME', 86400);
if (!defined('WEBKERNEL_SECURITY_REFRESH_TOKEN_LIFETIME')) define('WEBKERNEL_SECURITY_REFRESH_TOKEN_LIFETIME', 604800);
if (!defined('WEBKERNEL_SECURITY_RESET_TOKEN_LIFETIME')) define('WEBKERNEL_SECURITY_RESET_TOKEN_LIFETIME', 900);
if (!defined('WEBKERNEL_SECURITY_VERIFICATION_TOKEN_LIFETIME')) define('WEBKERNEL_SECURITY_VERIFICATION_TOKEN_LIFETIME', 3600);
if (!defined('WEBKERNEL_SECURITY_MAX_LOGIN_ATTEMPTS')) define('WEBKERNEL_SECURITY_MAX_LOGIN_ATTEMPTS', 5);
if (!defined('WEBKERNEL_SECURITY_LOGIN_THROTTLE_MINUTES')) define('WEBKERNEL_SECURITY_LOGIN_THROTTLE_MINUTES', 15);
if (!defined('WEBKERNEL_SECURITY_PASSWORD_HISTORY_COUNT')) define('WEBKERNEL_SECURITY_PASSWORD_HISTORY_COUNT', 12);
if (!defined('WEBKERNEL_SECURITY_FORCE_HTTPS')) define('WEBKERNEL_SECURITY_FORCE_HTTPS', true);
if (!defined('WEBKERNEL_SECURITY_SECURE_COOKIES')) define('WEBKERNEL_SECURITY_SECURE_COOKIES', true);
if (!defined('WEBKERNEL_SECURITY_SAME_SITE_COOKIES')) define('WEBKERNEL_SECURITY_SAME_SITE_COOKIES', 'strict');
if (!defined('WEBKERNEL_SECURITY_CONTENT_SECURITY_POLICY')) define('WEBKERNEL_SECURITY_CONTENT_SECURITY_POLICY', 'default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\'; style-src \'self\' \'unsafe-inline\'; img-src \'self\' data: https:; font-src \'self\' data:; connect-src \'self\'; frame-ancestors \'none\';');
if (!defined('WEBKERNEL_SECURITY_X_FRAME_OPTIONS')) define('WEBKERNEL_SECURITY_X_FRAME_OPTIONS', 'DENY');
if (!defined('WEBKERNEL_SECURITY_X_CONTENT_TYPE_OPTIONS')) define('WEBKERNEL_SECURITY_X_CONTENT_TYPE_OPTIONS', 'nosniff');
if (!defined('WEBKERNEL_SECURITY_X_XSS_PROTECTION')) define('WEBKERNEL_SECURITY_X_XSS_PROTECTION', '1; mode=block');
if (!defined('WEBKERNEL_SECURITY_REFERRER_POLICY')) define('WEBKERNEL_SECURITY_REFERRER_POLICY', 'strict-origin-when-cross-origin');
if (!defined('WEBKERNEL_SECURITY_PERMISSION_POLICY')) define('WEBKERNEL_SECURITY_PERMISSION_POLICY', 'camera=(), microphone=(), geolocation=(), interest-cohort=()');
if (!defined('WEBKERNEL_SECURITY_HSTS_MAX_AGE')) define('WEBKERNEL_SECURITY_HSTS_MAX_AGE', 31536000);
if (!defined('WEBKERNEL_SECURITY_HSTS_INCLUDE_SUBDOMAINS')) define('WEBKERNEL_SECURITY_HSTS_INCLUDE_SUBDOMAINS', true);
if (!defined('WEBKERNEL_SECURITY_HSTS_PRELOAD')) define('WEBKERNEL_SECURITY_HSTS_PRELOAD', true);
if (!defined('WEBKERNEL_SECURITY_FILE_MAX_SIZE_MB')) define('WEBKERNEL_SECURITY_FILE_MAX_SIZE_MB', 50);
if (!defined('WEBKERNEL_SECURITY_UPLOAD_MAX_FILES')) define('WEBKERNEL_SECURITY_UPLOAD_MAX_FILES', 10);
if (!defined('WEBKERNEL_SECURITY_SCAN_UPLOADED_FILES')) define('WEBKERNEL_SECURITY_SCAN_UPLOADED_FILES', true);
if (!defined('WEBKERNEL_SECURITY_QUARANTINE_SUSPICIOUS_FILES')) define('WEBKERNEL_SECURITY_QUARANTINE_SUSPICIOUS_FILES', true);
if (!defined('WEBKERNEL_SECURITY_LOG_SECURITY_EVENTS')) define('WEBKERNEL_SECURITY_LOG_SECURITY_EVENTS', true);
if (!defined('WEBKERNEL_SECURITY_ENABLE_AUDIT_LOG')) define('WEBKERNEL_SECURITY_ENABLE_AUDIT_LOG', true);
if (!defined('WEBKERNEL_SECURITY_SANITIZE_FILENAMES')) define('WEBKERNEL_SECURITY_SANITIZE_FILENAMES', true);
if (!defined('WEBKERNEL_SECURITY_VALIDATE_FILE_CONTENT')) define('WEBKERNEL_SECURITY_VALIDATE_FILE_CONTENT', true);
if (!defined('WEBKERNEL_SECURITY_ALLOWED_MIME_TYPES')) define('WEBKERNEL_SECURITY_ALLOWED_MIME_TYPES', '["image/jpeg","image/jpg","image/png","image/gif","image/bmp","image/webp","image/svg+xml","image/x-icon","image/tiff","application/pdf","application/msword","application/vnd.openxmlformats-officedocument.wordprocessingml.document","application/vnd.ms-excel","application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/vnd.ms-powerpoint","application/vnd.openxmlformats-officedocument.presentationml.presentation","application/vnd.oasis.opendocument.text","application/vnd.oasis.opendocument.spreadsheet","application/vnd.oasis.opendocument.presentation","application/rtf","text/plain","text/csv","audio/mpeg","audio/wav","audio/ogg","audio/flac","audio/aac","audio/x-m4a","audio/x-ms-wma","video/mp4","video/avi","video/quicktime","video/x-ms-wmv","video/x-flv","video/webm","video/x-matroska","video/x-m4v","video/3gpp","application/zip","application/x-rar-compressed","application/x-7z-compressed","application/x-tar","application/gzip","font/ttf","font/otf","font/woff","font/woff2","application/vnd.ms-fontobject","application/json","application/xml","text/xml","application/x-yaml","text/yaml"]');
if (!defined('WEBKERNEL_SECURITY_BLOCKED_MIME_TYPES')) define('WEBKERNEL_SECURITY_BLOCKED_MIME_TYPES', '["application/x-php","application/x-httpd-php","application/php","text/x-php","application/x-executable","application/x-msdownload","application/x-msdos-program","application/x-sh","application/x-shellscript","text/x-shellscript","application/javascript","text/javascript","application/x-javascript","application/x-asp","application/x-aspx","application/x-jsp"]');

// Webkernel\Constants\Definitions\Webkernel\v_1_0_0\Updates
// Source: Definitions/Webkernel/v_1_0_0/Updates.php
if (!defined('WEBKERNEL_DEFAULT_LOCAL_PATH')) define('WEBKERNEL_DEFAULT_LOCAL_PATH', 'packages/webkernel');
if (!defined('WEBKERNEL_DEFAULT_GITHUB_REMOTE_REPO')) define('WEBKERNEL_DEFAULT_GITHUB_REMOTE_REPO', 'https://github.com/numerimondes/webkernel');
if (!defined('WEBKERNEL_DEFAULT_ISSUES_LINK')) define('WEBKERNEL_DEFAULT_ISSUES_LINK', 'https://github.com/numerimondes/webkernel/issues');
if (!defined('NUMERIMONDES_DOT_COM')) define('NUMERIMONDES_DOT_COM', 'https://numerimondes.com');
if (!defined('WEBKERNEL_GITHUB_MAIN_BRANCH_NAME')) define('WEBKERNEL_GITHUB_MAIN_BRANCH_NAME', 'main');
if (!defined('WEBKERNEL_GITHUB_URL_ARGUMENTS')) define('WEBKERNEL_GITHUB_URL_ARGUMENTS', '');
if (!defined('WEBKERNEL_DEFAULT_NUMERIMONDES_REMOTE_REPO')) define('WEBKERNEL_DEFAULT_NUMERIMONDES_REMOTE_REPO', '');
if (!defined('WEBKERNEL_DEFAULT_NUMERIMONDES_URL_ARGUMENTS')) define('WEBKERNEL_DEFAULT_NUMERIMONDES_URL_ARGUMENTS', '');
if (!defined('WEBKERNEL_IS_NUMERIMONDES_REPO_USABLE')) define('WEBKERNEL_IS_NUMERIMONDES_REPO_USABLE', false);
if (!defined('WEBKERNEL_REMOTE_PACKAGES_PATH')) define('WEBKERNEL_REMOTE_PACKAGES_PATH', 'packages/webkernel/src/PlatformConfig/WebkernelConfigCoreFile.php');
if (!defined('WEBKERNEL_UPDATER')) define('WEBKERNEL_UPDATER', 'webkernel:update');
if (!defined('WEBKERNEL_MODULES_UPDATER')) define('WEBKERNEL_MODULES_UPDATER', 'webkernel:platform-update');
if (!defined('WEBKERNEL_INSTALLER')) define('WEBKERNEL_INSTALLER', 'webkernel:install');
if (!defined('WEBKERNEL_COMPOSER_GENERATOR')) define('WEBKERNEL_COMPOSER_GENERATOR', 'webkernel:composer-generate');
if (!defined('WEBKERNEL_CACHE_CLEAR')) define('WEBKERNEL_CACHE_CLEAR', 'webkernel:cache-clear');
if (!defined('WEBKERNEL_CONFIG_CACHE')) define('WEBKERNEL_CONFIG_CACHE', 'webkernel:config-cache');
if (!defined('WEBKERNEL_ARTISAN_CMD_UPDATER')) define('WEBKERNEL_ARTISAN_CMD_UPDATER', 'php artisan webkernel:update');

// Webkernel\Constants\Definitions\ReamMar\Mpr\v_1_0_0\Api
// Source: Definitions/ReamMar/Mpr/v_1_0_0/Api.php
if (!defined('API_ENDPOINT')) define('API_ENDPOINT', '/api/mpr');
if (!defined('API_KEY_REQUIRED')) define('API_KEY_REQUIRED', true);
if (!defined('RATE_LIMIT')) define('RATE_LIMIT', 100);

// Webkernel\Constants\Definitions\ReamMar\Mpr\v_1_0_0\Mpr
// Source: Definitions/ReamMar/Mpr/v_1_0_0/Mpr.php
if (!defined('MODULE_NAME')) define('MODULE_NAME', 'Mpr');
if (!defined('MODULE_VERSION')) define('MODULE_VERSION', '1.0.0');
if (!defined('ENABLED')) define('ENABLED', true);

