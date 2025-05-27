<?php

namespace Webkernel\Console\Package;

use Illuminate\Console\Command;
use Exception;

class TranslateTerm extends Command
{
    protected $signature = 'webkernel:lang-add {text? : English text to translate} {key? : Translation key (optional)} ' .
                          '{--change-key : Change existing key mode} ' .
                          '{--old-key= : Old key to change (use with --change-key)} ' .
                          '{--new-key= : New key to change to (use with --change-key)} ' .
                          '{--restore : Restore from backup} ' .
                          '{--validate-only : Only validate existing files} ' .
                          '{--repair : Repair syntax errors in existing files} ' .
                          '{--retranslate : Retranslate all existing entries from English} ' .
                          '{--lang= : Target specific language for retranslation (e.g., --lang=fr)} ' .
                          '{--protect : Mark keys as protected (interactive or with keys)} ' .
                          '{--keys= : Specify keys for --protect (comma-separated, e.g., --keys=key1,key2)} ' .
                          '{--all-langs : Apply protection to all languages (use with --protect)}';

    protected $description = 'Add translation or change key - robust translation management with auto-repair';

    protected $baseDir = 'packages/webkernel/src/lang';

    protected $languageMap = [
        // Priority languages first
        'en' => 'en', 'ar' => 'ar', 'fr' => 'fr',
        // Other languages
        'az' => 'az', 'bg' => 'bg', 'bn' => 'bn', 'ha' => 'ha', 'ca' => 'ca',
        'ckb' => 'ku', 'cs' => 'cs', 'da' => 'da', 'de' => 'de', 'el' => 'el',
        'es' => 'es', 'fa' => 'fa', 'fi' => 'fi', 'he' => 'he', 'hi' => 'hi',
        'hr' => 'hr', 'hu' => 'hu', 'hy' => 'hy', 'id' => 'id', 'it' => 'it', 'ja' => 'ja',
        'ka' => 'ka', 'km' => 'km', 'ko' => 'ko', 'ku' => 'ku', 'lt' => 'lt', 'lv' => 'lv',
        'mn' => 'mn', 'ms' => 'ms', 'my' => 'my', 'nl' => 'nl', 'no' => 'no', 'np' => 'ne',
        'pl' => 'pl', 'pt_BR' => 'pt-BR', 'pt' => 'pt-PT', 'ro' => 'ro', 'ru' => 'ru',
        'sk' => 'sk', 'sl' => 'sl', 'sq' => 'sq', 'sv' => 'sv', 'sw' => 'sw', 'th' => 'th',
        'tr' => 'tr', 'uk' => 'uk', 'uz' => 'uz', 'vi' => 'vi', 'zh_CN' => 'zh-CN', 'zh_TW' => 'zh-TW'
    ];

    // Language names for display
    protected $languageNames = [
        'en' => 'English', 'ar' => 'Arabic', 'fr' => 'French',
        'az' => 'Azerbaijani', 'bg' => 'Bulgarian', 'bn' => 'Bengali', 'ha' => 'Hausa', 'ca' => 'Catalan',
        'ckb' => 'Kurdish (Sorani)', 'cs' => 'Czech', 'da' => 'Danish', 'de' => 'German', 'el' => 'Greek',
        'es' => 'Spanish', 'fa' => 'Persian', 'fi' => 'Finnish', 'he' => 'Hebrew', 'hi' => 'Hindi',
        'hr' => 'Croatian', 'hu' => 'Hungarian', 'hy' => 'Armenian', 'id' => 'Indonesian', 'it' => 'Italian', 'ja' => 'Japanese',
        'ka' => 'Georgian', 'km' => 'Khmer', 'ko' => 'Korean', 'ku' => 'Kurdish', 'lt' => 'Lithuanian', 'lv' => 'Latvian',
        'mn' => 'Mongolian', 'ms' => 'Malay', 'my' => 'Myanmar', 'nl' => 'Dutch', 'no' => 'Norwegian', 'np' => 'Nepali',
        'pl' => 'Polish', 'pt_BR' => 'Portuguese (Brazil)', 'pt' => 'Portuguese', 'ro' => 'Romanian', 'ru' => 'Russian',
        'sk' => 'Slovak', 'sl' => 'Slovenian', 'sq' => 'Albanian', 'sv' => 'Swedish', 'sw' => 'Swahili', 'th' => 'Thai',
        'tr' => 'Turkish', 'uk' => 'Ukrainian', 'uz' => 'Uzbek', 'vi' => 'Vietnamese', 'zh_CN' => 'Chinese (Simplified)', 'zh_TW' => 'Chinese (Traditional)'
    ];

    protected $rtlLanguages = ['ar', 'fa', 'he', 'ku', 'ckb'];

    protected $preferredEngines = ['bing', 'google', 'yandex', 'deepl'];

    protected $retryAttempts = 3;
    protected $selectedEngine;
    protected $backupDir;
    protected $errorLog = [];

