<?php

namespace Webkernel\Commands\PackageManager;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


/**
 *
 *
 *
 * WARNING
 *
 * THIS DOESN'T WORK YET WELL
 *
 *
 *
 */

class WebkernelShellCoreTranslator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webkernel:trans {term : The term to translate} {--backupdir= : Optional backup directory}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate a term to all supported languages using translate-shell';

    /**
     * Language mapping for translation
     *
     * @var array
     */
    protected $langMap = [
        'ar' => 'ar',          // Arabic
        'az' => 'az',          // Azerbaijani
        'bg' => 'bg',          // Bulgarian
        'bn' => 'bn',          // Bengali
        'bs' => 'bs',          // Bosnian
        'ca' => 'ca',          // Catalan
        'ckb' => 'ku',         // Sorani Kurdish
        'cs' => 'cs',          // Czech
        'da' => 'da',          // Danish
        'de' => 'de',          // German
        'el' => 'el',          // Greek
        'en' => 'en',          // English
        'es' => 'es',          // Spanish
        'fa' => 'fa',          // Persian
        'fi' => 'fi',          // Finnish
        'fr' => 'fr',          // French
        'he' => 'he',          // Hebrew
        'hi' => 'hi',          // Hindi
        'hr' => 'hr',          // Croatian
        'hu' => 'hu',          // Hungarian
        'hy' => 'hy',          // Armenian
        'id' => 'id',          // Indonesian
        'it' => 'it',          // Italian
        'ja' => 'ja',          // Japanese
        'ka' => 'ka',          // Georgian
        'km' => 'km',          // Khmer
        'ko' => 'ko',          // Korean
        'ku' => 'ku',          // Kurdish
        'lt' => 'lt',          // Lithuanian
        'lv' => 'lv',          // Latvian
        'mn' => 'mn',          // Mongolian
        'ms' => 'ms',          // Malay
        'my' => 'my',          // Burmese
        'nl' => 'nl',          // Dutch
        'no' => 'no',          // Norwegian
        'np' => 'ne',          // Nepali
        'pl' => 'pl',          // Polish
        'pt_BR' => 'pt-BR',    // Brazilian Portuguese
        'pt_PT' => 'pt-PT',    // European Portuguese
        'ro' => 'ro',          // Romanian
        'ru' => 'ru',          // Russian
        'sk' => 'sk',          // Slovak
        'sl' => 'sl',          // Slovenian
        'sq' => 'sq',          // Albanian
        'sv' => 'sv',          // Swedish
        'sw' => 'sw',          // Swahili
        'th' => 'th',          // Thai
        'tr' => 'tr',          // Turkish
        'uk' => 'uk',          // Ukrainian
        'uz' => 'uz',          // Uzbek
        'vi' => 'vi',          // Vietnamese
        'zh_CN' => 'zh-CN',    // Simplified Chinese
        'zh_TW' => 'zh-TW',    // Traditional Chinese
    ];

    /**
     * RTL languages
     *
     * @var array
     */
    protected $rtlLangs = ['ar', 'fa', 'he', 'ku', 'ckb'];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Check if translate-shell is installed
        if (!$this->checkTranslateShell()) {
            $this->error('translate-shell is not installed. Please install it first.');
            $this->line('On Debian/Ubuntu: sudo apt-get install translate-shell');
            $this->line('On macOS: brew install translate-shell');
            return 1;
        }

        $term = $this->argument('term');
        $backupDir = $this->option('backupdir') ?: base_path('backups/webkernel/lang');
        $this->info("Translating '$term' to all supported languages...");

        // Define base directories
        $baseDir = base_path('packages/webkernel/src/lang');

        // Create backup directory if it doesn't exist
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $progressBar = $this->output->createProgressBar(count($this->langMap));
        $progressBar->start();

        foreach ($this->langMap as $fileLang => $transCode) {
            $this->translateAndSave($term, $fileLang, $transCode, $baseDir, $backupDir);
            $progressBar->advance();
            usleep(500000); // Sleep for half a second to avoid overloading the translation service
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info("Completed. Translations saved to:");
        $this->line("  $baseDir");
        $this->line("  $backupDir");

        return 0;
    }

    /**
     * Check if translate-shell is installed
     *
     * @return bool
     */
    protected function checkTranslateShell()
    {
        try {
            $process = new Process(['which', 'trans']);
            $process->run();
            return $process->isSuccessful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get direction (LTR or RTL) for a language
     *
     * @param string $lang
     * @return string
     */
    protected function getDirection($lang)
    {
        return in_array($lang, $this->rtlLangs) ? 'rtl' : 'ltr';
    }

    /**
     * Translate a term to a specific language
     *
     * @param string $term
     * @param string $transCode
     * @return string
     */
    protected function translateTerm($term, $transCode)
    {
        try {
            // Additional parameters for translator that help with RTL languages:
            // -no-ansi disables ANSI color codes that can interfere with RTL text
            // -no-auto-detect prevents auto language detection which can be unreliable
            $process = new Process(['trans', '-brief', '-no-ansi', '-no-auto-detect', "en:$transCode", $term]);
            $process->setTimeout(30); // Increase timeout to 30 seconds
            $process->run();

            if (!$process->isSuccessful()) {
                $this->warn("Translation to $transCode failed: " . $process->getErrorOutput());
                return $term;
            }

            // Get and clean the output
            $result = trim($process->getOutput());

            // If result is empty, return the original term
            if (empty($result)) {
                return $term;
            }

            // Clean the result by removing any debug or info lines
            $lines = preg_split('/\r\n|\r|\n/', $result);
            $cleanedLines = [];

            foreach ($lines as $line) {
                $line = trim($line);
                // Skip empty lines or lines that look like debug/info output
                if (empty($line) || strpos($line, 'Trans') === 0 || strpos($line, '[') === 0) {
                    continue;
                }
                $cleanedLines[] = $line;
            }

            return !empty($cleanedLines) ? implode(' ', $cleanedLines) : $term;
        } catch (\Exception $e) {
            $this->warn("Exception during translation: " . $e->getMessage());
            return $term;
        }
    }

    /**
     * Save translation to file
     *
     * @param string $term
     * @param string $lang
     * @param string $translation
     * @param string $baseDir
     * @param string $backupDir
     * @return void
     */
    protected function translateAndSave($term, $lang, $transCode, $baseDir, $backupDir)
    {
        $translation = $this->translateTerm($term, $transCode);
        $direction = $this->getDirection($lang);

        // Process both directories
        foreach ([$baseDir, $backupDir] as $dir) {
            $langDir = "$dir/$lang";
            $file = "$langDir/translations.php";

            // Create directory if it doesn't exist
            if (!File::isDirectory($langDir)) {
                File::makeDirectory($langDir, 0755, true);
            }

            // Create new file if it doesn't exist
            if (!File::exists($file)) {
                $content = $this->generateNewTranslationFile($direction, $term, $translation);
                File::put($file, $content);
            } else {
                // Update existing file
                $this->updateTranslationFile($file, $direction, $term, $translation);
            }
        }
    }

    /**
     * Generate content for a new translation file
     *
     * @param string $direction
     * @param string $term
     * @param string $translation
     * @return string
     */
    protected function generateNewTranslationFile($direction, $term, $translation)
    {
        // Escape single quotes in term and translation
        $escapedTerm = str_replace("'", "\'", $term);
        $escapedTranslation = str_replace("'", "\'", $translation);

        // PHP template with proper indentation
        return "<?php\n\nreturn [\n    'direction' => '$direction',\n    'actions' => [\n        '$escapedTerm' => [\n            'label' => '$escapedTranslation',\n        ],\n    ],\n];\n";
    }

    /**
     * Update an existing translation file
     *
     * @param string $file
     * @param string $direction
     * @param string $term
     * @param string $translation
     * @return void
     */
    protected function updateTranslationFile($file, $direction, $term, $translation)
    {
        // Backup the file
        File::copy($file, "$file.bak");

        // Load the PHP file content
        $content = File::get($file);

        // Parse the PHP file into an array
        $data = $this->parsePhpArray($file);

        if (!$data) {
            // If parsing fails, try manual update
            $this->manualUpdateTranslationFile($file, $direction, $term, $translation);
            return;
        }

        // Update direction
        $data['direction'] = $direction;

        // Initialize actions if needed
        if (!isset($data['actions'])) {
            $data['actions'] = [];
        }

        // Update or add translation
        $data['actions'][$term] = ['label' => $translation];

        // Convert back to PHP code and save
        $newContent = $this->arrayToPhpCode($data);
        File::put($file, $newContent);
    }

    /**
     * Parse a PHP file containing an array
     *
     * @param string $file
     * @return array|false
     */
    protected function parsePhpArray($file)
    {
        try {
            // Create a temporary file with a unique name
            $tempFile = tempnam(sys_get_temp_dir(), 'php_array_');

            // Write code to extract the array from the file
            $extractCode = '<?php
            $original = include ' . var_export($file, true) . ';
            file_put_contents(' . var_export($tempFile, true) . ', serialize($original));
            ';

            // Save the extraction code to another temp file
            $extractFile = tempnam(sys_get_temp_dir(), 'extract_');
            file_put_contents($extractFile, $extractCode);

            // Execute the extraction script
            $process = new Process(['php', $extractFile]);
            $process->run();

            if (!$process->isSuccessful()) {
                unlink($extractFile);
                if (file_exists($tempFile)) {
                    unlink($tempFile);
                }
                return false;
            }

            // Read the serialized data
            $serialized = file_get_contents($tempFile);

            // Clean up temp files
            unlink($extractFile);
            unlink($tempFile);

            if (!$serialized) {
                return false;
            }

            return unserialize($serialized);
        } catch (\Exception $e) {
            $this->warn("Error parsing PHP file: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Convert an array to PHP code
     *
     * @param array $data
     * @return string
     */
    protected function arrayToPhpCode($data)
    {
        $code = "<?php\n\nreturn [\n";

        foreach ($data as $key => $value) {
            if ($key === 'direction') {
                $code .= "    'direction' => '{$value}',\n";
            } elseif ($key === 'actions') {
                $code .= "    'actions' => [\n";

                foreach ($value as $actionKey => $actionValue) {
                    $escapedKey = str_replace("'", "\'", $actionKey);
                    $escapedValue = str_replace("'", "\'", $actionValue['label']);

                    $code .= "        '{$escapedKey}' => [\n";
                    $code .= "            'label' => '{$escapedValue}',\n";
                    $code .= "        ],\n";
                }

                $code .= "    ],\n";
            }
        }

        $code .= "];\n";
        return $code;
    }

    /**
     * Fallback method to update a file manually using regex
     *
     * @param string $file
     * @param string $direction
     * @param string $term
     * @param string $translation
     * @return void
     */
    protected function manualUpdateTranslationFile($file, $direction, $term, $translation)
    {
        // Read the file content
        $content = File::get($file);

        // Escape for regex
        $escapedTerm = preg_quote(str_replace("'", "\'", $term), '/');
        $escapedTranslation = str_replace("'", "\'", $translation);

        // Update direction
        $content = preg_replace("/'direction'\s*=>\s*'[^']*'/", "'direction' => '$direction'", $content);

        // Check if term already exists
        if (preg_match("/'$escapedTerm'\s*=>\s*\[/", $content)) {
            // Update existing term's label
            $pattern = "/'$escapedTerm'\s*=>\s*\[\s*'label'\s*=>\s*'[^']*'/";
            $replacement = "'$term' => [\n            'label' => '$escapedTranslation'";
            $content = preg_replace($pattern, $replacement, $content);
        } else {
            // Add new term after 'actions' => [
            $pattern = "/'actions'\s*=>\s*\[/";
            $replacement = "'actions' => [\n        '$term' => [\n            'label' => '$escapedTranslation',\n        ],";
            $content = preg_replace($pattern, $replacement, $content);
        }

        // Save the updated content
        File::put($file, $content);
    }
}
