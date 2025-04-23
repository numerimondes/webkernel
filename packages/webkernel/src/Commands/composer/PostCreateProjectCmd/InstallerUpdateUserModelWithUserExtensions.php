<?php
namespace Webkernel\Commands\composer\PostCreateProjectCmd;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallerUpdateUserModelWithUserExtensions extends Command
{
    protected $signature = 'webkernel:install-update-user-model';
    protected $description = 'Webkernel Update User Model with Adding UserExtensions (Trait)';
    protected $hidden = true;
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $filePath = base_path('app/Models/User.php');

        // Define what needs to be inserted
        $headCode = 'use Webkernel\Models\Traits\UserExtensions;';
        $insideClassCode = 'use UserExtensions; /** Do not remove this line to use Webkernel Capabilities */';

        $this->injectCodeIntoLaravelModelFile($filePath, $headCode, $insideClassCode);
    }

    /**
     * Inject code into a PHP file at specific locations
     *
     * @param string $filePath Path to the PHP file
     * @param string $headCode Code to add at the imports/namespace section
     * @param string $insideClassCode Code to add inside the class definition
     * @return void
     */
    protected function injectCodeIntoLaravelModelFile(string $filePath, string $headCode, string $insideClassCode): void
    {
        // Check if file exists
        if (!File::exists($filePath)) {
            $this->error("❌ File not found: {$filePath}");
            return;
        }

        $contents = File::get($filePath);
        $originalContents = $contents;
        $modified = false;

        // Process the head code (import statements)
        if (!empty($headCode) && strpos($contents, $headCode) === false) {
            $contents = $this->addCodeToImportSection($contents, $headCode);
            $modified = true;
            $this->info("[✓] Added to imports: {$headCode}");
        } else {
            $this->info("[✓] Import already exists: {$headCode}");
        }

        // Process the inside class code
        if (!empty($insideClassCode) && strpos($contents, $insideClassCode) === false) {
            $contents = $this->addCodeInsideClass($contents, $insideClassCode);
            if ($contents !== $originalContents) {
                $modified = true;
                $this->info("[✓] Added inside class: {$insideClassCode}");
            } else {
                $this->error("❌ Failed to add code inside class. Class structure not recognized.");
            }
        } else {
            $this->info("[✓] Inside class code already exists: {$insideClassCode}");
        }

        // Save changes if needed
        if ($modified) {
            try {
                File::put($filePath, $contents);
                $this->info("[✓] Successfully updated {$filePath}");
            } catch (\Exception $e) {
                $this->error("❌ Error writing to file: " . $e->getMessage());
            }
        } else {
            $this->info("[✓] No modifications needed for {$filePath}");
        }
    }

    /**
     * Add code to the import section of a PHP file
     *
     * @param string $contents File contents
     * @param string $code Code to add
     * @return string Modified contents
     */
    protected function addCodeToImportSection(string $contents, string $code): string
    {
        // Find the last import statement
        preg_match_all('/^use\s+.+;$/m', $contents, $useMatches);

        if (!empty($useMatches[0])) {
            // Add after the last use statement
            $lastUse = end($useMatches[0]);
            return str_replace($lastUse, $lastUse . "\n" . $code, $contents);
        }

        // If no use statements found, add after namespace
        preg_match('/^namespace\s+.+;$/m', $contents, $namespaceMatch);
        if (!empty($namespaceMatch[0])) {
            return str_replace($namespaceMatch[0], $namespaceMatch[0] . "\n\n" . $code, $contents);
        }

        // If no namespace found, add at the beginning after <?php
        return preg_replace('/^(<\?php)/i', "$1\n\n" . $code, $contents);
    }

    /**
     * Add code inside the class definition
     *
     * @param string $contents File contents
     * @param string $code Code to add
     * @return string Modified contents
     */
    protected function addCodeInsideClass(string $contents, string $code): string
    {
        // More generic approach to find class declaration and opening brace
        preg_match('/^class\s+\w+.*?\s*{/ms', $contents, $matches);

        if (!empty($matches[0])) {
            // Insert code after the opening brace
            return str_replace($matches[0], $matches[0] . "\n    " . $code, $contents);
        }

        return $contents; // Return unchanged if class structure not found
    }
}