    public function handle()
    {
        try {
            $this->initializeBackupDir();

            // Set up signal handler for Ctrl+C
            if (function_exists('pcntl_signal')) {
                pcntl_signal(SIGINT, function () {
                    $this->error('Operation interrupted by user.');
                    $this->cleanup();
                    exit(0); // Force immediate exit on Ctrl+C
                });
            }

            // Handle repair option FIRST
            if ($this->option('repair')) {
                return $this->handleRepair();
            }

            // Handle retranslate option
            if ($this->option('retranslate')) {
                return $this->handleRetranslate();
            }

            // Handle protect option
            if ($this->option('protect')) {
                return $this->handleProtect();
            }

            // Handle restore option
            if ($this->option('restore')) {
                return $this->handleRestore();
            }

            // Handle validation only
            if ($this->option('validate-only')) {
                return $this->handleValidateOnly();
            }

            // Handle change key option
            if ($this->option('change-key')) {
                return $this->handleChangeKey();
            }

            // Normal translation flow
            return $this->handleAddTranslation();

        } catch (Exception $e) {
            $this->error('Critical error: ' . $e->getMessage());
            $this->logError('Critical error in handle()', $e);
            return 1; // Never use exit()
        }
    }

    protected function initializeBackupDir()
    {
        try {
            $this->backupDir = storage_path('translation_backups/' . date('Y-m-d_H-i-s'));
            if (!is_dir($this->backupDir)) {
                mkdir($this->backupDir, 0755, true);
            }
            $this->info("Backup directory: {$this->backupDir}");
        } catch (Exception $e) {
            $this->warn("Could not create backup directory: " . $e->getMessage());
            $this->backupDir = sys_get_temp_dir() . '/translation_backups_' . time();
            mkdir($this->backupDir, 0755, true);
        }
    }

    protected function handleRepair()
    {
        $this->info('REPAIR MODE: Fixing syntax errors in translation files...');
        $totalFiles = 0;
        $repairedFiles = 0;
        $failedRepairs = [];
        $repairedLocales = [];

        foreach ($this->languageMap as $locale => $translationCode) {
            $filePath = $this->getLanguageFilePath($locale);
            if (file_exists($filePath)) {
                $totalFiles++;
                $this->line("Checking {$locale}...");

                if (!$this->validatePhpSyntax($filePath)) {
                    $this->line("  Found syntax errors, attempting repair...");

                    // Create backup before repair
                    $backupPath = $this->backupDir . "/{$locale}_before_repair.php";
                    copy($filePath, $backupPath);

                    if ($this->repairTranslationFile($filePath, $locale)) {
                        $repairedFiles++;
                        $repairedLocales[] = $locale;
                        $this->info("  Successfully repaired {$locale}");
                    } else {
                        $failedRepairs[] = $locale;
                        $this->error("  Failed to repair {$locale}");
                    }
                } else {
                    $this->line("  {$locale}: No issues found");
                }
            }
        }

        // Summary
        $this->newLine();
        $this->info("Repair complete!");
        $this->line("Total files checked: {$totalFiles}");
        $this->line("Files repaired: {$repairedFiles}");

        if (!empty($failedRepairs)) {
            $this->warn("Failed repairs: " . implode(', ', $failedRepairs));
        }

        // Offer retranslation for repaired files
        if (!empty($repairedLocales)) {
            $this->newLine();
            $this->info("Repaired files may have incomplete translations.");
            if ($this->confirm('Would you like to retranslate the repaired files now?', true)) {
                return $this->retranslateRepairedFiles($repairedLocales, $this->getEnglishTranslations());
            }
        }

        return empty($failedRepairs) ? 0 : 1;
    }

    protected function repairTranslationFile($filePath, $locale)
    {
        try {
            // Try to load with error suppression
            $content = file_get_contents($filePath);

            // Basic syntax repair attempts
            $repairedContent = $this->basicSyntaxRepair($content);

            // Test the repaired content
            $testFile = tempnam(sys_get_temp_dir(), 'repair_test_');
            file_put_contents($testFile, $repairedContent);

            if ($this->validatePhpSyntax($testFile)) {
                file_put_contents($filePath, $repairedContent);
                unlink($testFile);
                return true;
            }

            unlink($testFile);

            // If basic repair fails, create minimal valid structure
            $direction = in_array($locale, $this->rtlLanguages) ? 'rtl' : 'ltr';
            $this->createMinimalValidFile($filePath, $direction);

            return $this->validatePhpSyntax($filePath);

        } catch (Exception $e) {
            $this->logError("Repair failed for {$locale}", $e);
            return false;
        }
    }

    protected function basicSyntaxRepair($content)
    {
        // Remove obvious syntax issues
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);

        // Fix common quote issues
        $content = preg_replace("/(?<!\\\\)'/", "\\'", $content);
        $content = str_replace('\\\\\'', '\\\'', $content); // Fix double escaping

        // Ensure proper PHP tags
        if (!preg_match('/^<\?php/', $content)) {
            $content = "<?php\n\n" . ltrim($content, "<?php \n\r\t");
        }

