<?php
namespace Webkernel\Constants\Definitions\Webkernel\Generic;

class Updates
{
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