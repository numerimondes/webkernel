<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use ReflectionClass;
use ReflectionMethod;

/**
 * Generate Complete IDE Helper for User Model
 *
 * Leverages barryvdh/laravel-ide-helper while adding support for:
 * - Module extension methods and relationships
 * - Filament panel-specific extensions
 * - Priority-based conflict resolution
 * - Comprehensive documentation generation
 *
 * This command extends the standard IDE helper approach by integrating
 * module-specific UserModel classes, ensuring IDE autocomplete works
 * for dynamically merged methods from the modular architecture.
 *
 * Features:
 * - Automatic Eloquent method documentation (via parent class)
 * - Query builder method documentation
 * - Filament contract method documentation
 * - Module extension method/relationship documentation
 * - Conflict detection and reporting
 * - Backup creation with automatic cleanup
 * - Syntax validation before writing
 *
 * Usage:
 *   php artisan user:generate-ide-helper
 *   php artisan user:generate-ide-helper --show-conflicts
 *   php artisan user:generate-ide-helper --no-backup
 *
 * Integration with barryvdh/laravel-ide-helper:
 * This command focuses on the User model specifically, complementing
 * the standard ide-helper:models command. It handles the complex
 * dynamic method merging that the standard helper cannot detect.
 *
 * @package Webkernel\Aptitudes\Users\Commands
 * @author El Moumen Yassine, Numerimondes
 */
class GenerateUserIdeHelperCommand extends Command
{
  protected $signature = 'user:generate-ide-helper
                          {--show-conflicts : Display conflicts report}
                          {--no-backup : Skip backup creation}';

  protected $description = 'Generate complete IDE helper for User model with all methods';

  private const BACKUP_DIR = 'basix/backups/AppModelsUser';

  /**
   * Execute the console command
   *
   * Workflow:
   * 1. Scan all extension sources
   * 2. Build documentation sections
   * 3. Validate PHP syntax
   * 4. Create backup (optional)
   * 5. Write new file
   * 6. Display summary
   *
   * @return int Command::SUCCESS or Command::FAILURE
   */
  public function handle(): int
  {
    $userModelPath = app_path('Models/User.php');

    if (!file_exists($userModelPath)) {
      $this->error("File not found: {$userModelPath}");
      return Command::FAILURE;
    }

    $this->info('Scanning all sources...');

    // Get all documentation sources
    $extensions = \App\Models\User::availableUserExtensions();
    $eloquentMethods = $this->extractEloquentMethods();
    $queryBuilderMethods = $this->extractQueryBuilderMethods();
    $filamentMethods = $this->extractFilamentMethods();
    $tenancyMethods = $this->extractTenancyMethods();

    if ($this->option('show-conflicts') && !empty($extensions['conflicts'])) {
      $this->displayConflicts($extensions['conflicts']);
    }

    // Build documentation sections
    $moduleMethodDocs = $this->buildMethodDocs($extensions['methods'], 'Module Extensions');
    $modulePropertyDocs = $this->buildPropertyDocs($extensions['relationships']);
    $eloquentDocs = $this->buildStandardMethodDocs($eloquentMethods, 'Eloquent Model Methods');
    $queryBuilderDocs = $this->buildStandardMethodDocs($queryBuilderMethods, 'Query Builder Methods');
    $filamentDocs = $this->buildStandardMethodDocs($filamentMethods, 'Filament Contract Methods');
    $tenancyDocs = $this->buildStandardMethodDocs($tenancyMethods, 'HasTenancy Trait Methods');
    $conflictWarnings = $this->buildConflictWarnings($extensions['conflicts']);

    $currentContent = file_get_contents($userModelPath);
    $classBody = $this->extractClassBody($currentContent);

    $newContent = $this->generateNewContent(
      $moduleMethodDocs,
      $modulePropertyDocs,
      $eloquentDocs,
      $queryBuilderDocs,
      $filamentDocs,
      $tenancyDocs,
      $conflictWarnings,
      $classBody,
      $extensions['metadata'],
    );

    $this->info('Validating PHP syntax...');
    if (!$this->validateSyntax($newContent)) {
      $this->error('Generated file contains syntax errors. Aborting.');
      return Command::FAILURE;
    }

    if (!$this->option('no-backup')) {
      $this->info('Creating backup...');
      $backupPath = $this->createBackup($currentContent);
    }

    file_put_contents($userModelPath, $newContent);

    $this->displaySummary(
      $extensions,
      count($eloquentMethods),
      count($queryBuilderMethods),
      count($filamentMethods),
      count($tenancyMethods),
      $backupPath ?? null,
      $userModelPath,
    );

    return Command::SUCCESS;
  }

