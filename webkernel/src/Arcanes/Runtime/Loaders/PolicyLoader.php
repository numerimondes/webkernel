<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Runtime\Loaders;

use Webkernel\Arcanes\Runtime\WebkernelManager;
use Webkernel\Arcanes\Support\Base\ArcanesLoader;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Gate;
use Webkernel\Arcanes\QueryModules;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Discovers and registers policy classes from all modules
 */
class PolicyLoader implements ArcanesLoader
{
  public function __construct(private Application $app, private WebkernelManager $manager) {}

  /**
   * Load and register all policy classes
   *
   * @return void
   */
  public function load(): void
  {
    foreach ($this->manager->getModules() as $module) {
      $path = $module['basePath'] . '/Policies';

      if (!is_dir($path)) {
        continue;
      }

      $this->registerPoliciesFromPath($path);
    }
  }

  /**
   * Register policies from a directory
   *
   * @param string $path Directory path
   * @return void
   */
  private function registerPoliciesFromPath(string $path): void
  {
    $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::LEAVES_ONLY,
    );

    foreach ($iterator as $file) {
      if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
      }

      $policyData = $this->extractPolicyData($file->getPathname());

      if ($policyData) {
        $this->registerPolicy($policyData);
      }
    }
  }

  /**
   * Extract policy information from a file
   *
   * @param string $filePath File path
   * @return array|null Policy data or null if invalid
   */
  private function extractPolicyData(string $filePath): ?array
  {
    $content = file_get_contents($filePath);

    if (!preg_match('/class\s+(\w+Policy)\s*\{/m', $content)) {
      return null;
    }

    $namespace = null;
    $className = null;

    if (preg_match('/^\s*namespace\s+([^;]+);/m', $content, $nsMatch)) {
      $namespace = trim($nsMatch[1]);
    }
    if (preg_match('/class\s+(\w+Policy)\s*\{/m', $content, $classMatch)) {
      $className = trim($classMatch[1]);
    }

    if (!($namespace && $className)) {
      return null;
    }

    return [
      'namespace' => $namespace,
      'className' => $className,
      'fullClassName' => $namespace . '\\' . $className,
      'modelName' => str_replace('Policy', '', $className),
    ];
  }

  /**
   * Register a policy with the gate
   *
   * @param array $policyData Policy information
   * @return void
   */
  private function registerPolicy(array $policyData): void
  {
    $modelClass = $policyData['namespace'] . '\\Models\\' . $policyData['modelName'];

    if (class_exists($modelClass)) {
      Gate::policy($modelClass, $policyData['fullClassName']);
      return;
    }

    $this->findAndRegisterModelFromModules($policyData);
  }

  /**
   * Find model in other modules and register policy
   *
   * @param array $policyData Policy information
   * @return void
   */
  private function findAndRegisterModelFromModules(array $policyData): void
  {
    $modules = QueryModules::make()
      ->select(['namespace'])
      ->get();

    foreach ($modules as $module) {
      if (!isset($module['namespace'])) {
        continue;
      }

      $altModelClass = $module['namespace'] . '\\Models\\' . $policyData['modelName'];

      if (class_exists($altModelClass)) {
        Gate::policy($altModelClass, $policyData['fullClassName']);
        break;
      }
    }
  }
}
