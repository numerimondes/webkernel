<?php

declare(strict_types=1);

namespace Webkernel\Console\Package;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Console command to prepare composer.json with platform autoload, dependencies, and repositories,
 * fix namespace violations, and register service providers.
 */
class PlatformComposer extends Command
{
 private const AUTOLOAD_STANDARD_CHECK = 'psr-4';

 protected $signature = 'webkernel:prepare-platform-composer {--fix-namespaces : Fix namespace violations in module files} {--register-providers : Register service providers in bootstrap/providers.php}';
 protected $description = 'Prepare composer.json with platform autoload, dependencies, and repositories, fix namespaces, and register providers';

 /**
 * Execute the console command.
 *
 * @return int
 */
 public function handle(): int
 {
 $this->displayHeader();

 $composerPath = base_path('composer.json');

 if (!File::exists($composerPath)) {
 $this->error('composer.json not found at project root.');
 return self::FAILURE;
 }

 $composerContent = File::get($composerPath);
 $composerJson = json_decode($composerContent, true);

 if (!is_array($composerJson)) {
 $this->error('Invalid composer.json format.');
 return self::FAILURE;
 }

 if ($this->option('fix-namespaces')) {
 $this->info('Fixing namespace violations...');
 $namespaceFixer = new PlatformNamespaceFixer($this);
 $namespaceFixer->fixAllNamespaceViolations();
 }

 $this->checkPsr4Compliance();

 $generator = new PlatformComposerGenerator();
 $additions = $generator->getComposerAdditions();

 $hasChanges = false;
 $changes = [];

 $hasChanges |= $this->processAutoloadPsr4($composerJson, $additions, $generator, $changes);
 $hasChanges |= $this->processAutoloadFiles($composerJson, $additions, $generator, $changes);
 $hasChanges |= $this->processRequirements($composerJson, $additions, $changes);
 $hasChanges |= $this->processRepositories($composerJson, $additions, $changes);

 if ($hasChanges) {
 $this->saveComposerJson($composerPath, $composerJson);
 $this->displayChanges($changes);
 $this->info('composer.json updated successfully.');
 } else {
 $this->info('No changes needed: composer.json is up to date.');
 }

 if ($this->option('register-providers')) {
 $this->registerServiceProviders();
 }

 return self::SUCCESS;
 }

 /**
 * Check PSR-4 compliance for all module paths and packages.
 */
 protected function checkPsr4Compliance(): void
 {
 $this->info('Checking PSR-4 compliance...');

 $generator = new PlatformComposerGenerator();
 $modulePaths = $generator->findAllModulePaths();
 $violations = [];

 // Check module paths
 foreach ($modulePaths as $modulePath => $expectedNamespace) {
 $moduleViolations = $this->scanModuleForViolations($modulePath, $expectedNamespace);
 $violations = array_merge($violations, $moduleViolations);
 }

 // Check webkernel package
 $webkernelViolations = $this->scanModuleForViolations('packages/webkernel/src/', 'Webkernel\\');
 $violations = array_merge($violations, $webkernelViolations);

 if (!empty($violations)) {
 $this->warn('PSR-4 violations detected. Fixing automatically...');
 $this->fixPsr4Violations($violations);
 $this->info('PSR-4 violations fixed.');
 } else {
 $this->info('No PSR-4 violations found.');
 }
 }

 /**
 * Scan a module or package for PSR-4 violations.
 *
 * @param string $modulePath
 * @param string $expectedNamespace
 * @return array
 */
 protected function scanModuleForViolations(string $modulePath, string $expectedNamespace): array
 {
 $violations = [];
 $phpFiles = $this->findAllPhpFiles(base_path($modulePath));

 foreach ($phpFiles as $filePath) {
 $violation = $this->checkFileCompliance($filePath, $modulePath, $expectedNamespace);
 if ($violation) {
 $violations[] = $violation;
 }
 }

 return $violations;
 }

 /**
 * Check if a file complies with PSR-4 standards.
 *
 * @param string $filePath
 * @param string $modulePath
 * @param string $expectedBaseNamespace
 * @return array|null
 */
 protected function checkFileCompliance(string $filePath, string $modulePath, string $expectedBaseNamespace): ?array
 {
 if (!File::exists($filePath)) {
 return null;
 }

 $content = File::get($filePath);
 $currentNamespace = $this->extractCurrentNamespace($content);
 $className = $this->extractClassName($content);

 if (!$className) {
 return null;
 }

 $relativePath = str_replace(base_path($modulePath), '', $filePath);
 $relativePath = trim($relativePath, '/');
 $expectedFullNamespace = $this->calculateExpectedNamespace($expectedBaseNamespace, $relativePath);

 if ($currentNamespace !== $expectedFullNamespace) {
 return [
 'file' => $filePath,
 'current_namespace' => $currentNamespace,
 'expected_namespace' => $expectedFullNamespace,
 'class_name' => $className,
 'relative_path' => $relativePath,
 'module_path' => $modulePath,
 ];
 }

 return null;
 }

 /**
 * Fix PSR-4 violations in the provided files.
 *
 * @param array $violations
 */
 protected function fixPsr4Violations(array $violations): void
 {
 foreach ($violations as $violation) {
 $this->fixSingleViolation($violation);
 }
 }

