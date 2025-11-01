<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\UI\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Webkernel\Arcanes\QueryModules;
use Webkernel\Arcanes\Common\ArcanesMethods;

class MakeComponent extends Command
{
    protected $signature = 'make:ui-component
                            {name? : The name of the component}
                            {--module= : Target module (optional)}
                            {--path= : Custom path for the component}
                            {--force : Overwrite existing files}';

    protected $description = 'Create a new UI component in discovered modules';

    private string $componentName;
    private string $componentPath;
    private string $componentNamespace;
    private bool $isForce;
    private ?string $targetModule;
    private ?string $customPath;

    /**
     * Default component structure - all components use the same template
     */
    private array $componentStructure = [
        'index.blade.php' => 'component.blade.stub',
        '{{ className }}.php' => 'Component.stub',
        '{{ snakeName }}.js' => null,  // Empty file
        '{{ snakeName }}.css' => null, // Empty file
    ];

    public function handle(): int
    {
        $this->displayWelcome();
        $this->configureOptions();
        $this->configureComponentPath();
        $this->configureNamespace();

        $this->displayRecap();
        if (!$this->confirm('Create this component?', true)) {
            return static::SUCCESS;
        }

        return $this->createComponent();
    }

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

  ██╗   ██╗██╗               █████╗ ██████╗ ████████╗██╗████████╗██╗   ██╗██████╗ ███████╗███████╗
  ██║   ██║██║    ██╗██╗    ██╔══██╗██╔══██╗╚══██╔══╝██║╚══██╔══╝██║   ██║██╔══██╗██╔════╝██╔════╝
  ██║   ██║██║    ╚═╝╚═╝    ███████║██████╔╝   ██║   ██║   ██║   ██║   ██║██║  ██║█████╗  ███████╗
  ██║   ██║██║    ██╗██╗    ██╔══██║██╔═══╝    ██║   ██║   ██║   ██║   ██║██║  ██║██╔══╝  ╚════██║
  ╚██████╔╝██║    ╚═╝╚═╝    ██║  ██║██║        ██║   ██║   ██║   ╚██████╔╝██████╔╝███████╗███████║
   ╚═════╝ ╚═╝              ╚═╝  ╚═╝╚═╝        ╚═╝   ╚═╝   ╚═╝    ╚═════╝ ╚═════╝ ╚══════╝╚══════╝

