<?php
namespace Webkernel\Constants\Definitions\Webkernel\v_1_0_0;

class Security
{
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
}