<?php

namespace Webkernel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class HelpCommand extends Command
{
    // Command signature
    protected $signature = 'webkernel:create component';
    protected $description = 'Interactive assistant for Webkernel component creation';

    /**
     * Configure the command and add aliases
     *
     * protected $aliases = ['webkernel:cc'];
     *
     *    protected function configure()
     *    {
     *        $this->setAliases ($this->aliases);
     *        parent::configure();
     *    }
     *
     */


    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Display welcome banner
        $this->displayWelcomeBanner();

        // Create required directories
        $this->createRequiredDirectories();

        // Main process flow
        $this->processComponentCreation();

        // Display summary
        $this->displayWelcomeBanner();
        $this->displaySummary();

        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }

    /**
     * Create required directories
     */
    protected function createRequiredDirectories()
    {
        $directories = [
            base_path('packages/webkernel/src/resources/views/components'),
            base_path('packages/webkernel/src/resources/views/filament')
        ];

        foreach  ($directories as $directory) {
            if (!File::exists ($directory)) {
                File::makeDirectory ($directory, 0755, true);
                $this->info("Created directory: {\$directory}");
            }
        }
    }

    /**
     * Display welcome banner
     */
    protected function displayWelcomeBanner()
    {
        $this->info(PHP_EOL);
        $this->info(" __      __      ___.    ____  __.                         .__   ");
        $this->info("/  \    /  \ ____\\_ |__ |    |/ _|___________  ____   ____ |  |  ");
        $this->info("\\   \/\\/   // __ \| __ \|      <_/ __ \_  __ \/    \_/ __ \|  |  ");
        $this->info(" \        /\\  ___/| \_\\ \    |  \  ___/|  | \/   |  \  ___/|  |__");
        $this->info("  \__/\\  /  \___  >___  /____|__ \___  >__|  |___|  /\\___  >____/");
        $this->info("       \/       \/    \/        \/   \/           \/     \/      ");
        $this->info(PHP_EOL);
        $this->info("Interactive Assistant for Component Creation");
        $this->info("By \033]8;;http://www.numerimondes.com\033\\Numerimondes\033]8;;\033\\ for Laravel and FilamentPHP");
    }

    /**
     * Process component creation workflow
     */
    protected function processComponentCreation()
    {
        // Step 1: Choose section
        $section = $this->chooseSection();

        // Step 2: Choose component type
        $componentType = $this->chooseComponentType ($section);

        // Step 3: Choose role/feature if applicable
        $role = $this->chooseRole ($componentType);

        // Step 4: Choose complexity level if UI component
        $complexity = $this->chooseComplexity ($componentType);

        // Step 5: Get file type
        $fileType = $this->chooseFileType();

        // Step 6: Get component name
        $componentName = $this->getComponentName();

        // Step 7: Generate the component
        $filePath = $this->generateComponent ($componentType, $section, $role, $complexity, $fileType, $componentName);

        // Step 8: Save component details to CSV
        $this->saveComponentDetailsToCsv ($componentType, $section, $role, $complexity, $fileType, $componentName, $filePath);
    }

    protected function chooseSection()
    {
        $this->line('');
        $this->info('Step: Choose the section:');
        $this->line(' [0] : WebKernel        : Specific components     > /webkernel         [DEFAULT]');
        $this->line(' [1] : Filament Pages   : Custom Filament pages   > /filament/pages');
        $this->line(' [2] : Filament Forms   : Form & input components > /filament/forms');
                /**$this->line(' [3] Website Builder');  The Web Kernel Modular Website Builder Is Not Yet Initiated -- Do not remove this line*/

        $choice = $this->anticipate('Select a section [0-2]:', range(0, 2),0);

        if (!is_numeric ($choice) || $choice < 0 || $choice > 3) {
            $this->error('Invalid selection. Please select one valid section from 0 to 3.');
            return $this->chooseSection();
        }

        $sections = ['webkernel', 'filament', 'filament-forms','website-builder'];
        return $sections[$choice];
    }

    protected function chooseComponentType($section)
    {
        $this->line('');
        $this->info("Start: Choose the component type for section [$section]:");

        // Define component Types
        $componentTypes = [
            0 => 'Pages*           : Filament Pages which has internal layouts, widgets ...',
            1 => 'Layout           : Header, Footer, Sidebar, Page Wrapper, Content Slots ...',
            2 => 'UI               : Button, Input, Toggle, Custom Range, Dropdowns, Tags ...',
            3 => 'Internal Module  : Feature block like Internal Wiki, Email Notifications ...',
            4 => 'Role-based       : User-related views shown based on user roles ...',
            5 => 'External         : Public component consuming API GoogleMaps, Stripe ...',
            6 => 'Asset            : Diverse Static assets style.css, main.js, logo.svg ..',
        ];

        // Mapping sections to componentTypes
        $sectionOptions = [
            'filament' => [0, 1, 3, 4],
            'filament-forms' => [2, 3],
            'webkernel' => [1, 2, 3, 4, 5, 6],
            'website-builder' => [1, 2, 3, 4, 5, 6],
        ];

        $availableOptions = $sectionOptions[$section] ?? [];
        $choices = [];

        // Available Options
        foreach ($availableOptions as $index) {
            $choices[$index] = $componentTypes[$index];
            $this->line(" [$index] " . $componentTypes[$index]);
        }

        $choice = $this->anticipate(
            'Select a component type [' . implode('/', array_keys($choices)) . ']',
            array_keys($choices)
        );

        if (!is_numeric($choice) || !array_key_exists((int)$choice, $choices)) {
            $this->error('Invalid selection. Please select a valid component type.');
            return $this->chooseComponentType($section);
        }

        $types = ['page', 'layout', 'ui', 'module', 'rolebased', 'external', 'asset', 'other'];
        return $types[(int)$choice];
    }

    /**
     * Choose role directly with simple choices
     */
    protected function chooseRole ($componentType)
    {
        // Skip for non-role based components
        if  ($componentType !== 'rolebased') {
            return 'none';
        }

        $this->line('');
        $this->info('Step : Choose the role:');

        $this->line(' [0] : Administration   : Backend panels, dashboards, settings');
        $this->line(' [1] : User             : User-related views (profiles, preferences)');
        $this->line(' [2] : Shared/Common    : Reusable by all modules (e.g. alerts, layout)');
        $this->line(' [3] : Authentication   : Login, register, password reset, etc.');
        $this->line(' [4] : Product          : Product-specific views (catalog, detail)');
        $this->line(' [5] : General/Neutral  : Uncategorized or generic components');

        $choice = $this->anticipate('Select a role [0-5]:', range(0, 5));

        if (!is_numeric ($choice) || $choice < 0 || $choice > 5) {
            $this->warn('Invalid selection. Using default: none');
            return 'none';
        }

        // Map choice to role
        $roles = ['admin', 'user', 'common', 'auth', 'product', 'none'];
        return $roles[$choice];
    }

    /**
     * Choose complexity level directly with simple choices
     */
    protected function chooseComplexity ($componentType)
    {
        // Only applicable for UI components
        if  ($componentType !== 'ui') {
            return 'molecules';
        }

        $this->line('');
        $this->info('Step: Choose the complexity level:');

        $this->line(' [0] : Atom      : ▼ Button, input, icon, label, checkbox, radio, link');
        $this->line(' [1] : Molecule  : ▼▼ Input group, card, list, dropdown, tooltip, modal');
        $this->line(' [2] : Organism  : ▼▼▼ Navbar, form, sidebar, grid, footer, accordion');
        $this->line(' [3] : Page      : ▼▼▼▼ Dashboard, profile, settings, contact page, login page');

        $choice = $this->anticipate('Select a complexity level [0-3]:', range(0, 3));

        if (!is_numeric ($choice) || $choice < 0 || $choice > 3) {
            $this->warn('Invalid selection. Using default: molecules');
            return 'molecules';
        }

        // Map choice to complexity
        $complexities = ['atoms', 'molecules', 'organisms', 'page'];
        return $complexities[$choice];
    }

    /**
     * Choose file type directly with simple choices
     */
    protected function chooseFileType()
    {
        $this->line('');
        $this->info('Step : Choose the file type:');

        $this->line(' [0] Blade View (.blade.php)');
        $this->line(' [1] Stylesheet (.css)');
        $this->line(' [2] JavaScript (.js)');
        $this->line(' [3] Image      (.png/.jpg)');
        $this->line(' [4] Other      (custom format)');

        $choice = $this->anticipate('Select a file type [0-4]:', range(0, 4));

        if (!is_numeric ($choice) || $choice < 0 || $choice > 4) {
            $this->warn('Invalid selection. Using default: blade');
            return 'blade';
        }

        // Map choice to file type
        $fileTypes = ['blade', 'css', 'js', 'image', 'other'];
        return $fileTypes[$choice];
    }

    /**
     * Get component name
     */
    protected function getComponentName()
    {
        $this->line('');
        $this->info('Almost there: Enter the component name:');
        $name = $this->ask('Component name (lowercase, use dashes for spaces)');

        if (empty ($name)) {
            $this->warn('Name cannot be empty. Please enter a valid name.');
            return $this->getComponentName();
        }

        // Sanitize name
        $name = strtolower ($name);
        $name = preg_replace('/[^a-z0-9\-]/', '', $name);
        $name = trim ($name, '-');

        if (empty ($name)) {
            $this->warn('Name contained invalid characters. Please enter a valid name.');
            return $this->getComponentName();
        }

        return $name;
    }

    /**
     * Generate the component file with direct path construction
     */
    protected function generateComponent ($componentType, $section, $role, $complexity, $fileType, $componentName)
    {
        $this->line('');
        $this->info('Generating component...');

        // Base path for all components
        $basePath = base_path('packages/webkernel/src/resources/views');

        // Determine file extension based on file type
        $extension = $this->getFileExtension ($fileType);

        // Path construction using direct logic
        $componentPath = $this->getComponentPath ($basePath, $componentType, $section, $role, $complexity, $fileType);

        // Create directories if they don't exist
        if (!File::exists ($componentPath)) {
            File::makeDirectory ($componentPath, 0755, true);
        }

        // Final file path
        $filePath = $componentPath . '/' . $componentName . $extension;

        // Generate content based on file type
        $content = $this->generateComponentContent ($componentType, $fileType, $componentName, $role);

        // Write the file
        File::put ($filePath, $content);

        // Output success message
        $this->info('Component generated successfully:');
        $this->line ($filePath);

        return $filePath;
    }

    /**
     * Pure logic path determination without complex data structures
     */
    protected function getComponentPath($basePath, $componentType, $section, $role, $complexity, $fileType)
    {
        // Filament specific paths
        if ($section === 'filament') {
            return $basePath . '/filament/pages';
        }

        if ($section === 'filament-forms') {
            return $basePath . '/filament/form/' . $complexity;
        }

        // Website builder
        if ($section === 'website-builder') {
            return $basePath . '/components/website-builder';
        }

        // WebKernel paths with direct logic
        if ($section === 'webkernel') {
            // Component type specific paths
            if ($componentType === 'page') {
                return $basePath . '/components/webkernel/pages';
            }

            if ($componentType === 'layout') {
                return $basePath . '/components/webkernel/layouts';
            }

            if ($componentType === 'ui') {
                return $basePath . '/components/webkernel/ui/' . $complexity;
            }

            if ($componentType === 'module') {
                return $basePath . '/components/webkernel/modules';
            }

            if ($componentType === 'rolebased') {
                return $basePath . '/components/webkernel/rolebased/' . $role;
            }

            if ($componentType === 'external') {
                return $basePath . '/components/webkernel/external';
            }

            if ($componentType === 'asset') {
                // For assets, subfolders based on file type
                if ($fileType === 'css') {
                    return $basePath . '/components/webkernel/assets/css';
                } else if ($fileType === 'js') {
                    return $basePath . '/components/webkernel/assets/js';
                } else if ($fileType === 'image') {
                    return $basePath . '/components/webkernel/assets/images';
                } else {
                    return $basePath . '/components/webkernel/assets';
                }
            }

            // Other components
            return $basePath . '/components/webkernel/other';
        }

        // Default fallback path
        return $basePath . '/components';
    }

    /**
     * Get file extension based on file type
     */
    protected function getFileExtension($fileType)
    {
        if ($fileType === 'blade') {
            return '.blade.php';
        } else if ($fileType === 'css') {
            return '.css';
        } else if ($fileType === 'js') {
            return '.js';
        } else if ($fileType === 'image') {
            return '.svg';  // Default to SVG
        } else {
            return '.' . $fileType;
        }
    }

    /**
     * Generate component content based on component type and file type
     */
    protected function generateComponentContent($componentType, $fileType, $componentName, $role)
    {
        // Generate the appropriate content based on file type
        if ($fileType === 'blade') {
            return $this->generateBladeContent($componentType, $componentName, $role);
        } else if ($fileType === 'css') {
            return $this->generateCssContent($componentName);
        } else if ($fileType === 'js') {
            return $this->generateJsContent($componentName);
        } else if ($fileType === 'image') {
            return $this->generateSvgContent($componentName);
        } else {
            return "<!-- {$componentName} component -->\n<!-- Generated by Webkernel Component Creator -->\n";
        }
    }

    /**
     * Generate Blade content with appropriate structure based on component type
     */
    protected function generateBladeContent($componentType, $componentName, $role)
    {
        $className = str_replace('-', '_', $componentName);
        $content = "@props([])\n\n<div class=\"webkernel-component webkernel-{$componentType} {$className}\">\n";

        // Component type specific content
        if ($componentType === 'page') {
            $content .= "    <div class=\"page-header\">\n";
            $content .= "        <h1>{{ isset(\$title) ? \$title : '{$componentName}' }}</h1>\n";
            $content .= "    </div>\n\n";
            $content .= "    <div class=\"page-content\">\n";
            $content .= "        {{ \$slot ?? '' }}\n"; // Add a fallback to avoid using an unassigned variable
            $content .= "    </div>\n";
        } else if ($componentType === 'layout') {
            $content .= "    <div class=\"layout-container\">\n";
            $content .= "        <header class=\"layout-header\">\n";
            $content .= "            {{ isset(\$header) ? \$header : '' }}\n";
            $content .= "        </header>\n\n";
            $content .= "        <main class=\"layout-main\">\n";
            $content .= "            {{ \$slot ?? '' }}\n"; // Add a fallback to avoid using an unassigned variable
            $content .= "        </main>\n\n";
            $content .= "        <footer class=\"layout-footer\">\n";
            $content .= "            {{ isset(\$footer) ? \$footer : '' }}\n";
            $content .= "        </footer>\n";
            $content .= "    </div>\n";
        } else if ($componentType === 'ui') {
            $content .= "    <div class=\"ui-element\">\n";
            $content .= "        {{ \$slot ?? '' }}\n"; // Add a fallback to avoid using an unassigned variable
            $content .= "    </div>\n";
        } else if ($componentType === 'rolebased') {
            $content .= "    @auth\n";
            $content .= "        @if(auth()->user()->hasRole('{$role}'))\n";
            $content .= "            {{ \$slot ?? '' }}\n"; // Add a fallback to avoid using an unassigned variable
            $content .= "        @else\n";
            $content .= "            <!-- Content visible only to {$role} role -->\n";
            $content .= "            <div class=\"unauthorized-message\">\n";
            $content .= "                You don't have permission to view this content.\n";
            $content .= "            </div>\n";
            $content .= "        @endif\n";
            $content .= "    @endauth\n";
        } else {
            $content .= "    {{ \$slot ?? '' }}\n"; // Add a fallback to avoid using an unassigned variable
        }

        $content .= "</div>\n";
        return $content;
    }

    /**
     * Generate CSS content
     */
    protected function generateCssContent($componentName)
    {
        $className = str_replace('-', '_', $componentName);

        return "/**\n * {$componentName} Component Styles\n * Generated by Webkernel Component Creator\n */\n\n"
            . ".{$className} {\n"
            . "    /* Base styles */\n"
            . "    display: block;\n"
            . "    margin: 1rem 0;\n"
            . "    padding: 1rem;\n"
            . "}\n\n"
            . ".{$className} .container {\n"
            . "    /* Container styles */\n"
            . "}\n\n"
            . "/* Responsive styles */\n"
            . "@media (max-width: 768px) {\n"
            . "    .{$className} {\n"
            . "        /* Mobile styles */\n"
            . "    }\n"
            . "}\n";
    }

    /**
     * Generate JavaScript content
     */
    protected function generateJsContent($componentName)
    {
        $camelCase = str_replace('-', '', ucwords($componentName, '-'));
        $camelCase = lcfirst($camelCase);

        return "/**\n * {$componentName} Component Script\n * Generated by Webkernel Component Creator\n */\n\n"
            . "document.addEventListener('DOMContentLoaded', function() {\n"
            . "    /**\n"
            . "     * {$camelCase} component initialization\n"
            . "     */\n"
            . "    function init{$camelCase}() {\n"
            . "        const elements = document.querySelectorAll('.{$componentName}');\n\n"
            . "        if (elements.length === 0) return;\n\n"
            . "        elements.forEach(element => {\n"
            . "            // Component initialization code\n"
            . "            console.log('{$componentName} component initialized');\n\n"
            . "            // Event listeners\n"
            . "            element.addEventListener('click', function(e) {\n"
            . "                // Click handler\n"
            . "            });\n"
            . "        });\n"
            . "    }\n\n"
            . "    // Initialize the component\n"
            . "    init{$camelCase}();\n"
            . "});\n";
    }

    /**
     * Generate SVG content
     */
    protected function generateSvgContent($componentName)
    {
        return "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\" width=\"100\" height=\"100\">\n"
            . "    <!-- {$componentName} SVG Component -->\n"
            . "    <rect x=\"10\" y=\"10\" width=\"80\" height=\"80\" rx=\"5\" ry=\"5\" fill=\"#f0f0f0\" stroke=\"#333\" stroke-width=\"2\" />\n"
            . "    <text x=\"50\" y=\"55\" font-family=\"Arial\" font-size=\"12\" text-anchor=\"middle\">{$componentName}</text>\n"
            . "</svg>\n";
    }

