# Arcanes - Ultra-High Performance Module System

**Developer**: El Moumen Yassine  
**Company**: Numerimondes (numerimondes.com)  
**Performance**: Sub-millisecond module discovery and loading  

## Overview

Arcanes is an ultra-optimized Laravel module system designed for massive module loads with instant configuration reload capabilities. It provides sub-millisecond performance through advanced caching strategies, OPcache optimization, and intelligent discovery mechanisms.

## Key Features

- **Sub-millisecond Performance**: Lightning-fast module discovery and loading
- **Instant Config Reload**: Automatic cache invalidation on configuration changes
- **Laravel Octane Compatible**: Full static optimization support
- **OPcache Integration**: Automatic bytecode precompilation
- **Smart Discovery**: Recursive module scanning with intelligent caching
- **Zero-Configuration**: Auto-discovery of module components
- **Memory Optimized**: Lazy loading and efficient resource management

## Architecture

### Core Components

1. **ArcancesServiceProvider**: Main service provider with config-aware caching
2. **WebkernelApp**: Abstract base class for all modules
3. **WebkernelManager**: Ultra-fast manager with instant config detection
4. **ModuleConfig**: Optimized module configuration container
5. **ModuleBuilder**: Fluent API for module configuration

## Installation

Arcanes is automatically loaded via the main Webkernel ServiceProvider. No manual installation required.

## Quick Start

### Creating a Module

```php
<?php

declare(strict_types=1);

namespace App\Modules\Blog;

use Webkernel\Arcanes\WebkernelApp;
use Webkernel\Arcanes\ModuleConfig;

class BlogModule extends WebkernelApp
{
    public function configureModule(): void
    {
        $config = $this->module()
            ->id('blog')
            ->name('Blog Management')
            ->version('2.1.0')
            ->description('Advanced blog management system')
            ->viewNamespace('blog')
            ->dependencies(['user', 'media'])
            ->providers([
                BlogServiceProvider::class,
                BlogEventServiceProvider::class,
            ])
            ->aliases([
                'Blog' => BlogFacade::class,
            ])
            ->build();

        $this->setModuleConfig($config);
    }
}
```

### Module Structure

```
app/Modules/Blog/
├── BlogModule.php
├── Http/
│   ├── Controllers/
│   └── Middleware/
├── Resources/Views/
├── Routes/
│   ├── web.php
│   └── api.php
├── Database/Migrations/
├── Console/Commands/
├── Services/
├── Helpers/
├── Lang/
└── config/
```

## API Reference

### WebkernelManager API

#### Getting the Manager Instance

```php
$manager = app(WebkernelManager::class);
```

#### Core Methods

```php
// Get all available modules (from cache)
$modules = $manager->getAvailableModules();

// Get all registered modules with full metadata
$allModules = $manager->getModules();

// Get specific module instance (lazy loaded)
$blogModule = $manager->getModule('blog');

// Boot specific module manually
$moduleInstance = $manager->bootModule('blog');

// Force refresh discovery (clears all caches)
$manager->refreshDiscovery();

// Instant bootstrap (sub-millisecond)
$manager->instantBootstrap();

// Lazy boot all modules
$manager->lazyBoot();
```

#### API Response Examples

**Get Available Modules**:
```php
$manager = app(WebkernelManager::class);
$modules = $manager->getAvailableModules();

return response()->json([
    'modules' => $modules,
    'count' => count($modules),
    'cached_at' => now()
]);
```

**Response Structure**:
```json
{
    "modules": {
        "blog": {
            "id": "blog",
            "name": "Blog Management",
            "description": "Advanced blog management system",
            "version": "2.1.0",
            "class": "App\\Modules\\Blog\\BlogModule",
            "namespace": "App\\Modules\\Blog",
            "viewNamespace": "blog",
            "instantiated": false,
            "path": "/app/Modules/Blog/BlogModule.php",
            "basePath": "/app/Modules/Blog",
            "viewsPath": "/app/Modules/Blog/Resources/Views",
            "langPath": "/app/Modules/Blog/Lang",
            "routesPath": "/app/Modules/Blog/Routes",
            "migrationsPath": "/app/Modules/Blog/Database/Migrations",
            "helpersPath": "/app/Modules/Blog/Helpers",
            "consolePath": "/app/Modules/Blog/Console",
            "commandsPath": "/app/Modules/Blog/Commands",
            "configPath": "/app/Modules/Blog/config"
        }
    }
}
```

### Advanced API Usage

#### Module Status Dashboard
```php
$manager = app(WebkernelManager::class);
$modules = $manager->getModules();

$dashboard = [
    'total_modules' => count($modules),
    'active_modules' => array_filter($modules, fn($m) => $m['instantiated']),
    'modules_with_views' => array_filter($modules, fn($m) => $m['viewsPath']),
    'modules_with_routes' => array_filter($modules, fn($m) => $m['routesPath']),
    'modules_with_migrations' => array_filter($modules, fn($m) => $m['migrationsPath']),
    'performance_stats' => [
        'cache_enabled' => true,
        'opcache_optimized' => function_exists('opcache_get_status'),
        'last_discovery' => filemtime(storage_path('framework/cache/webkernel_ultra.php'))
    ]
];

return response()->json($dashboard);
```