  /**
   * Extract Eloquent model methods from base class
   *
   * Leverages reflection to get actual method signatures
   * from the parent Eloquent model.
   *
   * @return array<string, array{return_type: string, parameters: array}>
   */
  private function extractEloquentMethods(): array
  {
    $baseClass = \Webkernel\Aptitudes\Users\Models\User::class;
    $reflection = new ReflectionClass($baseClass);
    $methods = [];

    $methodsToDocument = [
      'save',
      'update',
      'delete',
      'forceDelete',
      'restore',
      'fresh',
      'refresh',
      'fill',
      'forceFill',
      'replicate',
      'is',
      'isNot',
      'getKey',
      'getKeyName',
      'getKeyType',
      'getIncrementing',
      'getAttribute',
      'setAttribute',
      'getOriginal',
      'getDirty',
      'getChanges',
      'isDirty',
      'isClean',
      'wasChanged',
      'wasRecentlyCreated',
      'touch',
      'push',
      'finishSave',
      'toArray',
      'toJson',
      'jsonSerialize',
      'getTable',
      'getConnectionName',
      'setConnection',
      'getAttributes',
      'setRawAttributes',
      'syncOriginal',
    ];

    foreach ($methodsToDocument as $methodName) {
      if ($reflection->hasMethod($methodName)) {
        $method = $reflection->getMethod($methodName);
        if ($method->isPublic()) {
          $methods[$methodName] = [
            'return_type' => $this->getReturnTypeString($method),
            'parameters' => $this->extractMethodParams($method),
          ];
        }
      }
    }

    return $methods;
  }

  /**
   * Extract Query Builder static methods
   *
   * Hardcoded with proper generic typing for optimal IDE support.
   * These are the most commonly used query methods.
   *
   * @return array<string, array{return_type: string, parameters: array, static?: bool}>
   */
  private function extractQueryBuilderMethods(): array
  {
    return [
      'query' => [
        'return_type' => '\Illuminate\Database\Eloquent\Builder<\App\Models\User>',
        'parameters' => [],
        'static' => true,
      ],
      'where' => [
        'return_type' => '\Illuminate\Database\Eloquent\Builder<\App\Models\User>',
        'parameters' => [
          ['name' => 'column', 'type' => 'string|array|\Closure'],
          ['name' => 'operator', 'type' => 'mixed', 'optional' => true],
          ['name' => 'value', 'type' => 'mixed', 'optional' => true],
        ],
        'static' => true,
      ],
      'find' => [
        'return_type' => '\App\Models\User|null',
        'parameters' => [
          ['name' => 'id', 'type' => 'int|string'],
          ['name' => 'columns', 'type' => 'array', 'optional' => true, 'default' => "['*']"],
        ],
        'static' => true,
      ],
      'findOrFail' => [
        'return_type' => '\App\Models\User',
        'parameters' => [
          ['name' => 'id', 'type' => 'int|string'],
          ['name' => 'columns', 'type' => 'array', 'optional' => true, 'default' => "['*']"],
        ],
        'static' => true,
      ],
      'create' => [
        'return_type' => '\App\Models\User',
        'parameters' => [['name' => 'attributes', 'type' => 'array', 'optional' => true, 'default' => '[]']],
        'static' => true,
      ],
      'firstOrCreate' => [
        'return_type' => '\App\Models\User',
        'parameters' => [
          ['name' => 'attributes', 'type' => 'array'],
          ['name' => 'values', 'type' => 'array', 'optional' => true, 'default' => '[]'],
        ],
        'static' => true,
      ],
      'updateOrCreate' => [
        'return_type' => '\App\Models\User',
        'parameters' => [
          ['name' => 'attributes', 'type' => 'array'],
          ['name' => 'values', 'type' => 'array', 'optional' => true, 'default' => '[]'],
        ],
        'static' => true,
      ],
      'all' => [
        'return_type' => '\Illuminate\Database\Eloquent\Collection<int, \App\Models\User>',
        'parameters' => [['name' => 'columns', 'type' => 'array', 'optional' => true, 'default' => "['*']"]],
        'static' => true,
      ],
      'first' => [
        'return_type' => '\App\Models\User|null',
        'parameters' => [],
        'static' => true,
      ],
      'firstOrFail' => [
        'return_type' => '\App\Models\User',
        'parameters' => [],
        'static' => true,
      ],
    ];
  }

