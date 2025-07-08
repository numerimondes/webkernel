<?php

declare(strict_types=1);

namespace Webkernel\Constants;

/**
 * ComposerGenerator is the single source of truth for generating composer.json fields
 * for Webkernel and its modules/submodules. It is designed to be used in pre-composer
 * autoloading, CLI scripts, or Artisan commands to ensure composer.json is always up to date.
 */
final class ComposerGenerator
{

    public static array $psr4Removals = [
        'SomeClassToRemove\\',
    ];

    /**
     * Core Webkernel composer.json requirements, matching the current composer.json.
     */
    public static array $webkernelCore = [
        'name' => 'webkernel/webkernel',
        'type' => 'project',
        'description' => ' A ready-to-start foundation with pre-configured services and seamless modularity, enabling the rapid development of interconnected systems built on top of Laravel and FilamentPHP. By Numerimondes.',
        'keywords' => [
            'laravel', 'framework', 'filament', 'Webkernel', 'StarterKit'
        ],
        'license' => 'MPL-2.0',
        'require' => [
            'php' => '^8.2',
            'filament/filament' => '^4.0@beta',
            'laravel/framework' => '^12.0',
            'laravel/tinker' => '^2.10.1',
            'spatie/laravel-permission' => '^6.0'
        ],
        'require-dev' => [
            'barryvdh/laravel-debugbar' => '*',
            'fakerphp/faker' => '^1.23',
            'filament/upgrade' => '^4.0',
            'laravel/pail' => '^1.2.2',
            'laravel/pint' => '^1.13',
            'laravel/sail' => '^1.41',
            'mockery/mockery' => '^1.6',
            'nunomaduro/collision' => '^8.6',
            'phpunit/phpunit' => '^11.5.3'
        ],
        'autoload' => [
            'psr-4' => [
                'Database\\Factories\\' => 'database/factories/',
                'Database\\Seeders\\' => 'database/seeders/',
                'Webkernel\\' => 'packages/webkernel/src/',
                'Numerimondes\\' => 'platform/',
                'App\\' => 'app/'
            ],
            'files' => [
                'packages/webkernel/src/Constants/Static/AutoloadStubs.php',
                'packages/webkernel/src/Constants/Static/GlobalConstants.php',
                'packages/webkernel/src/Core/Helpers/helpers.php'
            ]
        ],
        'autoload-dev' => [
            'psr-4' => [
                'Tests\\' => 'tests/'
            ]
        ],
        'scripts' => [
            'pre-autoload-dump' => [
                '@php packages/webkernel/src/Constants/ConstantsGenerator.php',
                '@php packages/webkernel/src/Constants/PlatformConstantsGenerator.php'
            ],
            'post-autoload-dump' => [
                'Illuminate\\Foundation\\ComposerScripts::postAutoloadDump',
                '@php artisan package:discover --ansi',
                '@php artisan filament:upgrade'
            ],
            'post-update-cmd' => [
                '@php artisan vendor:publish --tag=laravel-assets --ansi --force'
            ],
            'post-root-package-install' => [
                '@php -r "file_exists(\'.env\') || copy(\'.env.example\', \' .env\');"'
            ],
            'post-create-project-cmd' => [
                '@php artisan key:generate --ansi',
                '@php -r "file_exists(\'database/database.sqlite\') || touch(\'database/database.sqlite\');"',
                '@php artisan migrate --graceful --ansi',
                '@php artisan webkernel:first-install'
            ],
            'dev' => [
                'Composer\\Config::disableProcessTimeout',
                'npx concurrently -c "#93c5fd,#c4b5fd,#fb7185,#fdba74" "php artisan serve" "php artisan queue:listen --tries=1" "php artisan pail --timeout=0" "npm run dev" --names=server,queue,logs,vite'
            ],
            'test' => [
                '@php artisan config:clear --ansi',
                '@php artisan test'
            ]
        ],
        'extra' => [
            'laravel' => [
                'dont-discover' => []
            ]
        ],
        'config' => [
            'optimize-autoloader' => true,
            'preferred-install' => 'dist',
            'sort-packages' => true,
            'allow-plugins' => [
                'pestphp/pest-plugin' => true,
                'php-http/discovery' => true
            ]
        ],
        'minimum-stability' => 'beta',
        'prefer-stable' => true,
        'repositories' => [
            [ 'type' => 'path', 'url' => 'packages/webkernel' ],
            [ 'type' => 'path', 'url' => './packages/webkernel' ]
        ]
    ];