 /**
 * Fix a single PSR-4 violation.
 *
 * @param array $violation
 */
 protected function fixSingleViolation(array $violation): void
 {
 $filePath = $violation['file'];
 $expectedNamespace = $violation['expected_namespace'];
 $currentNamespace = $violation['current_namespace'];
 $className = $violation['class_name'];

 if (!File::exists($filePath)) {
 return;
 }

 $content = File::get($filePath);
 $originalContent = $content;

 if ($currentNamespace && $currentNamespace !== $expectedNamespace) {
 $content = preg_replace(
 '/^namespace\s+' . preg_quote($currentNamespace, '/') . '\s*;/m',
 "namespace {$expectedNamespace};",
 $content
 );
 } elseif (!$currentNamespace) {
 $content = $this->addNamespaceDeclaration($content, $expectedNamespace);
 }

 $newClassName = $this->resolveClassNameConflicts($className, $expectedNamespace);
 if ($newClassName !== $className) {
 $content = $this->replaceClassName($content, $className, $newClassName);
 }

 if ($content !== $originalContent) {
 File::put($filePath, $content);
 $relativePath = str_replace(base_path() . '/', '', $filePath);
 $this->line(" Fixed PSR-4 violation: {$relativePath} -> {$expectedNamespace}\\{$newClassName}");
 }
 }

 /**
 * Find all PHP files in a directory.
 *
 * @param string $directory
 * @return array
 */
 protected function findAllPhpFiles(string $directory): array
 {
 $phpFiles = [];

 if (!is_dir($directory)) {
 return $phpFiles;
 }

 try {
 $iterator = new \RecursiveIteratorIterator(
 new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
 );

 foreach ($iterator as $file) {
 if ($file->isFile() && $file->getExtension() === 'php') {
 $phpFiles[] = $file->getPathname();
 }
 }
 } catch (\Exception $e) {
 $this->warn("Error scanning directory {$directory}: {$e->getMessage()}");
 }

 return $phpFiles;
 }

 /**
 * Extract the namespace from a PHP file's content.
 *
 * @param string $content
 * @return string|null
 */
 protected function extractCurrentNamespace(string $content): ?string
 {
 if (preg_match('/^namespace\s+([^;]+);/m', $content, $matches)) {
 return trim($matches[1]);
 }
 return null;
 }

 /**
 * Extract the class name from a PHP file's content.
 *
 * @param string $content
 * @return string|null
 */
 protected function extractClassName(string $content): ?string
 {
 if (preg_match('/^(?:abstract\s+)?(?:final\s+)?(?:class|interface|trait)\s+(\w+)/m', $content, $matches)) {
 return $matches[1];
 }
 return null;
 }

 /**
 * Calculate the expected namespace for a file based on its path.
 *
 * @param string $baseNamespace The base namespace (e.g., 'Numerimondes\Modules\ReamMar\')
 * @param string $relativePath The relative path to the file (e.g., 'Policies/ClientPolicy.php')
 * @return string The expected namespace (e.g., 'Numerimondes\Modules\ReamMar\Policies')
 */
 protected function calculateExpectedNamespace(string $baseNamespace, string $relativePath): string
 {
 $pathParts = explode('/', dirname($relativePath));
 $pathParts = array_filter($pathParts, fn($part) => $part !== '.' && $part !== '');

 $namespaceParts = [rtrim($baseNamespace, '\\')];
 foreach ($pathParts as $part) {
 $namespaceParts[] = $this->capitalizeNamespacePart($part);
 }

 return implode('\\', $namespaceParts);
 }

 /**
 * Capitalize a namespace part.
 *
 * @param string $part
 * @return string
 */
 protected function capitalizeNamespacePart(string $part): string
 {
 return str_replace(['-', '_'], '', ucwords($part, '-_'));
 }

 /**
 * Resolve class name conflicts by appending the module name if necessary.
 *
 * @param string $className
 * @param string $namespace
 * @return string
 */
 protected function resolveClassNameConflicts(string $className, string $namespace): string
 {
 $conflictingClasses = [
 'AppServiceProvider',
 'Controller',
 'Model',
 'Factory',
 'Seeder',
 ];

 if (in_array($className, $conflictingClasses)) {
 $moduleName = $this->extractModuleFromNamespace($namespace);
 return $moduleName . $className;
 }

 return $className;
 }

 /**
 * Extract the module name from a namespace.
 *
 * @param string $namespace
 * @return string
 */
 protected function extractModuleFromNamespace(string $namespace): string
 {
 $parts = explode('\\', $namespace);
 $moduleIndex = array_search('Modules', $parts);

 return $moduleIndex !== false && isset($parts[$moduleIndex + 1]) ? $parts[$moduleIndex + 1] : 'Module';
 }

 /**
 * Replace class names in the content to resolve conflicts.
 *
 * @param string $content
 * @param string $oldName
 * @param string $newName
 * @return string
 */
 protected function replaceClassName(string $content, string $oldName, string $newName): string
 {
 $patterns = [
 '/^((?:abstract\s+)?(?:final\s+)?(?:class|interface|trait)\s+)' . preg_quote($oldName, '/') . '\b/m',
 '/\b' . preg_quote($oldName, '/') . '::class\b/',
 '/\bnew\s+' . preg_quote($oldName, '/') . '\s*\(/m',
 ];

 $replacements = [
 '${1}' . $newName,
 $newName . '::class',
 'new ' . $newName . '(',
 ];

 return preg_replace($patterns, $replacements, $content);
 }

