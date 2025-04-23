<?php
// This is a temporary script to import WebKernel data using Laravel's framework

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get database info from .env file (which is already set)
$connection = env('DB_CONNECTION');
$database = env('DB_DATABASE');
$username = env('DB_USERNAME');
$password = env('DB_PASSWORD');
$host = env('DB_HOST');
$port = env('DB_PORT');

echo "Using database connection: {$connection}\n";
echo "Database name: {$database}\n";

// Validate that database exists and is accessible
try {
    if (empty($database)) {
        throw new Exception("No database configured in .env file");
    }

    // Attempt database connection
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
            try {
                DB::statement($statement);
            } catch (\Exception $e) {
                echo "❌ Error executing statement: " . $e->getMessage() . "\n";
            }
        }

        echo "Initial data imported successfully!\n";
    } else {
        echo "No SQL file found for initial data import.\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}