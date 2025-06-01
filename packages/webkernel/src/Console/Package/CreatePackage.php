<?php
namespace Webkernel\Console\Package;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreatePackage extends Command
{
    protected $signature = 'webkernel:create-package {--key=}';
    protected $description = 'Create a new Webkernel sub-package interactively';
    protected $hidden = true;

    private const REQUIRED_KEY = '1c0b0a59f4ec32b483fc095846624316';
    private const FOLDER_PREFIX = 'webkernel-';
    private const NAMESPACE_PREFIX = 'Webkernel';

    public function handle()
    {
        if (!$this->validateKey()) {
            return 1;
        }

        $this->displayHeader();

        try {
            $packageData = $this->collectPackageData();

            if (!$this->confirmCreation($packageData)) {
                $this->info('Package creation cancelled.');
                return 0;
            }

            $this->createPackage($packageData);
            $this->displaySuccess($packageData);

            return 0;
        } catch (\Exception $e) {
            $this->error("Package creation failed: {$e->getMessage()}");
            return 1;
        }
    }

    private function validateKey(): bool
    {
        $providedKey = $this->option('key');

        if (empty($providedKey)) {
            $providedKey = $this->secret('Please provide the required key');
        }

        if ($providedKey !== self::REQUIRED_KEY) {
            $this->error('Invalid key provided.');
            return false;
        }

        return true;
    }

    private function displayHeader(): void
    {
        $this->info('==========================================');
        $this->info('       Webkernel Package Creator');
        $this->info('==========================================');
        $this->line('Creating a new Webkernel sub-package...');
        $this->newLine();
    }

    private function collectPackageData(): array
    {
        return [
            'folder_name' => $this->askFolderName(),
            'namespace' => null, // Will be derived from folder name
            'version' => $this->askVersion(),
            'description' => $this->askDescription()
        ];
    }

    private function askFolderName(): string
    {
        while (true) {
            $folderName = $this->anticipate(
                'Enter the package folder name (will be prefixed with "webkernel-")',
                ['website-builder', 'cms', 'ecommerce', 'blog'],
                'package'
            );

            $folderName = $this->sanitizeFolderName($folderName);
            $fullFolderName = self::FOLDER_PREFIX . $folderName;

            if ($this->validateFolderName($folderName)) {
                $this->info("✓ Folder name: {$fullFolderName}");
                return $fullFolderName;
            }
        }
    }

    private function sanitizeFolderName(string $name): string
    {
        // Remove webkernel- prefix if user added it
        $name = preg_replace('/^webkernel-/', '', $name);

        // Convert to lowercase and replace invalid characters
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9\-]/', '-', $name);
        $name = preg_replace('/-+/', '-', $name);
        $name = trim($name, '-');

