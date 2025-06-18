<?php
namespace Webkernel\Console\Install;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\password;

class DatabaseSetup extends Command

{
    protected $signature = 'webkernel:install-initial-db-setup {--database=}';
    protected $description = 'Webkernel Installer Initial Database Setup';
    protected $hidden = true;
    public function handle(): void
    {
        $this->displayDatabaseArt();
        $this->warn('/** Note: SQLite may have limitations for production environments, especially with high traffic and concurrency.');
        $this->warn(' *  SQLite is designed as a lightweight, serverless database, but it lacks advanced features such ');
        $this->warn(' *  as full ACID compliance, foreign key support (by default), and complex join optimizations.');
        $this->warn(' *  We avoid using it due to limitations in SQL syntax (e.g., missing support for certain complex queries/stored procedures)');
        $this->warn(' *  and dynamic type handling, which can lead to inconsistencies when migrating to other DBMS.');
        $this->warn(' *  We recommend using MySQL, PostgreSQL, or MariaDB for production environments,');
        $this->warn(' *  as they are designed to handle high concurrency, complex queries, and large-scale applications more efficiently.');
        $this->warn(' *  Note: PostgreSQL, while robust and highly recommended, hasn\'t been fully tested yet in all scenarios, especially for specific edge cases or advanced configurations.');
        $this->warn(' */');

        [$database, $migrate] = $this->promptForDatabaseOptions();

        $this->configureDefaultDatabaseConnection(base_path(), $database, config('app.name', 'webkernel'));

        if ($database === 'sqlite') {
            $this->createSqliteDatabase();
        }

        // Keep attempting database setup until successful
        $success = false;
        while (!$success) {
            $dbConfig = $this->setupDatabaseConnection($database);
            if ($dbConfig) {
                $success = true;
            } else {
                if (!$this->input->isInteractive() || !confirm('Retry database configuration?')) {
                    $this->error('Database setup failed. Exiting installation.');
                    return;
                }
            }
        }

        // Add a small delay after database configuration
        sleep(3);

        // Run migrations if requested
        if ($migrate) {
            $success = false;
            while (!$success) {
                if ($this->runMigrations()) {
                    $success = true;
                } else {
                    if (!$this->input->isInteractive() || !confirm('Retry migrations?')) {
                        $this->error('Migration failed. Exiting installation.');
                        return;
                    }
                }
            }
        }

        // Import initial data
        $success = false;
        while (!$success) {
            if ($this->importInitialData()) {
                $success = true;
            } else {
                if (!$this->input->isInteractive() || !confirm('Retry data import?')) {
                    $this->error('Data import failed. Exiting installation.');
                    return;
                }
            }
        }

        $this->info('Database setup completed successfully!');
    }

    protected function setupDatabaseConnection(string $database): array|bool
    {
        $this->info('Setting up database connection...');

        $dbConfig = [];

        if ($database !== 'sqlite' && $this->input->isInteractive()) {
            $this->info('Database connection configuration:');
            $this->info('[For default 127.0.0.1:3306 press enter]');

            $dbConfig['host'] = text('Database host:', env('DB_HOST', '127.0.0.1'));
            $dbConfig['port'] = text('Port:', env('DB_PORT', $this->getDefaultPortForDatabase($database)));
            $dbConfig['database'] = text('Database name:', env('DB_DATABASE', strtolower(config('app.name', 'webkernel'))));
            $dbConfig['username'] = text('Username:', env('DB_USERNAME', 'root'));
            $dbConfig['password'] = password('Password:');

            // Update the .env file
            $this->updateEnvFile($database, $dbConfig);

            // Temporarily update the database configuration
            $this->updateDatabaseConfig($database, $dbConfig);
        }

        if (!$this->testDatabaseConnection($database)) {
            $this->error('Database connection failed.');
            return false;
        }

        $this->info('Database connection established successfully!');
        return $dbConfig;
    }

    protected function updateEnvFile(string $database, array $dbConfig): void
    {
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            $this->error('.env file not found.');
            return;
        }

        $this->pregReplaceInFile('/DB_CONNECTION=.*/', 'DB_CONNECTION=' . $database, $envFile);
        $this->pregReplaceInFile('/DB_HOST=.*/', 'DB_HOST=' . $dbConfig['host'], $envFile);
        $this->pregReplaceInFile('/DB_PORT=.*/', 'DB_PORT=' . $dbConfig['port'], $envFile);
        $this->pregReplaceInFile('/DB_DATABASE=.*/', 'DB_DATABASE=' . $dbConfig['database'], $envFile);
        $this->pregReplaceInFile('/DB_USERNAME=.*/', 'DB_USERNAME=' . $dbConfig['username'], $envFile);
        $this->pregReplaceInFile('/DB_PASSWORD=.*/', 'DB_PASSWORD=' . $dbConfig['password'], $envFile);

