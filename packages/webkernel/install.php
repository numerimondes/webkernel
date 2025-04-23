<?php

/**
 *
 * Initial installer --- DO NOT USE IT
 *
 *
 * WebKernel Package Installer
 *
 * To install WebKernel, run:
 * > php packages/webkernel/install.php
 *
 * Options:
 * --now: Skip pauses between installation steps
 */

// Check for --now flag to skip pauses
$skipPauses = in_array('--now', $argv);

// Function to pause execution for readability
function pause(): void
{
    if (!$GLOBALS['skipPauses']) {
        echo "...\n";
        sleep(1);
    }
}

// Function to get base path
function base_path(string $path = ''): string
{
    return __DIR__ . '/../../' . ltrim($path, '/');
}

// Function to display colorized console output
function consoleOutput(string $message, string $type = 'info'): void
{
    $colors = [
        'info' => "\033[0;36m", // Cyan
        'success' => "\033[0;32m", // Green
        'warning' => "\033[0;33m", // Yellow
        'error' => "\033[0;31m", // Red
    ];

    $reset = "\033[0m";
    echo "{$colors[isset($colors[$type]) ? $type : 'info']}{$message}{$reset}\n";
}

/**
 * Check if Laravel and Filament are installed
 *
 * @return array Status of requirements with keys 'laravel' and 'filament'
 */
function checkEnvironmentVariables(): array
{
    $status = [
        'laravel' => false,
        'filament' => false
    ];

    consoleOutput("🔍 Checking environment requirements...", 'info');

    // Check for Laravel installation
    $composerPath = base_path('composer.json');
    if (!file_exists($composerPath)) {
        consoleOutput("❌ composer.json not found. Please run this script from your Laravel project root.", 'error');
        exit(1);
    }

    $composer = json_decode(file_get_contents($composerPath), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        consoleOutput("❌ Unable to parse composer.json. Please check if it's valid JSON.", 'error');
        exit(1);
    }

    // Check Laravel in composer.json
    if (isset($composer['require']['laravel/framework'])) {
        $status['laravel'] = true;
        consoleOutput("✅ Laravel framework detected: {$composer['require']['laravel/framework']}", 'success');
    } else {
        consoleOutput("❌ Laravel framework not found in dependencies.", 'error');
        exit(1);
    }

    // Check Filament in composer.json
    if (isset($composer['require']['filament/filament'])) {
        $status['filament'] = true;
        consoleOutput("✅ Filament detected: {$composer['require']['filament/filament']}", 'success');
    } else {
        consoleOutput("⚠️ Filament not found in dependencies.", 'warning');
    }

    // Double-check with composer show command for more accurate results
    exec('composer show -N', $installedPackages);
    $installedPackages = array_flip($installedPackages);

    // Check for Filament in installed packages
    if (isset($installedPackages['filament/filament'])) {
        $status['filament'] = true;
        consoleOutput("✅ Filament is installed according to Composer", 'success');
    }

    return $status;
}

/**
 * Install Filament if it's missing
 *
 * @param bool $filamentStatus Current status of Filament installation
 * @return bool True if installation was successful or already installed
 */
function installFilamentIfMissing(bool $filamentStatus): bool
{
    if ($filamentStatus) {
        consoleOutput("✅ Filament is already installed, skipping installation", 'success');
        return true;
    }

    consoleOutput("📦 Installing Filament v3.3...", 'info');

    // Execute composer require command to install Filament
    $command = "composer require filament/filament:\"^3.3\" -W";
    $output = [];
    $returnValue = 0;

    exec($command, $output, $returnValue);

    // Display output from command with line breaks
    echo implode("\n", $output) . "\n";

    if ($returnValue === 0) {
        consoleOutput("✅ Filament has been successfully installed!", 'success');
        return true;
    } else {
        consoleOutput("⚠️ Filament installation encountered issues. You may need to install it manually.", 'warning');
        // We don't exit here to allow the script to continue with other tasks
        return false;
    }
}

echo "🔧 Installing WebKernel package...\n";

// Run environment check and install Filament if needed
$requirements = checkEnvironmentVariables();
installFilamentIfMissing($requirements['filament']);

// Update app name in .env file
$envFile = base_path('.env');
$envContent = file_get_contents($envFile);

if (strpos($envContent, 'APP_NAME=Laravel') !== false) {
    file_put_contents($envFile, str_replace('APP_NAME=Laravel', 'APP_NAME=WebKernel', $envContent));
    consoleOutput("⚙️ APP_NAME updated to WebKernel.", 'success');
} else {
    consoleOutput("⚙️ APP_NAME already set.", 'info');
}