    /**
     * Module and submodule definitions. Add new modules here as needed.
     */
    public static array $modules = [
        'ReamMar' => [
            'name' => 'numerimondes/reammar',
            'type' => 'project',
            'description' => 'Renewable Energy Audit Management - All-in-one tool for audit firms.',
            'keywords' => [
                "renewable-energy", "audit", "management", 
                "laravel", "filament", "energy-audit", "software", "audit-management"
            ],
       'submodules' => [
            ],
        ]
    ];

   

    /**
     * Helper to determine the current platform mode.
     * Returns one of:
     *   - 'only_webkernel'
     *   - 'Module_With_No_SubModule'
     *   - 'Module_With_Core'
     *   - 'Module_With_SubModules'
     *   - 'Unknown'
     *
     * @return string
     */
    public static function IsPlatformMode(): string
    {
        $base = function_exists('base_path') ? base_path() : getcwd();
        $modulesDir = $base . '/platform/Modules';
        if (!is_dir($modulesDir)) {
            return 'only_webkernel';
        }
        $modules = array_filter(scandir($modulesDir), function($d) use ($modulesDir) {
            return $d !== '.' && $d !== '..' && is_dir($modulesDir . '/' . $d);
        });
        if (empty($modules)) {
            return 'only_webkernel';
        }
        foreach ($modules as $module) {
            $submodulesDir = $modulesDir . '/' . $module;
            $submodules = array_filter(scandir($submodulesDir), function($d) use ($submodulesDir) {
                return $d !== '.' && $d !== '..' && is_dir($submodulesDir . '/' . $d);
            });
            if (!empty($submodules)) {
                return 'Module_With_SubModules';
            }
        }
        // If we have modules but no submodules
        return 'Module_With_No_SubModule';
    }

