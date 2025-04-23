<?php
namespace Webkernel\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class InstallerInitialDatabaseSetup extends Command
{
    protected $signature = 'webkernel:install-initial-db-setup {--database=}';
    protected $description = 'Webkernel Installer Initial Database Setup';

    public function handle(): void
    {
        $this->displayDatabaseArt();
        [$database, $migrate] = $this->promptForDatabaseOptions();
        $this->configureDefaultDatabaseConnection(base_path(), $database, config('app.name', 'webkernel'));

        if ($database === 'sqlite') {
            $this->createSqliteDatabase();
        }

        if ($migrate) {
            $this->info('Running migrations...');
            $this->call('migrate', ['--force' => true]);
        }

        $this->importInitialData();
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
                'Would you like to run the default migrations now?'
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

        // For non-SQLite databases
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
            // Only comment if not already commented
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
            // Only uncomment if it exists and is commented
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

    protected function importInitialData(): void
    {
        $this->info('Importing initial data...');

        $sqlFile = base_path('packages/webkernel/src/database/init_dump/webkernel_seed_settings_languages.sql');

        if (file_exists($sqlFile)) {
            $this->info('Found SQL file for initial data import.');

            try {
                $sql = file_get_contents($sqlFile);
                $statements = array_filter(
                    array_map('trim', explode(";", $sql))
                );

                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        \DB::statement($statement);
                    }
                }

                $this->info('Initial data imported successfully!');
            } catch (\Exception $e) {
                $this->error('Error during data import: ' . $e->getMessage());
            }
        } else {
            $this->warn('No SQL file found for initial data import.');
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