 /**
 * Add a namespace declaration to a PHP file.
 *
 * @param string $content
 * @param string $namespace
 * @return string
 */
 protected function addNamespaceDeclaration(string $content, string $namespace): string
 {
 if (preg_match('/^<\?php\s*$/m', $content)) {
 return preg_replace('/^<\?php\s*$/m', "<?php\n\nnamespace {$namespace};", $content, 1);
 }

 return "<?php\n\nnamespace {$namespace};\n\n" . ltrim($content, "<?php\n ");
 }

 /**
 * Process PSR-4 autoload entries in composer.json.
 *
 * @param array $composerJson
 * @param array $additions
 * @param PlatformComposerGenerator $generator
 * @param array $changes
 * @return bool
 */
 
protected function processAutoloadPsr4(array &$composerJson, array $additions, PlatformComposerGenerator $generator, array &$changes): bool
{
    $hasChanges = false;

    $composerJson['autoload'] = $composerJson['autoload'] ?? [];
    $composerJson['autoload']['psr-4'] = $composerJson['autoload']['psr-4'] ?? [];


    $pathToNamespace = [];
    foreach ($additions['autoload']['psr-4'] ?? [] as $namespace => $path) {
        $pathToNamespace[$path] = $namespace;
        if (!isset($composerJson['autoload']['psr-4'][$namespace]) ||
            $composerJson['autoload']['psr-4'][$namespace] !== $path) {
            $composerJson['autoload']['psr-4'][$namespace] = $path;
            $changes['added'][] = "PSR-4: \"{$namespace}\" => \"{$path}\"";
            $hasChanges = true;
        }
    }


    $protectedNamespaces = array_keys(PlatformComposerGenerator::getStaticPathMappings($additions['root_namespace'] ?? 'App'));

    $namespacesToRemove = [];
    foreach ($composerJson['autoload']['psr-4'] as $namespace => $path) {
        if (in_array($namespace, $protectedNamespaces, true)) {
            continue; 
        }

        if ($generator->shouldRemoveNamespace($namespace, $path, $pathToNamespace)) {
            $namespacesToRemove[] = $namespace;
            $changes['removed'][] = "PSR-4: \"{$namespace}\" (path not found or conflicting: {$path})";
            $hasChanges = true;
        }
    }

    foreach ($namespacesToRemove as $namespace) {
        unset($composerJson['autoload']['psr-4'][$namespace]);
    }

    return $hasChanges;
}

 /**
 * Process autoload files in composer.json.
 *
 * @param array $composerJson
 * @param array $additions
 * @param PlatformComposerGenerator $generator
 * @param array $changes
 * @return bool
 */
 protected function processAutoloadFiles(array &$composerJson, array $additions, PlatformComposerGenerator $generator, array &$changes): bool
 {
 $hasChanges = false;

 $composerJson['autoload']['files'] = $composerJson['autoload']['files'] ?? [];

 foreach ($additions['autoload']['files'] ?? [] as $file) {
 if (!in_array($file, $composerJson['autoload']['files'])) {
 $composerJson['autoload']['files'][] = $file;
 $changes['added'][] = "File: \"{$file}\"";
 $hasChanges = true;
 }
 }

 $filesToRemove = [];
 foreach ($composerJson['autoload']['files'] as $index => $file) {
 if ($generator->shouldRemoveFile($file)) {
 $filesToRemove[] = $index;
 $changes['removed'][] = "File: \"{$file}\" (file not found)";
 $hasChanges = true;
 }
 }

 foreach (array_reverse($filesToRemove) as $index) {
 unset($composerJson['autoload']['files'][$index]);
 }

 $composerJson['autoload']['files'] = array_values($composerJson['autoload']['files']);

 if (empty($composerJson['autoload']['files'])) {
 unset($composerJson['autoload']['files']);
 }

 return $hasChanges;
 }

 /**
 * Process required dependencies in composer.json.
 *
 * @param array $composerJson
 * @param array $additions
 * @param array $changes
 * @return bool
 */
 protected function processRequirements(array &$composerJson, array $additions, array &$changes): bool
 {
 $hasChanges = false;

 $composerJson['require'] = $composerJson['require'] ?? [];

 foreach ($additions['require'] ?? [] as $package => $version) {
 if (!isset($composerJson['require'][$package]) ||
 $composerJson['require'][$package] !== $version) {
 $composerJson['require'][$package] = $version;
 $changes['added'][] = "Dependency: \"{$package}\": {$version}";
 $hasChanges = true;
 }
 }

 return $hasChanges;
 }

 /**
 * Process repositories in composer.json.
 *
 * @param array $composerJson
 * @param array $additions
 * @param array $changes
 * @return bool
 */
 protected function processRepositories(array &$composerJson, array $additions, array &$changes): bool
 {
 $hasChanges = false;

 $composerJson['repositories'] = $composerJson['repositories'] ?? [];

 foreach ($additions['repositories'] ?? [] as $repository) {
 $existing = array_filter(
 $composerJson['repositories'],
 fn($repo) => isset($repo['type'], $repo['url']) && $repo['type'] === $repository['type'] && $repo['url'] === $repository['url']
 );

 if (empty($existing)) {
 $composerJson['repositories'][] = $repository;
 $changes['added'][] = "Repository: type \"{$repository['type']}\", url \"{$repository['url']}\"";
 $hasChanges = true;
 }
 }

 return $hasChanges;
 }