/**
 * Display WebKernel logo
 */
function displayLogo(): void
{
    echo PHP_EOL;
    echo " __      __      ___.    ____  __.                         .__   \n";
    echo "/  \\    /  \\ ____\\_ |__ |    |/ _|___________  ____   ____ |  |  \n";
    echo "\\   \\/\\/   // __ \\| __ \\|      <_/ __ \\_  __ \\/    \\_/ __ \\|  |  \n";
    echo " \\        /\\  ___/| \\_\\ \\    |  \\  ___/|  | \\/   |  \\  ___/|  |__\n";
    echo "  \\__/\\  /  \\___  >___  /____|__ \\___  >__|  |___|  /\\___  >____/\n";
    echo "       \\/       \\/    \\/        \\/   \\/           \\/     \\/      \n\n";
    echo "By \033]8;;https://www.numerimondes.com\033\\Numerimondes\033]8;;\033\\ for Laravel and FilamentPHP\n";
    echo PHP_EOL;
}

function getEnvValue(string $key): ?string
{
    $envPath = base_path('.env');
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        if (str_starts_with($line, $key . '=')) {
            return trim(substr($line, strlen($key) + 1));
        }
    }
    return null;
}

function setEnvValue(string $key, string $newValue): void
{
    $envPath = base_path('.env');
    $content = file_get_contents($envPath);
    $pattern = "/^$key=.*$/m";
    $replacement = "$key=$newValue";
    $newContent = preg_replace($pattern, $replacement, $content);
    file_put_contents($envPath, $newContent);
}

function prompt(string $message): string
{
    echo $message . " ";
    return trim(fgets(STDIN));
}

/**
 * Modify composer.json to include WebKernel package
 */
