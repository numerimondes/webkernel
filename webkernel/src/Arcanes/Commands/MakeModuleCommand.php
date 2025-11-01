<?php
declare(strict_types=1);

namespace Webkernel\Arcanes\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Webkernel\Arcanes\Runtime\WebkernelManager;
use Webkernel\Arcanes\Support\Base\ArcanesMethods;

class MakeModuleCommand extends Command
{
  protected $signature = 'make:arcanes-module
                            {name? : The name of the module}
                            {--path= : Custom path for the module}
                            {--spath= : Strict path - no sub-path prompt}
                            {--modules= : Comma-separated list of modules to create}
                            {--bulk=1 : Number of modules to generate (for demo/testing)}
                            {--force : Overwrite existing files}';

  protected $description = 'Create a new Arcanes module with interactive CLI';

  private WebkernelManager $manager;
  private string $moduleName;
  private string $modulePath;
  private string $fullModulePath;
  private string $moduleNamespace;
  private int $bulkCount;
  private bool $isForce;
  private array $discoveryPaths;
  private array $composerPsr4;
  private ?string $strictPath;
  private ?array $modulesList;

  /**
   * Module structure mapping directories to their stubs
   * null means create .gitkeep, array means use stubs
   */
  private array $moduleStructure = [
    'Console' => ['Command.stub'],
    'Database/Migrations' => null,
    'Database/Factories' => null,
    'Database/Seeders' => null,
    'Http/Controllers' => ['Controller.stub'],
    'Http/Middleware' => ['Middleware.stub'],
    'Http/Requests' => ['Request.stub'],
    'Resources/Views' => ['view.blade.stub'],
    'Resources/Assets' => null,
    'Routes' => ['web.stub', 'api.stub'],
    'Services' => ['Service.stub'],
    'Models' => ['Model.stub'],
    'Helpers' => ['helpers.stub'],
    'Config' => ['config.stub'],
    'Lang' => null,
    'Tests/Feature' => null,
    'Tests/Unit' => null,
  ];

  public function handle(): int
  {
    $this->manager = app(WebkernelManager::class);

    $this->displayWelcome();

    $this->loadComposerPsr4();
    $this->configureOptions();

    if ($this->modulesList) {
      return $this->handleModulesListGeneration();
    }

    $this->configureDiscoveryPaths();
    $this->configureModulePath();
    $this->configureNamespace();

    if ($this->bulkCount > 1) {
      $this->displayBulkRecap();
      if (!$this->confirm('Proceed with bulk generation?', true)) {
        return static::SUCCESS;
      }
      return $this->handleBulkGeneration();
    }

    $this->displayRecap();
    if (!$this->confirm('Create this module?', true)) {
      return static::SUCCESS;
    }

    return $this->handleSingleGeneration();
  }