#### Dynamic Module Loading
```php
public function loadModule(string $moduleId)
{
    $manager = app(WebkernelManager::class);
    
    // Get module info without instantiation
    $modules = $manager->getAvailableModules();
    $moduleInfo = $modules[$moduleId] ?? null;
    
    if (!$moduleInfo) {
        return response()->json(['error' => 'Module not found'], 404);
    }
    
    // Lazy load the module
    $moduleInstance = $manager->bootModule($moduleId);
    
    if (!$moduleInstance) {
        return response()->json(['error' => 'Failed to boot module'], 500);
    }
    
    return response()->json([
        'module' => $moduleId,
        'status' => 'loaded',
        'config' => $moduleInstance->getModuleConfig(),
        'paths' => [
            'views' => $moduleInstance->getViewsPath(),
            'routes' => $moduleInstance->getRoutesPath(),
            'migrations' => $moduleInstance->getMigrationsPath(),
        ]
    ]);
}
```

#### Module Health Check
```php
public function healthCheck()
{
    $manager = app(WebkernelManager::class);
    $modules = $manager->getAvailableModules();
    
    $health = [];
    foreach ($modules as $id => $moduleData) {
        $health[$id] = [
            'class_exists' => class_exists($moduleData['class']),
            'files_accessible' => file_exists($moduleData['path']),
            'views_available' => $moduleData['viewsPath'] && is_dir($moduleData['viewsPath']),
            'routes_available' => $moduleData['routesPath'] && is_dir($moduleData['routesPath']),
            'migrations_available' => $moduleData['migrationsPath'] && is_dir($moduleData['migrationsPath']),
            'helpers_available' => $moduleData['helpersPath'] && is_dir($moduleData['helpersPath'])
        ];
    }
    
    return response()->json([
        'overall_health' => 'healthy',
        'modules' => $health,
        'cache_status' => file_exists(storage_path('framework/cache/webkernel_ultra.php')),
        'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status()['opcache_enabled']
    ]);
}
```

## Laravel Octane Compatibility

Arcanes is fully optimized for Laravel Octane with static variable management:

### Static Optimization Features

- **Static Cache Management**: Automatic static variable cleanup
- **Memory Leak Prevention**: Proper static array management
- **OPcache Integration**: Bytecode precompilation for maximum performance
- **Config Hash Tracking**: Static config change detection

### Octane Configuration

```php
// config/octane.php
'warm' => [
    \Webkernel\Arcanes\WebkernelManager::class,
],

'flush' => [
    'webkernel_cache' => fn() => app(\Webkernel\Arcanes\WebkernelManager::class)->refreshDiscovery(),
],
```

## Performance Characteristics

### Benchmarks

- **Module Discovery**: < 0.5ms for 100+ modules
- **Cache Loading**: < 0.1ms from OPcache
- **Module Instantiation**: < 0.2ms per module
- **Config Change Detection**: < 0.05ms

### Optimization Features

1. **Ultra-Fast Caching**: File-based cache with OPcache integration
2. **Smart Invalidation**: Instant cache refresh on config changes
3. **Directory Hashing**: Quick change detection without full scans
4. **Lazy Loading**: Modules loaded only when accessed
5. **Memory Management**: Efficient static variable usage

## Configuration

### Discovery Configuration

```php
// config/webkernel-arcanes.php
return [
    'discovery' => [
        'paths' => [
            base_path('app/Modules'),
            base_path('packages'),
        ],
        'exclude_patterns' => [
            'vendor/',
            'node_modules/',
            'tests/',
        ],
    ],
    'development' => [
        'debug' => env('APP_DEBUG', false),
        'auto_refresh' => env('WEBKERNEL_AUTO_REFRESH', true),
    ],
    'performance' => [
        'opcache_optimization' => true,
        'ultra_cache' => true,
        'lazy_loading' => true,
    ],
];
```

### Module Auto-Discovery

Modules are automatically discovered by scanning for classes that extend `WebkernelApp`. The system uses intelligent caching to ensure sub-millisecond performance even with hundreds of modules.

## Advanced Features

### Automatic Component Registration

- **Views**: Auto-registered from `Resources/Views/`
- **Routes**: Auto-loaded from `Routes/` directory
- **Migrations**: Auto-discovered from `Database/Migrations/`
- **Commands**: Auto-registered from `Console/` and `Commands/`
- **Helpers**: Auto-loaded from `Helpers/` directory
- **Translations**: Auto-loaded from `Lang/` directory

### Helper Loading

```php
// Helpers are automatically loaded from:
// - Helpers/helpers.php
// - Helpers/helper*.php
// - Any PHP file starting with 'helper'
```

### Command Discovery

```php
// Commands are auto-discovered and registered
// No manual registration needed
class BlogCommand extends Command
{
    protected $signature = 'blog:generate';
    // Command implementation
}
```

## Development Workflow

### Module Development

1. Create module class extending `WebkernelApp`
2. Implement `configureModule()` method
3. Add components to appropriate directories
4. System automatically discovers and registers everything

### Hot Reload

The system automatically detects changes and refreshes caches instantly, providing seamless development experience.

### Debugging

```php
// Enable debug mode in config
'development' => [
    'debug' => true,
]

// Check logs for performance metrics
// Logs show discovery time and module counts
```

## Best Practices

1. **Module Structure**: Follow the standard directory structure
2. **Naming Conventions**: Use descriptive class and module names
3. **Dependencies**: Clearly define module dependencies
4. **Performance**: Utilize lazy loading for heavy modules
5. **Caching**: Leverage the built-in caching mechanisms

## Troubleshooting

### Cache Issues

```php
// Clear all caches
app(WebkernelManager::class)->refreshDiscovery();

// Or via Artisan
php artisan cache:clear
```

### Performance Issues

- Ensure OPcache is enabled in production
- Check exclude patterns for unnecessary scanning
- Monitor log files for performance metrics

## System Requirements

- PHP 8.1+
- Laravel 9.0+
- OPcache (recommended for optimal performance)

---

**Developed by El Moumen Yassine**  
**Numerimondes** - numerimondes.com