 /**
 * Register service providers in bootstrap/providers.php.
 */
 protected function registerServiceProviders(): void
 {
 $this->info('Registering service providers...');

 $providersPath = base_path('bootstrap/providers.php');

 if (!File::exists($providersPath)) {
 $this->warn('bootstrap/providers.php not found. Creating...');
 $this->createProvidersFile($providersPath);
 }

 $generator = new PlatformComposerGenerator();
 $providers = $generator->discoverServiceProviders();

 $providersContent = File::get($providersPath);
 $providersArray = $this->parseProvidersFile($providersContent);

 $added = 0;
 foreach ($providers as $provider) {
 if (!in_array($provider, $providersArray)) {
 $providersArray[] = $provider;
 $this->line(" + {$provider}");
 $added++;
 }
 }

 if ($added > 0) {
 $this->saveProvidersFile($providersPath, $providersArray);
 $this->info("Registered {$added} service providers.");
 } else {
 $this->info('All service providers already registered.');
 }
 }

 /**
 * Create a new providers.php file with default content.
 *
 * @param string $path
 */
 protected function createProvidersFile(string $path): void
 {
 $content = <<<'PHP'
<?php

return [
 Numerimondes\Providers\AppServiceProvider::class,
];
PHP;
 File::put($path, $content);
 }

 /**
 * Parse the providers.php file to extract provider classes.
 *
 * @param string $content
 * @return array
 */
 protected function parseProvidersFile(string $content): array
 {
 if (preg_match('/return\s*\[(.*?)\];/s', $content, $matches)) {
 $arrayContent = $matches[1];
 preg_match_all('/[\'"]([^\'"]+)[\'"]/', $arrayContent, $providerMatches);
 return $providerMatches[1] ?? [];
 }
 return [];
 }

 /**
 * Save the updated providers.php file.
 *
 * @param string $path
 * @param array $providers
 */
 protected function saveProvidersFile(string $path, array $providers): void
 {
 $content = "<?php\n\nreturn [\n";
 foreach ($providers as $provider) {
 $content .= " {$provider}::class,\n";
 }
 $content .= "];\n";

 File::put($path, $content);
 }

 /**
 * Save the updated composer.json file.
 *
 * @param string $path
 * @param array $composerJson
 */
 protected function saveComposerJson(string $path, array $composerJson): void
 {
 $jsonContent = json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
 File::put($path, $jsonContent);
 }

 /**
 * Display changes made to composer.json.
 *
 * @param array $changes
 */
 protected function displayChanges(array $changes): void
 {
 if (!empty($changes['added'])) {
 $this->info('Added:');
 foreach ($changes['added'] as $change) {
 $this->line(" + {$change}");
 }
 }

 if (!empty($changes['removed'])) {
 $this->warn('Removed:');
 foreach ($changes['removed'] as $change) {
 $this->line(" - {$change}");
 }
 }
 }

 /**
 * Display the command header.
 */
 protected function displayHeader(): void
 {
 $this->info('====================================');
 $this->info(' Platform Composer Preparation');
 $this->info('====================================');
 }
}

/**
 * Generates composer.json additions and discovers autoload paths and dependencies.
 */
class PlatformComposerGenerator
{
 /**
 * Get the root namespace for the project.
 *
 * @return string
 */
 protected function getProjectRootNamespace(): string
 {
 return 'Numerimondes\\';
 }

 /**
 * Get composer.json additions for autoload, dependencies, and repositories.
 *
 * @return array
 */
 public function getComposerAdditions(): array
 {
 $entries = [];

 $psr4Paths = $this->discoverAutoloadPaths();
 foreach ($psr4Paths as $namespace => $path) {
 $entries['autoload']['psr-4'][$namespace] = $path;
 }

 $files = $this->discoverAutoloadFiles();
 if (!empty($files)) {
 $entries['autoload']['files'] = $files;
 }

 $dependencies = $this->discoverDependencies();
 foreach ($dependencies as $package => $version) {
 $entries['require'][$package] = $version;
 }

 $repositories = $this->discoverRepositories();
 if (!empty($repositories)) {
 $entries['repositories'] = $repositories;
 }

 return $entries;
 }

 /**
 * Discover PSR-4 autoload paths.
 *
 * @return array
 */
public function discoverAutoloadPaths(): array
{
    $paths = [];
    $rootNamespace = $this->getProjectRootNamespace();
    $staticPaths = static::getStaticPathMappings($rootNamespace);
    foreach ($staticPaths as $namespace => $relativePath) {
        $fullPath = base_path($relativePath);
        if (File::isDirectory($fullPath)) {
            $paths[$namespace] = $relativePath;
        }
    }
    $dynamicPaths = $this->scanForDynamicPaths($rootNamespace);
    $paths = array_merge($paths, $dynamicPaths);
    return $paths;
}

 /**
 * Get static PSR-4 path mappings.
 *
 * @param string $rootNamespace
 * @return array
 */
public static function getStaticPathMappings(string $rootNamespace): array
{
    $rootNamespace = rtrim($rootNamespace, '\\');
    return [
        'App\\' => 'app/',
        $rootNamespace . '\\' => 'platform/',
        'Webkernel\\' => 'packages/webkernel/src/',
        'Database\\Factories\\' => 'database/factories/',
        'Database\\Seeders\\' => 'database/seeders/',
        'Webkernel\\Database\\Seeders\\' => 'packages/webkernel/src/database/seeders/',
    ];
}


