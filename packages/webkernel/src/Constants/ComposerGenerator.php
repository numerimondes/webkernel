<?php

declare(strict_types=1);

namespace Webkernel\Constants;

use Illuminate\Support\Facades\File;

class ComposerGenerator
{
    private string $staticPath;

    public function __construct()
    {
        $this->staticPath = __DIR__ . '/Static';
        $this->ensureDirectoryExists($this->staticPath);
    }

    public function generate(): bool
    {
        $composerData = $this->getComposerData();
        $content = json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return file_put_contents($this->staticPath . '/generated.composer.json', $content, LOCK_EX) !== false;
    }

    private function getComposerData(): array
    {
        $version = \Webkernel\Constants\Definitions\Webkernel\Core::WEBKERNEL_VERSION;
        $timestamp = now()->timestamp;
        $hash = substr(md5($version . $timestamp), 0, 8);

        return [
            'name' => 'numerimondes/webkernel',
            'version' => $version,
            'description' => 'WebKernel Core - Numerimondes Platform Foundation',
            'type' => 'library',
            'license' => 'proprietary',
            'authors' => [
                [
                    'name' => 'Numerimondes',
                    'email' => 'dev@numerimondes.com',
                    'homepage' => 'https://numerimondes.com',
                    'role' => 'Developer',
                ],
            ],
            'homepage' => 'https://numerimondes.com',
            'support' => [
                'email' => 'support@numerimondes.com',
                'docs' => 'https://docs.numerimondes.com',
            ],
            'require' => [
                'php' => '^8.2',
                'laravel/framework' => '^10.0|^11.0',
                'filament/filament' => '^3.0',
                'spatie/laravel-permission' => '^5.0|^6.0',
                'spatie/laravel-multitenancy' => '^3.0',
                'spatie/laravel-backup' => '^8.0',
                'spatie/laravel-activitylog' => '^4.0',
            ],
            'autoload' => [
                'psr-4' => [
                    'Webkernel\\' => 'src/',
                    'Webkernel\\Constants\\' => 'src/Constants/',
                    'Webkernel\\Core\\' => 'src/Core/',
                    'Webkernel\\Models\\' => 'src/Models/',
                    'Webkernel\\Services\\' => 'src/Services/',
                    'Webkernel\\Providers\\' => 'src/Providers/',
                ],
                'files' => [
                    'packages/webkernel/src/Constants/ConstantsGenerator.php',
                    'packages/webkernel/src/Constants/Static/GlobalConstants.php',
                    'packages/webkernel/src/Constants/Static/AutoloadStubs.php',
                    'packages/webkernel/src/Core/Helpers/helpers.php',
                ],
            ],
            'extra' => [
                'laravel' => [
                    'providers' => [
                        'Webkernel\\Providers\\WebkernelServiceProvider',
                    ],
                ],
                'webkernel' => [
                    'version' => $version,
                    'build' => $hash,
                    'timestamp' => $timestamp,
                    'rolling_release' => true,
                    'update_channel' => 'stable',
                ],
            ],
        ];
    }

    private function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}