  /**
   * Extract Filament contract methods
   *
   * These methods are required by Filament's HasTenants interface.
   *
   * @return array<string, array{return_type: string, parameters: array}>
   */
  private function extractFilamentMethods(): array
  {
    return [
      'canAccessPanel' => [
        'return_type' => 'bool',
        'parameters' => [['name' => 'panel', 'type' => '\Filament\Panel']],
      ],
      'getTenants' => [
        'return_type' => '\Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model>',
        'parameters' => [['name' => 'panel', 'type' => '\Filament\Panel']],
      ],
      'canAccessTenant' => [
        'return_type' => 'bool',
        'parameters' => [['name' => 'tenant', 'type' => '\Illuminate\Database\Eloquent\Model']],
      ],
    ];
  }

  /**
   * Extract HasTenancy trait methods
   *
   * @return array<string, array{return_type: string, parameters: array}>
   */
  private function extractTenancyMethods(): array
  {
    return [
      'currentTenant' => [
        'return_type' => '\Illuminate\Database\Eloquent\Model|null',
        'parameters' => [],
      ],
    ];
  }

  /**
   * Get return type as string with self/static resolution
   *
   * @param ReflectionMethod $method
   * @return string Fully qualified class name or type hint
   */
  private function getReturnTypeString(ReflectionMethod $method): string
  {
    $returnType = $method->getReturnType();
    if (!$returnType) {
      return 'mixed';
    }

    $type = $returnType instanceof \ReflectionNamedType ? $returnType->getName() : (string) $returnType;

    if ($type === 'self') {
      return '\App\Models\User';
    }

    if ($type === 'static') {
      return '$this';
    }

    return $type;
  }

  /**
   * Extract method parameters with type hints and defaults
   *
   * @param ReflectionMethod $method
   * @return array<int, array{name: string, type: string, optional: bool, default?: mixed}>
   */
  private function extractMethodParams(ReflectionMethod $method): array
  {
    $params = [];

    foreach ($method->getParameters() as $param) {
      $paramInfo = [
        'name' => $param->getName(),
        'type' => $param->getType() ? (string) $param->getType() : 'mixed',
        'optional' => $param->isOptional(),
      ];

      if ($param->isOptional() && $param->isDefaultValueAvailable()) {
        try {
          $defaultValue = $param->getDefaultValue();
          $paramInfo['default'] = $defaultValue;
        } catch (\Throwable $e) {
          // Skip complex default values
        }
      }

      $params[] = $paramInfo;
    }

    return $params;
  }

  /**
   * Build standard method documentation
   *
   * @param array<string, array> $methods
   * @param string $sectionTitle
   * @return string PHPDoc formatted method declarations
   */
  private function buildStandardMethodDocs(array $methods, string $sectionTitle): string
  {
    if (empty($methods)) {
      return '';
    }

    $docs = [' *', " * {$sectionTitle}"];

    foreach ($methods as $name => $info) {
      $isStatic = $info['static'] ?? false;
      $prefix = $isStatic ? 'static ' : '';
      $params = $this->formatParameters($info['parameters'] ?? []);
      $returnType = $info['return_type'] ?? 'mixed';
      $docs[] = " * @method {$prefix}{$returnType} {$name}({$params})";
    }

    return implode("\n", $docs);
  }