 /**
 * Scan for dynamic PSR-4 paths in packages and modules.
 *
 * @param string $rootNamespace
 * @return array
 */
 protected function scanForDynamicPaths(string $rootNamespace): array
 {
 $paths = [];
 $rootNamespace = rtrim($rootNamespace, '\\');

 $packagesDir = base_path('packages');
 if (File::isDirectory($packagesDir)) {
 $packages = File::directories($packagesDir);

 foreach ($packages as $packagePath) {
 $packageName = basename($packagePath);
 if ($packageName === 'webkernel') {
 continue;
 }

 $srcPath = $packagePath . '/src';
 if (File::isDirectory($srcPath)) {
 $namespace = $rootNamespace . '\\' . $this->capitalizeNamespacePart($packageName) . '\\';
 $paths[$namespace] = "packages/{$packageName}/src/";
 }
 }
 }

 $modulePaths = $this->findAllModulePaths($rootNamespace);
 foreach ($modulePaths as $modulePath => $namespace) {
 $paths[$namespace] = $modulePath;
 }

 return $paths;
 }

 /**
 * Find all module paths and their corresponding namespaces.
 *
 * @param string|null $rootNamespace
 * @return array
 */
 public function findAllModulePaths(?string $rootNamespace = null): array
 {
 if ($rootNamespace === null) {
 $rootNamespace = $this->getProjectRootNamespace();
 }

 $modulePaths = [];

 $platformModulesDir = base_path('platform/Modules');
 if (!File::isDirectory($platformModulesDir)) {
 return $modulePaths;
 }

 $this->scanModulesRecursively($platformModulesDir, '', $modulePaths, $rootNamespace);

 return $modulePaths;
 }

 /**
 * Recursively scan for module directories.
 *
 * @param string $directory
 * @param string $parentPath
 * @param array $modulePaths
 * @param string $rootNamespace
 */
 protected function scanModulesRecursively(string $directory, string $parentPath, array &$modulePaths, string $rootNamespace): void
 {
 try {
 $items = File::directories($directory);

 foreach ($items as $itemPath) {
 $itemName = basename($itemPath);
 $srcPath = $itemPath . '/src';

 if (File::isDirectory($srcPath)) {
 $relativePath = 'platform/Modules/' . ($parentPath ? $parentPath . '/' : '') . $itemName . '/src/';
 $namespace = $this->generateModuleNamespace($relativePath, $rootNamespace);
 $modulePaths[$relativePath] = $namespace;
 } else {
 $newParentPath = $parentPath ? $parentPath . '/' . $itemName : $itemName;
 $this->scanModulesRecursively($itemPath, $newParentPath, $modulePaths, $rootNamespace);
 }
 }
 } catch (\Exception $e) {
 // Log errors via the caller context
 }
 }

 /**
 * Generate a namespace for a module path.
 *
 * @param string $modulePath
 * @param string|null $rootNamespace
 * @return string
 */
 public function generateModuleNamespace(string $modulePath, ?string $rootNamespace = null): string
 {
 if ($rootNamespace === null) {
 $rootNamespace = $this->getProjectRootNamespace();
 }

 $rootNamespace = rtrim($rootNamespace, '\\');
 $pathParts = explode('/', trim($modulePath, '/'));

 $moduleIndex = array_search('Modules', $pathParts);
 if ($moduleIndex === false) {
 return $rootNamespace . '\\Modules\\';
 }

 $moduleParts = array_slice($pathParts, $moduleIndex + 1);
 array_pop($moduleParts); // Remove 'src'

 $namespaceParts = [$rootNamespace, 'Modules'];
 foreach ($moduleParts as $part) {
 $namespaceParts[] = $this->capitalizeNamespacePart($part);
 }

 return implode('\\', $namespaceParts) . '\\';
 }

 /**
 * Capitalize a namespace part.
 *
 * @param string $part
 * @return string
 */
 public function capitalizeNamespacePart(string $part): string
 {
 return str_replace(['-', '_'], '', ucwords($part, '-_'));
 }

 /**
 * Discover autoload files.
 *
 * @return array
 */
 public function discoverAutoloadFiles(): array
 {
 $files = [];

 $potentialFiles = [
 'packages/webkernel/src/Helpers/helpers.php',
 'platform/helpers.php',
 'platform/functions.php',
 ];

 foreach ($potentialFiles as $file) {
 if (File::exists(base_path($file))) {
 $files[] = $file;
 }
 }

 $moduleHelpers = $this->scanForModuleHelpers();
 return array_merge($files, $moduleHelpers);
 }

 /**
 * Scan for module helper files.
 *
 * @return array
 */
 protected function scanForModuleHelpers(): array
 {
 $files = [];

 $modulePaths = $this->findAllModulePaths();
 foreach ($modulePaths as $modulePath => $namespace) {
 $moduleBasePath = str_replace('/src/', '', $modulePath);
 $helperPath = $moduleBasePath . '/Helpers/helpers.php';

 if (File::exists(base_path($helperPath))) {
 $files[] = $helperPath;
 }
 }

 return $files;
 }