  /**
   * Display welcome message ARCANES
   */
  private function displayWelcome(): void
  {
    $this->newLine();
    $this->line('
  ██╗    ██╗███████╗██████╗ ██╗  ██╗███████╗██████╗ ███╗   ██╗███████╗██╗
  ██║    ██║██╔════╝██╔══██╗██║ ██╔╝██╔════╝██╔══██╗████╗  ██║██╔════╝██║
  ██║ █╗ ██║█████╗  ██████╔╝█████╔╝ █████╗  ██████╔╝██╔██╗ ██║█████╗  ██║
  ██║███╗██║██╔══╝  ██╔══██╗██╔═██╗ ██╔══╝  ██╔══██╗██║╚██╗██║██╔══╝  ██║
  ╚███╔███╔╝███████╗██████╔╝██║  ██╗███████╗██║  ██║██║ ╚████║███████╗███████╗
   ╚══╝╚══╝ ╚══════╝╚═════╝ ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝╚═╝  ╚═══╝╚══════╝╚══════╝

  ██╗       █████╗ ██████╗  ██████╗ █████╗ ███╗   ██╗███████╗███████╗
  ╚██╗     ██╔══██╗██╔══██╗██╔════╝██╔══██╗████╗  ██║██╔════╝██╔════╝
   ╚██╗    ███████║██████╔╝██║     ███████║██╔██╗ ██║█████╗  ███████╗
   ██╔╝    ██╔══██║██╔══██╗██║     ██╔══██║██║╚██╗██║██╔══╝  ╚════██║
  ██╔╝     ██║  ██║██║  ██║╚██████╗██║  ██║██║ ╚████║███████╗███████║    ██╗██╗██╗
  ╚═╝      ╚═╝  ╚═╝╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝╚═╝  ╚═══╝╚══════╝╚══════╝    ╚═╝╚═╝╚═╝
  ');
    $this->line('  WebKernel\Arcanes - Advanced Module Architecture Generator');
    $this->line('  Lightning-fast • Smart Discovery • Auto-Configuration');
    $this->line('  Creates production-ready modules with enterprise best practices');
    $this->newLine();
  }

  private function loadComposerPsr4(): void
  {
    $this->composerPsr4 = ArcanesMethods::loadComposerPsr4();
  }

  private function configureOptions(): void
  {
    $this->bulkCount = (int) $this->option('bulk');
    $this->isForce = $this->option('force');
    $this->strictPath = $this->option('spath');

    $modulesOption = $this->option('modules');
    if ($modulesOption) {
      $this->modulesList = array_map('trim', explode(',', $modulesOption));
      $this->modulesList = array_filter($this->modulesList);

      $this->info('Modules list generation mode: ' . count($this->modulesList) . ' modules');

      $this->moduleName = 'Dummy';
    } else {
      $this->modulesList = null;
      $this->moduleName = $this->argument('name') ?: $this->ask('What is the module name?', 'DemoModule');
      $this->moduleName = ArcanesMethods::prepareName($this->moduleName);
    }

    if ($this->bulkCount > 1) {
      $this->info("Bulk generation mode: {$this->bulkCount} modules");
    }
  }

  private function configureDiscoveryPaths(): void
  {
    $this->discoveryPaths = ArcanesMethods::getDiscoveryPaths();

    if ($this->strictPath) {
      $this->modulePath = $this->strictPath;
      $this->info("Using strict path: {$this->modulePath}");
      return;
    }

    if ($this->option('path')) {
      $customPath = $this->option('path');
      if (!in_array($customPath, $this->discoveryPaths)) {
        $this->discoveryPaths[] = $customPath;
      }
      $this->modulePath = $customPath;
      $this->info("Using custom path: {$this->modulePath}");
      return;
    }

    if (count($this->discoveryPaths) === 1) {
      $this->modulePath = $this->discoveryPaths[0];
      return;
    }

    $this->modulePath = $this->choice('Which base path would you like to use?', $this->discoveryPaths, 0);
  }

  private function configureModulePath(): void
  {
    if ($this->strictPath) {
      $this->fullModulePath = ArcanesMethods::buildPath($this->strictPath, null, $this->moduleName);
    } else {
      $subPath = $this->ask('Enter the sub-path within the base path (optional)', '');

      if ($subPath) {
        $this->fullModulePath = ArcanesMethods::buildPath($this->modulePath, $subPath, $this->moduleName);
      } else {
        $this->fullModulePath = ArcanesMethods::buildPath($this->modulePath, null, $this->moduleName);
      }
    }
  }

  private function configureNamespace(): void
  {
    $resolvedNamespace = ArcanesMethods::resolveNamespaceFromPath($this->fullModulePath, $this->composerPsr4);

    if ($resolvedNamespace) {
      $this->moduleNamespace = $resolvedNamespace;
    } else {
      $suggestedNamespace = ArcanesMethods::generateFallbackNamespace($this->fullModulePath);
      $this->moduleNamespace = $this->ask('Enter the namespace for this module', $suggestedNamespace);
    }
  }

  private function displayRecap(): void
  {
    $this->newLine();
    $this->line('<fg=cyan>Module Configuration:</fg=cyan>');
    $this->line("  Name: <fg=yellow>{$this->moduleName}</fg=yellow>");
    $this->line("  Path: <fg=yellow>{$this->fullModulePath}</fg=yellow>");
    $this->line("  Namespace: <fg=yellow>{$this->moduleNamespace}</fg=yellow>");
    $this->line("  Class: <fg=yellow>{$this->moduleNamespace}\\{$this->moduleName}Module</fg=yellow>");
    $this->newLine();
  }

  private function displayBulkRecap(): void
  {
    $this->newLine();
    $this->line('<fg=cyan>Bulk Generation Configuration:</fg=cyan>');
    $this->line("  Base Name: <fg=yellow>{$this->moduleName}</fg=yellow>");
    $this->line("  Count: <fg=yellow>{$this->bulkCount}</fg=yellow>");
    $this->line("  Base Path: <fg=yellow>{$this->fullModulePath}</fg=yellow>");
    $this->line("  Base Namespace: <fg=yellow>{$this->moduleNamespace}</fg=yellow>");
    $this->newLine();
    $this->line(
      '  Generated modules will be named: ' . $this->moduleName . '0001, ' . $this->moduleName . '0002, etc.',
    );
    $this->newLine();
  }

  private function handleSingleGeneration(): int
  {
    $this->info("Creating module: {$this->moduleName}");

    $moduleDir = $this->createModule($this->moduleName, $this->fullModulePath, $this->moduleNamespace);

    $this->newLine();
    $this->components->info("Module [{$this->moduleName}] created successfully at {$moduleDir}");

    $this->displayNextSteps();

    if ($this->confirm('Create another module?', false)) {
      $this->call('make:arcanes-module');
    }

    return static::SUCCESS;
  }

  private function handleBulkGeneration(): int
  {
    $this->info("Generating {$this->bulkCount} demo modules...");

    $progressBar = $this->output->createProgressBar($this->bulkCount);
    $progressBar->start();

    $created = [];

    for ($i = 1; $i <= $this->bulkCount; $i++) {
      $moduleName = $this->moduleName . sprintf('%04d', $i);
      $modulePath = dirname($this->fullModulePath) . '/' . $moduleName;
      $moduleNamespace = dirname(str_replace('\\', '/', $this->moduleNamespace)) . '\\' . $moduleName;
      $moduleNamespace = str_replace('/', '\\', $moduleNamespace);

      try {
        $moduleDir = $this->createModule($moduleName, $modulePath, $moduleNamespace);
        $created[] = $moduleName;
        $progressBar->advance();
      } catch (\Exception $e) {
        $this->error("Failed to create {$moduleName}: " . $e->getMessage());
      }
    }

    $progressBar->finish();

    $this->newLine(2);
    $this->components->info('Successfully created ' . count($created) . ' modules');

    if (count($created) !== $this->bulkCount) {
      $failed = $this->bulkCount - count($created);
      $this->components->warn("{$failed} modules failed to generate");
    }

    return static::SUCCESS;
  }

  private function handleModulesListGeneration(): int
  {
    $this->info('Generating ' . count($this->modulesList) . ' specific modules...');

    if ($this->strictPath) {
      $basePath = $this->strictPath;
      $this->info("Using strict path: {$basePath}");
    } else {
      $this->configureDiscoveryPaths();
      $basePath = $this->modulePath;
    }

    $progressBar = $this->output->createProgressBar(count($this->modulesList));
    $progressBar->start();

    $created = [];
    $failed = [];

    foreach ($this->modulesList as $moduleName) {
      $moduleName = ArcanesMethods::prepareName($moduleName);
      $modulePath = ArcanesMethods::buildPath($basePath, null, $moduleName);

      $moduleNamespace = ArcanesMethods::resolveNamespaceFromPath($modulePath, $this->composerPsr4);
      if (!$moduleNamespace) {
        $moduleNamespace = ArcanesMethods::generateFallbackNamespace($modulePath);
      }

      try {
        $moduleDir = $this->createModule($moduleName, $modulePath, $moduleNamespace);
        $created[] = $moduleName;
        $progressBar->advance();
      } catch (\Exception $e) {
        $failed[] = ['name' => $moduleName, 'error' => $e->getMessage()];
        $progressBar->advance();
      }
    }

    $progressBar->finish();

    $this->newLine(2);
    $this->components->info('Successfully created ' . count($created) . ' modules');

    if (!empty($failed)) {
      $this->newLine();
      $this->components->warn('Failed to create ' . count($failed) . ' modules:');
      foreach ($failed as $failure) {
        $this->line("  - {$failure['name']}: {$failure['error']}");
      }
    }

    if (!empty($created)) {
      $this->newLine();
      $this->line('<fg=yellow>Created modules:</fg=yellow>');
      foreach ($created as $moduleName) {
        $this->line("  - {$moduleName}");
      }
    }

    return static::SUCCESS;
  }

  private function createModule(string $moduleName, string $modulePath, string $moduleNamespace): string
  {
    if (!$this->isForce && File::exists($modulePath)) {
      if (!$this->confirm("Module {$moduleName} already exists at {$modulePath}. Overwrite?", false)) {
        throw new \RuntimeException("Module {$moduleName} already exists");
      }
    }

    File::makeDirectory($modulePath, 0755, true);

    $this->createModuleClass($moduleName, $modulePath, $moduleNamespace);

    $this->createDirectoryStructure($modulePath);

    return $modulePath;
  }

  private function createModuleClass(string $moduleName, string $modulePath, string $moduleNamespace): void
  {
    $className = "{$moduleName}Module";

    $stubPaths = [__DIR__ . '/Stubs/', base_path('webkernel/src/Arcanes/Commands/Stubs/')];

    $stub = ArcanesMethods::getStub('Module.stub', $stubPaths);

    $content = ArcanesMethods::replacePlaceholders($stub, [
      '{{ namespace }}' => $moduleNamespace,
      '{{ className }}' => $className,
      '{{ moduleId }}' => Str::kebab($moduleName),
      '{{ moduleName }}' => $moduleName,
    ]);

    File::put($modulePath . '/' . $className . '.php', $content);
  }

  private function createDirectoryStructure(string $moduleDir): void
  {
    $stubPaths = [__DIR__ . '/Stubs/', base_path('webkernel/src/Arcanes/Commands/Stubs/')];

    ArcanesMethods::createDirectoryStructure($moduleDir, $this->moduleStructure, $stubPaths);
  }

  private function displayNextSteps(): void
  {
    $this->newLine();
    $this->line('<fg=yellow>Next steps:</fg=yellow>');
    $this->line('1. Configure your module in the configureModule() method');
    $this->line('2. Add your routes, controllers, and services');
    $this->line('3. Register any service providers in the providers() method');
    $this->line('4. Run <fg=cyan>php artisan webkernel:refresh</fg=cyan> to refresh module discovery');
    $this->newLine();
  }
}
