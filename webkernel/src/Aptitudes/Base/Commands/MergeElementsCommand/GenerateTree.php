<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\Base\Commands\MergeElementsCommand;

class GenerateTree
{
  /**
   * Generates a textual tree structure of a directory matching the Unix tree command output.
   *
   * @param string $directory The absolute path of the directory
   * @param string $rootName The display name for the root (typically the relative path)
   * @param callable|null $filter Filter function (e.g., fn(string $path): bool)
   * @return string Formatted tree structure
   */
  public static function generateTree(string $directory, string $rootName, ?callable $filter = null): string
  {
    if (!is_dir($directory)) {
      return $rootName . "\n";
    }

    $output = $rootName . "\n";
    $output .= self::printChildren($directory, '', $filter);

    return $output;
  }

  /**
   * Recursively prints the children of a directory with proper tree connectors.
   *
   * @param string $directory
   * @param string $prefix Current prefix for indentation and connectors
   * @param callable|null $filter
   * @return string
   */
  private static function printChildren(string $directory, string $prefix, ?callable $filter): string
  {
    $items = [];
    $scanned = scandir($directory);

    if ($scanned === false) {
      return '';
    }

    foreach ($scanned as $item) {
      if ($item === '.' || $item === '..') {
        continue;
      }

      $path = $directory . DIRECTORY_SEPARATOR . $item;
      if ($filter && !$filter($path)) {
        continue;
      }

      $items[] = $item;
    }

    sort($items);

    $output = '';
    $lastIndex = count($items) - 1;

    for ($i = 0; $i < count($items); $i++) {
      $item = $items[$i];
      $isLast = $i === $lastIndex;
      $linePrefix = $prefix . ($isLast ? '└── ' : '├── ');
      $output .= $linePrefix . $item . "\n";

      $itemPath = $directory . DIRECTORY_SEPARATOR . $item;
      if (is_dir($itemPath)) {
        $newPrefix = $prefix . ($isLast ? '    ' : '│   ');
        $output .= self::printChildren($itemPath, $newPrefix, $filter);
      }
    }

    return $output;
  }
}
