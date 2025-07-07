<?php

declare(strict_types=1);

namespace Webkernel\Core\Helpers;

use Illuminate\Support\Facades\File;

if (!function_exists('webkernel_assets_version')) {
    function webkernel_assets_version(): string
    {
        $version = \Webkernel\Constants\Definitions\Webkernel\Core::WEBKERNEL_VERSION;
        $timestamp = File::exists(base_path('packages/webkernel/src/Core/Assets')) ? File::mtime(base_path('packages/webkernel/src/Core/Assets')) : time();
        return $version . '.' . substr(md5((string)$timestamp), 0, 8);
    }
}

if (!function_exists('webkernel_build_hash')) {
    function webkernel_build_hash(): string
    {
        $version = \Webkernel\Constants\Definitions\Webkernel\Core::WEBKERNEL_VERSION;
        $hash = $version;
        $files = [
            base_path('packages/webkernel/src/Constants/Definitions/Webkernel/Core.php'),
            base_path('packages/webkernel/src/Constants/Definitions/BrandingDefinition.php'),
            base_path('packages/webkernel/src/Constants/Registry/PlatformRegistry.php'),
            base_path('packages/webkernel/src/Constants/ConstantsGenerator.php'),
        ];
        foreach ($files as $file) {
            if (File::exists($file)) {
                $hash .= File::mtime($file);
            }
        }
        return substr(md5($hash), 0, 12);
    }
}

if (!function_exists('webkernel_installation_id')) {
    function webkernel_installation_id(): string
    {
        $file = base_path('.webkernel_installation_id');
        if (File::exists($file)) {
            return trim(File::get($file));
        }
        $id = 'wk_' . bin2hex(random_bytes(16));
        File::put($file, $id);
        return $id;
    }
}

if (!function_exists('webkernel_available_modules')) {
    function webkernel_available_modules(): array
    {
        $platformPath = base_path('platform');
        $modules = [];
        if (!is_dir($platformPath)) {
            return [];
        }
        $directories = array_diff(scandir($platformPath), ['.', '..']);
        foreach ($directories as $dir) {
            $modulePath = $platformPath . '/' . $dir;
            if (!is_dir($modulePath)) {
                continue;
            }
            $moduleInfo = [
                'name' => $dir,
                'path' => $modulePath,
                'has_core' => is_dir($modulePath . '/Core'),
                'has_definition' => File::exists(base_path("packages/webkernel/src/Constants/Definitions/Modules/{$dir}/ModuleDefinition.php")),
                'has_core_definition' => File::exists(base_path("packages/webkernel/src/Constants/Definitions/Modules/{$dir}/CoreDefinition.php")),
                'enabled' => true,
            ];
            if ($moduleInfo['has_definition']) {
                $definition = include base_path("packages/webkernel/src/Constants/Definitions/Modules/{$dir}/ModuleDefinition.php");
                if (is_array($definition)) {
                    $moduleInfo['version'] = $definition['MODULE_VERSION'] ?? '0.0.1';
                    $moduleInfo['description'] = $definition['MODULE_DESCRIPTION'] ?? '';
                    $moduleInfo['enabled'] = $definition['MODULE_ENABLED'] ?? true;
                }
            }
            $modules[$dir] = $moduleInfo;
        }
        return $modules;
    }
}