<?php

namespace Webkernel\Console\Package;

use Illuminate\Console\Command;
use Exception;
use ParseError;

/**
 * TranslationHub - Advanced Multilingual Translation Management System
 *
 * Author: El Moumen Yassine
 * Email: yassine@numerimondes.com
 * Website: https://www.numerimondes.com
 * License: Mozilla Public License (MPL)
 *
 * Main Purpose: Automates translation from English to 53 languages with advanced data protection,
 * complete traceability and intelligent error recovery.
 *
 * Application Role: Centralized localization hub allowing developers to easily manage translations
 * without risking corruption of source data or losing critical metadata.
 *
 * Functional Scope:
 * - Creation/modification of translations with intelligent context
 * - Anti-overwrite protection system with temporal metadata
 * - Automatic repair of PHP syntax errors
 * - Complete backup/restoration with versioning
 * - Key refactoring with global consistency
 * - Validation and diagnostics without modification
 * - Automatic RTL/LTR support and placeholder preservation
 *
 * Planned Evolutions:
 * - API interface for graphical integration
 * - Database log storage
 * - Plugin system for new translation engines
 */

class TranslationHub extends Command
{
    // ==========================================
    // CONFIGURATION CONSTANTS
    // ==========================================

    /** @var int Threshold for slow operation warning (milliseconds) */
    private const SLOW_OPERATION_THRESHOLD_MS = 2000;

    /** @var int Number of empty lines for visual separation */
    private const VISUAL_SEPARATOR_LINES = 3;

    /** @var int Microseconds sleep between bulk operations */
    private const BULK_OPERATION_DELAY_US = 100000;

    /** @var int Maximum feedback silence time (milliseconds) */
    private const MAX_SILENCE_TIME_MS = 2500;

    /** @var int Time before reassuring user (milliseconds) */
    private const TTL_BEFORE_REASSURING_USER = 3000;

    // ==========================================
    // STATISTICS TRACKING
    // ==========================================

    private $translationStats = [
        'times' => [],
        'engines_used' => [],
        'fallbacks' => [],
        'complete_failures' => [],
        'incidents' => [],
        'language_times' => [],
        'total_failures' => 0
    ];

    // ==========================================
    // VISUAL SEPARATION HELPERS
    // ==========================================

    /**
     * Create visual separation between operations
     */
    private function addVisualSeparator()
    {
        for ($i = 0; $i < self::VISUAL_SEPARATOR_LINES; $i++) {
            $this->line('');
        }
    }

    protected $signature = 'webkernel:lang {text? : English text to translate} {key? : Translation key (optional)} ' .
                          '{--change-key : Change existing key mode} ' .
                          '{--old-key= : Old key to change (use with --change-key)} ' .
                          '{--new-key= : New key to change to (use with --change-key)} ' .
                          '{--restore : Restore from backup} ' .
                          '{--validate-only : Only validate existing files} ' .
                          '{--repair : Repair syntax errors in existing files} ' .
                          '{--retranslate : Retranslate all existing entries from English} ' .
                          '{--lang= : Target specific language for retranslation (e.g., --lang=fr)} ' .
                          '{--protect : Mark keys as protected (interactive or with keys)} ' .
                          '{--unprotect : Remove protection from specific keys (no bulk unprotect)} ' .
                          '{--before= : Unprotect keys protected before this time (epoch timestamp or relative like "1d", "1w")} ' .
                          '{--after= : Unprotect keys protected after this time (epoch timestamp or relative)} ' .
                          '{--keys= : Specify keys for --protect/--unprotect (comma-separated, e.g., --keys=key1,key2)} ' .
                          '{--all-langs : Apply protection to all languages (use with --protect)} ' .
                          '{--migrate-timestamps : Add missing protected_at timestamps to existing protected entries}';

    protected $description = 'Webkernel Dev-Tools: TranslationHub - Advanced multilingual translation management for Laravel development';

    /**
     * Laravel 12+ : override aliases using method.
     */
    public function getAliases(): array
    {
        return ['webkernel:translation-hub', 'webkernel:translate'];
    }

    /**
     * TranslationHub - Architecture Documentation
     *
     * Author: El Moumen Yassine
     * Email: yassine@numerimondes.com
     *
     * Main Purpose: Intelligent multilingual management system automating translation to multiple languages,
     * with advanced data protection, traceability and error recovery.
     *
     * Role: Centralized localization hub to manage translations while maintaining source consistency and security.
     *
     * Functional Scope: translation creation/modification, anti-overwrite protection, validation, backup,
     * key refactoring, diagnostics, etc.
     *
     * Recommended Script Structure:
     * 1. Properties (config, variables)
     * 2. handle() method as entry point
     * 3. Private/utility methods (validation, protection, processing)
     * 4. Centralized output() method for all output (logs, errors, success)
     * 5. Centralized error handling with rollback if necessary
     * 6. Configuration externalized in config/webkernel.php
     *
     * IMPORTANT:
     * - Never work directly on TranslateTerm.php: create/modify only TranslationHub.php.
     * - With each major modification (structural, functional addition or major correction),
     *   update this comment to track evolution.
     * - All validations and input checks must be centralized and systematic.
     * - All output must go through output() to facilitate evolution to graphical interfaces or database storage.
     */

    // 1. PROPERTIES (config, variables)
    protected $baseDir;
    protected $backupDir;
    protected $selectedEngine;
    protected $translationContext;
    protected $config;
    protected $errorLog = [];
    protected $outputBuffer = [];

    // Enhanced properties from TranslateTerm
    protected $locationMappings = [];
    protected $overrideKeys = [];
    protected $wordSubstitutions = [];
    protected $rtlLanguages = [];
    protected $retryAttempts = 3;
    protected $lastContextDestination;
    protected $protectedPlaceholders = [];

    /**
     * 2. HANDLE() METHOD - Main entry point
     */
    public function handle()
    {
        try {
            $this->initializeConfig();
            $this->validateEnvironment();
            $this->selectTargetDirectory();
            $this->initializeBackupDir();
            $this->setupSignalHandling();

            return $this->routeToHandler();

        } catch (Exception $e) {
            $this->output('error', 'Error encountered: ' . $e->getMessage());
            $this->output('warning', 'Attempting recovery...');

            // Try to recover gracefully
            try {
                $this->initializeBasicConfig();
                $this->output('success', 'Recovery successful - continuing with basic configuration');
                return $this->routeToHandler();
            } catch (Exception $recoveryError) {
                $this->output('error', 'Recovery failed: ' . $recoveryError->getMessage());
                $this->output('info', 'Please check your configuration and try again');
                return 1; // Return error code but don't terminate harshly
            }
        }
    }

    private function initializeBasicConfig(): void
    {
        // Initialize with minimal working configuration
        $this->config = [
            'languages' => [
                'en' => 'English',
                'ar' => 'Arabic',
                'fr' => 'French'
            ],
            'rtl_languages' => ['ar'],
            'translation' => [
                'engines' => ['bing', 'google']
            ],
            'priority_ticket_languages' => ['en', 'ar', 'fr'],
            'protection' => [
                'auto_backup' => false,
                'protected_source' => true
            ],
            'word_replacement_enabled' => false // Set to true to enable "Enter a word to replace" prompts
        ];

        // Set basic directory if not set
        if (empty($this->baseDir)) {
            $this->baseDir = base_path('packages/webkernel/src/lang');
        }

        $this->output('info', 'Using basic configuration for recovery');
    }

    /**
     * 3. PRIVATE/UTILITY METHODS
     */