 /**
 * Discover all required dependencies.
 *
 * @return array
 */
 public function discoverDependencies(): array
 {
 $dependencies = $this->getBaseDependencies();

 $webkernelDeps = $this->getWebkernelDependencies();
 $modulesDeps = $this->getModulesDependencies();
 $configBasedDeps = $this->getConfigBasedDependencies();

 return array_merge($dependencies, $webkernelDeps, $modulesDeps, $configBasedDeps);
 }

 /**
 * Get base dependencies.
 *
 * @return array
 */
 protected function getBaseDependencies(): array
 {
 return [
 'spatie/laravel-permission' => '^6.0',
 ];
 }

 /**
 * Get webkernel-specific dependencies.
 *
 * @return array
 */
 protected function getWebkernelDependencies(): array
 {
 $dependencies = [];

 $webkernelAppFile = base_path('packages/webkernel/src/constants/Application.php');
 if (File::exists($webkernelAppFile)) {
 $dependencies = $this->extractDependenciesFromFile($webkernelAppFile);
 }

 return $dependencies;
 }

 /**
 * Get dependencies from modules.
 *
 * @return array
 */
 protected function getModulesDependencies(): array
 {
 $dependencies = [];

 $modulePaths = $this->findAllModulePaths();
 foreach ($modulePaths as $modulePath => $namespace) {
 $moduleBasePath = str_replace('/src/', '', $modulePath);
 $appFile = base_path($moduleBasePath . '/constants/Application.php');

 if (File::exists($appFile)) {
 $moduleDeps = $this->extractDependenciesFromFile($appFile);
 $dependencies = array_merge($dependencies, $moduleDeps);
 }
 }

 return $dependencies;
 }

 /**
 * Extract dependencies from a file.
 *
 * @param string $filePath
 * @return array
 */
 protected function extractDependenciesFromFile(string $filePath): array
 {
 $dependencies = [];

 try {
 $content = File::get($filePath);

 if (preg_match('/(?:const\s+DEPENDENCIES|static\s+\$dependencies)\s*=\s*(\[.*?\]);/s', $content, $matches)) {
 $dependenciesString = $matches[1];
 $dependencies = eval("return $dependenciesString;");
 }
 } catch (\Exception $e) { 
 // Silently handle errors
 }

 return is_array($dependencies) ? $dependencies : [];
 }

 /**
 * Get dependencies from configuration.
 *
 * @return array
 */
 protected function getConfigBasedDependencies(): array
 {
 $dependencies = [];

 try {
 if (File::exists(base_path('platform/config/dependencies.php'))) {
 $configDeps = include base_path('platform/config/dependencies.php');
 if (is_array($configDeps)) {
 $dependencies = array_merge($dependencies, $configDeps);
 }
 }
 } catch (\Exception $e) {
 // Silently handle errors
 }

 return $dependencies;
 }

 /**
 * Discover repositories for composer.json.
 *
 * @return array
 */
 protected function discoverRepositories(): array
 {
 return [
 [
 'type' => 'path',
 'url' => 'packages/webkernel',
 ],
 [
 'type' => 'path',
 'url' => './packages/webkernel',
 ],
 ];
 }

 /**
 * Discover all service providers.
 *
 * @return array
 */
 public function discoverServiceProviders(): array
 {
 $providers = [];
 $rootNamespace = rtrim($this->getProjectRootNamespace(), '\\');

 $webkernelProvider = $this->findWebkernelProvider();
 if ($webkernelProvider) {
 $providers[] = $webkernelProvider;
 }

 $moduleProviders = $this->findModuleProviders($rootNamespace);
 $providers = array_merge($providers, $moduleProviders);

 $packageProviders = $this->findPackageProviders($rootNamespace);
 $providers = array_merge($providers, $packageProviders);

 return array_unique($providers);
 }

 /**
 * Find the webkernel service provider.
 *
 * @return string|null
 */
 protected function findWebkernelProvider(): ?string
 {
 $providerPath = base_path('packages/webkernel/src/Providers');
 if (!File::isDirectory($providerPath)) {
 return null;
 }

 try {
 $providers = File::files($providerPath);
 foreach ($providers as $provider) {
 if (Str::endsWith($provider->getFilename(), 'ServiceProvider.php')) {
 $className = pathinfo($provider->getFilename(), PATHINFO_FILENAME);
 return "Webkernel\\Providers\\{$className}";
 }
 }
 } catch (\Exception $e) {
 // Silently handle errors
 }

 return null;
 }

 /**
 * Find service providers in modules.
 *
 * @param string $rootNamespace
 * @return array
 */
 protected function findModuleProviders(string $rootNamespace): array
 {
 $providers = [];

 $modulePaths = $this->findAllModulePaths($rootNamespace);
 foreach ($modulePaths as $modulePath => $namespace) {
 $providersPath = base_path(str_replace('/src/', '/Providers/', $modulePath));

 try {
 if (File::isDirectory($providersPath)) {
 $providerFiles = File::files($providersPath);
 foreach ($providerFiles as $providerFile) {
 if (Str::endsWith($providerFile->getFilename(), 'ServiceProvider.php')) {
 $className = pathinfo($providerFile->getFilename(), PATHINFO_FILENAME);
 $providers[] = rtrim($namespace, '\\') . "\\Providers\\{$className}";
 }
 }
 }
 } catch (\Exception $e) {
 // Silently handle errors
 }
 }

 return $providers;
 }