        // Clear config cache
        $this->call('config:clear');
        $this->info('Database configuration updated successfully.');
    }

    protected function updateDatabaseConfig(string $database, array $dbConfig): void
    {
        // Dynamic runtime configuration update
        Config::set('database.default', $database);
        Config::set("database.connections.{$database}.host", $dbConfig['host']);
        Config::set("database.connections.{$database}.port", $dbConfig['port']);
        Config::set("database.connections.{$database}.database", $dbConfig['database']);
        Config::set("database.connections.{$database}.username", $dbConfig['username']);
        Config::set("database.connections.{$database}.password", $dbConfig['password']);

        // Purge database connections to ensure config is reloaded
        DB::purge($database);
    }

    protected function testDatabaseConnection(string $database): bool
    {
        $this->info('Verifying database connection...');
        try {
            DB::connection()->getPdo();
            $this->info('true'); // Connexion réussie
            return true;
        } catch (Exception $e) {
            $this->info('false'); // Connexion échouée
            return false;
        }
    }

    protected function runMigrations(): bool
    {
        $this->info('Running migrations...');
        try {
            $this->call('migrate', ['--force' => true]);
            $this->info('Migrations completed successfully!');
            return true;
        } catch (Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function getDefaultPortForDatabase(string $database): string
    {
        $defaultPorts = [
            'pgsql' => '5432',
            'sqlsrv' => '1433',
            'mysql' => '3306',
            'mariadb' => '3306'
        ];
        return $defaultPorts[$database] ?? '3306';
    }

    protected function createSqliteDatabase(): void
    {
        $databasePath = database_path('database.sqlite');
        $this->info("Creating SQLite database at: {$databasePath}");

        if (!is_dir(dirname($databasePath))) {
            mkdir(dirname($databasePath), 0755, true);
        }

        if (!file_exists($databasePath)) {
            if (touch($databasePath)) {
                $this->info('SQLite database file created successfully!');
            } else {
                $this->error('Failed to create SQLite database file.');
                $this->error('Please check the permissions and path.');
            }
        } else {
            $this->info('SQLite database file already exists.');
        }
    }

    protected function promptForDatabaseOptions(): array
    {
        $available = $this->databaseOptions();
        $default = array_key_first($available);
        $db = $this->option('database');

        if (!$db && $this->input->isInteractive()) {
            $db = select(
                label: 'Which database will your application use?',
                options: $available,
                default: $default,
            );
        } else if (!$db) {
            $db = $default;
        }

        if (!array_key_exists($db, $available)) {
            $this->error("Invalid database driver: {$db}");
            $db = $default;
        }

        $migrate = false;
        if ($this->input->isInteractive()) {
            $migrate = $db === 'sqlite' || confirm(
                'Would you like to run the migrations now?'
            );
        } else {
            $migrate = true;
        }

        return [$db, $migrate];
    }

    protected function databaseOptions(): array
    {
        return collect([
            'sqlite' => ['SQLite', extension_loaded('pdo_sqlite')],
            'mysql' => ['MySQL', extension_loaded('pdo_mysql')],
            'mariadb' => ['MariaDB', extension_loaded('pdo_mysql')],
            'pgsql' => ['PostgreSQL', extension_loaded('pdo_pgsql')],
            'sqlsrv' => ['SQL Server', extension_loaded('pdo_sqlsrv')],
        ])
        ->sortBy(fn ($db) => $db[1] ? 0 : 1)
        ->mapWithKeys(fn ($db, $key) => [$key => $db[0] . ($db[1] ? '' : ' (missing extension)')])
        ->toArray();
    }

    protected function configureDefaultDatabaseConnection(string $directory, string $database, string $name): void
    {
        $envFiles = [$directory.'/.env', $directory.'/.env.example'];

        foreach ($envFiles as $file) {
            if (!file_exists($file)) {
                $this->warn("Environment file not found: {$file}");
                continue;
            }
            $this->pregReplaceInFile('/DB_CONNECTION=.*/', 'DB_CONNECTION='.$database, $file);
        }

        if ($database === 'sqlite') {
            foreach ($envFiles as $file) {
                if (file_exists($file)) {
                    $this->commentDatabaseConfigurationForSqlite($file);
                }
            }
            return;
        }

        $defaultPorts = [
            'pgsql' => '5432',
            'sqlsrv' => '1433',
            'mysql' => '3306',
            'mariadb' => '3306'
        ];

        foreach ($envFiles as $file) {
            if (!file_exists($file)) continue;
            $this->uncommentDatabaseConfiguration($file);
            if (isset($defaultPorts[$database])) {
                $this->replaceInFile('DB_PORT=3306', 'DB_PORT=' . $defaultPorts[$database], $file);
            }
            $dbName = str_replace('-', '_', strtolower($name));
            $this->replaceInFile('DB_DATABASE=laravel', 'DB_DATABASE=' . $dbName, $file);
        }
    }

    protected function commentDatabaseConfigurationForSqlite(string $file): void
    {
        if (!file_exists($file)) return;

        $defaults = [
            'DB_HOST=127.0.0.1',
            'DB_PORT=3306',
            'DB_DATABASE=laravel',
            'DB_USERNAME=root',
            'DB_PASSWORD=',
        ];

        $content = file_get_contents($file);
        foreach ($defaults as $default) {
            if (strpos($content, $default) !== false && strpos($content, '# ' . $default) === false) {
                $content = str_replace($default, '# ' . $default, $content);
            }
        }
        file_put_contents($file, $content);
    }

    protected function uncommentDatabaseConfiguration(string $file): void
    {
        if (!file_exists($file)) return;

        $defaults = [
            '# DB_HOST=127.0.0.1',
            '# DB_PORT=3306',
            '# DB_DATABASE=laravel',
            '# DB_USERNAME=root',
            '# DB_PASSWORD=',
        ];

        $content = file_get_contents($file);
        foreach ($defaults as $default) {
            if (strpos($content, $default) !== false) {
                $content = str_replace($default, ltrim($default, '# '), $content);
            }
        }
        file_put_contents($file, $content);
    }

    protected function replaceInFile($search, $replace, $file): void
    {
        if (!file_exists($file)) return;
        $content = file_get_contents($file);
        $content = str_replace($search, $replace, $content);
        file_put_contents($file, $content);
    }

    protected function pregReplaceInFile($pattern, $replacement, $file): void
    {
        if (!file_exists($file)) return;
        $content = file_get_contents($file);
        $content = preg_replace($pattern, $replacement, $content);
        file_put_contents($file, $content);
    }

    protected function importInitialData(): bool
    {
        $this->info('Importing initial data...');
        $sqlFile = base_path('packages/webkernel/src/database/init_dump/webkernel_seed_settings_languages.sql');
        $url = 'https://example.com/path/to/webkernel_seed_settings_languages.sql'; // URL to download the SQL file

        // Check if the file exists locally
        if (!file_exists($sqlFile)) {
            $this->warn('SQL file not found locally. Attempting to download it from the URL...');
            try {
                $fileContents = file_get_contents($url);
                if ($fileContents === false) {
                    throw new Exception("Failed to download the SQL file from the URL.");
                }
                // Save the file locally
                file_put_contents($sqlFile, $fileContents);
                $this->info('SQL file downloaded successfully.');
            } catch (Exception $e) {
                $this->error('Error downloading the SQL file: ' . $e->getMessage());
                return false; // Fail if the file can't be downloaded
            }
        }

        try {
            // 1. Disable foreign key checks to avoid constraint violations
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

            // 2. Read the content of the SQL file
            $sql = file_get_contents($sqlFile);
            $statements = array_filter(array_map('trim', explode(";", $sql)));

            // 3. Modify the SQL if needed to prevent errors if table already exists
            // Add 'IF NOT EXISTS' for CREATE TABLE statements in the SQL
            $sql = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $sql);

            // 4. Execute each SQL statement from the file
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    DB::statement($statement);
                }
            }

            // 5. Re-enable foreign key checks after the import
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

            $this->info('Initial data imported successfully!');
            return true;
        } catch (Exception $e) {
            // Re-enable foreign key checks in case of an error
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
            $this->error('Data import error: ' . $e->getMessage());
            return false;
        }
    }

    protected function displayDatabaseArt(): void
    {
        $this->newLine();
        $this->line("  ⠀⠀⠀⠀⢀⣀⣀⣀⣤⣤⣤⣤⣀⣀⣀⡀⠀⠀⠀⠀⠀");
        $this->line("⠀⠀⣠⣴⣶⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⣦⣄⠀");
        $this->line("⠀⠀⠙⠻⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠟⠋⠀");
        $this->line("⠀⠀⣿⣶⣤⣄⣉⣉⠙⠛⠛⠛⠛⠛⠛⠋⣉⣉⣠⣤⣶⣿⠀");
        $this->line("⠀⠀⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠀");
        $this->line("⠀⠀⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠀");
        $this->newLine();
    }
}
