<?php

namespace App\Console\Commands;

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
                          '{--retranslate : Retranslate all existing entries from English}';

    protected $description = 'Add translation or change key - robust translation management with auto-repair';

    protected $baseDir = 'packages/webkernel/src/lang';

    protected $languageMap = [
        'ar' => 'ar', 'az' => 'az', 'bg' => 'bg', 'bn' => 'bn', 'ha' => 'ha', 'ca' => 'ca',
        'ckb' => 'ku', 'cs' => 'cs', 'da' => 'da', 'de' => 'de', 'el' => 'el', 'en' => 'en',
        'es' => 'es', 'fa' => 'fa', 'fi' => 'fi', 'fr' => 'fr', 'he' => 'he', 'hi' => 'hi',
        'hr' => 'hr', 'hu' => 'hu', 'hy' => 'hy', 'id' => 'id', 'it' => 'it', 'ja' => 'ja',
        'ka' => 'ka', 'km' => 'km', 'ko' => 'ko', 'ku' => 'ku', 'lt' => 'lt', 'lv' => 'lv',
        'mn' => 'mn', 'ms' => 'ms', 'my' => 'my', 'nl' => 'nl', 'no' => 'no', 'np' => 'ne',
        'pl' => 'pl', 'pt_BR' => 'pt-BR', 'pt' => 'pt-PT', 'ro' => 'ro', 'ru' => 'ru',
        'sk' => 'sk', 'sl' => 'sl', 'sq' => 'sq', 'sv' => 'sv', 'sw' => 'sw', 'th' => 'th',
        'tr' => 'tr', 'uk' => 'uk', 'uz' => 'uz', 'vi' => 'vi', 'zh_CN' => 'zh-CN', 'zh_TW' => 'zh-TW'
    ];

    protected $rtlLanguages = ['ar', 'fa', 'he', 'ku', 'ckb'];

    protected $preferredEngines = ['google', 'bing', 'deepl', 'yandex'];

    protected $selectedEngine = null;

    protected $backupDir = null;

    protected $errorLog = [];

    protected $retryAttempts = 3;

    public function handle()
    {
        // JAMAIS d'exit() - toujours retourner un code d'erreur
        try {
            // Initialize backup directory
            $this->initializeBackupDir();

            // Set up signal handler for Ctrl+C
            if (function_exists('pcntl_signal')) {
                pcntl_signal(SIGINT, function () {
                    $this->error('Operation interrupted by user.');
                    $this->cleanup();
                    return 1; // Au lieu d'exit(1)
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

            // Handle restore option
            if ($this->option('restore')) {
                return $this->handleRestore();
            }

            // Handle validate-only option
            if ($this->option('validate-only')) {
                return $this->handleValidateOnly();
            }

            // Check if change-key mode
            if ($this->option('change-key')) {
                return $this->handleChangeKey();
            }

            // Normal add translation mode
            return $this->handleAddTranslation();

        } catch (Exception $e) {
            $this->error('Critical error: ' . $e->getMessage());
            $this->logError('Critical error in handle()', $e);
            return 1; // Jamais d'exit()
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
                    $this->warn("  Syntax error detected in {$locale}");

                    if ($this->repairSyntaxErrors($filePath, $locale)) {
                        $repairedFiles++;
                        $repairedLocales[] = $locale;
                        $this->info("  Repaired {$locale}");
                    } else {
                        $failedRepairs[] = $locale;
                        $this->error("  Could not repair {$locale}");
                    }
                } else {
                    $this->line("  {$locale} is valid");
                }
            }
        }

        $this->newLine();
        $this->info("Repair complete:");
        $this->line("Total files checked: {$totalFiles}");
        $this->line("Files repaired: {$repairedFiles}");

        if (!empty($failedRepairs)) {
            $this->warn("Failed repairs: " . implode(', ', $failedRepairs));
        }

        // Propose retranslation for repaired files
        if (!empty($repairedLocales) && $repairedFiles > 0) {
            $this->newLine();
            $this->info("Translation Recovery:");

            // Check if English file exists and has translations
            $englishTranslations = $this->getEnglishTranslations();

            if (!empty($englishTranslations)) {
                $this->line("Found " . count($englishTranslations) . " English translations that can be retranslated.");

                if ($this->confirm('Do you want to retranslate from English to the repaired languages?', true)) {
                    // Use the same translation engine selection process as normal mode
                    $this->selectTranslationEngine();
                    return $this->retranslateRepairedFiles($repairedLocales, $englishTranslations);
                }
            } else {
                $this->warn("No English translations found to retranslate from.");
                $this->line("Add translations first using: php artisan webkernel:lang-add 'Your text'");
            }
        }

        if (!empty($failedRepairs)) {
            return 1;
        }

        $this->info("All files are now valid!");
        return 0;
    }

    protected function repairSyntaxErrors($filePath, $locale)
    {
        try {
            // Create backup first
            $backupPath = $this->backupDir . '/' . basename($filePath) . '.backup';
            copy($filePath, $backupPath);

            // Try to load and extract valid data
            $validData = $this->extractValidDataFromCorruptedFile($filePath);

            if ($validData === false) {
                $this->warn("Could not extract data from {$locale}, creating minimal file");
                $validData = [];
            }

            // Create a clean file with valid data
            $direction = in_array($locale, $this->rtlLanguages) ? 'rtl' : 'ltr';
            $this->createCleanTranslationFile($filePath, $validData, $direction);

            // Validate the repaired file
            if ($this->validatePhpSyntax($filePath)) {
                return true;
            } else {
                // Restore backup if repair failed
                copy($backupPath, $filePath);
                return false;
            }

        } catch (Exception $e) {
            $this->logError("Repair failed for {$locale}", $e);
            return false;
        }
    }

    protected function extractValidDataFromCorruptedFile($filePath)
    {
        try {
            $content = file_get_contents($filePath);

            // Try to extract translations using regex
            $validData = [];

            // Pattern pour extraire les entrées de traduction valides
            $pattern = "/'([^']+)'\s*=>\s*\[\s*'label'\s*=>\s*'([^']*)'\s*\]/";

            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $key = $match[1];
                    $translation = $match[2];
                    $validData[$key] = ['label' => $translation];
                }
            }

            // Fallback: essayer d'exécuter le fichier dans un environnement isolé
            if (empty($validData)) {
                $tempFile = tempnam(sys_get_temp_dir(), 'translation_test_');

                // Nettoyer le contenu et essayer de l'évaluer
                $cleanContent = $this->cleanCorruptedPhpContent($content);
                file_put_contents($tempFile, $cleanContent);

                if ($this->validatePhpSyntax($tempFile)) {
                    $data = include $tempFile;
                    if (is_array($data) && isset($data['actions'])) {
                        $validData = $data['actions'];
                    }
                }

                unlink($tempFile);
            }

            return $validData;

        } catch (Exception $e) {
            $this->logError("Data extraction failed", $e);
            return false;
        }
    }

    protected function cleanCorruptedPhpContent($content)
    {
        // Enlever les caractères problématiques et réparer les erreurs communes

        // 1. Réparer les guillemets non échappés
        $content = preg_replace("/(?<!\\\\)'(?=\w)/", "\\'", $content);

        // 2. Réparer les doubles quotes problématiques
        $content = preg_replace('/(\w)"(\w)/', '$1\\"$2', $content);

        // 3. Enlever les caractères de contrôle invalides
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);

        // 4. Réparer les concaténations de strings cassées
        $content = preg_replace("/'\.?'([^',\]]+)'/", "'$1'", $content);

        // 5. Enlever les duplicatas de strings dans les valeurs
        $content = preg_replace("/'([^']*)'([^']*)'([^']*)'/", "'$1$2$3'", $content);

        return $content;
    }

    protected function createCleanTranslationFile($filePath, $data, $direction)
    {
        $content = "<?php\n\nreturn [\n";
        $content .= "    'direction' => '{$direction}',\n";
        $content .= "    'actions' => [\n";

        foreach ($data as $key => $value) {
            $cleanKey = $this->safePhpEscape($key);
            $cleanValue = $this->safePhpEscape($value['label'] ?? '');
            $content .= "        {$cleanKey} => [\n";
            $content .= "            'label' => {$cleanValue},\n";
            $content .= "        ],\n";
        }

        $content .= "    ],\n";
        $content .= "];\n";

        file_put_contents($filePath, $content);
    }

    protected function handleValidateOnly()
    {
        $this->info('🔍 Validating all translation files...');
        $totalFiles = 0;
        $validFiles = 0;
        $invalidFiles = [];

        foreach ($this->languageMap as $locale => $translationCode) {
            $filePath = $this->getLanguageFilePath($locale);
            if (file_exists($filePath)) {
                $totalFiles++;
                if ($this->validatePhpSyntax($filePath)) {
                    $validFiles++;
                    $this->line("✅ {$locale}: Valid");
                } else {
                    $invalidFiles[] = $locale;
                    $this->error("❌ {$locale}: Invalid syntax");
                }
            }
        }

        $this->newLine();
        $this->info("📊 Validation complete:");
        $this->line("Total files: {$totalFiles}");
        $this->line("Valid files: {$validFiles}");
        $this->line("Invalid files: " . count($invalidFiles));

        if (!empty($invalidFiles)) {
            $this->warn("Files with syntax errors: " . implode(', ', $invalidFiles));
            $this->line("💡 Run with --repair option to fix these files automatically");
            return 1;
        }

        $this->info("🎉 All files have valid syntax!");
        return 0;
    }

    protected function validatePhpSyntax($filePath)
    {
        if (!file_exists($filePath)) {
            return false;
        }

        // Method 1: Use php -l for syntax check
        $output = shell_exec("php -l " . escapeshellarg($filePath) . " 2>&1");
        if ($output && strpos($output, 'No syntax errors') !== false) {
            return true;
        }

        // Method 2: Try to include in isolated environment
        try {
            $tempFile = tempnam(sys_get_temp_dir(), 'syntax_check_');
            $checkCode = "<?php\nerror_reporting(E_ALL);\ntry {\n    \$result = include " . var_export($filePath, true) . ";\n    echo 'VALID';\n} catch (Throwable \$e) {\n    echo 'INVALID: ' . \$e->getMessage();\n}\n";
            file_put_contents($tempFile, $checkCode);

            $output = shell_exec("php " . escapeshellarg($tempFile) . " 2>&1");
            unlink($tempFile);

            return strpos($output, 'VALID') === 0;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function writeTranslationSafely($locale, $key, $translation, $direction)
    {
        $filePath = $this->getLanguageFilePath($locale);
        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                // Create backup if file exists
                if (file_exists($filePath)) {
                    $backupPath = $filePath . '.backup.' . time();
                    copy($filePath, $backupPath);
                }

                // Load or create array structure
                $translations = $this->loadTranslationsArray($filePath, $direction);

                // Add/update the translation
                $translations['actions'][$key] = ['label' => $translation];

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
                    throw new Exception("Syntax validation failed after write");
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

    protected function loadTranslationsArray($filePath, $direction)
    {
        if (file_exists($filePath)) {
            try {
                // First validate syntax
                if ($this->validatePhpSyntax($filePath)) {
                    $data = include $filePath;
                    if (is_array($data)) {
                        // Ensure structure
                        if (!isset($data['direction'])) {
                            $data['direction'] = $direction;
                        }
                        if (!isset($data['actions'])) {
                            $data['actions'] = [];
                        }
                        return $data;
                    }
                } else {
                    // Try to repair and reload
                    $this->warn("Syntax error in {$filePath}, attempting repair...");
                    if ($this->repairSyntaxErrors($filePath, basename(dirname($filePath)))) {
                        $data = include $filePath;
                        if (is_array($data)) {
                            return $data;
                        }
                    }
                }
            } catch (Exception $e) {
                $this->logError("Failed to load {$filePath}", $e);
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
            $content .= "        ],\n";
        }

        $content .= "    ],\n";
        $content .= "];\n";

        file_put_contents($filePath, $content);
    }

    protected function safePhpEscape($value)
    {
        if (!is_string($value)) {
            return var_export($value, true);
        }

        // Try different escaping strategies

        // Strategy 1: Simple single quotes with escaping
        $escaped = "'" . str_replace(['\\', "'"], ['\\\\', "\\'"], $value) . "'";
        if ($this->testEscaping($escaped)) {
            return $escaped;
        }

        // Strategy 2: Double quotes with escaping
        $escaped = '"' . str_replace(['\\', '"', '$'], ['\\\\', '\\"', '\\$'], $value) . '"';
        if ($this->testEscaping($escaped)) {
            return $escaped;
        }

        // Strategy 3: Heredoc syntax
        $marker = 'EOD' . mt_rand(1000, 9999);
        $escaped = "<<<'{$marker}'\n{$value}\n{$marker}";
        if ($this->testEscaping($escaped)) {
            return $escaped;
        }

        // Strategy 4: Base64 fallback
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

        // Remove duplicated content patterns
        $translation = preg_replace('/(.+)\1+/', '$1', $translation);

        // Clean up spacing
        $translation = preg_replace('/\s+/', ' ', $translation);
        $translation = trim($translation);

        // Remove obvious artifacts
        $translation = preg_replace('/\b(apparence|interface)\b.*?\b(apparence|interface)\b/', 'apparence', $translation);

        return $translation;
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

    // ... [Rest of the methods like handleChangeKey, translateTerm, etc. remain the same but with error handling improvements]

    protected function handleAddTranslation()
    {
        try {
            $text = $this->argument('text');
            $key = $this->argument('key');

            // Interactive input if missing
            while (empty($text)) {
                $this->info('Enter English text to translate:');
                $this->line('(Use Ctrl+A to go to start, Ctrl+E to go to end, or arrow keys)');

                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }

                $text = readline('> ');
                if ($text === false) {
                    $this->info('Input cancelled.');
                    return 1;
                }

                $text = trim($text);
                if (empty($text)) {
                    $this->error('Text cannot be empty!');
                }
            }

            // Clean the input text
            $text = $this->cleanTranslation($text);

            // Select translation engine
            $this->selectTranslationEngine();

            // Select target directory
            $this->selectTargetDirectory();

            // Handle key generation or validation
            if (empty($key)) {
                $key = $this->generateUniqueKey($text);
                $this->info("Generated key: {$key}. Keep it? (y/n) or type your custom key [y]");

                $keepKey = readline('> ') ?: 'y';
                if ($keepKey === false) {
                    $this->info('Input cancelled.');
                    return 1;
                }

                $keepKey = trim($keepKey);
                if (strtolower($keepKey) === 'n' || strtolower($keepKey) === 'no') {
                    while (empty($key)) {
                        $this->info('Enter your custom key:');
                        $key = readline('> ');
                        if ($key === false) {
                            $this->info('Input cancelled.');
                            return 1;
                        }
                        $key = trim($key);
                        if (empty($key)) {
                            $this->error('Key cannot be empty!');
                        }
                    }
                } elseif (!in_array(strtolower($keepKey), ['y', 'yes']) && !empty($keepKey)) {
                    $key = $keepKey;
                    $this->info("Using custom key: {$key}");
                }
            }

            // Check if key exists and handle
            if ($this->keyExists($key)) {
                $choice = $this->choice("Key '{$key}' exists. What to do?", [
                    'overwrite' => 'Overwrite existing',
                    'unique' => 'Make unique key',
                    'cancel' => 'Cancel'
                ], 'unique');

                if ($choice === 'cancel') {
                    $this->info('Cancelled.');
                    return 0;
                }

                if ($choice === 'unique') {
                    $key = $this->makeUniqueKey($key);
                    $this->info("Using unique key: {$key}");
                }
            }

            // Final confirmation
            $this->newLine();
            $this->info("📋 Summary:");
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

    protected function selectTranslationEngine()
    {
        $this->info('Testing translation engines...');

        foreach ($this->preferredEngines as $engine) {
            if ($this->isTranslatorAvailable($engine)) {
                $this->selectedEngine = $engine;
                $this->info("Using {$engine} translation engine");
                return;
            }

            $this->line("{$engine} not available");
        }

        $this->selectedEngine = 'google';
        $this->warn("Using default engine (google) - quality may vary");
    }

    protected function isTranslatorAvailable($engine = 'google')
    {
        $testTerm = 'Hello';
        $expected = 'Bonjour'; // Expected French translation
        $cmd = "trans -e {$engine} -brief en:fr " . escapeshellarg($testTerm) . " 2>/dev/null";
        $output = trim(shell_exec($cmd));

        return stripos($output, $expected) !== false;
    }

    protected function selectTargetDirectory()
    {
        // Keep default for now
        $this->info("Target directory: {$this->baseDir}");
    }

    protected function translateTerm($translationCode, $text)
    {
        // Get configured engines from config
        $configuredEngines = config('webkernel.translation.engine_priority', ['google', 'bing', 'yandex']);
        $enableAI = config('webkernel.translation.quality.prefer_ai_engines', false);

        // Future: Check AI engines first if enabled
        if ($enableAI) {
            $aiTranslation = $this->tryAiTranslation($translationCode, $text);
            if ($aiTranslation !== null) {
                return $aiTranslation;
            }
        }

        // Use traditional engines
        return $this->translateWithTraditionalEngines($configuredEngines, $translationCode, $text);
    }

    protected function translateWithTraditionalEngines($engines, $translationCode, $text)
    {
        $translations = [];

        foreach ($engines as $engine) {
            // Skip AI engines in traditional method
            if (in_array($engine, ['openai', 'claude', 'gemini', 'local_llama', 'ollama'])) {
                continue;
            }

            $escapedText = escapeshellarg($text);
            $cmd = "trans -e {$engine} -brief en:{$translationCode} {$escapedText} 2>/dev/null";
            $translation = trim(shell_exec($cmd));

            if (!empty($translation) && $translation !== $text && strlen($translation) > 3) {
                $translations[$engine] = $translation;
            }
        }

        // If we have multiple translations, pick the best one
        if (count($translations) > 1) {
            return $this->selectBestTranslation($translations, $text);
        } elseif (count($translations) === 1) {
            return array_values($translations)[0];
        }

        // Fallback to selected engine if others failed
        if (empty($this->selectedEngine)) {
            $this->selectedEngine = 'google';
        }

        $escapedText = escapeshellarg($text);
        $cmd = "trans -e {$this->selectedEngine} -brief en:{$translationCode} {$escapedText} 2>/dev/null";
        $translation = trim(shell_exec($cmd));

        if (empty($translation) || $translation === $text) {
            $this->warn("Translation service unavailable for: {$text}");
            return $text;
        }

        return $translation;
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
                    case 'local_llama':
                        return $this->translateWithLocalLlama($translationCode, $text, $config);
                    case 'ollama':
                        return $this->translateWithOllama($translationCode, $text, $config);
                }
            } catch (Exception $e) {
                $this->logError("AI translation failed for {$engineName}", $e);
                continue;
            }
        }

        return null;
    }

    protected function translateWithOpenAI($translationCode, $text, $config)
    {
        // Future implementation for OpenAI translation
        // Will use OpenAI API with configured model and settings
        throw new Exception("OpenAI translation not yet implemented");
    }

    protected function translateWithClaude($translationCode, $text, $config)
    {
        // Future implementation for Claude translation
        // Will use Anthropic API with configured model and settings
        throw new Exception("Claude translation not yet implemented");
    }

    protected function translateWithGemini($translationCode, $text, $config)
    {
        // Future implementation for Google Gemini translation
        // Will use Google AI API with configured model and settings
        throw new Exception("Gemini translation not yet implemented");
    }

    protected function translateWithLocalLlama($translationCode, $text, $config)
    {
        // Future implementation for local Llama model translation
        // Will connect to local Llama server or use direct model inference
        throw new Exception("Local Llama translation not yet implemented");
    }

    protected function translateWithOllama($translationCode, $text, $config)
    {
        // Future implementation for Ollama translation
        // Will connect to local Ollama server with configured model
        throw new Exception("Ollama translation not yet implemented");
    }

    protected function selectBestTranslation($translations, $originalText)
    {
        // Scoring criteria for translation quality
        $scores = [];

        foreach ($translations as $engine => $translation) {
            $score = 0;

            // 1. Length similarity (prefer reasonable length)
            $lengthRatio = strlen($translation) / strlen($originalText);
            if ($lengthRatio >= 0.8 && $lengthRatio <= 2.0) {
                $score += 20;
            } elseif ($lengthRatio >= 0.5 && $lengthRatio <= 3.0) {
                $score += 10;
            }

            // 2. No repeated words (detect poor translations)
            $words = str_word_count($translation, 1, 'àáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ');
            $uniqueWords = array_unique($words);
            if (count($words) > 0) {
                $uniqueRatio = count($uniqueWords) / count($words);
                $score += $uniqueRatio * 15;
            }

            // 3. Contains meaningful characters (not just punctuation)
            if (preg_match('/[a-zA-Zàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ]/', $translation)) {
                $score += 15;
            }

            // 4. Proper capitalization
            if (preg_match('/^[A-ZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞŸ]/', $translation)) {
                $score += 10;
            }

            // 5. Penalize if translation contains original text (usually poor translation)
            if (stripos($translation, $originalText) !== false) {
                $score -= 30;
            }

            // 6. Penalize obvious errors or artifacts
            $errorPatterns = [
                '/\s{2,}/',           // Multiple spaces
                '/[a-z][A-Z]/',       // Improper capitalization
                '/\.\s*[a-z]/',       // Sentence not starting with capital
                '/[^\s]\.[^\s]/',     // Missing spaces around periods
            ];

            foreach ($errorPatterns as $pattern) {
                if (preg_match($pattern, $translation)) {
                    $score -= 5;
                }
            }

            // 7. Engine preference (Google usually better for common languages)
            if ($engine === 'google') {
                $score += 5;
            } elseif ($engine === 'bing') {
                $score += 3;
            }

            $scores[$engine] = $score;
        }

        // Select the highest scoring translation
        arsort($scores);
        $bestEngine = array_key_first($scores);
        $bestTranslation = $translations[$bestEngine];

        // Log the decision for debugging
        $this->line("  Engines: " . implode(', ', array_keys($translations)));
        $this->line("  Selected: {$bestEngine} (score: {$scores[$bestEngine]})");

        return $bestTranslation;
    }

    protected function generateUniqueKey($text)
    {
        $key = strtolower(trim($text));
        $key = preg_replace('/[^a-z0-9\s]/', '', $key);
        $key = preg_replace('/\s+/', '_', $key);
        $key = trim($key, '_');
        if (strlen($key) > 50) {
            $key = substr($key, 0, 50);
        }
        return $key ?: 'generated_' . time();
    }

    protected function makeUniqueKey($key)
    {
        $counter = 1;
        $originalKey = $key;

        while ($this->keyExists($key)) {
            $key = $originalKey . '_' . $counter;
            $counter++;
        }

        return $key;
    }

    protected function keyExists($key)
    {
        // Check if key exists in any language file
        foreach ($this->languageMap as $locale => $translationCode) {
            $filePath = $this->getLanguageFilePath($locale);
            if (file_exists($filePath)) {
                try {
                    if ($this->validatePhpSyntax($filePath)) {
                        $data = include $filePath;
                        if (is_array($data) && isset($data['actions'][$key])) {
                            return true;
                        }
                    }
                } catch (Exception $e) {
                    // Continue checking other files
                }
            }
        }
        return false;
    }

    protected function handleChangeKey()
    {
        // Implementation for changing keys
        $this->info('Change key functionality - to be implemented');
        return 0;
    }

    protected function handleRetranslate()
    {
        $this->info('RETRANSLATE MODE: Retranslating all existing entries from English...');

        // Select target directory
        $this->selectTargetDirectory();

        // Get English translations
        $englishTranslations = $this->getEnglishTranslations();

        if (empty($englishTranslations)) {
            $this->warn("No English translations found to retranslate from.");
            $this->line("Add translations first using: php artisan webkernel:lang-add 'Your text'");
            return 1;
        }

        $this->info("Found " . count($englishTranslations) . " English translations to retranslate.");

        // Get all languages except English
        $targetLocales = array_filter(array_keys($this->languageMap), function($locale) {
            return $locale !== 'en';
        });

        if (!$this->confirm("This will retranslate " . count($englishTranslations) . " entries to " . count($targetLocales) . " languages. Continue?", true)) {
            $this->info('Retranslation cancelled.');
            return 0;
        }

        // Create backup
        $this->createBackup();

        // Select translation engine
        $this->selectTranslationEngine();

        return $this->retranslateRepairedFiles($targetLocales, $englishTranslations);
    }

    protected function createBackup()
    {
        $this->line("Creating backup...");
        foreach ($this->languageMap as $locale => $translationCode) {
            $filePath = $this->getLanguageFilePath($locale);
            if (file_exists($filePath)) {
                $backupPath = $this->backupDir . '/' . $locale . '_translations.php';
                copy($filePath, $backupPath);
            }
        }
        $this->info("Backup created in: {$this->backupDir}");
    }

    protected function handleRestore()
    {
        // Implementation for restore functionality
        $this->info('Restore functionality - to be implemented');
        return 0;
    }

    protected function displayWrappedText($text, $width = 80)
    {
        $this->line(wordwrap($text, $width));
    }

    protected function getEnglishTranslations()
    {
        $englishFile = $this->baseDir . '/en/translations.php';

        if (!file_exists($englishFile)) {
            return [];
        }

        try {
            if ($this->validatePhpSyntax($englishFile)) {
                $data = include $englishFile;
                if (is_array($data) && isset($data['actions'])) {
                    return $data['actions'];
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
            $this->line("Retranslating to {$locale}...");

            foreach ($englishTranslations as $key => $englishData) {
                $totalTranslations++;
                $englishText = $englishData['label'] ?? '';

                if (empty($englishText)) {
                    continue;
                }

                try {
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
}
