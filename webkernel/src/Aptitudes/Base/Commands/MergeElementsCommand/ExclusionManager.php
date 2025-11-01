<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\Base\Commands\MergeElementsCommand;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExclusionManager
{
  /**
   * @var array<string>
   */
  private array $patterns = [];

  /**
   * @var Command
   */
  private Command $command;

  public function __construct(Command $command)
  {
    $this->command = $command;
  }

  /**
   * Load patterns from .gitignore file.
   *
   * @return void
   */
  public function loadGitignore(): void
  {
    $gitignorePath = base_path('.gitignore');

    if (!File::exists($gitignorePath)) {
      $this->command->warn('.gitignore file not found. Skipping gitignore patterns.');
      return;
    }

    $content = File::get($gitignorePath);
    $lines = explode("\n", $content);

    foreach ($lines as $line) {
      $line = trim($line);

      if (empty($line) || str_starts_with($line, '#')) {
        continue;
      }

      $this->patterns[] = $line;
    }

    $this->command->info('Loaded ' . count($this->patterns) . ' patterns from .gitignore');
  }

  /**
   * Add custom exclusion patterns.
   *
   * @param array<string> $patterns
   * @return void
   */
  public function addPatterns(array $patterns): void
  {
    foreach ($patterns as $pattern) {
      $trimmed = trim($pattern);
      if (!empty($trimmed)) {
        $this->patterns[] = $trimmed;
      }
    }
  }

  /**
   * Check if a path should be included based on exclusion patterns.
   *
   * @param string $path
   * @return bool
   */
  public function shouldInclude(string $path): bool
  {
    if (empty($this->patterns)) {
      return true;
    }

    $basePath = base_path();
    $relativePath = str_starts_with($path, $basePath)
      ? ltrim(substr($path, strlen($basePath)), DIRECTORY_SEPARATOR)
      : $path;

    $relativePath = str_replace('\\', '/', $relativePath);

    foreach ($this->patterns as $pattern) {
      if ($this->matchesPattern($relativePath, $pattern)) {
        return false;
      }
    }

    return true;
  }

  /**
   * Match a path against a gitignore-style pattern.
   *
   * @param string $path
   * @param string $pattern
   * @return bool
   */
  private function matchesPattern(string $path, string $pattern): bool
  {
    $pattern = str_replace('\\', '/', $pattern);

    $isNegation = str_starts_with($pattern, '!');
    if ($isNegation) {
      $pattern = substr($pattern, 1);
    }

    $endsWithSlash = str_ends_with($pattern, '/');
    if ($endsWithSlash) {
      $pattern = rtrim($pattern, '/');
    }

    $startsWithSlash = str_starts_with($pattern, '/');
    if ($startsWithSlash) {
      $pattern = ltrim($pattern, '/');
      $regexPattern = $this->convertToRegex($pattern);
      $matches = preg_match($regexPattern, $path) === 1;
    } else {
      $regexPattern = $this->convertToRegex($pattern);
      $matches = preg_match($regexPattern, $path) === 1 || preg_match('#(^|/)' . substr($regexPattern, 1), $path) === 1;
    }

    if ($endsWithSlash && $matches) {
      return true;
    }

    return $matches;
  }

  /**
   * Convert gitignore pattern to regex.
   *
   * @param string $pattern
   * @return string
   */
  private function convertToRegex(string $pattern): string
  {
    $pattern = preg_quote($pattern, '#');
    $pattern = str_replace('\*\*/', '(.*/)?', $pattern);
    $pattern = str_replace('\*\*', '.*', $pattern);
    $pattern = str_replace('\*', '[^/]*', $pattern);
    $pattern = str_replace('\?', '[^/]', $pattern);

    return '#^' . $pattern . '(/.*)?$#';
  }

  /**
   * Check if any patterns are loaded.
   *
   * @return bool
   */
  public function hasPatterns(): bool
  {
    return !empty($this->patterns);
  }

  /**
   * Get all loaded patterns.
   *
   * @return array<string>
   */
  public function getPatterns(): array
  {
    return $this->patterns;
  }
}
