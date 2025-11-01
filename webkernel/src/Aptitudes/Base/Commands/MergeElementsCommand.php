<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use Webkernel\Aptitudes\Base\Commands\MergeElementsCommand\ExclusionManager;
use Webkernel\Aptitudes\Base\Commands\MergeElementsCommand\ChecksumGenerator;
use Webkernel\Aptitudes\Base\Commands\MergeElementsCommand\PathCollector;
use Webkernel\Aptitudes\Base\Commands\MergeElementsCommand\GenerateTree;
use ZipArchive;
use function Laravel\Prompts\text;
use function Laravel\Prompts\select;

class MergeElementsCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * Usage examples:
   * php artisan merge:elements --simple
   * php artisan merge:elements --unique=/path/to/dir
   * php artisan merge:elements --multiple
   * php artisan merge:elements --output=custom/path.txt
   * php artisan merge:elements --exclude=vendor,node_modules
   * php artisan merge:elements --use-gitignore
   * php artisan merge:elements --checksum
   *
   * @var string
   */
  protected $signature = 'merge:elements
                          {--simple : Quick mode - merge multiple paths without complexity}
                          {--unique= : Merge a single path}
                          {--multiple : Merge multiple paths interactively}
                          {--output= : Custom output path}
                          {--exclude= : Comma-separated exclusion patterns}
                          {--use-gitignore : Load exclusions from .gitignore}
                          {--checksum : Generate checksums for integrity verification}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Merge directories or files into a single output file or ZIP archive with exclusion patterns and integrity checks.';

  private ExclusionManager $exclusionManager;
  private ChecksumGenerator $checksumGenerator;
  private PathCollector $pathCollector;

  /**
   * Execute the console command.
   */
  public function handle(): int
  {
    $this->output->writeln($this->getBanner());

    $this->exclusionManager = new ExclusionManager($this);
    $this->checksumGenerator = new ChecksumGenerator($this);
    $this->pathCollector = new PathCollector($this);

    if ($this->option('use-gitignore')) {
      $this->exclusionManager->loadGitignore();
    }

    if ($excludePatterns = $this->option('exclude')) {
      $this->exclusionManager->addPatterns(explode(',', $excludePatterns));
    }

    $paths = $this->collectPaths();

    if (empty($paths)) {
      $this->warn('No paths provided. Operation cancelled.');
      return self::SUCCESS;
    }

    $this->displayPathsSummary($paths);

    if (!$this->option('simple') && !$this->confirm('Proceed with merging?', true)) {
      $this->info('Operation cancelled.');
      return self::SUCCESS;
    }

    $outputOption = $this->option('output');
    $outputOption ??= $this->determineOutputPath();
    $outputFile = $this->processOutputPath($outputOption);
    $basePath = base_path($outputFile);

    $basePath = $this->resolveUniqueOutputPath($basePath, $outputFile);

    $outputExtension = strtolower(pathinfo($outputFile, PATHINFO_EXTENSION));

    return $outputExtension === 'zip'
      ? $this->createZipArchive($paths, $basePath, $outputFile)
      : $this->createTextFile($paths, $basePath, $outputFile);
  }

  /**
   * Collect paths based on options.
   *
   * @return array<string>
   */
  private function collectPaths(): array
  {
    if ($this->option('simple')) {
      return $this->pathCollector->collectSimpleMode();
    }

    $hasUnique = $this->option('unique') !== null;

    if ($hasUnique) {
      return $this->pathCollector->collectUnique($this->option('unique'));
    }

    return $this->pathCollector->collectMultiple();
  }

  /**
   * Display paths summary with exclusion info.
   *
   * @param array<string> $paths
   * @return void
   */
  private function displayPathsSummary(array $paths): void
  {
    $this->info('Paths to process:');
    foreach ($paths as $path) {
      $this->line('  - ' . $path);
    }

    if ($this->exclusionManager->hasPatterns()) {
      $this->newLine();
      $this->comment('Active exclusion patterns:');
      foreach ($this->exclusionManager->getPatterns() as $pattern) {
        $this->line('  - ' . $pattern);
      }
    }

    $this->newLine();
    $this->askForExclusionPatterns();
  }

  /**
   * Ask user for exclusion patterns interactively.
   *
   * @return void
   */
  private function askForExclusionPatterns(): void
  {
    if ($this->option('simple')) {
      return;
    }

    $choice = select(
      label: 'Add exclusion patterns?',
      options: [
        'no' => 'No exclusions',
        'yes' => 'Yes (files and directories)',
        'pattern' => 'Pattern only',
        'gitignore' => 'Load from .gitignore',
      ],
      default: 'no',
    );

    if ($choice === 'no') {
      return;
    }

    if ($choice === 'gitignore') {
      $this->exclusionManager->loadGitignore();
      return;
    }

    if ($choice === 'yes' || $choice === 'pattern') {
      $this->info('Enter exclusion patterns (type "done" or "exit" when finished).');
      $this->comment('Examples: vendor/, *.log, node_modules/, tests/, Filament/Resources/*/Pages');
      $this->newLine();

      while (true) {
        $input = text(
          label: 'Add exclusion pattern or type "done"/"exit"',
          placeholder: 'e.g., vendor/, *.log, tests/',
          hint: 'Use arrows to navigate, comma-separated for multiple',
        );

        if ($input === null || trim($input) === '') {
          $this->warn('Empty input ignored.');
          continue;
        }

        $input = trim($input);

        if (strtolower($input) === 'done' || strtolower($input) === 'exit') {
          break;
        }

        $patterns = array_map('trim', explode(',', $input));
        $this->exclusionManager->addPatterns($patterns);

        foreach ($patterns as $pattern) {
          if (!empty($pattern)) {
            $this->info("Added exclusion: {$pattern}");
          }
        }
      }

      if ($this->exclusionManager->hasPatterns()) {
        $this->newLine();
        $this->info('Applied exclusion patterns:');
        foreach ($this->exclusionManager->getPatterns() as $pattern) {
          $this->line('  - ' . $pattern);
        }
      }
    }
  }

  /**
   * Determine output path if not provided.
   */
  private function determineOutputPath(): string
  {
    if ($this->option('simple')) {
      $timestamp = date('Y-m-d-H-i-s');
      return 'merged-data-' . $timestamp . '.txt';
    }

    $timestamp = date('Y-m-d-H-i-s');
    $defaultName = 'merged-data-' . $timestamp;

    $baseName = $this->ask(
      '<fg=red;options=bold>Enter output file name (without extension, relative to base path):</fg=red;options=bold>',
      $defaultName,
    );

    if (empty($baseName)) {
      $baseName = $defaultName;
    }

    $pathInfo = pathinfo($baseName);
    $assumedExtension = null;
    if (isset($pathInfo['extension'])) {
      $assumedExtension = strtolower($pathInfo['extension']);
      $baseNameWithoutExt = $pathInfo['filename'];
    } else {
      $baseNameWithoutExt = $baseName;
    }

    $chosenExtension = null;
    if ($assumedExtension) {
      $assumedType = $assumedExtension === 'txt' ? 'zip' : 'txt';
      $confirmation = $this->confirm(
        "Detected .{$assumedExtension} in name. Do you want to create a {$assumedType} file? (y/n)",
        true,
      );
      if ($confirmation) {
        $chosenExtension = $assumedType;
      } else {
        $chosenExtension = $this->askExtension();
      }
    } else {
      $chosenExtension = $this->askExtension();
    }

    $finalBaseName = $baseNameWithoutExt ?? $baseName;
    $outputPath = $finalBaseName . '.' . $chosenExtension;

    if ($chosenExtension === 'zip' && $assumedExtension === 'txt') {
      $outputPath = $finalBaseName . '.txt.zip';
    } elseif ($chosenExtension === 'txt' && $assumedExtension === 'zip') {
      $outputPath = $finalBaseName . '.zip.txt';
    }

    return $outputPath;
  }

  /**
   * Ask for the extension type.
   */
  private function askExtension(): string
  {
    $type = $this->ask('<fg=red;options=bold>Select output type (txt or zip):</fg=red;options=bold>', 'txt');

    $type = strtolower(trim($type));
    if (!in_array($type, ['txt', 'zip'])) {
      $this->warn('Invalid type. Defaulting to txt.');
      $type = 'txt';
    }

    return $type;
  }

  /**
   * Resolve a unique output path if the base path already exists.
   */
  private function resolveUniqueOutputPath(string $basePath, string $outputFile): string
  {
    while (File::exists($basePath)) {
      $ext = pathinfo($outputFile, PATHINFO_EXTENSION);
      $name = pathinfo($outputFile, PATHINFO_FILENAME);
      $randStr = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
      $suggestedBase = $name . '-' . $randStr . ($ext ? '.' . $ext : '');
      $dir = dirname($outputFile);
      $suggestedFull = $dir && $dir !== '.' ? rtrim($dir, '/') . '/' . $suggestedBase : $suggestedBase;

      $newOption = $this->ask(
        '<fg=red;options=bold>Output file already exists. Please provide a different path:</fg=red;options=bold>',
        $suggestedFull,
      );
      $outputFile = $this->processOutputPath($newOption);
      $basePath = base_path($outputFile);
    }

    return $basePath;
  }

  /**
   * Create a text file with merged content.
   */
  private function createTextFile(array $paths, string $basePath, string $outputFile): int
  {
    $outputBasename = basename($outputFile);
    $projectRoot = base_path();
    $dateTime = date('Y-m-d H:i:s');
    $sourceOrigin = App::runningInConsole() ? 'from console' : 'remotely';

    $this->info('Merging paths into text file...');
    $mergedContent = "# All paths are displayed relative to the project root.\n";
    $mergedContent .= "# Generated on {$dateTime} {$sourceOrigin}\n";

    if ($this->exclusionManager->hasPatterns()) {
      $mergedContent .= '# Exclusion patterns applied: ' . implode(', ', $this->exclusionManager->getPatterns()) . "\n";
    }

    $mergedContent .= "\n";

    $processedFiles = [];

    foreach ($paths as $path) {
      if (!File::exists($path)) {
        $this->warn("Skipped (not found): {$path}");
        continue;
      }

      $this->info("Processing: {$path}");

      $relativePath = ltrim(str_replace($projectRoot, '', $path), DIRECTORY_SEPARATOR);

      if (is_dir($path)) {
        $mergedContent .= "===== DIRECTORY TREE FOR: {$relativePath} =====\n";

        $filter = function (string $fullPath) use ($outputBasename): bool {
          if (basename($fullPath) === $outputBasename) {
            return false;
          }
          return $this->exclusionManager->shouldInclude($fullPath);
        };

        $treeOutput = GenerateTree::generateTree($path, $relativePath, $filter);
        $mergedContent .= $treeOutput;

        $mergedContent .= "\n\n===== FILES CONTENT =====\n\n";

        $files = collect(File::allFiles($path))
          ->sortBy(static fn(SplFileInfo $file): string => $file->getPathname())
          ->filter(
            fn(SplFileInfo $file): bool => $file->getFilename() !== $outputBasename &&
              $this->exclusionManager->shouldInclude($file->getPathname()),
          );

        foreach ($files as $file) {
          $relativeFile = ltrim(str_replace($projectRoot, '', $file->getPathname()), DIRECTORY_SEPARATOR);
          $mergedContent .= "\n===== {$relativeFile} =====\n";
          $mergedContent .= File::get($file->getPathname()) . "\n";
          $processedFiles[] = $file->getPathname();
        }
      } else {
        if ($this->exclusionManager->shouldInclude($path)) {
          $mergedContent .= "\n===== {$relativePath} =====\n";
          $mergedContent .= File::get($path) . "\n";
          $processedFiles[] = $path;
        }
      }

      $mergedContent .= "\n\n";
    }

    if ($this->option('checksum')) {
      $checksumData = $this->checksumGenerator->generate($processedFiles);
      $mergedContent .= "\n\n===== INTEGRITY CHECKSUMS =====\n";
      $mergedContent .= $checksumData;
    }

    File::put($basePath, $mergedContent);

    $this->info("Merging completed. Output written to: {$basePath}");

    if ($this->option('checksum')) {
      $this->checksumGenerator->saveToFile($processedFiles, $basePath . '.checksum');
    }

    return self::SUCCESS;
  }

  /**
   * Create a ZIP archive with individual files.
   */
  private function createZipArchive(array $paths, string $basePath, string $outputFile): int
  {
    $this->info('Creating ZIP archive...');

    $zip = new ZipArchive();

    if ($zip->open($basePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
      $this->error("Cannot create ZIP archive: {$basePath}");
      return self::FAILURE;
    }

    $processedCount = 0;
    $processedFiles = [];

    foreach ($paths as $path) {
      if (!File::exists($path)) {
        $this->warn("Skipped (not found): {$path}");
        continue;
      }

      $this->info("Adding to ZIP: {$path}");

      if (is_dir($path)) {
        $files = collect(File::allFiles($path))->filter(
          fn(SplFileInfo $file): bool => $this->exclusionManager->shouldInclude($file->getPathname()),
        );

        foreach ($files as $file) {
          $relativePath = $this->getRelativePath($file->getPathname());
          $zip->addFile($file->getPathname(), $relativePath);
          $processedCount++;
          $processedFiles[] = $file->getPathname();
        }

        $this->addEmptyDirectories($path, $zip);
      } else {
        if ($this->exclusionManager->shouldInclude($path)) {
          $relativePath = $this->getRelativePath($path);
          $zip->addFile($path, $relativePath);
          $processedCount++;
          $processedFiles[] = $path;
        }
      }
    }

    if ($this->option('checksum')) {
      $checksumContent = $this->checksumGenerator->generate($processedFiles);
      $zip->addFromString('CHECKSUMS.txt', $checksumContent);
    }

    $zip->close();

    $this->info("ZIP archive created successfully: {$basePath}");
    $this->info("Total files added: {$processedCount}");

    if ($this->option('checksum')) {
      $this->checksumGenerator->saveToFile($processedFiles, $basePath . '.checksum');
      $this->comment("Checksum file saved: {$basePath}.checksum");
    }

    return self::SUCCESS;
  }

  /**
   * Get relative path for ZIP archive.
   */
  private function getRelativePath(string $absolutePath): string
  {
    $basePath = base_path();
    if (str_starts_with($absolutePath, $basePath)) {
      return ltrim(substr($absolutePath, strlen($basePath)), '/\\');
    }
    return basename($absolutePath);
  }

  /**
   * Add empty directories to ZIP recursively.
   */
  private function addEmptyDirectories(string $directory, ZipArchive $zip): void
  {
    $items = scandir($directory) ?: [];

    foreach ($items as $item) {
      if ($item === '.' || $item === '..') {
        continue;
      }

      $fullPath = $directory . '/' . $item;

      if (is_dir($fullPath) && $this->exclusionManager->shouldInclude($fullPath)) {
        $relativePath = $this->getRelativePath($fullPath);
        $zip->addEmptyDir($relativePath);
        $this->addEmptyDirectories($fullPath, $zip);
      }
    }
  }

  /**
   * Process the output path to ensure it includes the 'merged-data-' prefix.
   */
  private function processOutputPath(string $input): string
  {
    $dir = dirname($input);
    if ($dir === '.') {
      $dir = '';
    }

    $base = basename($input);
    $ext = pathinfo($base, PATHINFO_EXTENSION);
    $name = pathinfo($base, PATHINFO_FILENAME);

    if (!str_starts_with($name, 'merged-data-')) {
      $name = 'merged-data-' . $name;
    }

    $newBase = $name . ($ext ? '.' . $ext : '');

    return $dir ? rtrim($dir, '/') . '/' . $newBase : $newBase;
  }

  /**
   * Get the enhanced banner ASCII art.
   */
  private function getBanner(): string
  {
    return <<<ASCII
    <fg=cyan>MERGE</fg=cyan> (•) <fg=cyan>ELEMENTS</fg=cyan>
    Advanced Directory & File Bundler
    Traceable, auditable outputs for modular architecture,
    backup workflows, and automated inspection of nested structures.
    <fg=cyan>
    <Export ZIP + TXT> <merging> <gitignore> <checksum>
    <Export Module> <Backup> <Code Export> <Integrity Check>
    </fg=cyan>
    ASCII;
  }

  /**
   * Merge paths into a text file programmatically.
   *
   * @param array<string> $paths
   * @param string $outputFile
   * @return int
   */
  public function mergeToTextFile(array $paths, string $outputFile): int
  {
    $outputFile = $this->processOutputPath($outputFile);
    $basePath = base_path($outputFile);
    $basePath = $this->resolveUniqueOutputPath($basePath, $outputFile);

    return $this->createTextFile($paths, $basePath, $outputFile);
  }

  /**
   * Merge paths into a ZIP archive programmatically.
   *
   * @param array<string> $paths
   * @param string $outputFile
   * @return int
   */
  public function mergeToZipArchive(array $paths, string $outputFile): int
  {
    $outputFile = $this->processOutputPath($outputFile);
    $basePath = base_path($outputFile);
    $basePath = $this->resolveUniqueOutputPath($basePath, $outputFile);

    return $this->createZipArchive($paths, $basePath, $outputFile);
  }
}