function modifyComposerJson(): void
{
    displayLogo();

    $composerPath = base_path('composer.json');

    if (!file_exists($composerPath)) {
        consoleOutput("❌ composer.json not found. Please run this script from your Laravel project root.", 'error');
        exit(1);
    }

    $composer = json_decode(file_get_contents($composerPath), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        consoleOutput("❌ Unable to parse composer.json. Please check if it's valid JSON.", 'error');
        exit(1);
    }

    // Check for Laravel installation
    if (!isset($composer['require']['laravel/framework'])) {
        consoleOutput("❌ Laravel framework not found in dependencies.", 'error');
        exit(1);
    }

    consoleOutput("✅ Laravel detected.", 'success');
    consoleOutput("🔍 Checking installed Composer packages...", 'info');

    exec('composer show -N', $installedPackages);
    $installedPackages = array_flip($installedPackages);

    consoleOutput("[✓] Found composer.json at: $composerPath", 'success');
    pause();

    // Add PSR-4 autoload entry for WebKernel
    $namespace = 'Webkernel\\';
    $path = 'packages/webkernel/src/';

    if (!isset($composer['autoload']['psr-4'][$namespace]) || $composer['autoload']['psr-4'][$namespace] !== $path) {
        $composer['autoload']['psr-4'][$namespace] = $path;
        consoleOutput("[✓] Added PSR-4 autoload entry for WebKernel", 'success');
    } else {
        consoleOutput("[✓] PSR-4 autoload entry for WebKernel already present", 'info');
    }
    pause();

    // Add repository path for WebKernel
    $repo = ['type' => 'path', 'url' => 'packages/webkernel'];
    $foundRepo = false;

    foreach ($composer['repositories'] ?? [] as $r) {
        if ($r['type'] === 'path' && $r['url'] === $repo['url']) {
            $foundRepo = true;
            break;
        }
    }

    if (!$foundRepo) {
        $composer['repositories'][] = $repo;
        consoleOutput("[✓] Added local repository for WebKernel", 'success');
    } else {
        consoleOutput("[✓] Local repository for WebKernel already present", 'info');
    }
    pause();

    // Add WebKernel dependency
    if (!isset($composer['require']['webkernel/webkernel'])) {
        $composer['require']['webkernel/webkernel'] = '*';
        consoleOutput("[✓] Added webkernel/webkernel dependency", 'success');
    } else {
        consoleOutput("[✓] webkernel/webkernel dependency already present", 'info');
    }
    pause();

    // Set stability configuration
    if (!isset($composer['minimum-stability'])) {
        $composer['minimum-stability'] = 'dev';
        consoleOutput("[✓] Added minimum-stability: dev", 'success');
    } else {
        consoleOutput("[✓] minimum-stability already set: {$composer['minimum-stability']}", 'info');
    }
    pause();

    if (!isset($composer['prefer-stable']) || $composer['prefer-stable'] !== true) {
        $composer['prefer-stable'] = true;
        consoleOutput("[✓] Added prefer-stable: true", 'success');
    } else {
        consoleOutput("[✓] prefer-stable already set", 'info');
    }
    pause();

    // Save changes
    file_put_contents($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    consoleOutput("[✓] Saved changes to composer.json", 'success');
}

/**
 * Register WebKernel service providers in bootstrap/providers.php
 */
function registerServiceProviders(): void
{
    $providerPath = base_path('bootstrap/providers.php');

    if (!file_exists($providerPath)) {
        consoleOutput("❌ bootstrap/providers.php not found. Please ensure you're running this in a Laravel 12+ project.", 'error');
        exit(1);
    }

    consoleOutput("[✓] Found bootstrap/providers.php", 'success');
    pause();

    $providers = [
        "Webkernel\\Providers\\WebkernelServiceProvider::class",
        "Webkernel\\Providers\\Filament\\SystemPanelProvider::class",
        "Webkernel\\Providers\\WebkernelRenderHooksServiceProvider::class",
        "Webkernel\\Providers\\WebkernelBladeServiceProvider::class",
        "Webkernel\\Providers\\WebkernelCommandServiceProvider::class",
        "Webkernel\\Providers\\WebkernelHelperServiceProvider::class",
        "Webkernel\\Providers\\WebkernelMigrationServiceProvider::class",
        "Webkernel\\Providers\\WebkernelRouteServiceProvider::class",
        "Webkernel\\Providers\\WebkernelViewServiceProvider::class",
        "Webkernel\\Providers\\WebkernelWebhookServiceProvider::class",
    ];


    $contents = file_get_contents($providerPath);
    $modified = false;

    foreach ($providers as $provider) {
        if (strpos($contents, $provider) !== false) {
            consoleOutput("[✓] $provider already registered", 'info');
            continue;
        }

        // Add provider to the array
        $contents = preg_replace(
            '/(return\s*\[\s*(.*?))(\];)/s',
            "$1\n    $provider,\n$3",
            $contents
        );

        consoleOutput("[✓] Added $provider to providers.php", 'success');
        $modified = true;
    }

    if ($modified) {
        file_put_contents($providerPath, $contents);
        consoleOutput("[✓] Saved providers.php with new providers", 'success');
    }

    pause();
}

function putUserExtensionsTraitinUserModel(): void
{
    $filePath = base_path('app/Models/User.php');
    $content = file_get_contents($filePath);

    // Ajout du use dans l'en-tête (en dehors de la classe)
    if (!str_contains($content, 'use Webkernel\Models\Traits\UserExtensions;')) {
        $content = preg_replace(
            '/(namespace\s+App\\\Models;\s+)/',
            "$1\nuse Webkernel\Models\Traits\UserExtensions;",
            $content
        );
    }

    // Ajout du trait dans le corps de la classe
    if (!str_contains($content, 'use UserExtensions; /** Do not remove this line to use Webkernel Capabilities */')) {
        $content = preg_replace(
            '/(class\s+User\s+extends\s+[^\\{]+{)/',
            "$1\n    use UserExtensions; /** Do not remove this line to use Webkernel Capabilities */",
            $content
        );
    }

    file_put_contents($filePath, $content);

    echo "✅ Laravel's User model now uses Webkernel UserExtensions trait.\n";
}

/**
 * Run composer commands
 */
function runComposerCommands(): void
{
    $composerPaths = ['packages/webkernel/composer.json', 'composer.json'];

    consoleOutput("⚙️ Running Composer commands...", 'info');

    foreach ($composerPaths as $jsonPath) {
        $fullPath = base_path($jsonPath);

        if (!file_exists($fullPath)) {
            consoleOutput("⚠️ File not found: $jsonPath (skipping)", 'warning');
            continue;
        }

        $workingDir = dirname($fullPath);

        consoleOutput("📦 Running 'composer install' in: $jsonPath", 'info');
        $command = "composer install -d " . escapeshellarg($workingDir);

        // Output from command execution
        $output = [];
        $returnValue = 0;

        exec($command, $output, $returnValue);

        if ($returnValue === 0) {
            consoleOutput("✅ Composer install completed for: $jsonPath", 'success');
        } else {
            consoleOutput("⚠️ Composer install had issues: " . implode("\n", $output), 'warning');
        }

        if (!$GLOBALS['skipPauses']) {
            sleep(1);
        }
    }

    consoleOutput("🎉 All Composer commands have been executed", 'success');
}


function chooseDatabaseType()
{
    echo "Choose your database type:\n";
    echo "  [0] sqlite\n";
    echo "  [1] mysql\n";
    echo "  [2] mariadb\n";
    echo "  [3] pgsql\n";
    echo "  [4] sqlsrv\n";

    $choice = prompt("Enter your choice (0–4):");

    $options = [
        '0' => 'sqlite',
        '1' => 'mysql',
        '2' => 'mariadb',
        '3' => 'pgsql',
        '4' => 'sqlsrv',
    ];

    if (!isset($options[$choice])) {
        echo "Invalid choice. Please choose a valid option.\n";
        return;
    }

    $driver = $options[$choice];
    setEnvValue('DB_CONNECTION', $driver);
    echo "Selected DB_CONNECTION: {$driver}\n";

    if ($driver === 'sqlite') {
        $projectRoot = realpath(__DIR__ . '/../../../'); // racine du projet
        $sqlitePath = $projectRoot . '/database/database.sqlite';

        // Création du dossier s'il n'existe pas
        if (!is_dir(dirname($sqlitePath))) {
            mkdir(dirname($sqlitePath), 0755, true);
        }

        // Création du fichier s'il n'existe pas
        if (!file_exists($sqlitePath)) {
            echo "SQLite database file not found. Creating database at: {$sqlitePath}\n";
            if (file_put_contents($sqlitePath, '') !== false) {
                echo "SQLite database file created successfully!\n";
            } else {
                echo "Failed to create SQLite database file.\n";
                echo "Please check the permissions and path.\n";
                exit(1);
            }
        } else {
            echo "SQLite database file already exists at: {$sqlitePath}\n";
        }

        // Mise à jour du .env pour SQLite
        setEnvValue('DB_DATABASE', $sqlitePath);
        setEnvValue('DB_HOST', '');
        setEnvValue('DB_PORT', '');
        setEnvValue('DB_USERNAME', '');
        setEnvValue('DB_PASSWORD', '');
    }

    return $driver;
}

function checkMysqlCredentials()
{
    if (getEnvValue('DB_CONNECTION') !== 'mysql') {
        echo "DB_CONNECTION n'est pas 'mysql'. Opération annulée.\n";
        return;
    }

    $keys = ['DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];

    foreach ($keys as $key) {
        $value = getEnvValue($key);
        echo "$key actuel: $value\n";

        $confirm = strtolower(trim(prompt("Est-ce que cette valeur est correcte ? (o/N)")));
        if ($confirm !== 'o') {
            $newValue = prompt("Entrez la nouvelle valeur pour $key:");
            setEnvValue($key, $newValue);
            echo "$key mis à jour à: $newValue\n";
        } else {
            echo "$key conservé.\n";
        }
    }
}

/**
 * Display database ASCII art
 */
function displayDatabaseArt(): void
{
    echo PHP_EOL;
    echo "  ⠀⠀⠀⠀⢀⣀⣀⣀⣤⣤⣤⣤⣀⣀⣀⡀⠀⠀⠀⠀⠀\n";
    echo "⠀⠀⣠⣴⣶⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⣦⣄⠀\n";
    echo "⠀⠀⠙⠻⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠟⠋⠀\n";
    echo "⠀⠀⣿⣶⣤⣄⣉⣉⠙⠛⠛⠛⠛⠛⠛⠋⣉⣉⣠⣤⣶⣿⠀\n";
    echo "⠀⠀⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠀\n";
    echo "⠀⠀⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠀\n";
    echo "⠀⠀⣄⡉⠛⠻⠿⢿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠿⠟⠛⢉⣠⠀\n";
    echo "⠀⠀⣿⣿⣿⣶⣶⣤⣤⣤⣤⣤⣤⣤⣤⣶⣶⣿⣿⣿⣿⠀\n";
    echo "⠀⠀⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠀\n";
    echo "⠀⠀⠻⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠟⠀\n";
    echo "⠀⠀⣶⣤⣈⡉⠛⠛⠻⠿⠿⠿⠿⠿⠿⠿⠛⠛⢉⣁⣤⣶⠀\n";
    echo "⠀⠀⣿⣿⣿⣿⣿⣷⣶⣶⣶⣶⣶⣶⣶⣶⣾⣿⣿⣿⣿⣿⠀\n";
    echo "⠀⠀⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠀\n";
    echo "⠀⠀⠙⠻⠿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠿⠟⠋⠀\n";
    echo "⠀⠀⠀⠀⠀⠀⠈⠉⠉⠉⠛⠛⠛⠛⠉⠉⠉⠁⠀⠀⠀⠀⠀\n";
    echo PHP_EOL;
}

/**
 * Import initial language and settings data using Laravel
 *
 * This implementation uses Laravel's framework instead of direct SQL commands
 */
function importInitialData(): void
{
    consoleOutput("⏳ Importing language and settings data using Laravel...", 'info');

    // Get Laravel app's base directory
    $basePath = base_path();

    // Step 1: Check if we can bootstrap Laravel
    $bootstrapFile = $basePath . '/bootstrap/app.php';
    if (!file_exists($bootstrapFile)) {
        consoleOutput("❌ Laravel bootstrap file not found", 'error');
        exit(1);
    }

    try {
        // Step 2: Create Laravel bootstrap script
        $importScript = $basePath . '/storage/webkernel_import.php';

        $scriptContent = <<<'PHP'
        <?php
        // This is a temporary script to import WebKernel data using Laravel's framework

        require __DIR__ . '/../vendor/autoload.php';
        $app = require_once __DIR__ . '/../bootstrap/app.php';

        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        // Get database info from config
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        echo "Using database connection: {$connection}\n";
        echo "Database name: {$database}\n";

        // Validate that database exists and is accessible
        try {
            if (empty($database)) {
                throw new Exception("No database configured in .env file");
            }

            DB::connection()->getPdo();
            echo "Database connection successful!\n";

            // Check if database is empty by looking for migrations table
            $hasSchema = Schema::hasTable('migrations');

            if (!$hasSchema) {
                echo "Database schema not found. Running migrations...\n";
                Artisan::call('migrate', ['--force' => true]);
                echo Artisan::output();
            }

            // Import SQL from file if it exists
            $sqlFile = base_path('packages/webkernel/src/database/init_dump/webkernel_seed_settings_languages.sql');

            if (file_exists($sqlFile)) {
                echo "Importing WebKernel initial data...\n";

                // Read SQL file contents
                $sql = file_get_contents($sqlFile);

                // Split SQL into individual statements
                $statements = array_filter(
                    array_map('trim',
                        explode(";", $sql)
                    )
                );

                // Execute each statement
                foreach ($statements as $statement) {
                    DB::statement($statement);
                }

                echo "Initial data imported successfully!\n";
            } else {
                echo "No SQL file found for initial data import.\n";
            }

        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }
        PHP;

        file_put_contents($importScript, $scriptContent);

        // Step 3: Execute the Laravel bootstrap script
        echo "Running the Laravel import script...\n";
        include $importScript;

    } catch (Exception $e) {
        consoleOutput("❌ Error during import: " . $e->getMessage(), 'error');
        exit(1);
    }
}

// Database path function
function database_path($file = '')
{
    return base_path('database/' . $file);
}


















// Execute the installation steps in the proper order
// First, run the environment check and install Filament if needed (already done at the start)

//modifyComposerJson();
//exec('composer update');
//registerServiceProviders();
//putUserExtensionsTraitinUserModel();
//exec('composer dump-autoload');
//
//// Clear Laravel caches using artisan
//exec('php artisan config:clear');
//exec('php artisan cache:clear');
//exec('php artisan view:clear');
//
//runComposerCommands();
//
//consoleOutput("\n>>> Seeding database with languages (FR/EN/AR) and default settings...\n", 'info');
//displayDatabaseArt();
//chooseDatabaseType();
//checkMysqlCredentials();
//importInitialData();
//exec('composer dump-autoload');
//
//// Clear Laravel caches using artisan
//exec('php artisan config:clear');
//exec('php artisan cache:clear');
//exec('php artisan view:clear');
//
//consoleOutput("🎉 Installation completed successfully!", 'success');
//consoleOutput(">>> Running composer synchronization...", 'info');
//
//exec('php artisan webkernel:sync-composer');
//exec('php artisan filament:assets');
//exec('php artisan config:clear');
//exec('php artisan cache:clear');
//exec('php artisan view:clear');
//
//displayLogo();
//consoleOutput("✨ WebKernel is now installed and ready to use!", 'success');
//
