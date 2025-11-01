<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\Base\Commands\MergeElementsCommand;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use function Laravel\Prompts\text;

class PathCollector
{
  /**
   * @var Command
   */
  private Command $command;

  public function __construct(Command $command)
  {
    $this->command = $command;
  }

  /**
   * Collect paths in simple mode.
   *
   * @return array<string>
   */
  public function collectSimpleMode(): array
  {
    $this->command->info('Simple mode activated. Enter paths to merge.');
    $this->command->comment('Tip: You can enter multiple paths separated by commas.');
    $this->command->newLine();

    $paths = [];

    while (true) {
      $input = text(
        label: 'Add path(s) or type "done"',
        placeholder: 'e.g., platform/Numerimondes',
        hint: 'Press Tab for autocompletion, use arrows to navigate',
      );

      if ($input === null || trim($input) === '') {
        $this->command->warn('Empty input ignored.');
        continue;
      }

      $input = trim($input);

      if (strtolower($input) === 'done' || strtolower($input) === 'exit') {
        break;
      }

      $parsedPaths = $this->parseInputPaths($input);
      $this->validateAndAddPaths($parsedPaths, $paths);
    }

    return $paths;
  }

  /**
   * Collect paths in unique mode.
   *
   * @param string|null $uniquePath
   * @return array<string>
   */
  public function collectUnique(?string $uniquePath): array
  {
    $paths = [];

    if ($uniquePath === null) {
      $uniquePath = text(
        label: 'Enter the path to merge',
        placeholder: 'e.g., platform/Numerimondes',
        hint: 'Use arrows to navigate, Tab for autocompletion',
      );
    }

    if (!empty($uniquePath)) {
      $parsedPaths = $this->parseInputPaths($uniquePath);
      $this->validateAndAddPaths($parsedPaths, $paths);
    }

    return $paths;
  }

  /**
   * Collect paths in multiple mode.
   *
   * @return array<string>
   */
  public function collectMultiple(): array
  {
    $this->command->info(
      'Enter paths to merge (type "done" or "exit" when finished). You can add multiple paths separated by commas.',
    );

    $paths = [];

    while (true) {
      $input = text(
        label: 'Add a path or type "done"/"exit"',
        placeholder: 'e.g., src/app, config/app.php',
        hint: 'Use arrows to navigate, comma-separated for multiple',
      );

      if ($input === null || trim($input) === '') {
        $this->command->warn('Empty input ignored. Please provide a valid path, "done" or "exit".');
        continue;
      }

      $input = trim($input);

      if (strtolower($input) === 'done' || strtolower($input) === 'exit') {
        break;
      }

      if (!empty($input)) {
        $parsedPaths = $this->parseInputPaths($input);
        $this->validateAndAddPaths($parsedPaths, $paths);
      }
    }

    return $paths;
  }

  /**
   * Parse comma-separated input into individual paths.
   *
   * @param string $input
   * @return array<string>
   */
  private function parseInputPaths(string $input): array
  {
    return array_map('trim', explode(',', $input));
  }

  /**
   * Validate paths and add to collection.
   *
   * @param array<string> $candidatePaths
   * @param array<string> &$paths
   * @return void
   */
  private function validateAndAddPaths(array $candidatePaths, array &$paths): void
  {
    foreach ($candidatePaths as $candidate) {
      if (empty($candidate)) {
        continue;
      }

      $resolvedPath = $this->resolvePath($candidate);
      if ($resolvedPath === null) {
        $this->command->warn("Invalid or suspicious path skipped: {$candidate}");
        continue;
      }

      $paths[] = $resolvedPath;
      $this->command->info("Added: {$resolvedPath}");
    }
  }

  /**
   * Resolve and validate a path.
   *
   * @param string $path
   * @return string|null
   */
  private function resolvePath(string $path): ?string
  {
    if (empty(trim($path)) || preg_match('/[<>|?*"]/', $path)) {
      return null;
    }

    $resolved = base_path($path);
    if (!File::exists($resolved) && !str_starts_with($path, '/')) {
      $resolved = $path;
    }

    if (!is_file($resolved) && !is_dir($resolved)) {
      $this->command->warn("Path does not exist: {$resolved}. Proceeding, but content may be empty.");
    }

    return $resolved;
  }
}