  /**
   * Build method documentation with source attribution
   *
   * Groups methods by module for better organization in PHPDoc.
   * Each module's methods are documented in a separate section.
   *
   * @param array<string, array> $methods
   * @param string $defaultSection
   * @return string PHPDoc formatted method declarations
   */
  private function buildMethodDocs(array $methods, string $defaultSection): string
  {
    if (empty($methods)) {
      return '';
    }

    $docs = [];
    $byModule = [];

    // Group methods by module
    foreach ($methods as $name => $info) {
      $module = $info['module'];
      if (!isset($byModule[$module])) {
        $byModule[$module] = [];
      }
      $byModule[$module][$name] = $info;
    }

    // Generate documentation per module
    foreach ($byModule as $module => $moduleMethods) {
      $docs[] = ' *';
      $docs[] = " * Methods from: {$module}";

      foreach ($moduleMethods as $name => $info) {
        $params = $this->formatParameters($info['parameters']);
        $returnType = $info['return_type'] ?? 'mixed';
        $docs[] = " * @method {$returnType} {$name}({$params})";
      }
    }

    return implode("\n", $docs);
  }

  /**
   * Build property documentation for relationships
   *
   * Relationships are documented as @property-read to indicate
   * they should not be set directly (read-only access).
   *
   * @param array<string, array> $relationships
   * @return string PHPDoc formatted property declarations
   */
  private function buildPropertyDocs(array $relationships): string
  {
    if (empty($relationships)) {
      return '';
    }

    $docs = [];
    $byModule = [];

    // Group relationships by module
    foreach ($relationships as $name => $info) {
      $module = $info['module'];
      if (!isset($byModule[$module])) {
        $byModule[$module] = [];
      }
      $byModule[$module][$name] = $info;
    }

    // Generate documentation per module
    foreach ($byModule as $module => $moduleRels) {
      $docs[] = ' *';
      $docs[] = " * Relationships from: {$module}";

      foreach ($moduleRels as $name => $info) {
        $returnType = $info['return_type'] ?? 'Relation';
        $docs[] = " * @property-read {$returnType} \${$name}";
      }
    }

    return implode("\n", $docs);
  }

  /**
   * Build conflict warnings
   *
   * Generates clear warnings about detected conflicts,
   * showing which module won the conflict resolution.
   *
   * Note: Conflicts are NOT necessarily errors. They can occur when:
   * - Multiple panels implement the same Filament methods (getTenants, canAccessTenant)
   * - Different modules provide alternative implementations
   * - Priority-based resolution is intentional
   *
   * @param array<string, array> $conflicts
   * @return string PHPDoc formatted warnings
   */
  private function buildConflictWarnings(array $conflicts): string
  {
    if (empty($conflicts)) {
      return '';
    }

    $warnings = [' *', ' * WARNING: CONFLICTS DETECTED:', ' *'];

    foreach ($conflicts as $conflict) {
      $sources = collect($conflict['sources'])->pluck('module')->implode(', ');
      $winner = $conflict['sources'][0]['module'];
      $warnings[] = " * - {$conflict['type']} '{$conflict['name']}': {$sources}";
      $warnings[] = " *   Using: {$winner}";
    }

    $warnings[] = ' *';
    $warnings[] = ' * NOTE: Conflicts are resolved by priority. Higher priority wins.';
    $warnings[] = ' * For Filament panel methods (getTenants, canAccessTenant), this is expected';
    $warnings[] = ' * when multiple panels are registered with different tenant logic.';

    return implode("\n", $warnings);
  }

  /**
   * Format method parameters for PHPDoc
   *
   * Converts parameter metadata into PHPDoc-compatible strings
   * with type hints, optional markers, and default values.
   *
   * @param array<int, array> $params
   * @return string Comma-separated parameter list
   */
  private function formatParameters(array $params): string
  {
    if (empty($params)) {
      return '';
    }

    $formatted = [];

    foreach ($params as $param) {
      $type = $param['type'] ?? 'mixed';
      $name = $param['name'];
      $optional = $param['optional'] ?? false;

      $str = "{$type} \${$name}";
      if ($optional) {
        if (isset($param['default'])) {
          $default = $this->formatDefaultValue($param['default']);
          $str .= ' = ' . $default;
        } else {
          $str .= ' = null';
        }
      }
      $formatted[] = $str;
    }

    return implode(', ', $formatted);
  }