 /**
 * Find service providers in packages.
 *
 * @param string $rootNamespace
 * @return array
 */
 protected function findPackageProviders(string $rootNamespace): array
 {
 $providers = [];

 $packagesDir = base_path('packages');
 if (!File::isDirectory($packagesDir)) {
 return $providers;
 }

 try {
 $packages = File::directories($packagesDir);
 foreach ($packages as $packagePath) {
 $packageName = basename($packagePath);
 if ($packageName === 'webkernel') {
 continue;
 }

 $providersPath = $packagePath . '/src/Providers';
 if (File::isDirectory($providersPath)) {
 $providerFiles = File::files($providersPath);
 foreach ($providerFiles as $providerFile) {
 if (Str::endsWith($providerFile->getFilename(), 'ServiceProvider.php')) {
 $className = pathinfo($providerFile->getFilename(), PATHINFO_FILENAME);
 $packageNamespace = $this->capitalizeNamespacePart($packageName);
 $providers[] = "{$rootNamespace}\\{$packageNamespace}\\Providers\\{$className}";
 }
 }
 }
 }
 } catch (\Exception $e) {
 // Silently handle errors
 }

 return $providers;
 }

 /**
 * Determine if a namespace should be removed.
 *
 * @param string $namespace
 * @param string $path
 * @param array $pathToNamespace
 * @return bool
 */
 public function shouldRemoveNamespace(string $namespace, string $path, array $pathToNamespace): bool
 {
 $validNamespaces = [
 'Numerimondes\\',
 'Webkernel\\',
 'Database\\Factories\\',
 'Database\\Seeders\\',
 'Webkernel\\Database\\Seeders\\',
 'Numerimondes\Modules\\',
 ];

 $isManaged = false;
 foreach ($validNamespaces as $validNamespace) {
 if (str_starts_with($namespace, $validNamespace)) {
 $isManaged = true;
 break;
 }
 }

 if (!$isManaged) {
 return true; // Remove unmanaged namespaces
 }

 // Check if path exists
 if (!File::isDirectory(base_path($path))) {
 return true;
 }

 // Check for conflicting namespaces for the same path
 if (isset($pathToNamespace[$path]) && $namespace !== $pathToNamespace[$path]) {
 return true;
 }

 return false;
 }

 /**
 * Determine if a file should be removed from autoload.
 *
 * @param string $file
 * @return bool
 */
 public function shouldRemoveFile(string $file): bool
 {
 $managedFiles = [
 'packages/webkernel/src/Helpers/helpers.php',
 'platform/helpers.php',
 'platform/functions.php',
 ];

 foreach ($managedFiles as $managedFile) {
 if (str_contains($file, basename($managedFile))) {
 return !File::exists(base_path($file));
 }
 }

 return false;
 }
}

/**
 * Fixes namespace violations in PHP files.
 */
class PlatformNamespaceFixer
{
 protected $command;
 protected $generator;

 /**
 * Constructor.
 *
 * @param Command $command
 */
 public function __construct(Command $command)
 {
 $this->command = $command;
 $this->generator = new PlatformComposerGenerator();
 }

 /**
 * Fix all namespace violations in modules and packages.
 */
 public function fixAllNamespaceViolations(): void
 {
 $this->command->info('Scanning for namespace violations...');

 $modulePaths = $this->generator->findAllModulePaths();
 $totalFixed = 0;

 // Fix module namespaces
 foreach ($modulePaths as $modulePath => $expectedNamespace) {
 $fixed = $this->fixModuleNamespaces($modulePath, $expectedNamespace);
 $totalFixed += $fixed;
 }

 // Fix webkernel namespace
 $fixed = $this->fixModuleNamespaces('packages/webkernel/src/', 'Webkernel\\');
 $totalFixed += $fixed;

 if ($totalFixed > 0) {
 $this->command->info("Fixed {$totalFixed} namespace violations.");
 } else {
 $this->command->info('No namespace violations found.');
 }
 }

 /**
 * Fix namespaces in a specific module or package.
 *
 * @param string $modulePath
 * @param string $expectedBaseNamespace
 * @return int
 */
 protected function fixModuleNamespaces(string $modulePath, string $expectedBaseNamespace): int
 {
 $fixedCount = 0;
 $moduleBasePath = str_replace('/src/', '', $modulePath);
 $moduleName = basename($moduleBasePath);

 $this->command->line("Processing: {$moduleName}");

 $phpFiles = $this->findAllPhpFiles(base_path($modulePath));

 foreach ($phpFiles as $filePath) {
 if ($this->fixFileNamespace($filePath, $expectedBaseNamespace, $modulePath)) {
 $fixedCount++;
 }
 }

 return $fixedCount;
 }

 /**
 * Find all PHP files in a directory.
 *
 * @param string $directory
 * @return array
 */
 protected function findAllPhpFiles(string $directory): array
 {
 $phpFiles = [];

 if (!is_dir($directory)) {
 return $phpFiles;
 }

 try {
 $iterator = new \RecursiveIteratorIterator(
 new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
 );

 foreach ($iterator as $file) {
 if ($file->isFile() && $file->getExtension() === 'php') {
 $phpFiles[] = $file->getPathname();
 }
 }
 } catch (\Exception $e) {
 $this->command->warn("Error scanning directory {$directory}: {$e->getMessage()}");
 }

 return $phpFiles;
 }