/**
 * Save component details to CSV
 */
protected function saveComponentDetailsToCsv($componentType, $section, $role, $complexity, $fileType, $componentName, $filePath)
{
    $savePath = base_path('packages/webkernel/src/resources/components.csv');
    $now = Carbon::now();
    $date = $now->format('Y-m-d');
    $time = $now->format('H:i:s');

    // Get the relative file path instead of absolute path
    $relativeFilePath = str_replace(base_path(), '', $filePath); // Remove base_path from the file path

    // Check if the file exists
    if (!File::exists($savePath)) {
        // Create the file and write the header
        File::put($savePath, "Date,Time,Component Type,Section,Role,Complexity,File Type,Component Name,FilePath\n");
    }

    // Append the component details to the CSV file
    $csvLine = sprintf(
        '%s,%s,%s,%s,%s,%s,%s,%s,%s',
        $date,
        $time,
        $componentType,
        $section,
        $role,
        $complexity,
        $fileType,
        $componentName,
        $relativeFilePath
    );

    File::append($savePath, $csvLine . "\n");

    $this->info('Component details saved to CSV: ' . $savePath);
}

/**
 * Display summary of created components
 */
protected function displaySummary()
{
    $savePath = base_path('packages/webkernel/src/resources/components.csv');

    sleep(1);

    $this->info(PHP_EOL . "========================================");
    $this->info("       COMPONENT CREATION SUMMARY");
    $this->info("========================================" . PHP_EOL);

    // Check if the CSV file exists
    if (!File::exists($savePath)) {
        $this->error("No components created yet!");
        return;
    }

    // Read and format CSV data
    $contents = File::get($savePath);
    $lines = array_filter(explode("\n", $contents));

    // Ensure there are at least two lines (header + data)
    if (count($lines) < 2) {
        $this->info("No components found in registry");
        return;
    }

    // Display the last created component
    $this->info("LAST CREATED COMPONENT:");
    $this->info("");

    $lastLine = end($lines); // Get the last line (component)

    if ($lastLine) {
        // Split the line into individual components
        $details = explode(',', $lastLine);
        list($date, $time, $componentType, $section, $role, $complexity, $fileType, $componentName, $filePath) = $details;

        // Display the last component as a receipt
        $this->line(str_pad("Date: ", 15) . $date);
        $this->line(str_pad("Time: ", 15) . $time);
        $this->line(str_pad("Component Type: ", 15) . $componentType);
        $this->line(str_pad("Section: ", 15) . $section);
        $this->line(str_pad("Role: ", 15) . $role);
        $this->line(str_pad("Complexity: ", 15) . $complexity);
        $this->line(str_pad("File Type: ", 15) . $fileType);
        $this->line(str_pad("Component Name: ", 15) . $componentName);
        $this->info("");
        // Here, we show the relative file path instead of the absolute path
        $this->line(str_pad("File Path: ", 15) . $filePath);

    } else {
        $this->line(str_repeat(PHP_EOL . '-', 30));
        $this->line("No components \033]8;;https://github.com/numerimondes/webkernel/issues\033\\Please Report this issue on github !\033]8;;\033\\.");
        $this->line(str_repeat('-' . PHP_EOL, 30));
    }

    // Final message
    $this->info(PHP_EOL . "========================================");
    $this->info("     THANK YOU FOR USING WEB KERNEL ");
    $this->info("========================================");
}

}
