<?php declare(strict_types=1);
namespace Webkernel\Arcanes\Support\Base;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ArcanesMethods
{
  /**
   * Load PSR-4 autoload mappings from composer.json
   */
  public static function loadComposerPsr4(): array
  {
    $composerPath = base_path('composer.json');
    if (!File::exists($composerPath)) {
      return [];
    }

    $composer = json_decode(File::get($composerPath), true);
    return $composer['autoload']['psr-4'] ?? [];
  }

  /**
   * Resolve namespace from path using PSR-4 mappings
   */
  public static function resolveNamespaceFromPath(string $path, array $composerPsr4): ?string
  {
    $absolutePath = realpath($path) ?: realpath(base_path($path)) ?: base_path($path);

    foreach ($composerPsr4 as $namespace => $autoloadPath) {
      $autoloadAbsolutePath = realpath(base_path($autoloadPath)) ?: base_path($autoloadPath);

      if (str_starts_with($absolutePath, $autoloadAbsolutePath)) {
        $relativePath = substr($absolutePath, strlen($autoloadAbsolutePath));
        $relativePath = trim($relativePath, '/\\');

        if ($relativePath) {
          $namespaceParts = explode('/', str_replace('\\', '/', $relativePath));
          $namespaceParts = array_map([Str::class, 'studly'], $namespaceParts);
          return rtrim($namespace, '\\') . '\\' . implode('\\', $namespaceParts);
        } else {
          return rtrim($namespace, '\\');
        }
      }
    }

    return null;
  }

  /**
   * Generate fallback namespace from path
   */
  public static function generateFallbackNamespace(string $path): string
  {
    $relativePath = str_replace(base_path(), '', $path);
    $relativePath = trim($relativePath, '/\\');

    $parts = explode('/', str_replace('\\', '/', $relativePath));
    $parts = array_map([Str::class, 'studly'], $parts);

    return implode('\\', $parts);
  }

  /**
   * Get stub content from multiple possible paths
   */
  public static function getStub(string $stubName, array $stubPaths): ?string
  {
    foreach ($stubPaths as $stubPath) {
      $fullPath = rtrim($stubPath, '/\\') . '/' . $stubName;
      if (File::exists($fullPath)) {
        return File::get($fullPath);
      }
    }

    return null;
  }

  /**
   * Create directory structure with optional stub files
   */
  public static function createDirectoryStructure(string $baseDir, array $structure, array $stubPaths = []): void
  {
    foreach ($structure as $directory => $stubs) {
      $dirPath = rtrim($baseDir, '/\\') . '/' . ltrim($directory, '/\\');
      File::makeDirectory($dirPath, 0755, true);

      if ($stubs === null) {
        File::put($dirPath . '/.gitkeep', '');
      } else {
        foreach ($stubs as $stubFile) {
          self::createStubFile($dirPath, $stubFile, $stubPaths);
        }
      }
    }
  }

  /**
   * Create a file from stub template
   */
  public static function createStubFile(string $directory, string $stubFile, array $stubPaths): void
  {
    $stubContent = self::getStub($stubFile, $stubPaths);

    if ($stubContent === null) {
      return;
    }

    $fileName = str_replace('.stub', '.php', $stubFile);
    $filePath = rtrim($directory, '/\\') . '/' . $fileName;

    File::put($filePath, $stubContent);
  }

  /**
   * Replace placeholders in content
   */
  public static function replacePlaceholders(string $content, array $replacements): string
  {
    return str_replace(array_keys($replacements), array_values($replacements), $content);
  }

  /**
   * Normalize path separators and remove duplicates
   */
  public static function normalizePath(string $path): string
  {
    $path = str_replace('\\', '/', $path);
    return preg_replace('/\/+/', '/', $path);
  }

  /**
   * Build full path from base and sub-paths
   */
  public static function buildPath(string $basePath, ?string $subPath = null, ?string $finalPath = null): string
  {
    $path = rtrim($basePath, '/\\');

    if ($subPath) {
      $subPath = trim($subPath, '/\\');
      $path .= '/' . $subPath;
    }

    if ($finalPath) {
      $finalPath = trim($finalPath, '/\\');
      $path .= '/' . $finalPath;
    }

    return self::normalizePath($path);
  }

  /**
   * Get discovery paths from config
   */
  public static function getDiscoveryPaths(string $configKey = 'webkernel-arcanes.discovery'): array
  {
    $config = config($configKey, []);
    return $config['paths'] ?? [base_path('app')];
  }

  /**
   * Validate and prepare module/component name
   */
  public static function prepareName(string $name): string
  {
    return Str::studly(trim($name));
  }

  /**
   * Generate unique ID for components
   */
  public static function generateId(string $prefix = ''): string
  {
    $prefix = $prefix ? Str::kebab($prefix) . '_' : '';
    return $prefix . uniqid();
  }

  /**
   * Resolve namespace for components (removes components/component-name from path)
   */
  public static function resolveComponentNamespace(
    string $componentPath,
    array $composerPsr4,
    string $componentName,
  ): ?string {
    // Remove 'components/component-name' from the end of the path
    $parentPath = dirname(dirname($componentPath));

    $resolvedNamespace = self::resolveNamespaceFromPath($parentPath, $composerPsr4);

    if ($resolvedNamespace) {
      return $resolvedNamespace . '\\components\\' . $componentName;
    }

    return null;
  }

  /**
   * Generate fallback namespace for components
   */
  public static function generateComponentFallbackNamespace(string $componentPath, string $componentName): string
  {
    // Remove 'components/component-name' from the end of the path
    $parentPath = dirname(dirname($componentPath));

    $fallbackNamespace = self::generateFallbackNamespace($parentPath);

    return $fallbackNamespace . '\\components\\' . $componentName;
  }
}
