<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Runtime\Loaders;

use Webkernel\Arcanes\Runtime\WebkernelManager;
use Illuminate\Support\ServiceProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Service provider that discovers and registers console commands from all modules
 */
class CommandLoader extends ServiceProvider
{
  private array $registeredCommands = [];

  /**
   * Register services
   *
   * @return void
   */
  public function register(): void
  {
    // Nothing to register
  }

  /**
   * Bootstrap services
   *
   * @return void
   */
  public function boot(): void
  {
    if (!$this->app->runningInConsole()) {
      return;
    }

    $manager = $this->app->make(WebkernelManager::class);
    $commands = $this->discoverAllCommands($manager);

    if (!empty($commands)) {
      $this->commands($commands);
    }
  }

  /**
   * Discover all commands from all modules
   *
   * @param WebkernelManager $manager
   * @return array<int, string>
   */
  private function discoverAllCommands(WebkernelManager $manager): array
  {
    $commands = [];

    foreach ($manager->getModules() as $module) {
      $moduleCmds = $this->discoverCommandsInModule($module);
      $commands = array_merge($commands, $moduleCmds);
    }

    return array_unique($commands);
  }

  /**
   * Discover commands in a specific module
   *
   * @param array $module Module data
   * @return array<int, string>
   */
  private function discoverCommandsInModule(array $module): array
  {
    $commands = [];

    $commandPaths = [
      $module['consolePath'] ?? null,
      isset($module['consolePath']) ? $module['consolePath'] . '/Commands' : null,
      $module['commandsPath'] ?? null,
    ];

    foreach ($commandPaths as $path) {
      if ($path && is_dir($path)) {
        $commands = array_merge($commands, $this->scanCommandPath($path));
      }
    }

    return $commands;
  }

  /**
   * Scan a directory for command classes
   *
   * @param string $path Directory path
   * @return array<int, string>
   */
  private function scanCommandPath(string $path): array
  {
    $commands = [];

    $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::LEAVES_ONLY,
    );

    foreach ($iterator as $file) {
      if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
      }

      $commandClass = $this->extractCommandClass($file->getPathname());

      if ($commandClass && !in_array($commandClass, $this->registeredCommands, true)) {
        $commands[] = $commandClass;
        $this->registeredCommands[] = $commandClass;
      }
    }

    return $commands;
  }

  /**
   * Extract command class from a file
   *
   * @param string $filePath File path
   * @return string|null Full class name or null if not a command
   */
  private function extractCommandClass(string $filePath): ?string
  {
    $content = file_get_contents($filePath);

    if (!preg_match('/class\s+(\w+)\s+extends\s+.*Command/m', $content)) {
      return null;
    }

    $namespace = null;
    $className = null;

    if (preg_match('/^\s*namespace\s+([^;]+);/m', $content, $nsMatch)) {
      $namespace = trim($nsMatch[1]);
    }
    if (preg_match('/class\s+(\w+)\s+extends\s+.*Command/m', $content, $classMatch)) {
      $className = trim($classMatch[1]);
    }

    if (!($namespace && $className)) {
      return null;
    }

    $fullClassName = $namespace . '\\' . $className;

    return class_exists($fullClassName) ? $fullClassName : null;
  }
}