    /**
     * Merge core, valid modules, and valid submodules into a composer.json structure.
     * Only includes modules/submodules whose directories exist.
     *
     * @return array The merged composer.json structure.
     */
    public static function generate(): array
    {
        $composerPath = function_exists('base_path') ? base_path('composer.json') : getcwd() . '/composer.json';
        $existing = file_exists($composerPath) ? json_decode(file_get_contents($composerPath), true) : [];
        $merged = self::$webkernelCore;

        // If a module (like ReamMar) is present and has name/description/keywords, override core
        foreach (self::$modules as $module) {
            $validModule = false;
            foreach (($module['psr-4'] ?? []) as $ns => $path) {
                if (self::pathExists($path)) {
                    $validModule = true;
                }
            }
            if ($validModule) {
                foreach (['name', 'description', 'keywords', 'type'] as $field) {
                    if (!empty($module[$field])) {
                        $merged[$field] = $module[$field];
                    }
                }
            }
        }

        // Merge in any extra fields from existing composer.json not managed by the generator
        foreach ($existing as $key => $value) {
            if (!array_key_exists($key, $merged)) {
                $merged[$key] = $value;
            }
        }

        // Handle modules and submodules (remove if not present)
        foreach (self::$modules as $name => $module) {
            $validModule = false;
            foreach (($module['psr-4'] ?? []) as $ns => $path) {
                if (self::pathExists($path)) {
                    $validModule = true;
                    $merged['autoload']['psr-4'][$ns] = $path;
                } else {
                    unset($merged['autoload']['psr-4'][$ns]);
                }
            }
            if (!$validModule) {
                // Remove providers if module is not valid
                if (!empty($module['providers'])) {
                    foreach ($module['providers'] as $provider) {
                        if (($idx = array_search($provider, $merged['extra']['laravel']['providers'] ?? [])) !== false) {
                            unset($merged['extra']['laravel']['providers'][$idx]);
                        }
                    }
                }
                continue;
            }
            if (!empty($module['providers'])) {
                foreach ($module['providers'] as $provider) {
                    if (!in_array($provider, $merged['extra']['laravel']['providers'] ?? [], true)) {
                        $merged['extra']['laravel']['providers'][] = $provider;
                    }
                }
            }
            if (!empty($module['submodules'])) {
                foreach ($module['submodules'] as $subName => $sub) {
                    foreach (($sub['psr-4'] ?? []) as $subNs => $subPath) {
                        if (self::pathExists($subPath)) {
                            $merged['autoload']['psr-4'][$subNs] = $subPath;
                        } else {
                            unset($merged['autoload']['psr-4'][$subNs]);
                        }
                    }
                }
            }
        }

        // Suppression des namespaces PSR-4 listés dans $psr4Removals
        foreach (self::$psr4Removals as $nsToRemove) {
            if (isset($merged['autoload']['psr-4'][$nsToRemove])) {
                unset($merged['autoload']['psr-4'][$nsToRemove]);
            }
        }

        // Deduplicate providers
        if (isset($merged['extra']['laravel']['providers'])) {
            $merged['extra']['laravel']['providers'] = array_values(array_unique($merged['extra']['laravel']['providers']));
        }
        // Ensure no empty arrays for PSR-4
        foreach (['autoload', 'autoload-dev'] as $autoloadKey) {
            if (isset($merged[$autoloadKey]['psr-4']) && empty($merged[$autoloadKey]['psr-4'])) {
                unset($merged[$autoloadKey]['psr-4']);
            }
        }

        // Correction : forcer require et require-dev à être des objets vides si vides
        if (empty($merged['require'])) $merged['require'] = (object)[];
        if (empty($merged['require-dev'])) $merged['require-dev'] = (object)[];

        // Écriture du composer.json
        file_put_contents($composerPath, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $merged;
    }

    /**
     * Register an additional file for autoload.files (if it exists).
     */
    public static function addFile(string $path): void
    {
        if (self::pathExists($path) && !in_array($path, self::$webkernelCore['files'], true)) {
            self::$webkernelCore['files'][] = $path;
        }
    }

    /**
     * Register an additional Composer script.
     */
    public static function addScript(string $event, string $command): void
    {
        self::$webkernelCore['scripts'][$event][] = $command;
    }

    /**
     * Register or override a Composer config value.
     */
    public static function addConfig(string $key, mixed $value): void
    {
        self::$webkernelCore['config'][$key] = $value;
    }

    /**
     * Register an additional Laravel provider.
     */
    public static function addProvider(string $providerClass): void
    {
        if (!in_array($providerClass, self::$webkernelCore['extra']['laravel']['providers'] ?? [], true)) {
            self::$webkernelCore['extra']['laravel']['providers'][] = $providerClass;
        }
    }

    /**
     * Utility: Check if a path exists, using base_path() if available, else getcwd().
     */
    private static function pathExists(string $relativePath): bool
    {
        $base = function_exists('base_path') ? base_path() : getcwd();
        return is_dir($base . DIRECTORY_SEPARATOR . rtrim($relativePath, '/'));
    }
}

// CLI entry point: silent, writes composer.json, only outputs on error
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    try {
        $generated = \Webkernel\Constants\ComposerGenerator::generate();
        file_put_contents('composer.json', json_encode($generated, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
        exit(0);
    } catch (\Throwable $e) {
        file_put_contents('php://stderr', 'ComposerGenerator error: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}