        return $content;
    }

    protected function handleValidateOnly()
    {
        $this->info('VALIDATION MODE: Checking syntax of all translation files...');
        $totalFiles = 0;
        $invalidFiles = [];

        foreach ($this->languageMap as $locale => $translationCode) {
            $filePath = $this->getLanguageFilePath($locale);
            if (file_exists($filePath)) {
                $totalFiles++;
                $this->line("Validating {$locale}...");

                if (!$this->validatePhpSyntax($filePath)) {
                    $invalidFiles[] = $locale;
                    $this->error("  {$locale}: Syntax errors found");
                } else {
                    $this->line("  {$locale}: Valid");
                }
            }
        }

        $this->newLine();
        $this->info("Validation complete!");
        $this->line("Total files: {$totalFiles}");
        $this->line("Valid files: " . ($totalFiles - count($invalidFiles)));

        if (!empty($invalidFiles)) {
            $this->warn("Invalid files: " . implode(', ', $invalidFiles));
            $this->line("Run with --repair to fix syntax errors automatically.");
        }

        return empty($invalidFiles) ? 0 : 1;
    }

    protected function handleRestore()
    {
        $this->info('RESTORE MODE: Restoring from backup...');

        $backupDirs = glob(storage_path('translation_backups/*'), GLOB_ONLYDIR);

        if (empty($backupDirs)) {
            $this->warn('No backups found.');
            return 1;
        }

        $this->info('Available backups:');
        foreach ($backupDirs as $i => $dir) {
            $this->line(($i + 1) . '. ' . basename($dir));
        }

        $choice = $this->ask('Enter backup number to restore (or "cancel")');

        if ($choice === 'cancel' || !is_numeric($choice)) {
            $this->info('Restore cancelled.');
            return 0;
        }

        $selectedBackup = $backupDirs[$choice - 1] ?? null;

        if (!$selectedBackup || !is_dir($selectedBackup)) {
            $this->error('Invalid backup selection.');
            return 1;
        }

        if (!$this->confirm("Restore from backup: " . basename($selectedBackup) . "?", false)) {
            $this->info('Restore cancelled.');
            return 0;
        }

        return $this->performRestore($selectedBackup);
    }

    protected function performRestore($backupDir)
    {
        $restored = 0;
        $failed = 0;

        $files = glob($backupDir . '/*.php');

        foreach ($files as $backupFile) {
            $filename = basename($backupFile);
            $locale = str_replace(['_before_repair.php', '.php'], '', $filename);

            if (isset($this->languageMap[$locale])) {
                $targetPath = $this->getLanguageFilePath($locale);

                try {
                    copy($backupFile, $targetPath);
                    $this->line("Restored {$locale}");
                    $restored++;
                } catch (Exception $e) {
                    $this->error("Failed to restore {$locale}: " . $e->getMessage());
                    $failed++;
                }
            }
        }

        $this->newLine();
        $this->info("Restore complete!");
        $this->line("Files restored: {$restored}");

        if ($failed > 0) {
            $this->warn("Failed restorations: {$failed}");
        }

        return $failed > 0 ? 1 : 0;
    }

    protected function handleAddTranslation()
    {
        try {
            $text = $this->argument('text');
            $key = $this->argument('key');

            // Get text if not provided
            if (!$text) {
                $text = $this->ask('Enter the English text to translate');
                if (!$text) {
                    $this->error('Text is required for translation.');
                    return 1;
                }
            }

            // Generate or get key
            if (!$key) {
                $key = $this->option('key') ?: $this->generateKeyFromText($text);

                $customKey = $this->ask("Generated key: '{$key}'. Press Enter to use, or enter custom key", '');
                if ($customKey) {
                    $key = $customKey;
                }
            }

            // Validate key
            if (!$this->isValidKey($key)) {
                $this->error('Invalid key. Use only letters, numbers, and underscores.');
                return 1;
            }

            // Check for existing key
            if ($this->keyExists($key)) {
                $choice = $this->choice(
                    "Key '{$key}' already exists. What would you like to do?",
                    ['overwrite', 'cancel', 'unique'],
                    'unique'
                );

                if ($choice === 'cancel') {
                    $this->info('Cancelled.');
                    return 0;
                }

                if ($choice === 'unique') {
                    $key = $this->makeUniqueKey($key);
                    $this->info("Using unique key: {$key}");
                }
            }

            // Select translation engine
            $this->selectTranslationEngine();

            // Final confirmation
            $this->newLine();
            $this->info("SUMMARY:");
            $this->line("Text: " . $text);
            $this->line("Key: {$key}");
            $this->line("Location: {$this->baseDir}");
            $this->line("Engine: {$this->selectedEngine}");
            $this->line("Languages: " . count($this->languageMap) . " languages");

            if (!$this->confirm('Proceed with translation?', true)) {
                $this->info('Translation cancelled.');
                return 0;
            }

            return $this->processTranslations($text, $key);

        } catch (Exception $e) {
            $this->error('Error in translation process: ' . $e->getMessage());
            $this->logError('handleAddTranslation failed', $e);
            return 1;
        }
    }

    protected function processTranslations($text, $key)
    {
        $this->info("Translating '{$text}' with key '{$key}' to all languages...");
        $successCount = 0;
        $errorCount = 0;

        foreach ($this->languageMap as $locale => $translationCode) {
            $this->line("Processing {$locale}...");

            try {
                // Skip English - use original text
                if ($locale === 'en') {
                    $translation = $text;
                } else {
                    $translation = $this->translateTerm($translationCode, $text);
                }

                // Clean the translation
                $translation = $this->cleanTranslation($translation);
                $this->info("{$locale}: {$translation}");

                $direction = in_array($locale, $this->rtlLanguages) ? 'rtl' : 'ltr';

                if ($this->writeTranslationSafely($locale, $key, $translation, $direction)) {
                    $successCount++;
                } else {
                    $this->warn("{$locale}: Saved with fallback method");
                    $errorCount++;
                }

                usleep(300000); // Rate limiting
            } catch (Exception $e) {
                $this->error("{$locale}: " . $e->getMessage());
                $this->logError("Translation failed for {$locale}", $e);
                $errorCount++;
            }
        }

        // Summary
        $this->newLine();
        $this->info("Completed! Key: {$key}");
        $this->line("Saved to: {$this->baseDir}");
        $this->line("Success: {$successCount} files");

        if ($errorCount > 0) {
            $this->warn("Issues: {$errorCount} files (check files manually)");
        }

        return $errorCount > 0 ? 1 : 0;
    }

    protected function writeTranslationSafely($locale, $key, $translation, $direction = 'ltr')
    {
        $filePath = $this->getLanguageFilePath($locale);
        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                // Create backup before each attempt
                $backupPath = null;
                if (file_exists($filePath)) {
                    $backupPath = $this->backupDir . "/{$locale}_attempt_{$attempt}.php";
                    copy($filePath, $backupPath);
                }

                // Load or create array structure
                $translations = $this->loadTranslationsArray($filePath, $direction);

                // Add/update the translation with metadata
                $translations['actions'][$key] = [
                    'label' => $translation,
                    'auto_generated' => true,
                    'engine_used' => $this->selectedEngine ?? 'unknown',
                    'generated_at' => date('Y-m-d H:i:s'),
                    'protected' => false
                ];

                // Write the file with safe escaping
                $this->writeTranslationFile($filePath, $translations);

                // Validate syntax
                if ($this->validatePhpSyntax($filePath)) {
                    // Clean up backup on success
                    if (isset($backupPath) && file_exists($backupPath)) {
                        unlink($backupPath);
                    }
                    return true;
                } else {
                    throw new Exception("Syntax validation failed after write attempt {$attempt}");
                }

            } catch (Exception $e) {
                $this->logError("Write attempt {$attempt} failed for {$locale}", $e);

                // Restore backup if it exists
                if (isset($backupPath) && file_exists($backupPath)) {
                    copy($backupPath, $filePath);
                    unlink($backupPath);
                }

                if ($attempt === $maxAttempts) {
                    // Last attempt: create minimal valid file
                    $this->createMinimalValidFile($filePath, $direction);
                    return false;
                }

                // Try with more aggressive escaping on next attempt
                $translation = $this->aggressiveEscape($translation);
            }
        }

        return false;
    }

    protected function loadTranslationsArray($filePath, $direction = 'ltr')
    {
        if (file_exists($filePath)) {
            try {
                $translations = include $filePath;
                if (is_array($translations) && isset($translations['actions'])) {
                    return $translations;
                }
            } catch (Exception $e) {
                $this->logError("Failed to load translations from {$filePath}", $e);
            }
        }

        // Return default structure
        return [
            'direction' => $direction,
            'actions' => []
        ];
    }

    protected function writeTranslationFile($filePath, $translations)
    {
        // Ensure directory exists
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = "<?php\n\nreturn [\n";
        $content .= "    'direction' => " . $this->safePhpEscape($translations['direction']) . ",\n";
        $content .= "    'actions' => [\n";

        foreach ($translations['actions'] as $key => $value) {
            $safeKey = $this->safePhpEscape($key);
            $safeValue = $this->safePhpEscape($value['label']);
            $content .= "        {$safeKey} => [\n";
            $content .= "            'label' => {$safeValue},\n";

            // Add metadata if present
            if (isset($value['auto_generated'])) {
                $content .= "            'auto_generated' => " . ($value['auto_generated'] ? 'true' : 'false') . ",\n";
            }
            if (isset($value['engine_used'])) {
                $content .= "            'engine_used' => " . $this->safePhpEscape($value['engine_used']) . ",\n";
            }
            if (isset($value['generated_at'])) {
                $content .= "            'generated_at' => " . $this->safePhpEscape($value['generated_at']) . ",\n";
            }
            if (isset($value['protected']) && $value['protected']) {
                $content .= "            'protected' => true,\n";
            }

            $content .= "        ],\n";
        }

        $content .= "    ],\n";
        $content .= "];\n";

        file_put_contents($filePath, $content);
    }

    protected function validatePhpSyntax($filePath)
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $output = shell_exec("php -l " . escapeshellarg($filePath) . " 2>&1");
        return strpos($output, 'No syntax errors detected') !== false;
    }

    protected function safePhpEscape($value)
    {
        if (!is_string($value)) {
            return var_export($value, true);
        }

        // Try different escaping methods
        $methods = [
            function($v) { return "'" . addslashes($v) . "'"; },
            function($v) { return '"' . addslashes($v) . '"'; },
            function($v) { return $this->base64Escape($v); }
        ];

        foreach ($methods as $method) {
            $escaped = $method($value);
            if ($this->testEscaping($escaped)) {
                return $escaped;
            }
        }

        // Fallback to base64
        return $this->base64Escape($value);
    }

    protected function base64Escape($value)
    {
        $base64 = base64_encode($value);
        return "base64_decode('" . $base64 . "')";
    }

    protected function testEscaping($escapedValue)
    {
        try {
            $testCode = "<?php\n\$test = {$escapedValue};\necho 'OK';\n";
            $tempFile = tempnam(sys_get_temp_dir(), 'escape_test_');
            file_put_contents($tempFile, $testCode);

            $output = shell_exec("php " . escapeshellarg($tempFile) . " 2>&1");
            unlink($tempFile);

            return strpos($output, 'OK') !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function aggressiveEscape($value)
    {
        // Remove problematic characters and normalize
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        $value = str_replace(['<?', '?>', '${', '`'], ['&lt;?', '?&gt;', '$\\{', '\\`'], $value);
        return trim($value);
    }

    protected function cleanTranslation($translation)
    {
        if (!is_string($translation)) {
            return '';
        }

        // Clean up spacing only - DO NOT modify actual translation content
        $translation = preg_replace('/\s+/', ' ', $translation);
        $translation = trim($translation);

        // Remove only control characters that could break PHP syntax
        $translation = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $translation);

        return $translation;
    }

    protected function generateKeyFromText($text)
    {
        // Convert to lowercase and replace spaces/special chars with underscores
        $key = strtolower($text);
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);
        $key = trim($key, '_');

        // Limit length
        if (strlen($key) > 50) {
            $key = substr($key, 0, 50);
        }

        return $key ?: 'translation_' . time();
    }

    protected function isValidKey($key)
    {
        return preg_match('/^[a-zA-Z0-9_]+$/', $key) && strlen($key) <= 100;
    }

    protected function keyExists($key)
    {
        $englishFile = $this->getLanguageFilePath('en');
        if (!file_exists($englishFile)) {
            return false;
        }

        try {
            $translations = include $englishFile;
            return isset($translations['actions'][$key]);
        } catch (Exception $e) {
            return false;
        }
    }

    protected function makeUniqueKey($baseKey)
    {
        $counter = 1;
        $newKey = $baseKey;

        while ($this->keyExists($newKey)) {
            $newKey = $baseKey . '_' . $counter;
            $counter++;
        }

        return $newKey;
    }

    protected function createBackup()
    {
        $this->info('Creating backup...');

        foreach ($this->languageMap as $locale => $code) {
            $filePath = $this->getLanguageFilePath($locale);
            if (file_exists($filePath)) {
                $backupPath = $this->backupDir . "/{$locale}.php";
                copy($filePath, $backupPath);
            }
        }

        $this->info("Backup created in: {$this->backupDir}");
    }

    protected function getEnglishTranslations()
    {
        $englishFile = $this->getLanguageFilePath('en');

        try {
            if (file_exists($englishFile)) {
                $translations = include $englishFile;
                if (isset($translations['actions']) && is_array($translations['actions'])) {
                    return $translations['actions'];
                }
            }
        } catch (Exception $e) {
            $this->logError("Failed to load English translations", $e);
        }

        return [];
    }

    protected function retranslateRepairedFiles($repairedLocales, $englishTranslations)
    {
        $this->info("Retranslating " . count($englishTranslations) . " entries to " . count($repairedLocales) . " languages...");

        $totalTranslations = 0;
        $successfulTranslations = 0;
        $failedTranslations = 0;

        foreach ($repairedLocales as $locale) {
            if ($locale === 'en') {
                continue; // Skip English
            }

            $translationCode = $this->languageMap[$locale];
            $languageName = $this->languageNames[$locale] ?? $locale;
            $this->line("Retranslating to {$locale} ({$languageName})...");

            foreach ($englishTranslations as $key => $englishData) {
                $totalTranslations++;
                $englishText = $englishData['label'] ?? '';

                if (empty($englishText)) {
                    continue;
                }

                // Check if this translation is protected for this specific language
                if ($this->isTranslationProtected($locale, $key)) {
                    $this->line("  {$key}: PROTECTED - skipping");
                    $successfulTranslations++; // Count as success since it's intentionally skipped
                    continue;
                }

                try {
                    // Check for Ctrl+C between each translation
                    if (function_exists('pcntl_signal_dispatch')) {
                        pcntl_signal_dispatch();
                    }

                    // Translate from English
                    $translation = $this->translateTermWithRetry($translationCode, $englishText);
                    $translation = $this->cleanTranslation($translation);

                    $direction = in_array($locale, $this->rtlLanguages) ? 'rtl' : 'ltr';

                    if ($this->writeTranslationSafely($locale, $key, $translation, $direction)) {
                        $successfulTranslations++;
                        $this->line("  {$key}: {$translation}");
                    } else {
                        $failedTranslations++;
                        $this->warn("  {$key}: Saved with fallback method");
                    }

                    usleep(300000); // Rate limiting
                } catch (Exception $e) {
                    $failedTranslations++;
                    $this->error("  {$key}: " . $e->getMessage());
                    $this->logError("Retranslation failed for {$locale}:{$key}", $e);
                }
            }
        }

        // Summary
        $this->newLine();
        $this->info("Retranslation complete!");
        $this->line("Total translations: {$totalTranslations}");
        $this->line("Successful: {$successfulTranslations}");

        if ($failedTranslations > 0) {
            $this->warn("Failed: {$failedTranslations}");
        }

        return $failedTranslations > 0 ? 1 : 0;
    }

    protected function translateTermWithRetry($translationCode, $text)
    {
        $attempts = 0;
        $lastError = null;

        while ($attempts < $this->retryAttempts) {
            try {
                $translation = $this->translateTerm($translationCode, $text);
                if (!empty($translation) && $translation !== $text) {
                    return $translation;
                }
                throw new Exception("Empty or unchanged translation returned");
            } catch (Exception $e) {
                $lastError = $e;
                $attempts++;

                if ($attempts < $this->retryAttempts) {
                    $this->line("  Retry attempt {$attempts}...");
                    sleep(1); // Wait before retry
                }
            }
        }

        // If all retries failed, return original text with a note
        $this->warn("Translation failed for '{$text}', using original text");
        return $text;
    }

    protected function selectTranslationEngine()
    {
        $this->info('Testing translation engines...');

        foreach ($this->preferredEngines as $engine) {
            $this->line("-> Testing {$engine}...");
            if ($this->isTranslatorAvailable($engine)) {
                $this->selectedEngine = $engine;
                $this->info("SUCCESS: Using {$engine} translation engine");
                return;
            }

            $this->line("FAILED: {$engine} not available");
        }

        $this->selectedEngine = 'google';
        $this->warn("Using default engine (google) - quality may vary");
    }

    protected function isTranslatorAvailable($engine = 'google')
    {
        $testTerm = 'Hello';
        $expected = 'Bonjour'; // Expected French translation

        $this->line("  Testing '{$testTerm}' -> French...");
        $cmd = "timeout 5s trans -e {$engine} -brief en:fr " . escapeshellarg($testTerm) . " 2>/dev/null";
        $output = trim(shell_exec($cmd));

        $this->line("  Got: '{$output}'");
        $available = stripos($output, $expected) !== false;

        if ($available) {
            $this->line("  SUCCESS: Engine working correctly");
        } else {
            $this->line("  FAILED: Engine failed or returned unexpected result");
        }

        return $available;
    }

    protected function selectTargetDirectory()
    {
        // Keep default for now
        $this->info("Target directory: {$this->baseDir}");
    }

    protected function translateTerm($translationCode, $text)
    {
        // Get configured engines from config
        $configuredEngines = config('webkernel.translation.engine_priority', ['bing', 'google', 'yandex']);
        $enableAI = config('webkernel.translation.quality.prefer_ai_engines', false);

        // Future: Check AI engines first if enabled
        if ($enableAI) {
            $aiTranslation = $this->tryAiTranslation($translationCode, $text);
            if (!empty($aiTranslation) && $aiTranslation !== $text) {
                return $aiTranslation;
            }
        }

        // Preprocess text for better semantic language translation
        $optimizedText = $this->optimizeTextForTranslation($text, $translationCode);

        // Use traditional engines
        return $this->translateWithTraditionalEngines($configuredEngines, $translationCode, $optimizedText);
    }

    protected function translateWithTraditionalEngines($engines, $translationCode, $text)
    {
        // Try engines in order, stop at first good result
        foreach ($engines as $engine) {
            // Skip AI engines in traditional method
            if (in_array($engine, ['openai', 'claude', 'gemini', 'local_llama', 'ollama'])) {
                continue;
            }

            $this->line("  Trying {$engine}...");

            $escapedText = escapeshellarg($text);
            $cmd = "timeout 8s trans -e {$engine} -brief en:{$translationCode} {$escapedText} 2>/dev/null";
            $translation = trim(shell_exec($cmd));

            if (!empty($translation) && $translation !== $text && strlen($translation) > 3) {
                // Quick quality check
                if ($this->isGoodTranslation($translation, $text)) {
                    $this->line("  SUCCESS with {$engine}: {$translation}");
                    return $translation;
                }
                $this->line("  POOR QUALITY from {$engine}, trying next...");
            } else {
                $this->line("  FAILED/TIMEOUT with {$engine}, trying next...");
            }
        }

        // If no good translation found, try again with less strict criteria
        $this->line("  -> Retrying with relaxed quality checks...");
        foreach ($engines as $engine) {
            if (in_array($engine, ['openai', 'claude', 'gemini', 'local_llama', 'ollama'])) {
                continue;
            }

            $escapedText = escapeshellarg($text);
            $cmd = "timeout 6s trans -e {$engine} -brief en:{$translationCode} {$escapedText} 2>/dev/null";
            $translation = trim(shell_exec($cmd));

            if (!empty($translation) && $translation !== $text) {
                $this->line("  FALLBACK SUCCESS with {$engine}: {$translation}");
                return $translation;
            }
        }

        $this->warn("Translation service unavailable for: {$text}");
        return $text;
    }

    protected function isTranslationProtected($locale, $key)
    {
        $filePath = "{$this->baseDir}/{$locale}/translations.php";

        if (!file_exists($filePath)) {
            return false;
        }

        try {
            $translations = include $filePath;
            $translation = $translations['actions'][$key] ?? null;

            if (!$translation) {
                return false;
            }

            // Check if explicitly protected
            if (isset($translation['protected']) && $translation['protected']) {
                return true;
            }

            // Auto-protection rules
            return $this->shouldAutoProtect($translation);

        } catch (Exception $e) {
            return false; // If we can't read the file, don't protect
        }
    }

    protected function shouldAutoProtect($translation)
    {
        // Protection Rule 1: Manual translations (not auto-generated)
        if (!isset($translation['auto_generated']) || !$translation['auto_generated']) {
            return true; // Protect manual translations
        }

        // Protection Rule 2: High-quality Bing translations older than 24h
        if (isset($translation['engine_used']) && $translation['engine_used'] === 'bing') {
            if (isset($translation['generated_at'])) {
                $generatedTime = strtotime($translation['generated_at']);
                $dayAgo = time() - (24 * 60 * 60);

                if ($generatedTime < $dayAgo) {
                    return true; // Protect stable Bing translations
                }
            }
        }

        // Protection Rule 3: Translations that look manually corrected
        // (This could be enhanced with more sophisticated detection)

        return false; // Don't protect by default
    }

    protected function optimizeTextForTranslation($text, $targetLanguage)
    {
        // Define Semitic and Arabic-script languages
        $semiticLanguages = ['ar', 'he', 'fa', 'ku', 'ckb'];

        if (in_array($targetLanguage, $semiticLanguages)) {
            // Optimize logical connectors for better Semitic language translation
            $optimizations = [
                // Add emphasis to logical connectors
                ' and ' => ' AND ',
                ' or ' => ' OR ',
                ' with ' => ' WITH ',
                ' by ' => ' BY ',
                ' from ' => ' FROM ',
                ' to ' => ' TO ',
                ' of ' => ' OF ',
                ' in ' => ' IN ',
                ' on ' => ' ON ',
                ' at ' => ' AT ',
                ' for ' => ' FOR ',
                // Handle contractions properly
                "can't" => 'cannot',
                "won't" => 'will not',
                "don't" => 'do not',
                "it's" => 'it is',
                "you're" => 'you are',
                "we're" => 'we are',
                "they're" => 'they are',
            ];

            foreach ($optimizations as $search => $replace) {
                $text = str_ireplace($search, $replace, $text);
            }
        }

        return $text;
    }

    /*
    |--------------------------------------------------------------------------
    | Future AI Translation Methods (Not yet implemented)
    |--------------------------------------------------------------------------
    | These methods are prepared for future AI engine integration
    */

    protected function tryAiTranslation($translationCode, $text)
    {
        // Future implementation: Try AI engines based on configuration
        $aiEngines = config('webkernel.translation.ai_engines', []);

        foreach ($aiEngines as $engineName => $config) {
            if (!$config['enabled']) {
                continue;
            }

            try {
                switch ($engineName) {
                    case 'openai':
                        return $this->translateWithOpenAI($translationCode, $text, $config);
                    case 'claude':
                        return $this->translateWithClaude($translationCode, $text, $config);
                    case 'gemini':
                        return $this->translateWithGemini($translationCode, $text, $config);
                    default:
                        break;
                }
            } catch (Exception $e) {
                $this->logError("AI translation failed with {$engineName}", $e);
                continue;
            }
        }

        return null;
    }

    protected function translateWithOpenAI($translationCode, $text, $config)
    {
        // Future implementation for OpenAI
        return null;
    }

    protected function translateWithClaude($translationCode, $text, $config)
    {
        // Future implementation for Claude
        return null;
    }

    protected function translateWithGemini($translationCode, $text, $config)
    {
        // Future implementation for Gemini
        return null;
    }

    protected function isGoodTranslation($translation, $originalText)
    {
        // Basic quality checks
        if (empty($translation) || $translation === $originalText) {
            return false;
        }

        // Check for obvious translation failures
        if (strlen($translation) < 2) {
            return false;
        }

        // Check for untranslated content (still in English)
        $englishWords = ['the', 'and', 'or', 'in', 'on', 'at', 'to', 'from', 'with', 'by'];
        $foundEnglishWords = 0;

        foreach ($englishWords as $word) {
            if (stripos($translation, ' ' . $word . ' ') !== false) {
                $foundEnglishWords++;
            }
        }

        // If too many English words found, quality is poor
        if ($foundEnglishWords > 2) {
            return false;
        }

        return true;
    }

    protected function handleProtect()
    {
        $this->info('PROTECT MODE: Marking translations as protected...');

        // Get keys to protect
        $keysOption = $this->option('keys');
        $allLangs = $this->option('all-langs');

        $keysToProtect = [];

        if ($keysOption) {
            $keysToProtect = array_map('trim', explode(',', $keysOption));
        } else {
            // Interactive mode
            $this->info('Enter keys to protect (one per line, empty line to finish):');
            while (true) {
                $key = $this->ask('Key to protect');
                if (empty($key)) {
                    break;
                }
                $keysToProtect[] = trim($key);
            }
        }

        if (empty($keysToProtect)) {
            $this->warn('No keys specified for protection.');
            return 1;
        }

        // Determine target languages
        $targetLanguages = [];
        if ($allLangs) {
            $targetLanguages = array_keys($this->languageMap);
            $this->info('Applying protection to ALL languages: ' . implode(', ', $targetLanguages));
        } else {
            // Interactive language selection
            $this->info('Available languages: ' . implode(', ', array_keys($this->languageMap)));
            $langInput = $this->ask('Languages to protect (comma-separated, or "all" for all languages)', 'all');

            if ($langInput === 'all') {
                $targetLanguages = array_keys($this->languageMap);
            } else {
                $targetLanguages = array_map('trim', explode(',', $langInput));

                // Validate languages
                foreach ($targetLanguages as $lang) {
                    if (!isset($this->languageMap[$lang])) {
                        $this->error("Language '{$lang}' not supported.");
                        return 1;
                    }
                }
            }
        }

        $this->info('Keys to protect: ' . implode(', ', $keysToProtect));
        $this->info('Target languages: ' . implode(', ', $targetLanguages));

        if (!$this->confirm('Apply protection to these keys and languages?', true)) {
            $this->info('Protection cancelled.');
            return 0;
        }

        return $this->applyProtection($keysToProtect, $targetLanguages);
    }

    protected function applyProtection($keys, $languages)
    {
        $successCount = 0;
        $errorCount = 0;

        foreach ($languages as $locale) {
            $filePath = $this->getLanguageFilePath($locale);

            if (!file_exists($filePath)) {
                $this->line("  {$locale}: File not found, skipping");
                continue;
            }

            try {
                $translations = include $filePath;
                $modified = false;

                foreach ($keys as $key) {
                    if (isset($translations['actions'][$key])) {
                        $translations['actions'][$key]['protected'] = true;
                        $translations['actions'][$key]['protected_at'] = date('Y-m-d H:i:s');
                        $modified = true;
                        $this->line("  {$locale}: Protected '{$key}'");
                    } else {
                        $this->warn("  {$locale}: Key '{$key}' not found");
                    }
                }

                if ($modified) {
                    $direction = $translations['direction'] ?? 'ltr';
                    $this->writeTranslationFile($filePath, $translations);
                    $successCount++;
                } else {
                    $this->line("  {$locale}: No changes needed");
                }

            } catch (Exception $e) {
                $this->error("  {$locale}: Failed to apply protection - " . $e->getMessage());
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info('Protection applied!');
        $this->line("Success: {$successCount} languages");
        if ($errorCount > 0) {
            $this->warn("Errors: {$errorCount} languages");
        }

        return $errorCount > 0 ? 1 : 0;
    }

    protected function handleChangeKey()
    {
        // Implementation for changing keys
        $this->info('Change key functionality - to be implemented');
        return 0;
    }

    protected function handleRetranslate()
    {
        $targetLang = $this->option('lang');

        if ($targetLang) {
            $this->info("RETRANSLATE MODE: Retranslating to {$targetLang} only...");
        } else {
            $this->info('RETRANSLATE MODE: Retranslating all existing entries from English...');
        }

        // Select target directory
        $this->selectTargetDirectory();

        // Get English translations
        $englishTranslations = $this->getEnglishTranslations();

        if (empty($englishTranslations)) {
            $this->warn("No English translations found to retranslate from.");
            $this->line("Add translations first using: php artisan webkernel:lang-add 'Your text'");
            return 1;
        }

        $this->info("Found " . count($englishTranslations) . " English translations to process.");

        // Get target languages
        if ($targetLang) {
            if (!isset($this->languageMap[$targetLang])) {
                $this->error("Language '{$targetLang}' not supported.");
                $this->line("Supported languages: " . implode(', ', array_keys($this->languageMap)));
                return 1;
            }
            $targetLocales = [$targetLang];
            $languageName = $this->languageNames[$targetLang] ?? $targetLang;
            $this->info("Target: {$targetLang} ({$languageName})");
        } else {
            // Get all languages except English
            $targetLocales = array_filter(array_keys($this->languageMap), function($locale) {
                return $locale !== 'en';
            });
        }

        $this->info("Protected translations will be automatically skipped.");

        if (!$this->confirm("This will retranslate entries to " . count($targetLocales) . " languages (respecting protections). Continue?", true)) {
            $this->info('Retranslation cancelled.');
            return 0;
        }

        // Create backup
        $this->createBackup();

        // Select translation engine
        $this->selectTranslationEngine();

        // Perform retranslation
        return $this->retranslateRepairedFiles($targetLocales, $englishTranslations);
    }

    protected function createMinimalValidFile($filePath, $direction = 'ltr')
    {
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = "<?php\n\nreturn [\n    'direction' => '{$direction}',\n    'actions' => [\n    ],\n];\n";
        file_put_contents($filePath, $content);
    }

    protected function getLanguageFilePath($locale)
    {
        return $this->baseDir . '/' . $locale . '/translations.php';
    }

    protected function logError($message, $exception = null)
    {
        $this->errorLog[] = [
            'message' => $message,
            'exception' => $exception ? $exception->getMessage() : null,
            'time' => date('Y-m-d H:i:s')
        ];
    }

    protected function cleanup()
    {
        // Cleanup temporary files if any
        if (!empty($this->errorLog)) {
            $this->displayErrorLog();
        }
    }

    protected function displayErrorLog()
    {
        if (empty($this->errorLog)) {
            return;
        }

        $this->newLine();
        $this->warn('Error Log:');
        foreach ($this->errorLog as $entry) {
            $this->line("[{$entry['time']}] {$entry['message']}");
            if ($entry['exception']) {
                $this->line("  Exception: {$entry['exception']}");
            }
        }
    }
}
