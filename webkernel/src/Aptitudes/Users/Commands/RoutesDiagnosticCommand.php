<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

/**
 * Diagnostic command to check route registration
 */
class RoutesDiagnosticCommand extends Command
{
  protected $signature = 'routes:diagnostic {uri? : Specific URI to search for}';

  protected $description = 'Diagnose route loading issues and check if routes are registered';

  public function handle(): int
  {
    $uri = $this->argument('uri');

    if ($uri) {
      $this->checkSpecificRoute($uri);
    } else {
      $this->showRouteSummary();
    }

    return Command::SUCCESS;
  }

  private function checkSpecificRoute(string $uri): void
  {
    $routes = Route::getRoutes();
    $found = false;

    $this->info("Searching for routes matching: {$uri}");
    $this->newLine();

    foreach ($routes as $route) {
      if (str_contains($route->uri(), $uri)) {
        $found = true;
        $this->line("URI: {$route->uri()}");
        $this->line('Name: ' . ($route->getName() ?? 'N/A'));
        $this->line('Method: ' . implode('|', $route->methods()));
        $this->line('Action: ' . $route->getActionName());
        $this->newLine();
      }
    }

    if (!$found) {
      $this->error("No routes found matching: {$uri}");
      $this->newLine();
      $this->warn('Possible issues:');
      $this->line('1. Route file not loaded by RouteLoader');
      $this->line('2. Syntax error in route file');
      $this->line('3. Route registered under different URI');
      $this->line('4. Module not registered in WebkernelManager');
    }
  }

  private function showRouteSummary(): void
  {
    $routes = Route::getRoutes();
    $total = count($routes);

    $this->info("Total routes registered: {$total}");
    $this->newLine();

    $byMethod = [];
    foreach ($routes as $route) {
      foreach ($route->methods() as $method) {
        $byMethod[$method] = ($byMethod[$method] ?? 0) + 1;
      }
    }

    $this->table(['Method', 'Count'], collect($byMethod)->map(fn($count, $method) => [$method, $count])->toArray());

    $this->newLine();
    $this->line('To check a specific route: php artisan routes:diagnostic /debug-system');
  }
}