    private function setupSignalHandling()
    {
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, function() {
                $this->output('warning', 'Operation interrupted by user');
                exit(1);
            });
            pcntl_signal(SIGTERM, function() {
                $this->output('warning', 'Operation terminated');
                exit(1);
            });
        }
    }

    private function initializeConfig()
    {
        // Priority 1: Main Laravel config/webkernel.php
        $this->config = config('webkernel', []);

        // Priority 2: Package fallback packages/webkernel/src/config/webkernel.php
        if (empty($this->config)) {
            $packageConfigPath = base_path('packages/webkernel/src/config/webkernel.php');
            if (file_exists($packageConfigPath)) {
                $packageConfig = include $packageConfigPath;
                // Register the package config in Laravel's config system
                config(['webkernel' => $packageConfig]);
                $this->config = config('webkernel', []);
            }
        }

        // Config should always be found from package
        if (empty($this->config)) {
            throw new Exception('WebKernel configuration not found. Package config should always be available.');
        }

        $this->validateConfig();
    }

    private function getDefaultConfig()
    {
        return [
            'engines' => ['bing', 'google', 'yandex'],
            'languages' => [
                'en' => 'en', 'ar' => 'ar', 'fr' => 'fr',
                'az' => 'az', 'bg' => 'bg', 'bn' => 'bn', 'ha' => 'ha', 'ca' => 'ca',
                'ckb' => 'ku', 'cs' => 'cs', 'da' => 'da', 'de' => 'de', 'el' => 'el',
                'es' => 'es', 'fa' => 'fa', 'fi' => 'fi', 'he' => 'he', 'hi' => 'hi',
                'hr' => 'hr', 'hu' => 'hu', 'hy' => 'hy', 'id' => 'id', 'it' => 'it', 'ja' => 'ja',
                'ka' => 'ka', 'km' => 'km', 'ko' => 'ko', 'ku' => 'ku', 'lt' => 'lt', 'lv' => 'lv',
                'mk' => 'mk', 'ml' => 'ml', 'mn' => 'mn', 'ms' => 'ms', 'my' => 'my', 'ne' => 'ne',
                'nl' => 'nl', 'no' => 'no', 'pa' => 'pa', 'pl' => 'pl', 'ps' => 'ps', 'pt' => 'pt',
                'ro' => 'ro', 'ru' => 'ru', 'si' => 'si', 'sk' => 'sk', 'sl' => 'sl', 'so' => 'so',
                'sq' => 'sq', 'sr' => 'sr', 'sv' => 'sv', 'sw' => 'sw', 'ta' => 'ta', 'th' => 'th',
                'tr' => 'tr', 'uk' => 'uk', 'ur' => 'ur', 'uz' => 'uz', 'vi' => 'vi',
                'zh' => 'zh', 'zh_CN' => 'zh-cn', 'zh_TW' => 'zh-tw'
            ],
            'rtl_languages' => ['ar', 'fa', 'he', 'ur', 'ps', 'ckb', 'ku'],
            'protection' => [
                'auto_backup' => true,
                'retain_backups' => 30,
                'protected_source' => true
            ],
            'output' => [
                'format' => 'console',
                'detail_level' => 'normal',
                'colors' => true
            ]
        ];
    }

    private function validateConfig()
    {
        // Support both direct config and translation.* structure
        $configSection = $this->config['translation'] ?? $this->config;

        $required = ['engines', 'languages', 'rtl_languages'];

        foreach ($required as $key) {
            if (!isset($configSection[$key]) || empty($configSection[$key])) {
                $this->output('warning', "Missing config: {$key}, using defaults");
                $this->useRecoveryConfig();
                return;
            }
        }

        // Update config to use the correct section
        if (isset($this->config['translation'])) {
            $this->config = $this->config['translation'];
        }
    }

    private function useRecoveryConfig()
    {
        $this->output('info', 'Attempting recovery...');
        $this->output('info', 'Using basic configuration for recovery');

        $this->config = [
            'engines' => ['bing', 'google'],
            'priority_ticket_languages' => ['en', 'ar', 'fr'],
            'languages' => [
                'en' => 'en', 'ar' => 'ar', 'fr' => 'fr',
                'az' => 'az', 'bn' => 'bn', 'de' => 'de', 'es' => 'es'
            ],
            'rtl_languages' => ['ar'],
            'language_names' => [
                'en' => 'English', 'ar' => 'Arabic', 'fr' => 'French',
                'az' => 'Azerbaijani', 'bn' => 'Bengali', 'de' => 'German', 'es' => 'Spanish'
            ],
            'native_names' => [
                'ar' => 'العربية', 'fr' => 'Français', 'az' => 'Azərbaycan dili', 'bn' => 'বাংলা'
            ]
        ];

        $this->output('success', 'Recovery successful - continuing with basic configuration');
    }

    private function validateEnvironment()
    {
        if (!$this->validateInput()) {
            throw new Exception('Invalid input parameters provided');
        }

        $this->selectTranslationEngine();
    }

    private function validateInput(): bool
    {
        $text = $this->argument('text');
        $key = $this->argument('key');

        // Skip validation for option-based commands
        if ($this->hasOptions()) {
            return true;
        }

        if (empty($text) && empty($key)) {
            // Show help first
            $this->showHelp();

            // Ask user if they want interactive mode with validation
            $wantInteractive = $this->askWithValidation(
                "Do you want to enter interactive translation mode? (y/N)",
                [$this, 'validateYesNo'],
                "Please enter 'y' for yes, 'n' for no, or press Enter for default (no).",
                'n'
            );

            if ($wantInteractive === null) return false; // Max attempts reached

            if (strtolower($wantInteractive) === 'y' || strtolower($wantInteractive) === 'yes') {
                $this->enterInteractiveMode();
                return true;
            } else {
                $this->output('info', 'Webkernel Dev-Tools: Use webkernel:lang "text" for quick translation or webkernel:lang --help for more options');
                return false;
            }
        }

        if (!empty($key) && !preg_match('/^[a-zA-Z0-9_\-\.]+$/', $key)) {
            $this->output('error', 'Key contains invalid characters. Use only letters, numbers, hyphens, underscores, and dots');
            return false;
        }

        return true;
    }

    private function hasOptions(): bool
    {
        return $this->option('repair') || $this->option('retranslate') ||
               $this->option('protect') || $this->option('unprotect') ||
               $this->option('restore') || $this->option('validate-only') ||
               $this->option('change-key') || $this->option('migrate-timestamps');
    }

    private function selectTranslationEngine()
    {
        $engines = $this->config['engines'];

        $this->output('info', 'Testing translation engines...');
        $this->addVisualSeparator();

        foreach ($engines as $engine) {
            if ($this->isEngineAvailable($engine)) {
                $this->selectedEngine = $engine;
                $this->output('success', "Using {$engine} translation engine");
                $this->addVisualSeparator();
                return;
            }

            $this->output('warning', "{$engine} not available");
        }

        $this->selectedEngine = 'google';
        $this->output('warning', 'Using fallback engine (google) - quality may vary');
    }

    private function isEngineAvailable($engine): bool
    {
        $testText = 'Hello';
        $expectedTranslation = 'Bonjour';

        $this->line("<fg=cyan>→ Testing {$engine} translation engine...</>");
        $this->line("  <fg=white>• Test phrase:</> <fg=yellow>'{$testText}'</> <fg=cyan>→</> <fg=green>French</>");

        $cmd = "trans -e {$engine} -brief en:fr " . escapeshellarg($testText) . " 2>/dev/null";
        $this->line("  <fg=white>• Command:</> <fg=gray>{$cmd}</>");

        $this->line("  <fg=white>• Sending request to {$engine} servers...</>");
        $this->line("  <fg=white>• Establishing connection...</>");
        $this->line("  <fg=white>• Transmitting test phrase...</>");

        $startTime = microtime(true);
        $result = trim(shell_exec($cmd));

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $this->line("  <fg=white>• Response received in {$duration}ms</>");
        $this->line("  <fg=white>• Raw result:</> <fg=yellow>'{$result}'</>");
        $this->line("  <fg=white>• Expected:</> <fg=green>'{$expectedTranslation}'</>");

        $isWorking = stripos($result, $expectedTranslation) !== false;

        if (empty($result)) {
            $this->line("  <fg=red>✗ Engine returned empty result - connection or service issue</>");
        } elseif ($result === $testText) {
            $this->line("  <fg=red>✗ Engine returned original text unchanged - translation failed</>");
        } elseif ($isWorking) {
            $this->line("  <fg=green>✓ Engine working correctly - translation successful</>");
        } else {
            $this->line("  <fg=red>✗ Engine returned unexpected result - may not contain expected translation</>");
        }

        return $isWorking;
    }

    private function selectTargetDirectory()
    {
        $locations = [
            'packages/webkernel/src/lang',
            'app/lang',
            'database'
        ];

        $this->output('info', 'Available translation locations:');
        $this->output('info', '  [1] packages/webkernel/src/lang');
        $this->output('info', '  [2] app/lang');
        $this->output('info', '  [3] database (webkernel_lang_words table via Eloquent)');

        $choice = $this->askWithValidation(
            'Choose location number',
            function($input) { return in_array($input, ['1', '2', '3']); },
            'Please enter 1, 2, or 3 to select a valid translation location.',
            '1'
        );

        $selectedIndex = intval($choice) - 1;

        if ($selectedIndex === 2) {
            $this->baseDir = 'database';
            $this->output('info', 'Target location: Database (webkernel_lang_words table)');
            $this->output('info', 'Model: Webkernel\\Models\\LanguageTranslation');
            $this->output('warning', 'Database insertion temporarily disabled - structure changes pending');
        } else {
            if (!isset($locations[$selectedIndex]) || $selectedIndex < 0) {
                $selectedIndex = 0;
            }
            $this->baseDir = base_path($locations[$selectedIndex]);
            $this->output('info', "Target location: {$this->baseDir}");
        }
    }

    private function initializeBackupDir()
    {
        if (!$this->config['protection']['auto_backup']) {
            return;
        }

        try {
            $packageName = basename(dirname($this->baseDir, 2));
            $timestamp = date('Y-m-d_H-i-s');
            $this->backupDir = storage_path("translation_backups/{$packageName}/{$timestamp}");

            if (!is_dir($this->backupDir)) {
                mkdir($this->backupDir, 0755, true);
            }

            $this->output('info', "Backup directory: {$this->backupDir}");

        } catch (Exception $e) {
            $this->output('warning', 'Could not create backup directory: ' . $e->getMessage());
            $this->backupDir = sys_get_temp_dir() . '/translation_backups_' . time();
            mkdir($this->backupDir, 0755, true);
        }
    }

    private function routeToHandler()
    {
        // Handle options in order of priority
        if ($this->option('repair')) return $this->handleRepair();
        if ($this->option('retranslate')) return $this->handleRetranslate();
        if ($this->option('protect')) return $this->handleProtect();
        if ($this->option('unprotect')) return $this->handleUnprotect();
        if ($this->option('migrate-timestamps')) return $this->handleMigrateTimestamps();
        if ($this->option('restore')) return $this->handleRestore();
        if ($this->option('validate-only')) return $this->handleValidateOnly();
        if ($this->option('change-key')) return $this->handleChangeKey();

        // Default: Add new translation
        return $this->handleAddTranslation();
    }

    /**
     * Handlers for different operations
     */

    private function handleAddTranslation()
    {
        $text = $this->argument('text');
        $key = $this->argument('key');

        if (empty($text)) {
            $text = $this->ask('Enter the English text to translate');
        }

        if (empty($key)) {
            $key = $this->ask('Enter translation key (or press Enter to auto-generate)');
            if (empty($key)) {
                $key = $this->generateKeyFromText($text);
            }
        }

        return $this->processNewTranslation($text, $key);
    }

    private function handleRetranslate()
    {
        $targetLang = $this->option('lang');
        $languagesToProcess = $targetLang ?
            [$targetLang => $this->config['languages'][$targetLang]] :
            $this->config['languages'];

        if (!$this->analyzeRetranslationScope($languagesToProcess)) {
            return 1;
        }

        if (!$this->confirm('Do you want to proceed with retranslation?')) {
            $this->output('info', 'Retranslation cancelled.');
            return 0;
        }

        return $this->executeRetranslation($languagesToProcess);
    }

    private function handleValidateOnly()
    {
        $this->output('info', 'Validating translation files...');

        $totalFiles = 0;
        $validFiles = 0;
        $invalidFiles = [];

        foreach ($this->config['languages'] as $locale => $code) {
            $filePath = $this->getLanguageFilePath($locale);
            if (file_exists($filePath)) {
                $totalFiles++;

                if ($this->validateTranslationFile($filePath)) {
                    $validFiles++;
                    $this->output('success', "→ {$locale}: Valid");
                } else {
                    $invalidFiles[] = $locale;
                    $this->output('error', "→ {$locale}: Invalid syntax");
                }
            }
        }

        $this->output('info', "Validation completed!");
        $this->output('info', "Valid files: {$validFiles}/{$totalFiles}");

        if (!empty($invalidFiles)) {
            $this->output('warning', 'Invalid files found: ' . implode(', ', $invalidFiles));
            $this->output('info', 'Use --repair to fix syntax errors');
            return 1;
        }

        return 0;
    }

    private function handleRepair()
    {
        $this->output('info', 'Repairing translation files...');

        $repaired = 0;
        $errors = 0;

        foreach ($this->config['languages'] as $locale => $code) {
            // Protect English source file from repair
            if ($locale === 'en' && $this->config['protection']['protected_source']) {
                $this->output('warning', "→ Skipping English source file (protected)");
                continue;
            }

            $filePath = $this->getLanguageFilePath($locale);
            if (file_exists($filePath) && !$this->validateTranslationFile($filePath)) {

                if ($this->repairTranslationFile($filePath, $locale)) {
                    $repaired++;
                    $this->output('success', "→ {$locale}: Repaired");
                } else {
                    $errors++;
                    $this->output('error', "→ {$locale}: Repair failed");
                }
            }
        }

        $this->output('info', "Repair completed!");
        $this->output('info', "Repaired: {$repaired}");
        $this->output('info', "Errors: {$errors}");

        return $errors > 0 ? 1 : 0;
    }

    private function handleChangeKey()
    {
        $oldKey = $this->option('old-key') ?: $this->ask('Enter the old key to change');
        $newKey = $this->option('new-key') ?: $this->ask('Enter the new key');

        if (empty($oldKey) || empty($newKey)) {
            return $this->handleError('Both old and new keys are required');
        }

        $this->output('info', "Changing key '{$oldKey}' to '{$newKey}' in all languages...");

        $changed = 0;
        $errors = 0;

        foreach ($this->config['languages'] as $locale => $code) {
            $filePath = $this->getLanguageFilePath($locale);

            if (file_exists($filePath)) {
                if ($this->changeKeyInFile($filePath, $oldKey, $newKey, $locale)) {
                    $changed++;
                } else {
                    $errors++;
                }
            }
        }

        $this->output('info', "Key change completed!");
        $this->output('info', "Changed: {$changed}");
        $this->output('info', "Errors: {$errors}");

        return $errors > 0 ? 1 : 0;
    }

    private function handleProtect() { return $this->handleProtectionOperation(true); }
    private function handleUnprotect() { return $this->handleProtectionOperation(false); }
    private function handleMigrateTimestamps() { return $this->migrateProtectionTimestamps(); }
    private function handleRestore() { return $this->restoreFromBackup(); }

    /**
     * Core processing methods
     */

    private function processNewTranslation($text, $key)
    {
        $this->translationContext = $this->gatherContextFromUser();

        $this->output('info', "Processing translation for key: {$key}");
        $this->output('info', "Text: {$text}");

        if (!empty($this->translationContext)) {
            $this->output('info', "Context: {$this->translationContext}");
        }

        $this->createBackupIfNeeded();

        $successful = 0;
        $failed = 0;

        // 1. Create backup first
        $this->createBackupIfNeeded();
        $this->output('info', '');

        // 2. Generate translation previews with verbose messaging
        $this->output('info', 'Generating translation previews...');
        $this->output('info', 'Testing translation engines and generating preview tickets...');
        $this->output('info', '');

        $previewTranslations = $this->generateTranslationPreviews($key, $text);

        // 3. Show all translation tickets
        $this->displayTranslationTickets($key, $text, $previewTranslations);

        // 4. Ask for confirmation with Change and Resume options
        while (true) {
            $confirm = $this->ask('Do you want to proceed with bulk translation and file creation? (y/N/C/R) [N]', 'n');
            $confirm = strtolower(trim($confirm));

            if ($confirm === 'y' || $confirm === 'yes') {
                break; // Proceed with translation
            } elseif ($confirm === 'c' || $confirm === 'change') {
                $this->output('info', 'Changing context...');
                $this->addVisualSeparator();

                // Restart the interactive process without asking for mode again
                return $this->processNewTranslation($text, $key);
            } elseif ($confirm === 'r' || $confirm === 'restart') {
                $this->output('info', 'Reprend la traduction...');
                $this->addVisualSeparator();

                // Start completely fresh from the beginning
                return $this->enterInteractiveMode();
            } else {
                $this->output('info', 'Translation cancelled by user');
                return 0;
            }
        }

        // 5. Process ALL translations in bulk with file creation
        $this->output('info', 'Starting bulk translation and file creation...');
        $this->output('info', '');

        $languageNames = [
            'en' => 'English', 'ar' => 'Arabic', 'fr' => 'French', 'es' => 'Spanish',
            'de' => 'German', 'it' => 'Italian', 'pt' => 'Portuguese', 'ru' => 'Russian',
            'zh' => 'Chinese', 'ja' => 'Japanese', 'ko' => 'Korean', 'hi' => 'Hindi',
            'bg' => 'Bulgarian', 'ca' => 'Catalan', 'cs' => 'Czech', 'da' => 'Danish',
            'nl' => 'Dutch', 'fi' => 'Finnish', 'el' => 'Greek', 'he' => 'Hebrew',
            'hu' => 'Hungarian', 'id' => 'Indonesian', 'ga' => 'Irish', 'lv' => 'Latvian',
            'lt' => 'Lithuanian', 'mk' => 'Macedonian', 'ms' => 'Malay', 'mt' => 'Maltese',
            'no' => 'Norwegian', 'pl' => 'Polish', 'ro' => 'Romanian', 'sk' => 'Slovak',
            'sl' => 'Slovenian', 'sv' => 'Swedish', 'th' => 'Thai', 'tr' => 'Turkish',
            'uk' => 'Ukrainian', 'vi' => 'Vietnamese', 'cy' => 'Welsh'
        ];
        // FIRST: Create English source file (protected by default)
        if ($this->saveTranslation('en', $key, $text)) {
            $successful++;
            $this->output('success', "✓ English: Source created and protected");
        } else {
            $failed++;
            $this->output('error', "✗ English: Failed to create source");
        }

        $totalLanguages = count($this->config['languages']) - 1; // Exclude English
        $currentCount = 0;

        foreach ($this->config['languages'] as $locale => $code) {
            if ($locale === 'en') {
                continue; // English already processed above
            }

            try {
                $currentCount++;
                $languageName = $languageNames[$locale] ?? ucfirst($locale);

                $this->output('info', "Translating {$currentCount}/{$totalLanguages}: {$languageName} ({$locale})...");
                $this->output('info', "  → Sending text to translation engine ({$this->selectedEngine})...");
                $translation = $this->translateText($text, $code);
                $this->output('info', "  → Translation received, processing result...");
                $this->output('info', "  → Creating language files and directories...");
                $this->output('info', "  → Preparing file structure...");
                $this->output('info', "  → Writing translation data...");

                $saveStartTime = microtime(true);
                $saveResult = $this->saveTranslation($locale, $key, $translation);
                $saveDuration = round((microtime(true) - $saveStartTime) * 1000, 2);

                if ($saveResult) {
                    $successful++;
                    $this->output('success', "✓ {$languageName}: Translation saved ({$saveDuration}ms)");
                } else {
                    $failed++;
                    $this->output('error', "✗ {$languageName}: Save failed");
                }

                $this->addVisualSeparator();
                usleep(self::BULK_OPERATION_DELAY_US); // Rate limiting for bulk operations

            } catch (Exception $e) {
                $failed++;
                $this->output('error', "✗ {$languageName}: Translation failed - {$e->getMessage()}");
            }
        }

        // Display comprehensive translation statistics instead of simple message
        $this->displayTranslationStatistics();

        $this->addVisualSeparator();

        // Ask if user wants to add another translation
        $addAnother = $this->askWithValidation(
            "Do you want to add a new translation? (y/N)",
            [$this, 'validateYesNo'],
            "Please enter 'y' for yes, 'n' for no, or press Enter for default (no).",
            'n'
        );

        if ($addAnother && (strtolower($addAnother) === 'y' || strtolower($addAnother) === 'yes')) {
            $this->addVisualSeparator();
            return $this->enterInteractiveMode();
        }

        return $failed > 0 ? 1 : 0;
    }

    private function analyzeRetranslationScope($languagesToProcess)
    {
        $englishFile = $this->getLanguageFilePath('en');
        if (!file_exists($englishFile)) {
            $this->output('error', 'English source file not found!');
            return false;
        }

        $englishContent = include $englishFile;
        if (!is_array($englishContent) || !isset($englishContent['lang_ref'])) {
            $this->output('error', 'Invalid English file format!');
            return false;
        }

        $englishEntries = $englishContent['lang_ref'];
        $totalKeys = count($englishEntries);
        $languageCount = count($languagesToProcess) - 1; // Exclude English itself
        $protectedCount = 0;
        $unprotectedCount = 0;

        // Count protected vs unprotected entries in target languages
        foreach ($englishEntries as $key => $entry) {
            $hasProtectedInTargets = false;
            foreach ($languagesToProcess as $locale => $code) {
                if ($locale === 'en') continue;
                if ($this->isTranslationProtected($locale, $key)) {
                    $hasProtectedInTargets = true;
                    break;
                }
            }

            if ($hasProtectedInTargets) {
                $protectedCount++;
            } else {
                $unprotectedCount++;
            }
        }

        $this->output('info', '=== RETRANSLATION ANALYSIS ===');
        $this->output('info', "Source entries (English): {$totalKeys}");
        $this->output('info', "Target languages: {$languageCount}");
        $this->output('info', "Protected entries: {$protectedCount}");
        $this->output('info', "Unprotected entries: {$unprotectedCount}");
        $this->output('info', "Total operations: " . ($unprotectedCount * $languageCount));

        if ($protectedCount > 0) {
            $this->output('warning', "NOTE: {$protectedCount} protected entries will be skipped");
        }

        return true;
    }

    /**
     * 4. MÉTHODE OUTPUT() CENTRALISÉE
     */
    public function output($type, $message, $data = [])
    {
        $output = [
            'type' => $type,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()
        ];

        $this->outputBuffer[] = $output;

        // Current implementation: console output
        $this->formatConsoleOutput($type, $message, $data);

        // Future: database logging, API responses, etc.
    }

    private function formatConsoleOutput($type, $message, $data = [])
    {
        switch ($type) {
            case 'success':
                $this->info($message);
                break;
            case 'error':
                $this->error($message);
                break;
            case 'warning':
                $this->warn($message);
                break;
            case 'progress':
                $this->line("-> {$message}");
                break;
            case 'info':
            default:
                $this->line($message);
                break;
        }

        if (!empty($data) && $this->config['output']['detail_level'] === 'verbose') {
            foreach ($data as $key => $value) {
                $this->line("  {$key}: {$value}");
            }
        }
    }

    /**
     * 5. GESTION D'ERREURS CENTRALISÉE
     */
    public function handleError($message, $context = [], $fatal = false): int
    {
        $error = [
            'message' => $message,
            'context' => $context,
            'timestamp' => now(),
            'fatal' => $fatal
        ];

        $this->errorLog[] = $error;

        $this->output('error', $message, $context);

        if ($fatal) {
            $this->output('error', 'Critical error occurred - operation terminated');
            $this->displayErrorLog();
            return 1;
        }

        return 0;
    }

    private function displayErrorLog()
    {
        if (empty($this->errorLog)) {
            return;
        }

        $this->output('warning', 'Error Summary:');
        foreach ($this->errorLog as $error) {
            $timestamp = $error['timestamp']->format('H:i:s');
            $this->output('error', "[{$timestamp}] {$error['message']}");

            if (!empty($error['context']) && $this->config['output']['detail_level'] === 'verbose') {
                foreach ($error['context'] as $key => $value) {
                    $this->output('info', "  {$key}: {$value}");
                }
            }
        }
    }

    /**
     * Translation and file processing methods
     */

    private function translateText($text, $targetLanguageCode)
    {
        // Store current target language for failure detection
        $this->currentTargetLanguage = $targetLanguageCode;

        $this->output('info', "    • Optimizing text for translation...");
        $optimizedText = $this->optimizeTextForTranslation($text);

        $this->output('info', "    • Connecting to {$this->selectedEngine} translation service...");
        $cmd = "trans -e {$this->selectedEngine} -brief en:{$targetLanguageCode} " . escapeshellarg($optimizedText) . " 2>/dev/null";

        $this->output('info', "    • Sending request to translation server...");
        $this->output('info', "    • Connecting to {$this->selectedEngine} servers...");
        $this->output('info', "    • Transmitting text for translation...");

        $startTime = microtime(true);

        // Execute command with progress monitoring
        $result = $this->executeWithProgressFeedback($cmd, "Translating text via {$this->selectedEngine}");

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        // Track translation statistics
        $this->translationStats['times'][] = $duration;
        $this->translationStats['engines_used'][$this->selectedEngine] =
            ($this->translationStats['engines_used'][$this->selectedEngine] ?? 0) + 1;

        // Track per-language timing
        if (isset($this->currentTargetLanguage)) {
            $this->translationStats['language_times'][$this->currentTargetLanguage] = $duration;
        }

        if ($duration > self::SLOW_OPERATION_THRESHOLD_MS) {
            $this->line("    <fg=yellow>• Response took {$duration}ms (slower than usual)</>");
        } else {
            $this->line("    <fg=green>• Response received in {$duration}ms</>");
        }

        $this->output('info', "    • Processing and cleaning translation result...");
        return $this->cleanTranslationResult($result, $text);
    }

    private function optimizeTextForTranslation($text)
    {
        $optimized = $text;

        // STEP 1: Protect placeholders before any processing
        $protectedPlaceholders = $this->extractAndProtectPlaceholders($optimized);
        $optimized = $protectedPlaceholders['text'];

        // STEP 2: Apply runtime word substitutions from user input
        $optimized = $this->applyWordSubstitutions($optimized);

        // STEP 3: Apply config-based word substitutions if available
        if (!empty($this->config['word_substitutions'])) {
            foreach ($this->config['word_substitutions'] as $from => $to) {
                $optimized = str_ireplace($from, $to, $optimized);
            }
        }

        // STEP 4: Add context if available using the >)>>>> ("in") <<<<(< marker
        if (!empty($this->translationContext)) {
            $optimized = "{$optimized} >)>>>> (\"in\") <<<<(< {$this->translationContext}";
        }

        // Store placeholders for restoration after translation
        $this->protectedPlaceholders = $protectedPlaceholders['placeholders'];

        return $optimized;
    }

    /**
     * Extract and protect placeholders from translation
     */
    private function extractAndProtectPlaceholders($text)
    {
        $placeholders = [];
        $protectedText = $text;

        $this->output('info', "Analyzing text for placeholders: {$text}");

        // Pattern for Laravel placeholders: :word, :attribute, etc.
        preg_match_all('/(:[a-zA-Z_][a-zA-Z0-9_]*\b)/', $text, $matches);
        $this->output('info', "Found " . count($matches[0]) . " Laravel placeholders");

        foreach ($matches[0] as $index => $placeholder) {
            $token = "__PLACEHOLDER_{$index}__";
            $placeholders[$token] = $placeholder;
            $protectedText = str_replace($placeholder, $token, $protectedText);
            $this->line("<fg=green>Protected:</> <fg=yellow>{$placeholder}</> <fg=cyan>→</> <fg=magenta>{$token}</>");
        }

        // Pattern for printf placeholders: %s, %d, %1$s, etc.
        preg_match_all('/(%[a-zA-Z0-9\$]+)/', $protectedText, $matches);
        $this->output('info', "Found " . count($matches[0]) . " printf placeholders");

        foreach ($matches[0] as $index => $placeholder) {
            $token = "__PRINTF_{$index}__";
            $placeholders[$token] = $placeholder;
            $protectedText = str_replace($placeholder, $token, $protectedText);
            $this->line("<fg=green>Protected:</> <fg=cyan>{$placeholder}</> <fg=cyan>→</> <fg=magenta>{$token}</>");
        }

        $this->output('info', "Protected text: {$protectedText}");

        return [
            'text' => $protectedText,
            'placeholders' => $placeholders
        ];
    }

    private function applyWordSubstitutions($text)
    {
        if (empty($this->wordSubstitutions)) {
            return $text;
        }

        $optimized = $text;
        foreach ($this->wordSubstitutions as $word => $replacement) {
            $optimized = str_ireplace($word, $replacement, $optimized);
        }

        return $optimized;
    }





    private function saveTranslation($locale, $key, $translation)
    {
        $filePath = $this->getLanguageFilePath($locale);

        // Ensure directory exists
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                $this->output('error', "Failed to create directory: {$dir}");
                return false;
            }
            $this->output('success', "Created directory: {$dir}");
        }

        // Create translation file if it doesn't exist
        if (!file_exists($filePath)) {
            $direction = in_array($locale, $this->config['rtl_languages']) ? 'rtl' : 'ltr';
            $languageName = $this->config['languages'][$locale] ?? ucfirst($locale);

            $phpContent = "<?php\n\nreturn [\n\n";
            $phpContent .= "    /*\n";
            $phpContent .= "    |--------------------------------------------------------------------------\n";
            $phpContent .= "    | Webkernel Language File - {$languageName}\n";
            $phpContent .= "    |--------------------------------------------------------------------------\n";
            $phpContent .= "    |\n";
            $phpContent .= "    | This file contains translations for the Webkernel ecosystem.\n";
            $phpContent .= "    | Auto-generated translations are marked accordingly.\n";
            $phpContent .= "    |\n";
            $phpContent .= "    */\n\n";
            $phpContent .= "    'direction' => '{$direction}',\n";
            $phpContent .= "    'lang_ref' => [\n";
            $phpContent .= "        // Translation entries will be added here\n";
            $phpContent .= "    ],\n";
            $phpContent .= "];\n";

            if (file_put_contents($filePath, $phpContent) === false) {
                $this->output('error', "Failed to create translation file: {$filePath}");
                return false;
            }
            $this->output('success', "Created translation file: {$filePath}");
        }

        $content = include $filePath;

        // Fix scalar value issue - ensure we have proper array structure
        if (!is_array($content)) {
            $content = [
                'direction' => in_array($locale, $this->config['rtl_languages'] ?? []) ? 'rtl' : 'ltr',
                'lang_ref' => []
            ];
        }

        // Initialize structure if needed
        if (!isset($content['lang_ref'])) {
            $content['lang_ref'] = [];
        }

        $entry = [
            'label' => $translation,
        ];

        // Add context information first
        if (!empty($this->translationContext)) {
            if ($locale === 'en') {
                // For English: only context (original user input)
                $entry['context'] = $this->translationContext;
                // No context_destination for English since it's the same
            } else {
                // For other languages: context (original) + context_destination (translated)
                $entry['context'] = $this->translationContext;

                // Use parsed context_destination if available, otherwise translate separately
                if (!empty($this->lastContextDestination)) {
                    $entry['context_destination'] = $this->lastContextDestination;
                } else {
                    // Translate context separately for clean result
                    $entry['context_destination'] = $this->translateContextSeparately($this->translationContext, $this->config['languages'][$locale]);
                }
            }
        }

        // Add metadata in the requested order
        $entry['engine_used'] = $this->selectedEngine ?? 'bing';
        $entry['auto_generated'] = true;
        $entry['generated_at'] = date('Y-m-d H:i:s');
        $entry['protected'] = false;

        // Check if key already exists before writing
        if (isset($content['lang_ref'][$key])) {
            $this->output('warning', "Key '{$key}' already exists in {$locale} translations. Will be updated.");
        }

        $content['lang_ref'][$key] = $entry;

        // Store file path in content for direction detection
        $content['path'] = $filePath;

        // During bulk mode, write directly without confirmation
        return $this->writeTranslationFileDirect($filePath, $content);
    }

    private function writeTranslationFile($filePath, $content)
    {
        try {
            // Pre-write validation
            if (!$this->validateBeforeWrite($filePath, $content)) {
                return false;
            }

            // Show summary of what will be written BEFORE asking for agreement
            $newKey = null;
            $newEntry = null;

            // Find the new entry being added
            foreach ($content['lang_ref'] as $k => $e) {
                if (!isset($e['existing'])) {
                    $newKey = $k;
                    $newEntry = $e;
                    break;
                }
            }

            $this->showWriteSummary($filePath, $content, $newKey, $newEntry);

            // Ask for confirmation after showing the summary with validation
            $confirm = $this->askWithValidation(
                "Proceed with writing translation file? (y/N)",
                [$this, 'validateYesNo'],
                "Please enter 'y' to proceed, 'n' to cancel, or press Enter for default (no).",
                'n'
            );

            if ($confirm === null || strtolower($confirm) !== 'y' && strtolower($confirm) !== 'yes') {
                $this->output('info', 'Translation file write cancelled by user');
                return false;
            }

            // Ensure directory exists
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    $this->output('error', "Failed to create directory: {$dir}");
                    return false;
                }
                $this->output('success', "Created directory: {$dir}");
            }

            // Check write permissions
            if (!is_writable($dir)) {
                $this->output('error', "Directory not writable: {$dir}");
                return false;
            }

            // Generate PHP content with clean formatting
            $phpContent = $this->generateCleanPhpContent($content);

            // Write file
            if (file_put_contents($filePath, $phpContent) === false) {
                $this->output('error', "Failed to write file: {$filePath}");
                return false;
            }

            // Validate the written file
            if (!$this->validatePhpSyntax($filePath)) {
                $this->output('error', "PHP syntax validation failed for: {$filePath}");
                return false;
            }

            $this->output('success', "Translation file written successfully: {$filePath}");
            return true;

        } catch (Exception $e) {
            $this->handleError("Failed to write translation file", ['file' => $filePath, 'error' => $e->getMessage()]);
            return false;
        }
    }

    private function validateBeforeWrite($filePath, $content): bool
    {
        // Check if content is valid
        if (empty($content) || !is_array($content)) {
            $this->output('error', 'Invalid content provided for translation file');
            return false;
        }

        // Check if file path is valid
        if (empty($filePath) || !is_string($filePath)) {
            $this->output('error', 'Invalid file path provided');
            return false;
        }

        // Check if parent directory can be created
        $dir = dirname($filePath);
        if (!is_dir($dir) && !is_writable(dirname($dir))) {
            $this->output('error', "Cannot create directory (parent not writable): {$dir}");
            return false;
        }

        return true;
    }

    private function showWriteSummary($filePath, $content, $key = null, $entry = null): void
    {
        $locale = basename(dirname($filePath));
        $langRefEntries = $content['lang_ref'] ?? [];
        $direction = $content['direction'] ?? 'ltr';
        $totalEntries = count($langRefEntries);
        $newEntries = $key ? 1 : 0;

        $languageName = strtoupper($this->config['languages'][$locale] ?? $locale);

        $this->output('info', '');
        $this->output('info', '-------------------------------------------------------');
        $this->output('info', "         TRANSLATION FOR {$languageName} - {$locale} ({$direction})");
        $this->output('info', '-------------------------------------------------------');
        $this->output('info', "File: {$filePath}");
        $this->output('info', '-------------------------------------------------------');
        $this->output('info', '');

        if ($key && $entry) {
            $this->output('info', "'lang_ref'             => '{$key}'");
            $this->output('info', "'label'                => '{$entry['label']}'");
            $this->output('info', "'auto_generated'       => '" . ($entry['auto_generated'] ?? 'true') . "'");
            $this->output('info', "'engine_used'          => '" . ($entry['engine_used'] ?? 'bing') . "'");
            $this->output('info', "'protected'            => '" . ($entry['protected'] ?? 'false') . "'");

            if (isset($entry['context'])) {
                $this->output('info', "'context'              => '{$entry['context']}'");
            }

            if (isset($entry['context_destination']) && $locale !== 'en') {
                $this->output('info', "'context_destination'  => '{$entry['context_destination']}'");
            }

            $this->output('info', "'generated_at'         => '" . ($entry['generated_at'] ?? date('Y-m-d H:i:s')) . "'");
        }

        $this->output('info', '');
        $this->output('info', '-------------------------------------------------------');
        $this->output('info', "New entries: {$newEntries}");
        $this->output('info', "Total entries: {$totalEntries}");
        $this->output('info', '-------------------------------------------------------');
        $this->output('info', '');
    }

    private function showFinalTranslationSummary($key, $translationSummary): void
    {
        $this->output('info', '');
        $this->output('info', '═══════════════════════════════════════════════════════════════════════════════');
        $this->output('info', '                      WEBKERNEL TRANSLATION SUMMARY');
        $this->output('info', '═══════════════════════════════════════════════════════════════════════════════');
        $this->output('info', '');

        // Show detailed tickets for priority languages (English, Arabic, French)
        $priorityLanguages = $this->config['priority_ticket_languages'] ?? ['en', 'ar', 'fr'];
        foreach ($priorityLanguages as $locale) {
            if (isset($translationSummary[$locale])) {
                $info = $translationSummary[$locale];
                $this->showDetailedLanguageTicket($locale, $key, $info);
            }
        }

        // Show summary for other languages
        $otherLanguages = array_diff(array_keys($translationSummary), $priorityLanguages);
        if (!empty($otherLanguages)) {
            $this->output('info', '-------------------------------------------------------');
            $this->output('info', '                   OTHER LANGUAGES SUMMARY');
            $this->output('info', '-------------------------------------------------------');
            $this->output('info', '');

            foreach ($otherLanguages as $locale) {
                $info = $translationSummary[$locale];
                $status = $info['success'] ? '✓' : '✗';
                $truncatedText = strlen($info['text']) > 50 ? substr($info['text'], 0, 47) . '...' : $info['text'];
                $this->output('info', "{$status} {$locale} ({$info['language']}): \"{$truncatedText}\"");
            }
            $this->output('info', '');
        }

        $successCount = count(array_filter($translationSummary, function($info) { return $info['success']; }));
        $totalCount = count($translationSummary);

        $this->output('info', '═══════════════════════════════════════════════════════════════════════════════');
        $this->output('info', "RESULT: {$successCount}/{$totalCount} translations completed successfully");
        $this->output('info', '═══════════════════════════════════════════════════════════════════════════════');
        $this->output('info', '');

        // Display comprehensive translation statistics
        $this->displayTranslationStatistics();
    }

    private function showDetailedLanguageTicket($locale, $key, $info): void
    {
        $languageName = strtoupper($this->config['languages'][$locale] ?? $locale);
        $direction = in_array($locale, $this->config['rtl_languages']) ? 'rtl' : 'ltr';
        $status = $info['success'] ? '✓' : '✗';

        $this->output('info', '-------------------------------------------------------');
        $this->output('info', "     {$status} TRANSLATION FOR {$languageName} - {$locale} ({$direction})");
        $this->output('info', '-------------------------------------------------------');
        $this->output('info', '');
        $this->output('info', "'lang_ref'             => '{$key}'");
        $this->output('info', "'label'                => '{$info['text']}'");
        $this->output('info', "'auto_generated'       => 'true'");
        $this->output('info', "'engine_used'          => 'bing'");
        $this->output('info', "'protected'            => 'false'");

        if (!empty($this->translationContext)) {
            $this->output('info', "'context'              => '{$this->translationContext}'");
            if ($locale !== 'en') {
                // Show translated context for non-English
                $translatedContext = $this->translateText($this->translationContext, $this->config['languages'][$locale]);
                $this->output('info', "'context_destination'  => '{$translatedContext}'");
            }
        }

        $this->output('info', "'generated_at'         => '" . date('Y-m-d H:i:s') . "'");
        $this->output('info', '');
        $this->output('info', '-------------------------------------------------------');
        $this->output('info', 'Status: ' . ($info['success'] ? 'SUCCESS' : 'FAILED'));
        $this->output('info', '-------------------------------------------------------');
        $this->output('info', '');
    }

    private function generateCleanPhpContent($content): string
    {
        // ALWAYS extract locale from the file path being written to
        $filePath = $content['path'] ?? '';
        $locale = basename(dirname($filePath));

        // Fallback if path extraction fails
        if (empty($locale) || $locale === '.') {
            $locale = $content['code'] ?? 'en';
        }

        // Use DYNAMIC language names directly from config
        $languageName = $this->config['language_names'][$locale] ?? ucfirst($locale);
        $direction = $content['direction'] ?? (in_array($locale, $this->config['rtl_languages'] ?? []) ? 'rtl' : 'ltr');

        // Get native name from config or fallback to language name
        $nativeName = $this->config['native_names'][$locale] ?? $languageName;

        $php = "<?php\n\nreturn [\n\n";
        $php .= "    /*\n";
        $php .= "    |--------------------------------------------------------------------------\n";
        $php .= "    | Webkernel Language File - {$languageName}\n";
        $php .= "    |--------------------------------------------------------------------------\n";
        $php .= "    |\n";
        $php .= "    | This file contains translations for the Webkernel ecosystem.\n";
        $php .= "    | Auto-generated translations are marked accordingly.\n";
        $php .= "    |\n";
        $php .= "    */\n\n";
        // Ensure we have proper values, especially for English
        $finalLanguageName = !empty($languageName) ? $languageName : ucfirst($locale);
        $finalLocale = !empty($locale) ? $locale : 'en';

        $php .= "    'language' => '{$finalLanguageName}',\n";
        if ($finalLocale === 'en') {
            $php .= "    'language_destination' => 'English',\n";
        } else {
            $finalNativeName = !empty($nativeName) ? $nativeName : $finalLanguageName;
            $php .= "    'language_destination' => '{$finalNativeName}',\n";
        }
        $php .= "    'code' => '{$finalLocale}',\n";
        $php .= "    'direction' => '{$direction}',\n\n";
        $php .= "    'lang_ref' => [\n";

        if (isset($content['lang_ref']) && !empty($content['lang_ref'])) {
            foreach ($content['lang_ref'] as $key => $entry) {
                $php .= "        '{$key}' => [\n";
                $php .= "            'label' => " . $this->safePhpEscape($entry['label']) . ",\n";

                if (isset($entry['auto_generated'])) {
                    $autoGen = $entry['auto_generated'] === true ? 'true' : 'false';
                    $php .= "            'auto_generated' => {$autoGen},\n";
                }

                if (isset($entry['engine_used'])) {
                    $php .= "            'engine_used' => '{$entry['engine_used']}',\n";
                }

                if (isset($entry['context'])) {
                    $php .= "            'context' => '" . addslashes($entry['context']) . "',\n";
                }

                if (isset($entry['context_destination'])) {
                    $php .= "            'context_destination' => '" . addslashes($entry['context_destination']) . "',\n";
                }

                if (isset($entry['generated_at'])) {
                    $php .= "            'generated_at' => '{$entry['generated_at']}',\n";
                }

                if (isset($entry['protected'])) {
                    $protected = $entry['protected'] === true ? 'true' : 'false';
                    $php .= "            'protected' => {$protected},\n";
                }

                $php .= "        ],\n";
            }
        } else {
            $php .= "        // Translation entries will be added here\n";
        }

        $php .= "    ],\n";
        $php .= "];\n";

        return $php;
    }

    /**
     * Simple Laravel directory creation
     */
    private function ensureDirectoryExists($dir)
    {
        if (is_dir($dir)) {
            return true;
        }

        try {
            mkdir($dir, 0755, true);
            $this->output('success', "Created directory: {$dir}");
            return true;
        } catch (Exception $e) {
            $this->output('warning', "Cannot create {$dir}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Suggest permission fixes to the user
     */
    private function suggestPermissionFix($path)
    {
        $this->output('info', "Permission troubleshooting suggestions:");
        $this->output('info', "1. Check directory ownership: ls -la " . dirname($path));
        $this->output('info', "2. Fix permissions: sudo chmod 755 {$path}");
        $this->output('info', "3. Fix ownership: sudo chown -R \$(whoami):www-data {$path}");

        // Get current user info for better diagnostics
        $currentUser = get_current_user();

        $this->output('info', "Current system user: {$currentUser}");

        // Check if running via web server or CLI
        if (php_sapi_name() === 'cli') {
            $this->output('info', "Running via CLI - check file system permissions");
        } else {
            $this->output('info', "Running via web server - check web server user permissions");
        }
    }

    /**
     * Get detailed permission information
     */
    private function getPermissionInfo($path)
    {
        if (!file_exists($path)) {
            return "Path does not exist";
        }

        $perms = fileperms($path);
        $info = [
            'path' => $path,
            'permissions' => substr(sprintf('%o', $perms), -4),
            'owner' => fileowner($path) ?? 'unknown',
            'group' => filegroup($path) ?? 'unknown',
            'is_readable' => is_readable($path),
            'is_writable' => is_writable($path),
            'is_executable' => is_executable($path)
        ];

        return $info;
    }

    /**
     * Get language name dynamically from config or generate it
     */
    private function getLanguageNameFromConfig($locale): string
    {
        // First check if it's in the main config languages
        foreach ($this->config['languages'] as $code => $name) {
            if ($code === $locale) {
                return ucfirst($name);
            }
        }

        // Fallback: convert locale code to readable name
        return $this->localeToLanguageName($locale);
    }

    /**
     * Convert locale code to human readable language name using config
     */
    private function localeToLanguageName($locale): string
    {
        return $this->config['language_names'][$locale] ?? ucfirst($locale);
    }

    /**
     * Get native language name using dynamic config
     */
    private function getNativeLanguageName($locale): string
    {
        return $this->config['native_names'][$locale] ?? $this->config['language_names'][$locale] ?? ucfirst($locale);
    }

    /**
     * Check if base directory is accessible, if not suggest alternatives
     */
    private function ensureBaseDirectoryAccess(): bool
    {
        // If base directory doesn't exist, try to create it
        if (!is_dir($this->baseDir)) {
            $this->output('info', "Base directory doesn't exist: {$this->baseDir}");

            // Try to create base directory
            if (!$this->ensureDirectoryExists($this->baseDir)) {
                $this->output('warning', "Cannot create base directory, trying alternative location");

                // Try alternative writable directory
                $alternatives = [
                    base_path('storage/app/translations'),
                    sys_get_temp_dir() . '/webkernel_translations',
                    getcwd() . '/translations'
                ];

                foreach ($alternatives as $alt) {
                    if ($this->testDirectoryWritability($alt)) {
                        $this->baseDir = $alt;
                        $this->output('success', "Using alternative directory: {$alt}");
                        return true;
                    }
                }

                return false;
            }
        }

        // Check if base directory is writable
        if (!is_writable($this->baseDir)) {
            $this->output('error', "Base directory not writable: {$this->baseDir}");
            $this->suggestPermissionFix($this->baseDir);
            return false;
        }

        return true;
    }

    /**
     * Test if a directory can be created and is writable
     */
    private function testDirectoryWritability($dir): bool
    {
        if (!is_dir($dir)) {
            $oldUmask = umask(0);
            $created = @mkdir($dir, 0755, true);
            umask($oldUmask);

            if (!$created) {
                return false;
            }
        }

        return is_writable($dir);
    }

    /**
     * Create minimal fallback files for essential languages
     */
    private function createFallbackFiles(): bool
    {
        $this->output('info', 'Creating fallback translation files...');

        // Use priority languages from config if we have permission issues
        $priorityLanguages = $this->config['priority_ticket_languages'] ?? ['en', 'ar', 'fr'];

        foreach ($priorityLanguages as $locale) {
            $name = $this->config['language_names'][$locale] ?? ucfirst($locale);
            $filePath = $this->getLanguageFilePath($locale);

            if (!file_exists($filePath)) {
                $this->output('info', "Fallback: Creating minimal file for {$locale}");
                // We'll create these files during the actual translation process
            }
        }

        $this->output('success', 'Fallback approach ready - files will be created as needed');
        return true;
    }

    private function ensureAllTranslationFilesExist(): bool
    {
        $this->output('info', 'Checking translation files...');

        // Just check the 3 essential language files exist
        $files = [
            'en' => $this->baseDir . '/en/translations.php',
            'ar' => $this->baseDir . '/ar/translations.php',
            'fr' => $this->baseDir . '/fr/translations.php'
        ];

        foreach ($files as $locale => $filePath) {
            $dir = dirname($filePath);

            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            if (!file_exists($filePath)) {
                $direction = ($locale === 'ar') ? 'rtl' : 'ltr';
                $content = "<?php\n\nreturn [\n    'direction' => '{$direction}',\n    'lang_ref' => [],\n];\n";
                @file_put_contents($filePath, $content);
            }
        }

        $this->output('info', 'Files ready');
        $this->addVisualSeparator();
        return true;
    }

    private function showAllTranslationTickets($key, $text): void
    {
        $this->output('info', '');
        $this->output('info', '═══════════════════════════════════════════════════════════════════════════════');
        $this->output('info', '                      WEBKERNEL TRANSLATION PREVIEW');
        $this->output('info', '═══════════════════════════════════════════════════════════════════════════════');
        $this->output('info', '');

        // Generate preview translations for priority languages
        $priorityLanguages = ['en', 'ar', 'fr'];

        foreach ($priorityLanguages as $locale) {
            $languageName = strtoupper($this->config['languages'][$locale] ?? $locale);
            $direction = in_array($locale, $this->config['rtl_languages'] ?? []) ? 'rtl' : 'ltr';

            // Get the translation text
            if ($locale === 'en') {
                $translatedText = $text;
            } else {
                try {
                    $translatedText = $this->translateText($text, $this->config['languages'][$locale]);
                } catch (Exception $e) {
                    $translatedText = '[Translation will be generated]';
                }
            }

            $this->output('info', '-------------------------------------------------------');
            $this->output('info', "            TRANSLATION FOR {$languageName} - {$locale} ({$direction})");
            $this->output('info', '-------------------------------------------------------');
            $this->output('info', '');
            $this->output('info', "'lang_ref'             => '{$key}'");
            $this->output('info', "'label'                => '{$translatedText}'");
            $this->output('info', "'auto_generated'       => 'true'");
            $this->output('info', "'engine_used'          => 'bing'");
            $this->output('info', "'protected'            => 'false'");

            if (!empty($this->translationContext)) {
                $this->output('info', "'context'              => '{$this->translationContext}'");
                if ($locale !== 'en') {
                    // Use parsed context_destination if available, otherwise translate separately
                    $contextDestination = $this->lastContextDestination ??
                                        $this->translateContextSeparately($this->translationContext, $this->config['languages'][$locale]);
                    $this->output('info', "'context_destination'  => '{$contextDestination}'");
                }
            }

            $this->output('info', "'generated_at'         => '" . date('Y-m-d H:i:s') . "'");
            $this->output('info', '');
            $this->output('info', '-------------------------------------------------------');
            $this->output('info', '');
        }

        $totalLanguages = count($this->config['languages']);
        $this->output('info', "Total languages to process: {$totalLanguages}");
        $this->output('info', '');
    }

    private function generateTranslationPreviews($key, $text): array
    {
        $previews = [];
        $priorityLanguages = ['en', 'ar', 'fr'];

        foreach ($priorityLanguages as $locale) {
            $this->output('info', "Generating preview for {$locale}...");

            if ($locale === 'en') {
                $previews[$locale] = [
                    'text' => $text,
                    'context' => $this->translationContext,
                    'context_destination' => $this->translationContext
                ];
            } else {
                try {
                    $translatedText = $this->translateText($text, $this->config['languages'][$locale]);
                    $translatedContext = !empty($this->translationContext) ?
                        $this->translateText($this->translationContext, $this->config['languages'][$locale]) : '';

                    $previews[$locale] = [
                        'text' => $translatedText,
                        'context' => $this->translationContext,
                        'context_destination' => $translatedContext
                    ];

                    $this->output('success', "✓ Preview generated for {$locale}");
                } catch (Exception $e) {
                    $previews[$locale] = [
                        'text' => '[Will be generated during bulk process]',
                        'context' => $this->translationContext,
                        'context_destination' => '[Will be generated]'
                    ];
                    $this->output('warning', "Preview for {$locale} will be generated during bulk process");
                }
            }
        }

        return $previews;
    }

    private function displayTranslationTickets($key, $text, $previews): void
    {
        $this->output('info', '');
        $this->output('info', '═══════════════════════════════════════════════════════════════════════════════');
        $this->output('info', '                      WEBKERNEL TRANSLATION TICKETS');
        $this->output('info', '═══════════════════════════════════════════════════════════════════════════════');
        $this->output('info', '');

        foreach ($previews as $locale => $data) {
            $languageName = strtoupper($this->config['languages'][$locale] ?? $locale);
            $direction = in_array($locale, $this->config['rtl_languages'] ?? []) ? 'rtl' : 'ltr';

            $this->output('info', '-------------------------------------------------------');
            $this->output('info', "            TRANSLATION FOR {$languageName} - {$locale} ({$direction})");
            $this->output('info', '-------------------------------------------------------');
            $this->output('info', '');
            $this->output('info', "'lang_ref'             => '{$key}'");
            $this->output('info', "'label'                => '{$data['text']}'");
            $this->output('info', "'auto_generated'       => 'true'");
            $this->output('info', "'engine_used'          => 'bing'");
            $this->output('info', "'protected'            => 'false'");

            if (!empty($data['context'])) {
                $this->output('info', "'context'              => '{$data['context']}'");
                if ($locale !== 'en' && !empty($data['context_destination'])) {
                    $this->output('info', "'context_destination'  => '{$data['context_destination']}'");
                }
            }

            $this->output('info', "'generated_at'         => '" . date('Y-m-d H:i:s') . "'");
            $this->output('info', '');
            $this->output('info', '-------------------------------------------------------');
            $this->output('info', '');
        }

        $totalLanguages = count($this->config['languages']);
        $this->output('info', "Total languages to process: {$totalLanguages}");
        $this->output('info', '');
    }

    private function writeTranslationFileDirect($filePath, $content)
    {
        try {
            // Ensure directory exists
            $dir = dirname($filePath);
            if (!$this->ensureDirectoryExists($dir)) {
                return false;
            }

            // Generate PHP content with clean formatting
            $phpContent = $this->generateCleanPhpContent($content);

            // Write file directly without confirmation
            $bytesWritten = file_put_contents($filePath, $phpContent);
            if ($bytesWritten === false) {
                return false;
            }

            // Validate the written file
            if (!$this->validatePhpSyntax($filePath)) {
                $this->output('error', "PHP syntax validation failed for: {$filePath}");
                return false;
            }

            // Success - no verbose message during bulk mode
            return true;

        } catch (Exception $e) {
            $this->output('error', "Error writing file {$filePath}: {$e->getMessage()}");
            return false;
        }
    }





    private function validateTranslationFile($filePath)
    {
        try {
            $content = include $filePath;
            return is_array($content);
        } catch (Exception $e) {
            return false;
        }
    }

    private function repairTranslationFile($filePath, $locale)
    {
        try {
            $backupPath = $filePath . '.backup.' . time();
            copy($filePath, $backupPath);

            // Try to load and re-save with proper formatting
            $content = include $filePath;
            if (is_array($content)) {
                return $this->writeTranslationFile($filePath, $content);
            }

            // If still invalid, create minimal valid structure
            $this->createMinimalValidFile($filePath, in_array($locale, $this->config['rtl_languages']) ? 'rtl' : 'ltr');
            return true;

        } catch (Exception $e) {
            $this->handleError("Repair failed for {$locale}", ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function createMinimalValidFile($filePath, $direction = 'ltr')
    {
        $content = ['direction' => $direction];
        return $this->writeTranslationFile($filePath, $content);
    }

    private function changeKeyInFile($filePath, $oldKey, $newKey, $locale)
    {
        try {
            $content = include $filePath;
            if (!is_array($content) || !isset($content[$oldKey])) {
                return true; // Key doesn't exist, consider it success
            }

            $content[$newKey] = $content[$oldKey];
            unset($content[$oldKey]);

            if ($this->writeTranslationFile($filePath, $content)) {
                $this->output('success', "→ {$locale}: Key changed");
                return true;
            }

            return false;

        } catch (Exception $e) {
            $this->handleError("Key change failed for {$locale}", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * User interaction and utility methods
     */

    private function gatherContextFromUser()
    {
        return $this->analyzeAndOptimizeText();
    }

    private function analyzeAndOptimizeText()
    {
        $this->output('info', 'Context specification for better translations:');

        // Dynamic context gathering
        $context = $this->askWithReadline("Specify the context domain (e.g., 'software' ...) or press Enter to skip", '');

        $this->translationContext = $context;

        if (!empty($context)) {
            $this->output('info', "Context set: {$context}");
        }

        // Word substitutions for better translation accuracy
        $this->output('info', 'Word substitutions for better translation understanding:');
        $this->output('info', 'Some technical words may be misunderstood. You can suggest replacements that preserve meaning.');
        $this->output('info', "Example: 'key' -> 'configuration-key' or 'parameter'");

        $substitutions = [];

        // Only ask for word replacements if enabled in config
        if ($this->config['word_replacement_enabled'] ?? false) {
            while (true) {
                $word = $this->askWithReadline("Enter a word to replace (or press Enter to skip)");
                if (empty($word)) break;

            // Validate replacement word
            $replacement = $this->askWithValidation(
                "Replace '{$word}' with",
                [$this, 'validateNonEmpty'],
                "Replacement cannot be empty. Please enter a replacement word or phrase.",
                null
            );

            if ($replacement !== null && !empty($replacement)) {
                $substitutions[$word] = $replacement;
                $this->output('success', "Will replace '{$word}' with '{$replacement}' for translation");
            } else {
                $this->output('info', "Skipping replacement for '{$word}' - no valid replacement provided");
            }
        }
        } else {
            $this->output('info', 'Word replacement is disabled in config. Skipping substitution prompts.');
        }

        $this->wordSubstitutions = $substitutions;

        return $context;
    }

    private function askWithReadline($question, $default = '')
    {
        if (function_exists('readline')) {
            // Enable readline editing features for left-right navigation
            readline_completion_function(function() { return []; });

            $prompt = $default ? "{$question} [{$default}]: " : "{$question}: ";
            $answer = readline($prompt);

            // Add to history for up-down navigation
            if ($answer !== false && !empty(trim($answer))) {
                readline_add_history($answer);
            }

            return $answer !== false ? ($answer ?: $default) : $default;
        }

        return $this->ask($question, $default);
    }

    private function askWithValidation($question, $validator, $errorMessage, $default = null, $maxAttempts = 5)
    {
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            $input = $this->askWithReadline($question, $default);

            if ($validator($input)) {
                return $input;
            }

            $attempts++;
            $this->output('error', $errorMessage);
            $this->output('info', "Please correct your input. Attempt {$attempts}/{$maxAttempts}");

            if ($attempts >= $maxAttempts) {
                $this->output('error', "Maximum attempts reached. Operation cancelled.");
                return null;
            }
        }

        return null;
    }

    private function validateTranslationKey($key): bool
    {
        return !empty($key) && preg_match('/^[a-zA-Z0-9_\-\.]+$/', $key);
    }

    private function validateYesNo($input): bool
    {
        $input = strtolower(trim($input));
        return in_array($input, ['y', 'yes', 'n', 'no', '']);
    }

    private function validateNonEmpty($input): bool
    {
        return !empty(trim($input));
    }



    private function createBackupIfNeeded()
    {
        if (!$this->config['protection']['auto_backup'] || empty($this->backupDir)) {
            return;
        }

        foreach ($this->config['languages'] as $locale => $code) {
            $sourceFile = $this->getLanguageFilePath($locale);
            if (file_exists($sourceFile)) {
                $backupFile = $this->backupDir . "/{$locale}.php";
                copy($sourceFile, $backupFile);
            }
        }

        $this->output('info', 'Backup created successfully');
    }

    private function executeRetranslation($languagesToProcess)
    {
        $englishFile = $this->getLanguageFilePath('en');
        $englishContent = include $englishFile;

        if (!isset($englishContent['lang_ref'])) {
            $this->output('error', 'Invalid English file format - no lang_ref section found!');
            return 1;
        }

        $englishEntries = $englishContent['lang_ref'];
        $retranslated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($languagesToProcess as $locale => $code) {
            if ($locale === 'en') continue;

            $this->output('progress', "Processing {$locale}...");

            foreach ($englishEntries as $key => $englishEntry) {
                if ($this->isTranslationProtected($locale, $key)) {
                    $skipped++;
                    continue;
                }

                try {
                    $englishText = $englishEntry['label'] ?? $englishEntry;
                    if (is_array($englishText)) {
                        $englishText = $englishText['label'] ?? '';
                    }

                    // Get original context for consistent retranslation
                    $originalContext = '';
                    if (is_array($englishEntry) && isset($englishEntry['context'])) {
                        $originalContext = $englishEntry['context'];
                        $this->translationContext = $originalContext;
                    }

                    $translation = $this->translateText($englishText, $code);

                    if ($this->saveTranslation($locale, $key, $translation)) {
                        $retranslated++;
                    } else {
                        $errors++;
                    }

                    usleep(300000); // Rate limiting

                } catch (Exception $e) {
                    $this->handleError("Translation failed for {$locale}:{$key}", ['error' => $e->getMessage()]);
                    $errors++;
                }
            }
        }

        $this->output('info', 'Retranslation completed!');
        $this->output('info', "Retranslated: {$retranslated}");
        $this->output('info', "Skipped (protected): {$skipped}");

        if ($errors > 0) {
            $this->output('warning', "Errors: {$errors}");
        }

        return $errors > 0 ? 1 : 0;
    }

    private function handleProtectionOperation($protect)
    {
        $keys = $this->option('keys');
        $allLangs = $this->option('all-langs');

        if ($keys) {
            $keyList = explode(',', $keys);
            return $protect ?
                $this->applyProtection($keyList, $allLangs) :
                $this->applyUnprotection($keyList, $this->option('before'), $this->option('after'));
        }

        // Interactive mode
        while (true) {
            $key = $this->askWithReadline('Enter translation key to ' . ($protect ? 'protect' : 'unprotect') . ' (or press Enter to finish)');
            if (empty($key)) break;

            $keyList = [$key];
            if ($protect) {
                $this->applyProtection($keyList, $allLangs);
            } else {
                $this->applyUnprotection($keyList, null, null);
            }
        }

        return 0;
    }

    private function applyProtection($keys, $allLangs = false)
    {
        $protected = 0;
        $errors = 0;

        $languages = $allLangs ? $this->config['languages'] : ['en' => 'en'];

        foreach ($languages as $locale => $code) {
            $filePath = $this->getLanguageFilePath($locale);
            if (!file_exists($filePath)) continue;

            try {
                $content = include $filePath;
                $updated = false;

                foreach ($keys as $key) {
                    if (isset($content[$key])) {
                        if (!is_array($content[$key])) {
                            $content[$key] = ['label' => $content[$key]];
                        }
                        $content[$key]['protected_at'] = time();
                        $updated = true;
                        $protected++;
                    }
                }

                if ($updated && $this->writeTranslationFile($filePath, $content)) {
                    $this->output('success', "→ {$locale}: Protection applied");
                }

            } catch (Exception $e) {
                $this->handleError("Protection failed for {$locale}", ['error' => $e->getMessage()]);
                $errors++;
            }
        }

        $this->output('info', "Protection applied! Protected: {$protected}, Errors: {$errors}");
        return $errors > 0 ? 1 : 0;
    }

    private function applyUnprotection($keys, $beforeTime, $afterTime)
    {
        $unprotected = 0;
        $errors = 0;

        $beforeTimestamp = $beforeTime ? $this->parseTimeOption($beforeTime) : null;
        $afterTimestamp = $afterTime ? $this->parseTimeOption($afterTime) : null;

        foreach ($this->config['languages'] as $locale => $code) {
            $filePath = $this->getLanguageFilePath($locale);
            if (!file_exists($filePath)) continue;

            try {
                $content = include $filePath;
                $updated = false;

                foreach ($keys as $key) {
                    if (isset($content[$key]) && is_array($content[$key]) && isset($content[$key]['protected_at'])) {
                        $protectedAt = $content[$key]['protected_at'];

                        $shouldUnprotect = true;
                        if ($beforeTimestamp && $protectedAt >= $beforeTimestamp) $shouldUnprotect = false;
                        if ($afterTimestamp && $protectedAt <= $afterTimestamp) $shouldUnprotect = false;

                        if ($shouldUnprotect) {
                            unset($content[$key]['protected_at']);
                            $updated = true;
                            $unprotected++;
                        }
                    }
                }

                if ($updated && $this->writeTranslationFile($filePath, $content)) {
                    $this->output('success', "→ {$locale}: Unprotection applied");
                }

            } catch (Exception $e) {
                $this->handleError("Unprotection failed for {$locale}", ['error' => $e->getMessage()]);
                $errors++;
            }
        }

        $this->output('info', "Unprotection applied! Unprotected: {$unprotected}, Errors: {$errors}");
        return $errors > 0 ? 1 : 0;
    }

    private function migrateProtectionTimestamps()
    {
        $migrated = 0;
        $errors = 0;

        foreach ($this->config['languages'] as $locale => $code) {
            if ($locale === 'en' && $this->config['protection']['protected_source']) {
                $this->output('warning', "→ Skipping English source file (protected)");
                continue;
            }

            $filePath = $this->getLanguageFilePath($locale);
            if (!file_exists($filePath)) continue;

            try {
                $content = include $filePath;
                $updated = false;

                foreach ($content as $key => &$entry) {
                    if (is_array($entry) && !isset($entry['protected_at']) &&
                        (isset($entry['protected']) || isset($entry['manual']))) {
                        $entry['protected_at'] = time();
                        $updated = true;
                        $migrated++;
                    }
                }

                if ($updated && $this->writeTranslationFile($filePath, $content)) {
                    $this->output('success', "→ {$locale}: Timestamps migrated");
                }

            } catch (Exception $e) {
                $this->handleError("Migration failed for {$locale}", ['error' => $e->getMessage()]);
                $errors++;
            }
        }

        $this->output('info', "Migration completed! Migrated: {$migrated}, Errors: {$errors}");
        return $errors > 0 ? 1 : 0;
    }

    private function restoreFromBackup()
    {
        $backupBaseDir = storage_path('translation_backups');
        if (!is_dir($backupBaseDir)) {
            $this->output('error', 'No backups found!');
            return 1;
        }

        $backups = glob($backupBaseDir . '/*/*');
        if (empty($backups)) {
            $this->output('error', 'No backup directories found!');
            return 1;
        }

        $backups = array_reverse($backups);
        $this->output('info', 'Available backups:');
        foreach ($backups as $index => $backup) {
            $this->output('info', "  [" . ($index + 1) . "] " . basename(dirname($backup)) . '/' . basename($backup));
        }

        $choice = $this->askWithReadline('Enter backup number to restore');
        $backupIndex = intval($choice) - 1;

        if (!isset($backups[$backupIndex])) {
            $this->output('error', 'Invalid backup selection!');
            return 1;
        }

        $selectedBackup = $backups[$backupIndex];
        if (!$this->confirm('This will overwrite current translations. Continue?')) {
            $this->output('info', 'Restore cancelled.');
            return 0;
        }

        $restored = 0;
        $errors = 0;

        $backupFiles = glob($selectedBackup . '/*.php');
        foreach ($backupFiles as $backupFile) {
            $locale = basename($backupFile, '.php');
            $targetFile = $this->getLanguageFilePath($locale);

            try {
                if (copy($backupFile, $targetFile)) {
                    $this->output('success', "→ Restored {$locale}");
                    $restored++;
                } else {
                    $this->output('error', "→ Failed to restore {$locale}");
                    $errors++;
                }
            } catch (Exception $e) {
                $this->handleError("Restore failed for {$locale}", ['error' => $e->getMessage()]);
                $errors++;
            }
        }

        $this->output('info', "Restore completed! Restored: {$restored}, Errors: {$errors}");
        return $errors > 0 ? 1 : 0;
    }

    private function isTranslationProtected($locale, $key)
    {
        $filePath = $this->getLanguageFilePath($locale);
        if (!file_exists($filePath)) return false;

        try {
            $content = include $filePath;
            return isset($content[$key]) &&
                   is_array($content[$key]) &&
                   isset($content[$key]['protected_at']);
        } catch (Exception $e) {
            return false;
        }
    }

    private function parseTimeOption($timeStr)
    {
        if (is_numeric($timeStr)) {
            return intval($timeStr);
        }

        $timeStr = strtolower($timeStr);
        $now = time();

        if (preg_match('/^(\d+)([dhw])$/', $timeStr, $matches)) {
            $amount = intval($matches[1]);
            $unit = $matches[2];

            switch ($unit) {
                case 'd': return $now - ($amount * 24 * 60 * 60);
                case 'h': return $now - ($amount * 60 * 60);
                case 'w': return $now - ($amount * 7 * 24 * 60 * 60);
            }
        }

        return null;
    }

    // Helper methods for interactive mode
    private function showHelp(): void
    {
        $this->output('info', '=== WEBKERNEL DEVELOPMENT TOOLS ===');
        $this->output('info', 'TranslationHub - Advanced Multilingual Translation Management');
        $this->output('info', 'Part of Webkernel Dev-Tools Suite for Laravel Development');
        $this->output('info', '');
        $this->output('info', 'Author: El Moumen Yassine (yassine@numerimondes.com)');
        $this->output('info', 'License: Mozilla Public License (MPL)');
        $this->output('info', 'Website: https://www.numerimondes.com');
        $this->output('info', '');
        $this->output('info', 'Usage Examples:');
        $this->output('info', '  webkernel:lang "Hello world" welcome_message');
        $this->output('info', '  webkernel:lang --change-key --old-key=old_name --new-key=new_name');
        $this->output('info', '  webkernel:lang --restore');
        $this->output('info', '  webkernel:lang --validate-only');
        $this->output('info', '  webkernel:lang --repair');
        $this->output('info', '');
        $this->output('info', 'Available Development Options:');
        $this->output('info', '  --change-key         Change existing translation key');
        $this->output('info', '  --restore            Restore from backup');
        $this->output('info', '  --validate-only      Validate files without changes');
        $this->output('info', '  --repair             Repair syntax errors');
        $this->output('info', '  --retranslate        Retranslate existing entries');
        $this->output('info', '  --protect            Protect translations from modification');
        $this->output('info', '  --unprotect          Remove protection from translations');
        $this->output('info', '');
        $this->output('info', 'Webkernel Dev-Tools: Accelerating Laravel Development');
        $this->output('info', '====================================');
    }

    private function enterInteractiveMode(): int
    {
        $this->output('info', 'Webkernel Dev-Tools: Entering interactive translation mode...');

        // Validate English text input
        $text = $this->askWithValidation(
            "Enter English text to translate",
            [$this, 'validateNonEmpty'],
            "Text cannot be empty. Please enter the English text you want to translate.",
            null
        );

        if ($text === null) {
            $this->output('error', 'No valid text provided. Exiting interactive mode.');
            return 1;
        }

        // Auto-generate translation key from text
        $autoKey = $this->generateKeyFromText($text);
        $this->output('info', "Auto-generated key: {$autoKey}");

        // Ask for translation key with auto-generated as default
        $key = $this->askWithValidation(
            "Enter translation key",
            [$this, 'validateTranslationKey'],
            "Invalid key format. Use only letters, numbers, hyphens, underscores, and dots (a-z, A-Z, 0-9, -, _, .).",
            $autoKey
        );

        if ($key === null) {
            $this->output('info', 'No valid key provided, using auto-generated key');
            $key = $autoKey;
        }

        $this->output('success', "Using translation key: {$key}");
        $this->output('info', '');

        // Set the arguments for processing
        $this->input->setArgument('text', $text);
        $this->input->setArgument('key', $key);

        // Ensure all translation files exist before starting
        $this->ensureAllTranslationFilesExist();

        // Continue with normal translation process
        return $this->handle();
    }

    private function generateKeyFromText(string $text): string
    {
        $key = strtolower($text);
        $key = preg_replace('/[^a-z0-9\s]/', '', $key);
        $key = preg_replace('/\s+/', '_', trim($key));
        $key = substr($key, 0, 50); // Limit length

        return $key ?: 'auto_generated_' . time();
    }

    // Utility methods
    private function getLanguageFilePath($locale)
    {
        return $this->baseDir . '/' . $locale . '/translations.php';
    }

    /**
     * Advanced PHP content escaping with specialized handling for Semitic languages
     * Supports Arabic, Hebrew, and other complex character sets with Unicode normalization
     */
    private function safePhpEscape($content): string
    {
        if (empty($content)) {
            return "''";
        }

        // Normalize Unicode characters to NFC (Normalization Form Canonical Composition)
        if (function_exists('normalizer_normalize')) {
            $content = normalizer_normalize($content, \Normalizer::FORM_C);
        }

        // Check if the content is Semitic (Arabic, Hebrew, etc.)
        $isSemitic = $this->isSemiticText($content);

        // Strategy 1: Try single quotes first (cleanest for Arabic)
        $escaped = str_replace(['\\', "'"], ['\\\\', "\\'"], $content);
        $test1 = "'" . $escaped . "'";

        if ($this->validatePhpSyntax("<?php return $test1;")) {
            return $test1;
        }

        // Strategy 2: Try double quotes with proper escaping (more reliable for Unicode)
        $escaped = addslashes($content);
        $test2 = '"' . $escaped . '"';

        if ($this->validatePhpSyntax("<?php return $test2;")) {
            return $test2;
        }

        // Strategy 3: Use heredoc only for really complex content
        if ($isSemitic || $this->hasProblematicChars($content)) {
            $marker = 'EOT_' . uniqid();
            $test3 = "<<<{$marker}\n{$content}\n{$marker}";

            if ($this->validatePhpSyntax("<?php return $test3;")) {
                return $test3;
            }
        }

        // Strategy 3: Fallback to base64 encoding for problematic content
        $encoded = base64_encode($content);
        $test = "base64_decode('{$encoded}')";

        if ($this->validatePhpSyntax("<?php return $test;")) {
            return $test;
        }

        // Final fallback: Hex encoding
        $encoded = bin2hex($content);
        return "hex2bin('{$encoded}')";
    }

    /**
     * Detect if text contains Semitic language characters
     */
    private function isSemiticText($text): bool
    {
        // Arabic Unicode ranges
        if (preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $text)) {
            return true;
        }

        // Hebrew Unicode ranges
        if (preg_match('/[\x{0590}-\x{05FF}\x{FB1D}-\x{FB4F}]/u', $text)) {
            return true;
        }

        // Aramaic, Syriac, and other Semitic scripts
        if (preg_match('/[\x{0700}-\x{074F}\x{0860}-\x{086F}]/u', $text)) {
            return true;
        }

        return false;
    }

    /**
     * Check for characters that commonly cause PHP syntax issues
     */
    private function hasProblematicChars($text): bool
    {
        // Check for characters that often cause escaping problems
        $problematicChars = [
            "\x00", "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07",
            "\x08", "\x0B", "\x0C", "\x0E", "\x0F", "\x10", "\x11", "\x12",
            "\x13", "\x14", "\x15", "\x16", "\x17", "\x18", "\x19", "\x1A",
            "\x1B", "\x1C", "\x1D", "\x1E", "\x1F", "\x7F"
        ];

        foreach ($problematicChars as $char) {
            if (strpos($text, $char) !== false) {
                return true;
            }
        }

        // Check for mixed RTL/LTR content that might cause issues
        $hasRTL = preg_match('/[\x{0590}-\x{08FF}\x{FB1D}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $text);
        $hasLTR = preg_match('/[a-zA-Z]/', $text);

        return $hasRTL && $hasLTR;
    }

    /**
     * Comprehensive PHP syntax validation with Semitic language support
     */
    private function validatePhpSyntax($phpCode): bool
    {
        // Use token_get_all for syntax validation
        $tokens = @token_get_all($phpCode);

        if ($tokens === false) {
            return false;
        }

        // Check for parse errors in tokens
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] === T_BAD_CHARACTER) {
                return false;
            }
        }

        // Additional validation with eval in sandbox
        $tempFile = tempnam(sys_get_temp_dir(), 'php_syntax_check_');
        file_put_contents($tempFile, $phpCode);

        $output = [];
        $returnCode = 0;
        exec("php -l " . escapeshellarg($tempFile) . " 2>&1", $output, $returnCode);

        unlink($tempFile);

        return $returnCode === 0;
    }

    /**
     * Enhanced translation result cleaning with context parsing and Arabic/RTL Unicode normalization
     */
    private function cleanTranslationResult($translation, $originalText)
    {
        if (empty($translation)) {
            return $originalText;
        }

        // If no context was used, apply basic cleaning
        if (empty($this->translationContext)) {
            $cleaned = trim($translation, '"\'');
            $cleaned = preg_replace('/\s+/', ' ', $cleaned);

            // CRITICAL: Restore protected placeholders EVEN without context
            $cleaned = $this->restoreProtectedPlaceholders($cleaned);

            // Normalize Unicode for Arabic and other Semitic languages
            if (function_exists('normalizer_normalize')) {
                $cleaned = normalizer_normalize($cleaned, \Normalizer::FORM_C);
            }

            return trim($cleaned) ?: $originalText;
        }

        // Parse the translated result to extract clean label and context_destination
        $parsed = $this->parseTranslatedResult($translation);

        // Store the context_destination for later use
        if (!empty($parsed['context_destination'])) {
            $this->lastContextDestination = $this->restoreProtectedPlaceholders($parsed['context_destination']);
        }

        $cleaned = $parsed['label'];

        // CRITICAL: Restore protected placeholders FIRST before any other processing
        $cleaned = $this->restoreProtectedPlaceholders($cleaned);

        // Apply standard cleaning and normalization
        $cleaned = trim($cleaned, '"\'');
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);



        // Normalize Unicode for Arabic and other Semitic languages
        if (function_exists('normalizer_normalize')) {
            $cleaned = normalizer_normalize($cleaned, \Normalizer::FORM_C);
        }

        // CRITICAL: Detect if translation actually failed (text unchanged or too similar)
        $similarity = similar_text(strtolower($cleaned), strtolower($originalText), $percent);

        if (strlen($cleaned) < 3 ||
            preg_match('/^[\x{2000}-\x{206F}\x{00}-\x{1F}\.]+$/u', $cleaned) ||
            $percent > 85 || // Text is too similar to original (likely unchanged)
            $cleaned === $originalText) {

            $this->line("<fg=red>⚠ Translation failed or unchanged (similarity: {$percent}%), attempting fallback...</>");

            // Track incident with engine details
            $this->translationStats['incidents'][] = [
                'engine' => $this->selectedEngine,
                'similarity' => $percent,
                'language' => $this->currentTargetLanguage ?? 'unknown'
            ];
            $this->translationStats['total_failures']++;

            // Store current target language for fallback
            $targetLang = $this->currentTargetLanguage ?? 'ar';

            // Try Google as fallback
            $cmd = "trans -e google -brief en:{$targetLang} " . escapeshellarg($originalText) . " 2>/dev/null";
            $fallbackResult = trim(shell_exec($cmd));

            if (!empty($fallbackResult) && $fallbackResult !== $originalText) {
                $similarity2 = similar_text(strtolower($fallbackResult), strtolower($originalText), $percent2);
                if ($percent2 < 85) {
                    $cleaned = trim($fallbackResult, '"\'');
                    $cleaned = $this->restoreProtectedPlaceholders($cleaned);
                    if (function_exists('normalizer_normalize')) {
                        $cleaned = normalizer_normalize($cleaned, \Normalizer::FORM_C);
                    }
                    $this->line("<fg=yellow>→ Fallback translation successful</>");

                    // Track successful fallback
                    $this->translationStats['fallbacks'][] = $targetLang;
                    $this->translationStats['engines_used']['google'] =
                        ($this->translationStats['engines_used']['google'] ?? 0) + 1;
                } else {
                    // Track complete failure
                    $this->translationStats['complete_failures'][] = $targetLang;
                    $this->translationStats['incidents'][] = [
                        'engine' => 'google',
                        'similarity' => $percent2,
                        'language' => $targetLang
                    ];
                    throw new Exception("Translation failed: both Bing and Google returned unchanged text (similarity > 85%)");
                }
            } else {
                // Track complete failure
                $this->translationStats['complete_failures'][] = $targetLang;
                throw new Exception("Translation failed: both engines returned unchanged or empty text");
            }
        }

        return trim($cleaned) ?: $originalText;
    }

    /**
     * Execute command with progress feedback for long operations
     */
    private function executeWithProgressFeedback($command, $operationDescription = "Processing")
    {
        $startTime = microtime(true);
        $feedbackGiven = false;

        // Start the command in background and monitor
        $descriptorSpec = [
            0 => ["pipe", "r"],  // stdin
            1 => ["pipe", "w"],  // stdout
            2 => ["pipe", "w"]   // stderr
        ];

        $process = proc_open($command, $descriptorSpec, $pipes);

        if (!is_resource($process)) {
            return trim(shell_exec($command)); // Fallback to normal execution
        }

        // Close stdin as we don't need it
        fclose($pipes[0]);

        // Make stdout and stderr non-blocking
        stream_set_blocking($pipes[1], 0);
        stream_set_blocking($pipes[2], 0);

        $output = '';
        $startTime = microtime(true);

        while (true) {
            $status = proc_get_status($process);
            if (!$status['running']) {
                // Process finished, collect remaining output
                $output .= stream_get_contents($pipes[1]);
                break;
            }

            $currentTime = microtime(true);
            $elapsed = ($currentTime - $startTime) * 1000; // Convert to milliseconds

            // Check if we should reassure the user
            if ($elapsed > self::TTL_BEFORE_REASSURING_USER && !$feedbackGiven) {
                $this->line('');
                $this->line("    <fg=cyan>• Please wait, operation still in progress ({$operationDescription})...</>");
                $this->line('');
                $feedbackGiven = true;
            }

            // Read any available output
            $output .= stream_get_contents($pipes[1]);

            // Small delay to prevent high CPU usage
            usleep(100000); // 100ms
        }

        // Close pipes and process
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        return trim($output);
    }

    /**
     * Display comprehensive translation statistics
     */
    private function displayTranslationStatistics()
    {
        $this->newLine();
        $this->drawSeparator("", "cyan");

        // Calculate overall statistics
        $totalTranslations = count($this->translationStats['times']);
        $totalFailures = $this->translationStats['total_failures'];
        $successfulTranslations = $totalTranslations - count($this->translationStats['complete_failures']);
        $fallbackSuccesses = count($this->translationStats['fallbacks']);
        $completeFails = count($this->translationStats['complete_failures']);
        $incidents = count($this->translationStats['incidents']);

        // Calculate rates
        $failureRate = $totalTranslations > 0 ? round(($totalFailures / $totalTranslations) * 100, 1) : 0;
        $recoveryRate = $totalFailures > 0 ? round(($fallbackSuccesses / $totalFailures) * 100, 1) : 0;
        $completeFailureRate = $totalTranslations > 0 ? round(($completeFails / $totalTranslations) * 100, 1) : 0;

        // Calculate engine percentages
        $engineStats = [];
        $totalEngineUsage = array_sum($this->translationStats['engines_used']);
        foreach ($this->translationStats['engines_used'] as $engine => $count) {
            $percentage = $totalEngineUsage > 0 ? round(($count / $totalEngineUsage) * 100, 1) : 0;
            $engineStats[$engine] = ['count' => $count, 'percentage' => $percentage];
        }

        // Performance calculations
        $avgDuration = count($this->translationStats['times']) > 0 ?
            round(array_sum($this->translationStats['times']) / count($this->translationStats['times']), 2) : 0;

        // Find slowest and fastest with language info
        $slowestTime = 0;
        $fastestTime = PHP_FLOAT_MAX;
        $slowestLang = '';
        $fastestLang = '';

        foreach ($this->translationStats['language_times'] as $lang => $time) {
            if ($lang !== 'en') { // Exclude English
                if ($time > $slowestTime) {
                    $slowestTime = $time;
                    $slowestLang = $lang;
                }
                if ($time < $fastestTime) {
                    $fastestTime = $time;
                    $fastestLang = $lang;
                }
            }
        }

        // Display main results
        $this->line("<fg=green;options=bold>Translation completed!</>");
        $this->newLine();

        if ($successfulTranslations > 0) {
            $engineInfo = '';
            foreach ($engineStats as $engine => $stats) {
                $engineInfo .= "{$engine}: {$stats['percentage']}%, ";
            }
            $engineInfo = rtrim($engineInfo, ', ');
            $this->line("<fg=green>Successful: {$successfulTranslations}</> ({$engineInfo})");
        }

        if ($totalFailures > 0) {
            $engineInfo = '';
            foreach ($engineStats as $engine => $stats) {
                $engineInfo .= "{$engine}: {$stats['percentage']}%, ";
            }
            $engineInfo = rtrim($engineInfo, ', ');
            $this->line("<fg=red>Failed: " . $totalFailures . "</> (failure rate: {$failureRate}%) ({$engineInfo})");
        }

        if ($fallbackSuccesses > 0) {
            $this->line("<fg=yellow>Recovered after failure: {$fallbackSuccesses}</> (recovery rate: {$recoveryRate}%)");
        }

        if ($completeFails > 0) {
            $this->line("<fg=red>Complete failures: {$completeFails}</> (complete failure rate: {$completeFailureRate}%)");
            $this->line("<fg=red>Failed languages:</> " . implode(', ', $this->translationStats['complete_failures']));
        }

        // Performance metrics
        $this->newLine();
        $this->line("<fg=cyan;options=bold>Performance Metrics:</>");
        $this->line("Average duration: <fg=yellow>{$avgDuration}ms</>");
        if ($slowestTime > 0) {
            $this->line("Slowest translation: <fg=red>{$slowestTime}ms</> (language: {$slowestLang})");
        }
        if ($fastestTime < PHP_FLOAT_MAX) {
            $this->line("Fastest translation: <fg=green>{$fastestTime}ms</> (language: {$fastestLang})");
        }

        // Incidents
        if ($incidents > 0) {
            $this->newLine();
            $this->line("<fg=red;options=bold>Incident count: {$incidents}</>");
            $this->line("<fg=cyan>Incident types:</>");
            foreach ($this->translationStats['incidents'] as $incident) {
                $this->line("- Engine: <fg=yellow>{$incident['engine']}</> | Similarity: <fg=red>{$incident['similarity']}%</>");
            }
        }

        // Additional metrics suggestions
        if ($totalTranslations > 0) {
            $this->newLine();
            $this->line("<fg=cyan;options=bold>Additional Insights:</>");

            // Quality assessment
            $qualityScore = 100 - $failureRate - ($completeFailureRate * 2);
            $qualityLevel = $qualityScore >= 90 ? 'Excellent' : ($qualityScore >= 75 ? 'Good' : ($qualityScore >= 60 ? 'Acceptable' : 'Needs Improvement'));
            $this->line("Quality Score: <fg=green>{$qualityScore}%</> ({$qualityLevel})");

            // Engine reliability
            if (isset($engineStats['bing']) && isset($engineStats['google'])) {
                $primaryEngine = $engineStats['bing']['percentage'] > $engineStats['google']['percentage'] ? 'bing' : 'google';
                $this->line("Primary Engine: <fg=cyan>{$primaryEngine}</> ({$engineStats[$primaryEngine]['percentage']}% usage)");
            }

            // Language complexity insights
            if ($slowestLang && $fastestLang) {
                $complexityRatio = round($slowestTime / $fastestTime, 2);
                $this->line("Complexity Ratio: <fg=yellow>{$complexityRatio}x</> (most/least complex languages)");
            }
        }

        $this->drawSeparator("", "cyan");
    }


    /**
     * Draw a colored separator line in the console
     *
     * @param string $text The text to display in the separator (optional)
     * @param string $color The color for the separator (e.g., 'cyan')
     */
    private function drawSeparator(string $text = "", string $color = "white")
    {
        $separator = str_repeat("═", 80);
        if ($text) {
            $this->line("<fg={$color}>{$text}</>");
        }
        $this->line("<fg={$color}>{$separator}</>");
    }

    /**
     * Parse translated result using the >)>>>> (...) <<<<(< universal marker
     */
    private function parseTranslatedResult($translatedText)
    {
        // Look for the pattern >)>>>> (...) <<<<(< or its broken variations
        // Pattern 1: Complete marker
        if (preg_match('/^(.+?)\s*>\)>>>>\s*\([^)]*\)\s*<<<<\(<\s*(.+)$/u', $translatedText, $matches)) {
            return [
                'label' => trim($matches[1]),
                'context_destination' => trim($matches[2])
            ];
        }

        // Pattern 2: Broken marker like ">)>>>> (< something"
        if (preg_match('/^(.+?)\s*>\)>>>>\s*\(<\s*(.+)$/u', $translatedText, $matches)) {
            return [
                'label' => trim($matches[1]),
                'context_destination' => trim($matches[2])
            ];
        }

        // Pattern 3: Any ">)>" pattern
        if (preg_match('/^(.+?)\s*>\).*?<\s*(.+)$/u', $translatedText, $matches)) {
            return [
                'label' => trim($matches[1]),
                'context_destination' => trim($matches[2])
            ];
        }

        // Fallback: return original text as label, context will be translated separately
        return [
            'label' => $translatedText,
            'context_destination' => null
        ];
    }

    /**
     * Restore protected placeholders after translation
     */
    private function restoreProtectedPlaceholders($text)
    {
        if (empty($this->protectedPlaceholders)) {
            $this->output('warning', 'No protected placeholders stored for restoration');
            return $text;
        }

        $restored = $text;
        $this->output('info', 'Restoring ' . count($this->protectedPlaceholders) . ' placeholders');

        foreach ($this->protectedPlaceholders as $token => $originalPlaceholder) {
            $before = $restored;
            $restored = str_replace($token, $originalPlaceholder, $restored);
            if ($before !== $restored) {
                $this->line("<fg=blue>Restored:</> <fg=magenta>{$token}</> <fg=cyan>→</> <fg=yellow>{$originalPlaceholder}</>");
            }
        }

        // Validate that all placeholders were restored
        if (preg_match('/__(?:PLACEHOLDER|PRINTF)_\d+__/', $restored)) {
            $this->output('warning', 'Some placeholders may not have been properly restored');
        }

        return $restored;
    }

    /**
     * Translate context separately for clean results
     */
    private function translateContextSeparately($context, $targetLanguageCode)
    {
        try {
            $cmd = "trans -e {$this->selectedEngine} -brief en:{$targetLanguageCode} " . escapeshellarg($context) . " 2>/dev/null";
            $result = trim(shell_exec($cmd));

            return !empty($result) && $result !== $context ? $result : $context;
        } catch (Exception $e) {
            return $context;
        }
    }
}
