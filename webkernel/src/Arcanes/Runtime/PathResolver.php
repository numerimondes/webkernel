<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Runtime;

/**
 * Resolves and caches module paths
 */
class PathResolver
{
  private array $pathCache = [];

  /**
   * Resolve path relative to base path
   *
   * @param string $basePath Base module path
   * @param string $relativePath Relative path to resolve
   * @param string $subPath Optional sub-path
   * @return string
   */
  public function resolve(string $basePath, string $relativePath, string $subPath = ''): string
  {
    $key = $basePath . '|' . $relativePath . '|' . $subPath;

    if (isset($this->pathCache[$key])) {
      return $this->pathCache[$key];
    }

    $fullPath = $basePath . '/' . $relativePath;
    if ($subPath) {
      $fullPath .= '/' . $subPath;
    }

    return $this->pathCache[$key] = $fullPath;
  }

  /**
   * Clear path cache
   *
   * @return void
   */
  public function clearCache(): void
  {
    $this->pathCache = [];
  }
}