 /**
 * Fix the namespace of a single file.
 *
 * @param string $filePath
 * @param string $expectedBaseNamespace
 * @param string $modulePath
 * @return bool
 */
 protected function fixFileNamespace(string $filePath, string $expectedBaseNamespace, string $modulePath): bool
 {
 if (!File::exists($filePath)) {
 return false;
 }

 $content = File::get($filePath);
 $originalContent = $content;

 $relativePath = str_replace(base_path($modulePath), '', $filePath);
 $relativePath = trim($relativePath, '/');

 $expectedFullNamespace = $this->calculateExpectedNamespace($expectedBaseNamespace, $relativePath);
 $currentNamespace = $this->extractCurrentNamespace($content);
 $className = $this->extractClassName($content);

 if (!$className) {
 return false;
 }

 $needsUpdate = false;

 if ($currentNamespace !== $expectedFullNamespace) {
 if ($currentNamespace) {
 $content = preg_replace(
 '/^namespace\s+' . preg_quote($currentNamespace, '/') . '\s*;/m',
 "namespace {$expectedFullNamespace};",
 $content
 );
 } else {
 $content = $this->addNamespaceDeclaration($content, $expectedFullNamespace);
 }
 $needsUpdate = true;
 }

 $newClassName = $this->resolveClassNameConflicts($className, $expectedFullNamespace);
 if ($newClassName !== $className) {
 $content = $this->replaceClassName($content, $className, $newClassName);
 $needsUpdate = true;
 }

 if ($needsUpdate && $content !== $originalContent) {
 File::put($filePath, $content);
 $relativePath = str_replace(base_path() . '/', '', $filePath);
 $this->command->line(" Fixed: {$relativePath} -> {$expectedFullNamespace}\\{$newClassName}");
 return true;
 }

 return false;
 }

 /**
 * Calculate the expected namespace for a file.
 *
 * @param string $baseNamespace
 * @param string $relativePath
 * @return string
 */
 protected function calculateExpectedNamespace(string $baseNamespace, string $relativePath): string
 {
 $pathParts = explode('/', dirname($relativePath));
 $pathParts = array_filter($pathParts, fn($part) => $part !== '.' && $part !== '');

 $namespaceParts = [rtrim($baseNamespace, '\\')];
 foreach ($pathParts as $part) {
 $namespaceParts[] = $this->generator->capitalizeNamespacePart($part);
 }

 return implode('\\', $namespaceParts);
 }

 /**
 * Extract the current namespace from file content.
 *
 * @param string $content
 * @return string|null
 */
 protected function extractCurrentNamespace(string $content): ?string
 {
 if (preg_match('/^namespace\s+([^;]+);/m', $content, $matches)) {
 return trim($matches[1]);
 }
 return null;
 }

 /**
 * Extract the class name from file content.
 *
 * @param string $content
 * @return string|null
 */
 protected function extractClassName(string $content): ?string
 {
 if (preg_match('/^(?:abstract\s+)?(?:final\s+)?(?:class|interface|trait)\s+(\w+)/m', $content, $matches)) {
 return $matches[1];
 }
 return null;
 }

 /**
 * Resolve class name conflicts.
 *
 * @param string $className
 * @param string $namespace
 * @return string
 */
 protected function resolveClassNameConflicts(string $className, string $namespace): string
 {
 $conflictingClasses = [
 'AppServiceProvider',
 'Controller',
 'Model',
 'Factory',
 'Seeder',
 ];

 if (in_array($className, $conflictingClasses)) {
 $moduleName = $this->extractModuleFromNamespace($namespace);
 return $moduleName . $className;
 }

 return $className;
 }

 /**
 * Extract the module name from a namespace.
 *
 * @param string $namespace
 * @return string
 */
 protected function extractModuleFromNamespace(string $namespace): string
 {
 $parts = explode('\\', $namespace);
 $moduleIndex = array_search('Modules', $parts);

 return $moduleIndex !== false && isset($parts[$moduleIndex + 1]) ? $parts[$moduleIndex + 1] : 'Module';
 }

 /**
 * Replace class names in file content.
 *
 * @param string $content
 * @param string $oldName
 * @param string $newName
 * @return string
 */
 protected function replaceClassName(string $content, string $oldName, string $newName): string
 {
 $patterns = [
 '/^((?:abstract\s+)?(?:final\s+)?(?:class|interface|trait)\s+)' . preg_quote($oldName, '/') . '\b/m',
 '/\b' . preg_quote($oldName, '/') . '::class\b/',
 '/\bnew\s+' . preg_quote($oldName, '/') . '\s*\(/m',
 ];

 $replacements = [
 '${1}' . $newName,
 $newName . '::class',
 'new ' . $newName . '(',
 ];

 return preg_replace($patterns, $replacements, $content);
 }

 /**
 * Add a namespace declaration to a PHP file.
 *
 * @param string $content
 * @param string $namespace
 * @return string
 */
 protected function addNamespaceDeclaration(string $content, string $namespace): string
 {
 if (preg_match('/^<\?php\s*$/m', $content)) {
 return preg_replace('/^<\?php\s*$/m', "<?php\n\nnamespace {$namespace};", $content, 1);
 }

 return "<?php\n\nnamespace {$namespace};\n\n" . ltrim($content, "<?php\n ");
 }
}