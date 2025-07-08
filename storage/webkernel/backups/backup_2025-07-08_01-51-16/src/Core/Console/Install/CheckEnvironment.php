<?php

namespace Webkernel\Core\Console\Install;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckEnvironment extends Command
{
    protected $signature = 'webkernel:install-check-env';
    protected $description = 'Webkernel Installer Environment Checker';
    protected $hidden = true;
    public function __construct()
    {
        parent::__construct();
    }

    protected function checkEnvironmentVariables(): array
    {
        $status = [
            'laravel' => false,
            'filament' => false
        ];

        $this->info('🔍 Checking environment requirements...');

        // Check if composer.json exists
        if (!File::exists(base_path('composer.json'))) {
            $this->error('❌ composer.json not found. Please run this command from your Laravel project root.');
            return $status; // Exit gracefully
        }

        $composer = json_decode(File::get(base_path('composer.json')), true);

        // Check if Laravel framework is in the dependencies
        if (isset($composer['require']['laravel/framework'])) {
            $status['laravel'] = true;
            $this->info('✅ Laravel framework detected: ' . $composer['require']['laravel/framework']);
        } else {
            $this->error('❌ Laravel framework not found in dependencies.');
            return $status; // Exit gracefully
        }

        // Check if Filament is in the dependencies
        if (isset($composer['require']['filament/filament'])) {
            $status['filament'] = true;
            $this->info('✅ Filament detected: ' . $composer['require']['filament/filament']);
        } else {
            $this->warn('⚠️ Filament not found in dependencies.');
        }

        // Check if Filament is installed
        exec('composer show -N', $installedPackages, $returnValue);
        $installedPackages = array_flip($installedPackages);

        if ($returnValue === 0 && isset($installedPackages['filament/filament'])) {
            $status['filament'] = true;
            $this->info('✅ Filament is installed according to Composer.');
        } elseif ($returnValue !== 0) {
            $this->warn('⚠️ Failed to check installed packages via Composer.');
        }

        return $status;
    }

    protected function installFilamentIfMissing(bool $filamentStatus): bool
    {
        if ($filamentStatus) {
            $this->info('✅ Filament is already installed, skipping installation.');
            return true;
        }

        $this->info('📦 Installing Filament v3.3...');

        $command = 'composer require filament/filament:"^3.3" -W';
        exec($command, $output, $returnValue);

        $this->line(implode("\n", $output));

        if ($returnValue === 0) {
            $this->info('✅ Filament has been successfully installed!');
            return true;
        } else {
            $this->warn('⚠️ Filament installation encountered issues. You may need to install it manually.');
            $this->warn('Error output: ' . implode("\n", $output));
            return false;
        }
    }

    protected function updateEnvFile(): void
    {
        $envFile = base_path('.env');

        // Check if the .env file exists and contains the APP_NAME
        if (File::exists($envFile)) {
            $envContent = File::get($envFile);

            if (strpos($envContent, 'APP_NAME=Laravel') !== false) {
                File::put($envFile, str_replace('APP_NAME=Laravel', 'APP_NAME=WebKernel', $envContent));
                $this->info('⚙️ APP_NAME updated to WebKernel.');
            } else {
                $this->info('⚙️ APP_NAME already set.');
            }
        } else {
            $this->error('❌ .env file not found.');
        }
    }

    public function handle()
{
    // Create .env if missing
    if (!File::exists(base_path('.env'))) {
        if (File::exists(base_path('.env.example'))) {
            File::copy(base_path('.env.example'), base_path('.env'));
            $this->info('📄 .env file was missing and has been created from .env.example.');
        } else {
            $this->error('❌ Both .env and .env.example are missing. Cannot create .env.');
            return;
        }
    }

    // Run environment check
    $status = $this->checkEnvironmentVariables();

    // Check if Filament is missing and install it
    if (!$status['filament']) {
        $this->installFilamentIfMissing($status['filament']);
    }

    // Update the .env file for APP_NAME
    $this->updateEnvFile();
}

}
