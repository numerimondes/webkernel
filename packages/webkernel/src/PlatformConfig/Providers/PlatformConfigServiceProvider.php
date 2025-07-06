<?php

namespace Webkernel\PlatformConfig\Providers;

use Illuminate\Support\ServiceProvider;

class PlatformConfigServiceProvider extends ServiceProvider
{

   // public function __construct(PlatformConfigServiceProvider $app)
   // {
   // "Argument '1' passed to __construct() is expected to be of type Illuminate\\Contracts\\Foundation\\Application, WebkernelPlatform\\Providers\\PlatformConfigServiceProvider given",
   //     parent::__construct($app);
   // }

    public function register(): void
    {
        $this->mergePlatformConfigs();
    }

    protected function mergePlatformConfigs(): void
    {
        $platforms = [
            'webkernel' => [
                'default_version' => '1.0.0',
                'path' => base_path('packages/webkernel/platformConfig/Platforms/Webkernel'),
            ],
            'reammar_core' => [
                'default_version' => '1.0.0',
                'path' => base_path('packages/webkernel/platformConfig/Platforms/Modules/ReamMar/Core'),
            ],
        ];

        foreach ($platforms as $key => $config) {
            $version = $config['default_version'];
            $path = "{$config['path']}/v_" . str_replace('.', '_', $version) . '.php';

            if (file_exists($path)) {
                $this->app->singleton("platform.{$key}", function () use ($path) {
                    return include $path;
                });
            }
        }
    }
}