  /**
   * Format default value for inline display
   *
   * Converts PHP values to their string representation
   * suitable for PHPDoc documentation.
   *
   * @param mixed $value
   * @return string String representation of the value
   */
  private function formatDefaultValue($value): string
  {
    if ($value === null) {
      return 'null';
    }

    if ($value === []) {
      return '[]';
    }

    if ($value === '') {
      return "''";
    }

    if (is_bool($value)) {
      return $value ? 'true' : 'false';
    }

    if (is_numeric($value)) {
      return (string) $value;
    }

    if (is_string($value)) {
      if (strpos($value, "'") === false) {
        return "'{$value}'";
      }
      return '"' . addslashes($value) . '"';
    }

    if (is_array($value)) {
      return '[]';
    }

    return 'null';
  }

  /**
   * Extract class body from existing file
   *
   * Preserves any existing code in the User class body,
   * or provides a helpful placeholder if empty.
   *
   * @param string $content Current file content
   * @return string Class body content
   */
  private function extractClassBody(string $content): string
  {
    $pattern = '/final\\s+class\\s+User\\s+extends[^{]*\\{(.*)\\}/s';

    if (preg_match($pattern, $content, $matches)) {
      $body = trim($matches[1]);
      $hasCode = preg_match('/(?:public|protected|private|function)\\s+/', $body);

      if ($hasCode) {
        return $body;
      }
    }

    return '  // This class is intentionally empty.' .
      "\n" .
      '  // All functionality is inherited and auto-merged from module-defined UserModel classes.' .
      "\n" .
      '  // Do not add methods or properties here.';
  }

  /**
   * Generate new file content
   *
   * Assembles all documentation sections into a complete PHP file
   * with proper formatting and metadata.
   *
   * @param string $moduleMethodDocs
   * @param string $modulePropertyDocs
   * @param string $eloquentDocs
   * @param string $queryBuilderDocs
   * @param string $filamentDocs
   * @param string $tenancyDocs
   * @param string $conflictWarnings
   * @param string $classBody
   * @param array<string, mixed> $metadata
   * @return string Complete PHP file content
   */
  private function generateNewContent(
    string $moduleMethodDocs,
    string $modulePropertyDocs,
    string $eloquentDocs,
    string $queryBuilderDocs,
    string $filamentDocs,
    string $tenancyDocs,
    string $conflictWarnings,
    string $classBody,
    array $metadata,
  ): string {
    $timestamp = $metadata['scan_timestamp'] ?? 'unknown';
    $modulesCount = $metadata['total_modules_scanned'] ?? 0;
    $filesCount = $metadata['total_files_loaded'] ?? 0;

    return <<<PHP
    <?php declare(strict_types=1);

    namespace App\Models;

    /**
     * Application User Model Proxy
     *
     * Strict, immutable wrapper around Webkernel\Aptitudes\Users\Models\User.
     * Automatically inherits methods and relationships from all User extension
     * models across registered modules.
     *
     * DO NOT MODIFY: Changes will break authentication, Filament integration,
     * and access control. All logic resides in the base Webkernel implementation
     * and dynamically loaded extension models.
     *
     * To extend the User model, create {YourModule}/User/UserModel.php.
     * See: webkernel/src/Aptitudes/Users/Traits/MergeUserMethodsFromModules.md
     *
     * Generation Info:
     * - Scanned: {$modulesCount} modules
     * - Loaded: {$filesCount} UserModel files
     * - Timestamp: {$timestamp}
     *
    {$moduleMethodDocs}
    {$modulePropertyDocs}
    {$eloquentDocs}
    {$queryBuilderDocs}
    {$filamentDocs}
    {$tenancyDocs}
    {$conflictWarnings}
     *
     * @return \App\Models\User For PHPActor and Intelephense chaining support
     *
     * @author El Moumen Yassine, Numerimondes
     */
    final class User extends \Webkernel\Aptitudes\Users\Models\User
    {
    {$classBody}
    }

    PHP;
  }

