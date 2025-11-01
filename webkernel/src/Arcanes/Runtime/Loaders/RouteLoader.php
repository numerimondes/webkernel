<?php declare(strict_types=1);
namespace Webkernel\Arcanes\Runtime\Loaders;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Illuminate\Support\Facades\Route;
use Webkernel\Arcanes\Runtime\WebkernelManager;
use Webkernel\Arcanes\Support\Base\ArcanesLoader;

/**
 * Loads route files from all registered modules with enhanced error detection
 * Zero-overhead by default, detailed logging when debug enabled
 */
class RouteLoader implements ArcanesLoader
{
  private bool $debugMode;
  private array $loadedFiles = [];
  private array $failedFiles = [];

  public function __construct(private WebkernelManager $manager)
  {
    $this->debugMode = config('webkernel-arcanes.development.debug', false);
  }

  /**
   * Load all route files from registered modules
   *
   * @return void
   */
  public function load(): void
  {
    $modules = $this->manager->getModules();

    foreach ($modules as $moduleId => $module) {
      if (isset($module['routesPath']) && is_dir($module['routesPath'])) {
        $this->loadRoutesFromPath($module['routesPath'], $moduleId);
      }
    }

    if ($this->debugMode && !empty($this->failedFiles)) {
      error_log(
        sprintf(
          'RouteLoader: Loaded %d files successfully, %d files failed',
          count($this->loadedFiles),
          count($this->failedFiles),
        ),
      );
    }
  }

  /**
   * Load all route files from a directory recursively
   *
   * @param string $path Directory path
   * @param string $moduleId Module identifier for error logging
   * @return void
   */
  private function loadRoutesFromPath(string $path, string $moduleId): void
  {
    $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::LEAVES_ONLY,
    );

    foreach ($iterator as $file) {
      if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
      }