  ');
        $this->line('  WebKernel\UI - Component Generator');
        $this->line('  Lightning-fast • Smart Discovery • Auto-Configuration');
        $this->line('  Creates UI components in discovered modules');
        $this->newLine();
    }

    private function configureOptions(): void
    {
        $this->isForce = $this->option('force');
        $this->targetModule = $this->option('module');
        $this->customPath = $this->option('path');

        // Get component name interactively if not provided
        $this->componentName = $this->argument('name') ?: $this->ask('What is the component name?', 'MyComponent');
        $this->componentName = ArcanesMethods::prepareName($this->componentName);

        if ($this->targetModule) {
            $this->info("Target module: {$this->targetModule}");
        }

        if ($this->customPath) {
            $this->info("Custom path: {$this->customPath}");
        }
    }


    private function configureComponentPath(): void
    {
        if ($this->customPath) {
            $this->componentPath = ArcanesMethods::buildPath($this->customPath, 'components', $this->componentName);
            return;
        }

        $modules = QueryModules::make()->get();

        if (empty($modules)) {
            $this->error('No modules discovered. Run php artisan webkernel:refresh first.');
            return;
        }

        if ($this->targetModule) {
            $module = collect($modules)->firstWhere('name', $this->targetModule);
            if (!$module) {
                $this->error("Module '{$this->targetModule}' not found.");
                $this->line('Available modules:');
                foreach ($modules as $mod) {
                    $this->line("  - {$mod['name']}");
                }
                return;
            }
            $viewsPath = $module['viewsPath'] ?? $module['basePath'] . '/Resources/Views';
            $this->componentPath = ArcanesMethods::buildPath($viewsPath, 'components', $this->componentName);
        } else {
            if (count($modules) === 1) {
                $module = $modules[0];
                $viewsPath = $module['viewsPath'] ?? $module['basePath'] . '/Resources/Views';
                $this->componentPath = ArcanesMethods::buildPath($viewsPath, 'components', $this->componentName);
            } else {
                $moduleNames = array_column($modules, 'name');
                $selectedModule = $this->choice('Which module would you like to use?', $moduleNames, 0);
                $module = collect($modules)->firstWhere('name', $selectedModule);
                $viewsPath = $module['viewsPath'] ?? $module['basePath'] . '/Resources/Views';
                $this->componentPath = ArcanesMethods::buildPath($viewsPath, 'components', $this->componentName);
            }
        }
    }

    private function configureNamespace(): void
    {
        $composerPsr4 = ArcanesMethods::loadComposerPsr4();
        $resolvedNamespace = ArcanesMethods::resolveComponentNamespace($this->componentPath, $composerPsr4, $this->componentName);

        if ($resolvedNamespace) {
            $this->componentNamespace = $resolvedNamespace;
        } else {
            $suggestedNamespace = ArcanesMethods::generateComponentFallbackNamespace($this->componentPath, $this->componentName);
            $this->componentNamespace = $this->ask('Enter the namespace for this component', $suggestedNamespace);
        }
    }

    private function displayRecap(): void
    {
        $this->newLine();
        $this->line('<fg=cyan>Component Configuration:</fg=cyan>');
        $this->line("  Name: <fg=yellow>{$this->componentName}</fg=yellow>");
        $this->line("  Path: <fg=yellow>{$this->componentPath}</fg=yellow>");
        $this->line("  Namespace: <fg=yellow>{$this->componentNamespace}</fg=yellow>");
        $this->line("  Class: <fg=yellow>{$this->componentNamespace}\\{$this->componentName}</fg=yellow>");
        $this->newLine();
    }

    private function createComponent(): int
    {
        $this->info("Creating component: {$this->componentName}");

        if (!$this->isForce && File::exists($this->componentPath)) {
            if (!$this->confirm("Component {$this->componentName} already exists at {$this->componentPath}. Overwrite?", false)) {
                $this->error("Component {$this->componentName} already exists");
                return static::FAILURE;
            }
        }

        File::makeDirectory($this->componentPath, 0755, true);

        $this->createComponentFiles();

        $this->newLine();
        $this->components->info("Component [{$this->componentName}] created successfully at {$this->componentPath}");

        $this->displayNextSteps();

        return static::SUCCESS;
    }

    private function createComponentFiles(): void
    {
        $stubPaths = [
            __DIR__ . '/Stubs/',
            base_path('webkernel/src/Aptitudes/UI/Commands/Stubs/'),
        ];

        $replacements = [
            '{{ namespace }}' => $this->componentNamespace,
            '{{ className }}' => $this->componentName,
            '{{ componentName }}' => $this->componentName,
            '{{ kebabName }}' => Str::kebab($this->componentName),
            '{{ snakeName }}' => Str::snake($this->componentName),
        ];

        foreach ($this->componentStructure as $fileName => $stubFile) {
            // Replace placeholders in filename
            $actualFileName = ArcanesMethods::replacePlaceholders($fileName, $replacements);

            if ($stubFile === null) {
                // Create empty file
                $filePath = ArcanesMethods::buildPath($this->componentPath, null, $actualFileName);
                File::put($filePath, '');
                $this->line("Created empty file: {$actualFileName}");
                continue;
            }

            $stub = ArcanesMethods::getStub($stubFile, $stubPaths);

            if ($stub === null) {
                $this->warn("Stub file {$stubFile} not found, creating empty file: {$actualFileName}");
                $filePath = ArcanesMethods::buildPath($this->componentPath, null, $actualFileName);
                File::put($filePath, '');
                continue;
            }

            $content = ArcanesMethods::replacePlaceholders($stub, $replacements);

            $filePath = ArcanesMethods::buildPath($this->componentPath, null, $actualFileName);
            File::put($filePath, $content);
            $this->line("Created file from stub: {$actualFileName}");
        }
    }

    private function displayNextSteps(): void
    {
        $this->newLine();
        $this->line('<fg=yellow>Next steps:</fg=yellow>');
        $this->line('1. Configure your component in the Component.php file');
        $this->line('2. Customize the Blade template in index.blade.php');
        $this->line('3. Use your component: <x-ui::' . Str::kebab($this->componentName) . ' />');
        $this->line('4. Run <fg=cyan>php artisan webkernel:refresh</fg=cyan> to refresh module discovery');
        $this->newLine();
    }
}