  /**
   * Validate PHP syntax
   *
   * Uses PHP's built-in syntax checker (php -l) to validate
   * the generated file before writing it.
   *
   * @param string $content PHP file content to validate
   * @return bool True if syntax is valid
   */
  private function validateSyntax(string $content): bool
  {
    $tmpFile = storage_path('app/tmp_user_model_' . uniqid() . '.php');

    try {
      file_put_contents($tmpFile, $content);
      $output = [];
      $returnCode = 0;
      exec('php -l ' . escapeshellarg($tmpFile) . ' 2>&1', $output, $returnCode);

      if ($returnCode !== 0) {
        $this->error('Syntax validation failed:');
        $this->error(implode("\n", $output));
        return false;
      }

      return true;
    } finally {
      if (file_exists($tmpFile)) {
        unlink($tmpFile);
      }
    }
  }

  /**
   * Create backup of existing file
   *
   * Stores timestamped backup in storage directory.
   * Automatically cleans old backups (keeps last 10).
   *
   * @param string $content Current file content
   * @return string Backup file path
   */
  private function createBackup(string $content): string
  {
    $backupDir = storage_path(self::BACKUP_DIR);

    if (!File::isDirectory($backupDir)) {
      File::makeDirectory($backupDir, 0755, true);
    }

    $timestamp = now()->format('Y-m-d_His');
    $backupPath = "{$backupDir}/User.php.{$timestamp}";
    file_put_contents($backupPath, $content);
    $this->cleanOldBackups($backupDir);

    return $backupPath;
  }

  /**
   * Clean old backups
   *
   * Keeps only the 10 most recent backups to prevent
   * unlimited storage growth.
   *
   * @param string $backupDir Backup directory path
   * @return void
   */
  private function cleanOldBackups(string $backupDir): void
  {
    $backups = collect(File::files($backupDir))
      ->sortByDesc(function ($file) {
        return $file->getMTime();
      })
      ->skip(10);

    foreach ($backups as $backup) {
      File::delete($backup->getPathname());
    }
  }

  /**
   * Display conflicts in console
   *
   * Shows detailed conflict information with winner indication
   * for developer awareness during generation.
   *
   * @param array<string, array> $conflicts
   * @return void
   */
  private function displayConflicts(array $conflicts): void
  {
    $this->warn('WARNING: CONFLICTS DETECTED:');
    $this->newLine();

    foreach ($conflicts as $key => $conflict) {
      $this->error("  {$conflict['type']}: {$conflict['name']}");
      foreach ($conflict['sources'] as $idx => $source) {
        $marker = $idx === 0 ? 'WINNER' : 'IGNORED';
        $this->line("    [{$marker}] {$source['module']} (priority: {$source['priority']})");
      }
      $this->newLine();
    }

    $this->info('NOTE: For Filament panel methods like getTenants and canAccessTenant,');
    $this->info('conflicts are expected when multiple panels define tenant logic.');
    $this->info('The winner is determined by module priority.');
  }

  /**
   * Display summary information
   *
   * Shows comprehensive statistics about the generation process
   * including method counts and file locations.
   *
   * @param array<string, mixed> $extensions
   * @param int $eloquentCount
   * @param int $queryBuilderCount
   * @param int $filamentCount
   * @param int $tenancyCount
   * @param string|null $backupPath
   * @param string $userModelPath
   * @return void
   */
  private function displaySummary(
    array $extensions,
    int $eloquentCount,
    int $queryBuilderCount,
    int $filamentCount,
    int $tenancyCount,
    ?string $backupPath,
    string $userModelPath,
  ): void {
    $methodsCount = count($extensions['methods']);
    $relsCount = count($extensions['relationships']);
    $conflictsCount = count($extensions['conflicts']);

    $this->info('----------------------------------------');
    $this->info('  IDE Helper Generated Successfully!');
    $this->line("   Module Methods: {$methodsCount}");
    $this->line("   Module Relationships: {$relsCount}");
    $this->line("   Eloquent Methods: {$eloquentCount}");
    $this->line("   Query Builder Methods: {$queryBuilderCount}");
    $this->line("   Filament Methods: {$filamentCount}");
    $this->line("   Tenancy Methods: {$tenancyCount}");
    $this->line("   Conflicts: {$conflictsCount}");
    if ($backupPath) {
      $this->line("   Backup: {$backupPath}");
    }
    $this->line("   File: {$userModelPath}");
    $this->info('----------------------------------------');

    if ($conflictsCount > 0) {
      $this->newLine();
      $this->warn('Run with --show-conflicts to see detailed conflict report');
    }
  }
}