      $this->loadRouteFile($file->getPathname(), $moduleId);
    }
  }

  /**
   * Load a single route file with enhanced error detection
   *
   * @param string $filepath Full path to route file
   * @param string $moduleId Module identifier
   * @return void
   */
  private function loadRouteFile(string $filepath, string $moduleId): void
  {
    $config = $this->extractRouteFileConfig($filepath);

    $routesBefore = collect(Route::getRoutes())
      ->map(fn($r) => $r->uri() . '|' . implode(',', $r->methods()))
      ->toArray();

    if ($config['enableSafeRouteLoading']) {
      $this->loadWithIsolation($filepath, $moduleId, $config);
    } else {
      $this->loadNormally($filepath, $moduleId);
    }

    $routesAfter = collect(Route::getRoutes())->map(fn($r) => $r->uri() . '|' . implode(',', $r->methods()))->toArray();
    $newRoutes = array_diff($routesAfter, $routesBefore);
    $routesAdded = count($newRoutes);

    if ($this->debugMode) {
      $status = $routesAdded > 0 ? 'OK' : 'EMPTY';
      error_log(sprintf('[%s] %s -> %d routes | File: %s', $status, $moduleId, $routesAdded, basename($filepath)));

      if ($routesAdded > 0) {
        foreach ($newRoutes as $route) {
          error_log('  + ' . $route);
        }
      }
    }

    if ($routesAdded > 0) {
      $this->loadedFiles[] = $filepath;
    } else {
      $this->failedFiles[] = [
        'file' => $filepath,
        'module' => $moduleId,
        'reason' => 'No routes registered',
      ];
    }
  }

  /**
   * Load route file normally with basic error handling
   *
   * @param string $filepath Full path to route file
   * @param string $moduleId Module identifier
   * @return void
   */
  private function loadNormally(string $filepath, string $moduleId): void
  {
    set_error_handler(function ($errno, $errstr, $errfile, $errline) {
      if (str_contains($errstr, 'has no effect')) {
        return true;
      }
      return false;
    });

    try {
      require_once $filepath;
    } catch (\ParseError $e) {
      $this->failedFiles[] = [
        'file' => $filepath,
        'module' => $moduleId,
        'reason' => 'Parse error: ' . $e->getMessage(),
      ];

      if ($this->debugMode) {
        error_log(
          sprintf(
            'Parse error in %s (module: %s, line: %d): %s',
            $filepath,
            $moduleId,
            $e->getLine(),
            $e->getMessage(),
          ),
        );
      }
    } catch (\Throwable $e) {
      if (!str_contains($e->getMessage(), 'has no effect')) {
        $this->failedFiles[] = [
          'file' => $filepath,
          'module' => $moduleId,
          'reason' => 'Runtime error: ' . $e->getMessage(),
        ];

        if ($this->debugMode) {
          error_log(
            sprintf(
              'Error loading %s (module: %s): %s in %s:%d',
              $filepath,
              $moduleId,
              $e->getMessage(),
              $e->getFile(),
              $e->getLine(),
            ),
          );
        }
      }
    } finally {
      restore_error_handler();
    }
  }

  /**
   * Extract configuration from route file without executing it
   *
   * @param string $filepath Path to route file
   * @return array Configuration array
   */
  private function extractRouteFileConfig(string $filepath): array
  {
    $content = @file_get_contents($filepath);

    if ($content === false) {
      return [
        'enableSafeRouteLoading' => false,
        'enableIsolatedGrouping' => false,
      ];
    }

    $defaults = [
      'enableSafeRouteLoading' => false,
      'enableIsolatedGrouping' => false,
    ];

    if (preg_match('/protected\s+static\s+\$enableSafeRouteLoading\s*=\s*(true|false);/', $content, $matches)) {
      $defaults['enableSafeRouteLoading'] = $matches[1] === 'true';
    }

    if (preg_match('/protected\s+static\s+\$enableIsolatedGrouping\s*=\s*(true|false);/', $content, $matches)) {
      $defaults['enableIsolatedGrouping'] = $matches[1] === 'true';
    }

    return $defaults;
  }

  /**
   * Load route file with per-route isolation
   *
   * @param string $filepath Full path to route file
   * @param string $moduleId Module identifier
   * @param array $config Configuration options
   * @return void
   */
  private function loadWithIsolation(string $filepath, string $moduleId, array $config): void
  {
    $content = file_get_contents($filepath);

    $content = preg_replace('/protected\s+static\s+\$enable\w+\s*=\s*(?:true|false);/', '', $content);
    $content = preg_replace('/<\?php/', '', $content, 1);

    $tokens = token_get_all('<?php ' . $content);
    $routeDefinitions = $this->extractRouteDefinitions($tokens);

    if (empty($routeDefinitions)) {
      if ($this->debugMode) {
        error_log("No route definitions found in {$filepath}");
      }
      return;
    }

    foreach ($routeDefinitions as $index => $definition) {
      try {
        eval($definition['code']);
      } catch (\Throwable $e) {
        if ($this->debugMode) {
          error_log(
            sprintf(
              "Route error in %s (module: %s, line: %d): %s\nCode: %s",
              basename($filepath),
              $moduleId,
              $definition['line'],
              $e->getMessage(),
              trim($definition['code']),
            ),
          );
        }
      }
    }
  }

  /**
   * Extract individual route definitions from tokenized PHP code
   *
   * @param array $tokens PHP tokens from token_get_all
   * @return array Array of route definitions with code and line number
   */
  private function extractRouteDefinitions(array $tokens): array
  {
    $definitions = [];
    $currentDefinition = '';
    $startLine = 1;
    $depth = 0;
    $inRouteCall = false;

    for ($i = 0; $i < count($tokens); $i++) {
      $token = $tokens[$i];

      if (is_array($token)) {
        [$id, $text, $line] = $token;

        if ($id === T_STRING && $text === 'Route') {
          if ($currentDefinition && $inRouteCall) {
            $definitions[] = [
              'code' => trim($currentDefinition),
              'line' => $startLine,
            ];
          }
          $currentDefinition = $text;
          $startLine = $line;
          $inRouteCall = true;
          $depth = 0;
        } else {
          $currentDefinition .= $text;
        }
      } else {
        if ($token === '(') {
          $depth++;
        } elseif ($token === ')') {
          $depth--;
        }

        if ($token === ';' && $depth === 0 && $inRouteCall) {
          $currentDefinition .= $token;
          $definitions[] = [
            'code' => trim($currentDefinition),
            'line' => $startLine,
          ];
          $currentDefinition = '';
          $inRouteCall = false;
        } else {
          $currentDefinition .= $token;
        }
      }
    }

    if ($currentDefinition && $inRouteCall) {
      $definitions[] = [
        'code' => trim($currentDefinition),
        'line' => $startLine,
      ];
    }

    return $definitions;
  }

  /**
   * Get summary of loaded and failed files
   *
   * @return array
   */
  public function getSummary(): array
  {
    return [
      'loaded' => count($this->loadedFiles),
      'failed' => count($this->failedFiles),
      'failed_details' => $this->failedFiles,
    ];
  }
}