        return $name;
    }

    private function validateFolderName(string $name): bool
    {
        if (empty($name)) {
            $this->warn('Folder name cannot be empty');
            return false;
        }

        if (!preg_match('/^[a-z0-9\-]+$/', $name)) {
            $this->warn('Folder name should contain only lowercase letters, numbers, and hyphens');
            return false;
        }

        $fullPath = base_path('packages/' . self::FOLDER_PREFIX . $name);
        if (File::exists($fullPath)) {
            $this->warn("Package folder already exists: {$fullPath}");
            return false;
        }

        return true;
    }

    private function deriveNamespace(string $folderName): string
    {
        // Remove webkernel- prefix
        $name = preg_replace('/^webkernel-/', '', $folderName);

        // Convert to StudlyCase
        $namespace = Str::studly($name);

        return self::NAMESPACE_PREFIX . $namespace;
    }

    private function askVersion(): string
    {
        while (true) {
            $version = $this->ask('Enter the package version', '0.0.1');

            if ($this->validateVersion($version)) {
                $this->info("✓ Version: {$version}");
                return $version;
            }
        }
    }

    private function validateVersion(string $version): bool
    {
        if (!preg_match('/^\d+\.\d+\.\d+(-[a-zA-Z0-9\-]+)?$/', $version)) {
            $this->warn('Version format should be X.Y.Z or X.Y.Z-suffix (e.g., 1.0.0 or 1.0.0-beta)');
            return false;
        }

        return true;
    }

    private function askDescription(): string
    {
        $description = $this->ask('Enter package description (optional)', '');
        return $description ?: 'A Webkernel sub-package';
    }

    private function confirmCreation(array $packageData): bool
    {
        $packageData['namespace'] = $this->deriveNamespace($packageData['folder_name']);

        $this->newLine();
        $this->info('Package Configuration:');
        $this->line("├─ Folder: {$packageData['folder_name']}");
        $this->line("├─ Namespace: {$packageData['namespace']}");
        $this->line("├─ Version: {$packageData['version']}");
        $this->line("└─ Description: {$packageData['description']}");
        $this->newLine();

        return $this->confirm('Do you want to proceed with package creation?', true);
    }

    private function createPackage(array $packageData): void
    {
        $packageData['namespace'] = $this->deriveNamespace($packageData['folder_name']);
        $baseDir = base_path('packages');
        $packageDir = "{$baseDir}/{$packageData['folder_name']}";

        $this->ensureBaseDirectory($baseDir);
        $this->createDirectoryStructure($packageDir);
        $this->generateFiles($packageDir, $packageData);
    }

    private function ensureBaseDirectory(string $baseDir): void
    {
        if (!File::exists($baseDir)) {
            File::makeDirectory($baseDir, 0755, true);
            $this->info("Created packages directory: {$baseDir}");
        }
    }

    private function createDirectoryStructure(string $packageDir): void
    {
        $this->info('Creating directory structure...');

        $directories = [
            'bin',
            'src',
            'src/config',
            'src/Console',
            'src/Console/Commands',
            'src/Console/Install',
            'src/Console/Package',
            'src/Console/Scripts',
            'src/constants',
            'src/database',
            'src/database/init_dump',
            'src/database/migrations',
            'src/database/seeders',
            'src/Filament',
            'src/Filament/Clusters',
            'src/Filament/Pages',
            'src/Filament/Pages/Auth',
            'src/Filament/Resources',
            'src/Filament/Widgets',
            'src/Helpers',
            'src/Http',
            'src/Http/Controllers',
            'src/Http/Middleware',
            'src/lang',
            'src/Layouts',
            'src/Models',
            'src/Observers',
            'src/Policies',
            'src/Providers',
            'src/Providers/Filament',
            'src/public',
            'src/public/assets',
            'src/public/css',
            'src/public/js',
            'src/resources',
            'src/resources/components',
            'src/resources/views',
            'src/resources/views/components',
            'src/resources/views/filament',
            'src/resources/views/widgets',
            'src/routes',
            'src/Traits',
            'tests',
            'tests/Feature',
            'tests/Unit'
        ];

        foreach ($directories as $dir) {
            $fullPath = "{$packageDir}/{$dir}";
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
                File::put("{$fullPath}/.gitkeep", '');
            }
        }

        $this->info('✓ Directory structure created successfully');
    }

    private function generateFiles(string $packageDir, array $packageData): void
    {
        $generators = [
            'generateComposerJson',
            'generateApplication',
            'generateServiceProvider',
            'generateConfig',
            'generateHelpers',
            'generateTranslations',
            'generateRoutes',
            'generateController',
            'generateView',
            'generateModel',
            'generateMigration',
            'generateLicense',
            'generateReadme',
            'generateInstallScript',
            'generateGitignore'
        ];

        $this->info('Generating package files...');

        foreach ($generators as $generator) {
            try {
                $this->$generator($packageDir, $packageData);
            } catch (\Exception $e) {
                $this->warn("Warning: Failed to generate file with {$generator}: {$e->getMessage()}");
            }
        }

        $this->info('✓ Package files generated successfully');
    }

    private function generateComposerJson(string $packageDir, array $packageData): void
    {
        $composerName = strtolower(str_replace('\\', '/', $packageData['folder_name']));

        $content = [
            'name' => "webkernel/{$packageData['folder_name']}",
            'description' => $packageData['description'],
            'type' => 'library',
            'version' => $packageData['version'],
            'license' => 'MIT',
            'authors' => [
                [
                    'name' => 'Webkernel Team',
                    'email' => 'team@webkernel.org'
                ]
            ],
            'require' => [
                'php' => '^8.1',
                'laravel/framework' => '^10.0|^11.0',
                'webkernel/webkernel' => '^1.0'
            ],
            'require-dev' => [
                'phpunit/phpunit' => '^10.0',
                'orchestra/testbench' => '^8.0|^9.0'
            ],
            'autoload' => [
                'psr-4' => [
                    "{$packageData['namespace']}\\" => 'src/'
                ],
                'files' => [
                    'src/Helpers/helpers.php'
                ]
            ],
            'autoload-dev' => [
                'psr-4' => [
                    "{$packageData['namespace']}\\Tests\\" => 'tests/'
                ]
            ],
            'extra' => [
                'laravel' => [
                    'providers' => [
                        "{$packageData['namespace']}\\Providers\\{$packageData['namespace']}ServiceProvider"
                    ]
                ]
            ],
            'minimum-stability' => 'stable',
            'prefer-stable' => true,
            'scripts' => [
                'test' => 'vendor/bin/phpunit',
                'test-coverage' => 'vendor/bin/phpunit --coverage-html coverage'
            ]
        ];

        File::put("{$packageDir}/composer.json", json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function generateApplication(string $packageDir, array $packageData): void
    {
        $configPrefix = str_replace('-', '_', $packageData['folder_name']);

        $content = "<?php

namespace {$packageData['namespace']}\\Constants;

class Application
{
    public const NAME = '{$packageData['namespace']}';
    public const VERSION = '{$packageData['version']}';
    public const PACKAGE_NAME = '{$packageData['folder_name']}';
    public const DESCRIPTION = '{$packageData['description']}';
    public const CONFIG_PREFIX = '{$configPrefix}';
    public const TRANSLATION_NAMESPACE = '{$packageData['folder_name']}';

    public static function getVersion(): string
    {
        return self::VERSION;
    }

    public static function getName(): string
    {
        return self::NAME;
    }

    public static function getPackageName(): string
    {
        return self::PACKAGE_NAME;
    }

    public static function getDescription(): string
    {
        return self::DESCRIPTION;
    }

    public static function getConfigPrefix(): string
    {
        return self::CONFIG_PREFIX;
    }

    public static function getTranslationNamespace(): string
    {
        return self::TRANSLATION_NAMESPACE;
    }
}
";

        File::put("{$packageDir}/src/constants/Application.php", $content);
    }

    private function generateServiceProvider(string $packageDir, array $packageData): void
    {
        $serviceProviderName = "{$packageData['namespace']}ServiceProvider";
        $configPrefix = str_replace('-', '_', $packageData['folder_name']);

        $content = "<?php

namespace {$packageData['namespace']}\\Providers;

use Illuminate\\Support\\ServiceProvider;
use {$packageData['namespace']}\\Constants\\Application;

class {$serviceProviderName} extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        \$this->mergeConfigFrom(
            __DIR__ . '/../config/{$configPrefix}.php',
            Application::CONFIG_PREFIX
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \$this->registerPublishing();
        \$this->registerViews();
        \$this->registerTranslations();
        \$this->registerRoutes();
        \$this->registerMigrations();
        \$this->registerCommands();
    }

    /**
     * Register publishing resources.
     */
    private function registerPublishing(): void
    {
        if (\$this->app->runningInConsole()) {
            \$this->publishes([
                __DIR__ . '/../config/{$configPrefix}.php' => config_path('{$configPrefix}.php'),
            ], '{$packageData['folder_name']}-config');

            \$this->publishes([
                __DIR__ . '/../public' => public_path('vendor/{$packageData['folder_name']}'),
            ], '{$packageData['folder_name']}-assets');

            \$this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/{$packageData['folder_name']}'),
            ], '{$packageData['folder_name']}-views');
        }
    }

    /**
     * Register views.
     */
    private function registerViews(): void
    {
        \$this->loadViewsFrom(__DIR__ . '/../resources/views', Application::TRANSLATION_NAMESPACE);
    }

    /**
     * Register translations.
     */
    private function registerTranslations(): void
    {
        \$this->loadTranslationsFrom(__DIR__ . '/../lang', Application::TRANSLATION_NAMESPACE);
    }

    /**
     * Register routes.
     */
    private function registerRoutes(): void
    {
        if (file_exists(__DIR__ . '/../routes/web.php')) {
            \$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        if (file_exists(__DIR__ . '/../routes/api.php')) {
            \$this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        }
    }

    /**
     * Register migrations.
     */
    private function registerMigrations(): void
    {
        \$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Register commands.
     */
    private function registerCommands(): void
    {
        if (\$this->app->runningInConsole()) {
            // Register your commands here
        }
    }
}
";

        File::put("{$packageDir}/src/Providers/{$serviceProviderName}.php", $content);
    }

    private function generateConfig(string $packageDir, array $packageData): void
    {
        $configPrefix = str_replace('-', '_', $packageData['folder_name']);
        $envPrefix = strtoupper($configPrefix);

        $content = "<?php

return [
    /*
    |--------------------------------------------------------------------------
    | {$packageData['namespace']} Configuration
    |--------------------------------------------------------------------------
    |
    | {$packageData['description']}
    |
    */

    'name' => '{$packageData['namespace']}',
    'version' => '{$packageData['version']}',
    'enabled' => env('{$envPrefix}_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Package Settings
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'debug' => env('{$envPrefix}_DEBUG', false),
        'cache_enabled' => env('{$envPrefix}_CACHE', true),
        'cache_ttl' => env('{$envPrefix}_CACHE_TTL', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Settings
    |--------------------------------------------------------------------------
    */

    'database' => [
        'table_prefix' => env('{$envPrefix}_TABLE_PREFIX', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Settings
    |--------------------------------------------------------------------------
    */

    'assets' => [
        'css_version' => env('{$envPrefix}_CSS_VERSION', '1.0.0'),
        'js_version' => env('{$envPrefix}_JS_VERSION', '1.0.0'),
    ],
];
";

        File::put("{$packageDir}/src/config/{$configPrefix}.php", $content);
    }

    private function generateHelpers(string $packageDir, array $packageData): void
    {
        $functionPrefix = str_replace('-', '_', $packageData['folder_name']);

        $content = "<?php

use {$packageData['namespace']}\\Constants\\Application;

if (!function_exists('{$functionPrefix}_version')) {
    /**
     * Get the version of {$packageData['namespace']} package
     */
    function {$functionPrefix}_version(): string
    {
        return Application::getVersion();
    }
}

if (!function_exists('{$functionPrefix}_config')) {
    /**
     * Get package configuration value
     */
    function {$functionPrefix}_config(string \$key = null, mixed \$default = null): mixed
    {
        if (\$key === null) {
            return config(Application::CONFIG_PREFIX);
        }

        return config(Application::CONFIG_PREFIX . '.' . \$key, \$default);
    }
}

if (!function_exists('{$functionPrefix}_enabled')) {
    /**
     * Check if the package is enabled
     */
    function {$functionPrefix}_enabled(): bool
    {
        return {$functionPrefix}_config('enabled', true);
    }
}

if (!function_exists('{$functionPrefix}_route')) {
    /**
     * Generate route URL for the package
     */
    function {$functionPrefix}_route(string \$name, array \$parameters = []): string
    {
        return route(Application::CONFIG_PREFIX . '.' . \$name, \$parameters);
    }
}
";

        File::put("{$packageDir}/src/Helpers/helpers.php", $content);
    }

    private function generateTranslations(string $packageDir, array $packageData): void
    {
        $content = "<?php

return [
    'package_name' => '{$packageData['namespace']}',
    'version' => 'Version',
    'description' => '{$packageData['description']}',

    'messages' => [
        'success' => 'Operation completed successfully',
        'error' => 'An error occurred',
        'not_found' => 'Resource not found',
        'unauthorized' => 'Unauthorized access',
        'validation_failed' => 'Validation failed',
    ],

    'labels' => [
        'name' => 'Name',
        'description' => 'Description',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'actions' => 'Actions',
    ],

    'buttons' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'create' => 'Create',
        'back' => 'Back',
    ],
];
";

        File::put("{$packageDir}/src/lang/en/translations.php", $content);
    }

    private function generateRoutes(string $packageDir, array $packageData): void
    {
        $routePrefix = str_replace('-', '_', $packageData['folder_name']);
        $cleanPrefix = str_replace('webkernel_', '', $routePrefix);

        $content = "<?php

use Illuminate\\Support\\Facades\\Route;
use {$packageData['namespace']}\\Http\\Controllers\\Controller;

Route::prefix('{$cleanPrefix}')
    ->name('{$routePrefix}.')
    ->middleware(['web'])
    ->group(function () {
        Route::get('/', [Controller::class, 'index'])->name('index');
        Route::get('/about', [Controller::class, 'about'])->name('about');
    });
";

        File::put("{$packageDir}/src/routes/web.php", $content);
    }

    private function generateController(string $packageDir, array $packageData): void
    {
        $content = "<?php

namespace {$packageData['namespace']}\\Http\\Controllers;

use Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests;
use Illuminate\\Foundation\\Validation\\ValidatesRequests;
use Illuminate\\Http\\Request;
use Illuminate\\Http\\Response;
use Illuminate\\Routing\\Controller as BaseController;
use Illuminate\\View\\View;
use {$packageData['namespace']}\\Constants\\Application;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Package index page
     */
    public function index(Request \$request): View
    {
        return view('{$packageData['folder_name']}::index', [
            'package_name' => Application::getName(),
            'version' => Application::getVersion(),
            'description' => Application::getDescription(),
        ]);
    }

    /**
     * Package about page
     */
    public function about(Request \$request): View
    {
        return view('{$packageData['folder_name']}::about', [
            'package_name' => Application::getName(),
            'version' => Application::getVersion(),
            'description' => Application::getDescription(),
        ]);
    }
}
";

        File::put("{$packageDir}/src/Http/Controllers/Controller.php", $content);
    }

    private function generateView(string $packageDir, array $packageData): void
    {
        $indexContent = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{{ \$package_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #007cba; margin: 20px 0; }
        .version { color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class=\"container\">
        <h1>{{ \$package_name }}</h1>
        <div class=\"info\">
            <p><strong>{{ __('{{$packageData['folder_name']}}::translations.description') }}</strong></p>
            <p class=\"version\">{{ __('{{$packageData['folder_name']}}::translations.version') }}: {{ \$version }}</p>
        </div>
        <p>{{ \$description }}</p>

        <h2>Getting Started</h2>
        <p>Welcome to your new Webkernel package! You can now customize this package according to your needs.</p>

        <h2>Next Steps</h2>
        <ul>
            <li>Customize the configuration in <code>config/</code></li>
            <li>Add your models in <code>src/Models/</code></li>
            <li>Create controllers in <code>src/Http/Controllers/</code></li>
            <li>Add views in <code>src/resources/views/</code></li>
            <li>Write tests in <code>tests/</code></li>
        </ul>
    </div>
</body>
</html>
";

        $aboutContent = "@extends('{$packageData['folder_name']}::layouts.app')

@section('title', 'About')

@section('content')
    <h2>About {{ \$package_name }}</h2>
    <p>{{ \$description }}</p>
    <p>Version: {{ \$version }}</p>

    <a href=\"{{ route('" . str_replace('-', '_', $packageData['folder_name']) . ".index') }}\">
        {{ __('{{$packageData['folder_name']}}::translations.buttons.back') }}
    </a>
@endsection
";

        File::put("{$packageDir}/src/resources/views/index.blade.php", $indexContent);
        File::put("{$packageDir}/src/resources/views/about.blade.php", $aboutContent);
    }

    private function generateModel(string $packageDir, array $packageData): void
    {
        $modelName = Str::studly(str_replace(['webkernel-', '-'], ['', '_'], $packageData['folder_name']));

        $content = "<?php

namespace {$packageData['namespace']}\\Models;

use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class {$modelName} extends Model
{
    use HasFactory, SoftDeletes;

    protected \$fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected \$casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected \$attributes = [
        'is_active' => true,
    ];

    /**
     * Scope to get only active records
     */
    public function scopeActive(\$query)
    {
        return \$query->where('is_active', true);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
";

        File::put("{$packageDir}/src/Models/{$modelName}.php", $content);
    }

    private function generateMigration(string $packageDir, array $packageData): void
    {
        $tableName = str_replace(['webkernel-', '-'], ['', '_'], $packageData['folder_name']) . 's';
        $timestamp = date('Y_m_d_His');
        $className = 'Create' . Str::studly($tableName) . 'Table';

        $content = "<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->text('description')->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->timestamps();
            \$table->softDeletes();

            \$table->index(['is_active']);
            \$table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
";

        File::put("{$packageDir}/src/database/migrations/{$timestamp}_create_{$tableName}_table.php", $content);
    }

    private function generateLicense(string $packageDir): void
    {
        $year = date('Y');
        $content = "MIT License

Copyright (c) {$year} Webkernel Team

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the \"Software\"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED \"AS IS\", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
";

        File::put("{$packageDir}/LICENSE", $content);
    }

    private function generateReadme(string $packageDir, array $packageData): void
    {
        $configPrefix = str_replace('-', '_', $packageData['folder_name']);
        $cleanName = str_replace(['webkernel-', '-'], ['', ' '], $packageData['folder_name']);
        $cleanName = ucwords($cleanName);

        $content = "# {$packageData['namespace']}

{$packageData['description']}

## Installation

Install the package via Composer:

```bash
composer require webkernel/{$packageData['folder_name']}
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag={$packageData['folder_name']}-config
```

This will create a `config/{$configPrefix}.php` file where you can customize the package settings.

Publish the assets:

```bash
php artisan vendor:publish --tag={$packageData['folder_name']}-assets
```

Publish the views (optional):

```bash
php artisan vendor:publish --tag={$packageData['folder_name']}-views
```

## Usage

### Basic Usage

The package integrates automatically with Webkernel. Once installed, you can:

1. Access the package via the configured routes
2. Use the provided helpers in your application
3. Extend the base functionality as needed

### Configuration Options

Key configuration options in `config/{$configPrefix}.php`:

- `enabled`: Enable/disable the package
- `settings.debug`: Enable debug mode
- `settings.cache_enabled`: Enable caching
- `database.table_prefix`: Database table prefix

### Helper Functions

The package provides several helper functions:

```php
// Get package version
\$version = {$configPrefix}_version();

// Get configuration value
\$config = {$configPrefix}_config('settings.debug');

// Check if package is enabled
\$enabled = {$configPrefix}_enabled();

// Generate package route
\$url = {$configPrefix}_route('index');
```

## Development

### Testing

Run the test suite:

```bash
composer test
```

Run tests with coverage:

```bash
composer test-coverage
```

### Directory Structure

```
src/
├── Console/           # Artisan commands
├── Http/              # Controllers and middleware
├── Models/            # Eloquent models
├── Providers/         # Service providers
├── config/            # Configuration files
├── database/          # Migrations and seeders
├── lang/              # Translation files
├── resources/         # Views and assets
└── routes/            # Route definitions
```

## Requirements

- PHP ^8.1
- Laravel ^10.0|^11.0
- Webkernel ^1.0

## Changelog

### v{$packageData['version']}

- Initial release
- Basic package structure
- Configuration system
- Helper functions
- Test suite

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute to this project.

## Security

If you discover any security-related issues, please email team@webkernel.org instead of using the issue tracker.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

- [Webkernel Team](https://github.com/webkernel)
- [All Contributors](../../contributors)

## Support

For support, please contact team@webkernel.org or create an issue on GitHub.
";

        File::put("{$packageDir}/README.md", $content);
    }

    private function generateInstallScript(string $packageDir, array $packageData): void
    {
        $content = "<?php

/**
 * Installation script for {$packageData['namespace']}
 * Version: {$packageData['version']}
 *
 * This script is executed during package installation
 */

declare(strict_types=1);

echo \"Installing {$packageData['namespace']} v{$packageData['version']}...\\n\";

// Check for Composer
if (!class_exists('\\\\Composer\\\\Autoload\\\\ClassLoader')) {
    echo \"Error: Composer autoloader not detected.\\n\";
    exit(1);
}

// Check PHP version
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    echo \"Error: PHP 8.1 or higher is required.\\n\";
    exit(1);
}

// Check Laravel
if (!function_exists('app') && !class_exists('\\\\Illuminate\\\\Foundation\\\\Application')) {
    echo \"Warning: Laravel not detected. Make sure this package is installed in a Laravel application.\\n\";
}

echo \"✓ System requirements check passed\\n\";
echo \"✓ {$packageData['namespace']} v{$packageData['version']} installed successfully.\\n\";
echo \"\\nNext steps:\\n\";
echo \"1. Run 'php artisan vendor:publish --tag={$packageData['folder_name']}-config'\\n\";
echo \"2. Run 'php artisan vendor:publish --tag={$packageData['folder_name']}-assets'\\n\";
echo \"3. Run 'php artisan migrate' if you have database migrations\\n\";
echo \"\\nFor more information, see README.md\\n\";
";

        File::put("{$packageDir}/install.php", $content);
    }

    private function generateGitignore(string $packageDir): void
    {
        $content = "/vendor/
/node_modules/
/coverage/
/.phpunit.result.cache
/.php-cs-fixer.cache
.DS_Store
Thumbs.db
*.log
*.tmp
*.cache
.env
.env.backup
.env.production
composer.lock
package-lock.json
yarn.lock
";

        File::put("{$packageDir}/.gitignore", $content);
    }

    private function displaySuccess(array $packageData): void
    {
        $packageData['namespace'] = $this->deriveNamespace($packageData['folder_name']);
        $packageDir = base_path("packages/{$packageData['folder_name']}");

        $this->newLine();
        $this->info('==========================================');
        $this->info('    PACKAGE CREATED SUCCESSFULLY!');
        $this->info('==========================================');
        $this->newLine();

        $this->line("📦 Package: <fg=green>{$packageData['folder_name']}</>");
        $this->line("🏷️  Namespace: <fg=green>{$packageData['namespace']}</>");
        $this->line("📊 Version: <fg=green>{$packageData['version']}</>");
        $this->line("📁 Location: <fg=green>{$packageDir}</>");
        $this->newLine();

        $this->info('📋 Generated Files:');
        $this->line('   ├─ composer.json');
        $this->line('   ├─ README.md');
        $this->line('   ├─ LICENSE');
        $this->line('   ├─ src/constants/Application.php');
        $this->line('   ├─ src/Providers/ServiceProvider.php');
        $this->line('   ├─ src/Http/Controllers/Controller.php');
        $this->line('   ├─ src/Models/Model.php');
        $this->line('   ├─ src/config/config.php');
        $this->line('   ├─ src/routes/web.php');
        $this->line('   ├─ src/resources/views/');
        $this->line('   ├─ src/lang/en/translations.php');
        $this->line('   ├─ tests/Feature/PackageTest.php');
        $this->line('   └─ tests/Unit/ApplicationTest.php');
        $this->newLine();

        $this->info('🚀 Next Steps:');
        $this->line('   1. Navigate to the package directory');
        $this->line('   2. Run "composer install" to install dependencies');
        $this->line('   3. Add the package to your main composer.json repositories');
        $this->line('   4. Customize the generated files according to your needs');
        $this->line('   5. Run "php artisan vendor:publish" to publish assets');
        $this->line('   6. Run "php artisan migrate" for database changes');
        $this->newLine();

        $configPrefix = str_replace('-', '_', $packageData['folder_name']);
        $this->info('💡 Quick Commands:');
        $this->line("   php artisan vendor:publish --tag={$packageData['folder_name']}-config");
        $this->line("   php artisan vendor:publish --tag={$packageData['folder_name']}-assets");
        $this->line("   php artisan vendor:publish --tag={$packageData['folder_name']}-views");
        $this->newLine();

        $this->info('📖 Documentation: See README.md for detailed usage instructions');
        $this->info('🧪 Testing: Run "composer test" in the package directory');
        $this->newLine();
    }
}
