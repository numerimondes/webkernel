<?php
namespace Webkernel\Commands\PackageManager;
use Illuminate\Console\Command;
class TranslateTerm extends Command
{
    protected $signature = 'webkernel:lang-add {text? : English text to translate} {key? : Translation key (optional)}
                            {--change-key : Change existing key mode}
                            {--old-key= : Old key to change (use with --change-key)}
                            {--new-key= : New key to change to (use with --change-key)}';
    protected $description = 'Add translation or change key - all translation management in one command';
    protected $baseDir = 'packages/webkernel/src/lang';
    protected $backupDir = 'backups/webkernel/lang';
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
    public function handle()
    {
        // Set up signal handler for Ctrl+C
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, function () {
                $this->info('Input cancelled.');
                exit(1);
            });
        }
        // Check if change-key mode
        if ($this->option('change-key')) {
            return $this->handleChangeKey();
        }
        // Normal add translation mode
        return $this->handleAddTranslation();
    }
    protected function handleAddTranslation()
    {
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
        // Dynamically detect available packages
        $packages = glob('packages/*/src/lang');
        $locationOptions = [];
        $locationMappings = [];
        // Always add webkernel and app
        $locationOptions[] = 'packages/webkernel/src/lang';
        $locationMappings[1] = ['dir' => 'packages/webkernel/src/lang', 'backup' => 'backups/webkernel/lang'];
        $locationOptions[] = 'app/lang';
        $locationMappings[2] = ['dir' => 'app/lang', 'backup' => 'backups/app/lang'];
        // Add other packages dynamically
        $nextIndex = 3;
        foreach ($packages as $package) {
            $packageName = basename(dirname(dirname($package)));
            if ($packageName !== 'webkernel') {
                $locationOptions[] = "packages/{$packageName}/src/lang";
                $locationMappings[$nextIndex] = ['dir' => $package, 'backup' => "backups/{$packageName}/lang"];
                $nextIndex++;
            }
        }
        $this->info('Available locations:');
        foreach ($locationOptions as $index => $option) {
            $this->line("  [" . ($index + 1) . "] {$option}");
        }
        if (function_exists('pcntl_signal_dispatch')) {
            pcntl_signal_dispatch();
        }
        $choice = readline('Choose location number [1]: ') ?: '1';
        if ($choice === false) {
            $this->info('Input cancelled.');
            return 1;
        }
        $choiceIndex = (int)trim($choice);
        if (!isset($locationMappings[$choiceIndex])) {
            $this->error('Invalid choice! Using webkernel as default.');
            $choiceIndex = 1;
        }
        $this->baseDir = $locationMappings[$choiceIndex]['dir'];
        $this->backupDir = $locationMappings[$choiceIndex]['backup'];
        $this->info("Target: {$this->baseDir}");
        // Handle key generation
        if (empty($key)) {
            $this->newLine();
            $key = $this->generateUniqueKey($text);
            $this->info("Here is the key that will be used: {$key}. Keep it? (y/n) or type your custom key [y]");
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }
            $keepKey = readline('> ') ?: 'y';
            if ($keepKey === false) {
                $this->info('Input cancelled.');
                return 1;
            }

            $keepKey = trim($keepKey);

            // Check if user typed 'y' or 'yes' to keep the generated key
            if (strtolower($keepKey) === 'y' || strtolower($keepKey) === 'yes') {
                // Keep the generated key
            }
            // Check if user typed 'n' or 'no' to reject the key
            else if (strtolower($keepKey) === 'n' || strtolower($keepKey) === 'no') {
                $key = null;
                while (empty($key)) {
                    $this->info('Enter your custom key:');
                    if (function_exists('pcntl_signal_dispatch')) {
                        pcntl_signal_dispatch();
                    }
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
            }
            // If user typed something else, treat it as a custom key
            else if (!empty($keepKey)) {
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
        // Final confirmation before translating
        $this->newLine();
        $this->info("Summary:");
        $this->displayWrappedText("Text: " . $text);
        $this->line("Key: {$key}");
        $this->line("Location: {$this->baseDir}");
        $this->line("Languages: " . count($this->languageMap) . " languages");
        if (!$this->confirm('Proceed with translation?', true)) {
            $this->info('Translation cancelled.');
            return 0;
        }
        $this->info("Translating '{$text}' with key '{$key}' to all languages...");
        foreach ($this->languageMap as $locale => $translationCode) {
            $this->line("Processing {$locale}...");
            $translation = $this->translateTerm($translationCode, $text);
            $direction = in_array($locale, $this->rtlLanguages) ? 'rtl' : 'ltr';
            $this->writeTranslation($locale, $key, $translation, $direction);
            $this->info("✓ {$locale}: {$translation}");
            usleep(500000);
        }
        $this->info("Completed! Key: {$key}");
        $this->line("Saved to: {$this->baseDir} and {$this->backupDir}");
        return 0;
    }
    protected function handleChangeKey()
    {
        $oldKey = $this->option('old-key');
        $newKey = $this->option('new-key');
        // Interactive input if missing
        while (empty($oldKey)) {
            $this->info('Enter current key to change:');
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }
            $oldKey = readline('> ');
            if ($oldKey === false) {
                $this->info('Input cancelled.');
                return 1;
            }
            $oldKey = trim($oldKey);
            if (empty($oldKey)) {
                $this->error('Key cannot be empty!');
            }
        }
        while (empty($newKey)) {
            $this->info('Enter new key:');
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }
            $newKey = readline('> ');
            if ($newKey === false) {
                $this->info('Input cancelled.');
                return 1;
            }
            $newKey = trim($newKey);
            if (empty($newKey)) {
                $this->error('Key cannot be empty!');
            }
        }
        // Check if old key exists
        if (!$this->keyExists($oldKey)) {
            $this->error("Key '{$oldKey}' not found!");
            $this->showAvailableKeys();
            return $this->handleChangeKey(); // Ask again
        }
        // Check if new key exists
        if ($this->keyExists($newKey)) {
            $choice = $this->choice("New key '{$newKey}' exists. What to do?", [
                'overwrite' => 'Overwrite existing',
                'unique' => 'Make unique key',
                'cancel' => 'Cancel'
            ], 'unique');
            if ($choice === 'cancel') {
                $this->info('Cancelled.');
                return 0;
            }
            if ($choice === 'unique') {
                $newKey = $this->makeUniqueKey($newKey);
                $this->info("Using unique key: {$newKey}");
            }
        }
        $this->info("Changing '{$oldKey}' to '{$newKey}' in all languages...");
        $changed = 0;
        foreach ($this->languageMap as $locale => $translationCode) {
            if ($this->changeKeyInFile($locale, $oldKey, $newKey)) {
                $this->line("✓ {$locale}");
                $changed++;
            }
        }
        $this->info("Changed key in {$changed} files");
        return 0;
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
        $key = $key ?: 'generated_' . time();
        return $this->makeUniqueKey($key);
    }
    protected function makeUniqueKey($baseKey)
    {
        $key = $baseKey;
        $counter = 1;
        while ($this->keyExists($key)) {
            $key = $baseKey . '_' . $counter;
            $counter++;
        }
        return $key;
    }
    protected function keyExists($key)
    {
        // Check in ALL packages/*/src/lang directories
        $packageDirs = glob('packages/*/src/lang');
        foreach ($packageDirs as $packageDir) {
            $filePath = $packageDir . '/en/translations.php';
            if (file_exists($filePath)) {
                $translations = include $filePath;
                if (isset($translations['actions'][$key])) {
                    return true;
                }
            }
        }
        // Check in app/lang
        $appLangPath = 'app/lang/en/translations.php';
        if (file_exists($appLangPath)) {
            $translations = include $appLangPath;
            if (isset($translations['actions'][$key])) {
                return true;
            }
        }
        // Check in database
        if (class_exists('Webkernel\Models\LanguageTranslation')) {
            try {
                $translations = \Webkernel\Models\LanguageTranslation::where('lang_ref', $key)->first();
                if ($translations) {
                    return true;
                }
            } catch (\Exception $e) {
                // Continue if database fails
            }
        }
        return false;
    }
    protected function showAvailableKeys()
    {
        $keys = $this->getAllAvailableKeys();
        if (!empty($keys)) {
            $this->info('Available keys:');
            foreach (array_slice($keys, 0, 10) as $key) {
                $this->line("  - {$key}");
            }
            if (count($keys) > 10) {
                $this->line("  ... and more keys");
                $this->line("Available keys: " . count($keys));
            }
        } else {
            $this->warn('No translation keys found');
        }
    }
    protected function getAllAvailableKeys()
    {
        $allKeys = [];
        // Collect keys from all packages
        $packageDirs = glob('packages/*/src/lang');
        foreach ($packageDirs as $packageDir) {
            $filePath = $packageDir . '/en/translations.php';
            if (file_exists($filePath)) {
                $translations = include($filePath);
                if (isset($translations['actions'])) {
                    $allKeys = array_merge($allKeys, array_keys($translations['actions']));
                }
            }
        }
        // Collect keys from app/lang
        $appLangPath = 'app/lang/en/translations.php';
        if (file_exists($appLangPath)) {
            $translations = include($appLangPath);
            if (isset($translations['actions'])) {
                $allKeys = array_merge($allKeys, array_keys($translations['actions']));
            }
        }
        // Collect keys from database
        if (class_exists('Webkernel\Models\LanguageTranslation')) {
            try {
                $dbKeys = \Webkernel\Models\LanguageTranslation::distinct()->pluck('lang_ref')->toArray();
                $allKeys = array_merge($allKeys, $dbKeys);
            } catch (\Exception $e) {
                // Continue if database fails
            }
        }
        return array_unique(array_filter($allKeys));
    }
    protected function changeKeyInFile($locale, $oldKey, $newKey)
    {
        $directories = [$this->baseDir . '/' . $locale, $this->backupDir . '/' . $locale];
        foreach ($directories as $dir) {
            $filePath = $dir . '/translations.php';
            if (!file_exists($filePath)) continue;
            copy($filePath, $filePath . '.bak');
            $content = file_get_contents($filePath);
            $escapedOld = str_replace("'", "\\'", $oldKey);
            $escapedNew = str_replace("'", "\\'", $newKey);
            $pattern = "/'$escapedOld'(\s*=>\s*\[)/";
            $replacement = "'$escapedNew'$1";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replacement, $content);
                file_put_contents($filePath, $content);
                return true;
            }
        }
        return false;
    }
    protected function translateTerm($transCode, $term)
    {
        if (empty($transCode)) return $term;
        // For Arabic and RTL languages, try different translation approaches
        if (in_array($transCode, ['ar', 'fa', 'he', 'ku'])) {
            // Try with different word order hints for RTL languages
            $contextualTerm = "settings for displaying components: " . $term;
            $result = shell_exec("trans -brief en:{$transCode} " . escapeshellarg($contextualTerm) . " 2>/dev/null");
            if (!empty($result)) {
                $lines = explode("\n", trim($result));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line) && strpos($line, 'Trans') !== 0) {
                        // Clean up the contextual part - remove duplicated text
                        $cleanedLine = $line;
                        // Try to extract just the main translation by removing duplicates
                        $parts = explode(':', $cleanedLine);
                        if (count($parts) >= 2) {
                            // Take the first part before the colon which should be cleaner
                            $cleanedLine = trim($parts[0]);
                        }
                        // If it's still duplicated, try to find the shortest meaningful part
                        $words = explode(' ', $cleanedLine);
                        $wordCount = count($words);
                        if ($wordCount > 3) {
                            // For RTL languages, try taking first half of words
                            $halfPoint = intval($wordCount / 2);
                            $firstHalf = implode(' ', array_slice($words, 0, $halfPoint));
                            $secondHalf = implode(' ', array_slice($words, $halfPoint));
                            // If first half seems complete, use it
                            if (strlen($firstHalf) > 5) {
                                $cleanedLine = $firstHalf;
                            }
                        }
                        return $cleanedLine;
                    }
                }
            }
        }
        // Standard translation
        $result = shell_exec("trans -brief en:{$transCode} " . escapeshellarg($term) . " 2>/dev/null");
        if (!empty($result)) {
            $lines = explode("\n", trim($result));
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && strpos($line, 'Trans') !== 0) {
                    return $line;
                }
            }
        }
        return $term;
    }
    protected function writeTranslation($locale, $term, $translation, $direction)
    {
        $directories = [$this->baseDir . '/' . $locale, $this->backupDir . '/' . $locale];
        foreach ($directories as $dir) {
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $filePath = $dir . '/translations.php';
            $escapedTerm = str_replace("'", "\\'", $term);
            $escapedTranslation = str_replace("'", "\\'", $translation);
            if (!file_exists($filePath)) {
                file_put_contents($filePath, "<?php\n\nreturn [\n    'direction' => '{$direction}',\n    'actions' => [\n        '{$escapedTerm}' => [\n            'label' => '{$escapedTranslation}',\n        ],\n    ],\n];\n");
            } else {
                copy($filePath, $filePath . '.bak');
                $content = file_get_contents($filePath);
                $content = preg_replace("/'direction'\s*=>\s*'[^']*'/", "'direction' => '{$direction}'", $content);
                if (preg_match("/'$escapedTerm'\s*=>\s*\[[^\]]*'label'\s*=>\s*'[^']*'/", $content)) {
                    $content = preg_replace("/('$escapedTerm'\s*=>\s*\[[^\]]*'label'\s*=>\s*')[^']*/", "$1$escapedTranslation", $content);
                } else {
                    $newEntry = "        '$escapedTerm' => [\n            'label' => '$escapedTranslation',\n        ],";
                    $content = preg_replace("/(\s*'actions'\s*=>\s*\[)/", "$1\n$newEntry", $content);
                }
                file_put_contents($filePath, $content);
            }
        }
    }
    protected function displayWrappedText($text, $prefix = '')
    {
        $terminalWidth = $this->getTerminalWidth();
        $effectiveWidth = $terminalWidth - strlen($prefix) - 2; // Account for prefix and margin
        $lines = explode("\n", $text);
        foreach ($lines as $line) {
            if (strlen($line) <= $effectiveWidth) {
                $this->line($prefix . $line);
            } else {
                $wrapped = wordwrap($line, $effectiveWidth, "\n", true);
                $wrappedLines = explode("\n", $wrapped);
                foreach ($wrappedLines as $index => $wrappedLine) {
                    if ($index === 0) {
                        $this->line($prefix . $wrappedLine);
                    } else {
                        $this->line(str_repeat(' ', strlen($prefix)) . $wrappedLine);
                    }
                }
            }
        }
    }
    protected function getTerminalWidth()
    {
        $width = 80; // Default width
        if (function_exists('shell_exec')) {
            $output = shell_exec('tput cols 2>/dev/null');
            if ($output && is_numeric(trim($output))) {
                $width = (int)trim($output);
            }
        }
        return max($width, 40); // Minimum width of 40
    }
}
