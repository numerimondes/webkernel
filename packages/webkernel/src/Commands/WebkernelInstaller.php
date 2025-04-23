<?php

namespace Webkernel\Commands;

use Illuminate\Console\Command;

class WebkernelInstaller extends Command
{
    // Signature with options and arguments
    protected $signature = 'webkernel:init
                            {--install : To perform the full installation of Webkernel.}
                            {--db-seed : To run database setup and seeding.}
                            {--no-db : To skip the database installation step.}
                            {--update : To perform only an update.}
                            {--force : To force the installation even if files already exist.}';

    protected $description = 'Executes all Webkernel installation steps with various options.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Check if no argument is provided
        if (!$this->hasArgument('install') && !$this->hasArgument('update')) {
            $this->info('Available arguments:');
            $this->info('--install    : To perform the full installation of Webkernel.');
            $this->info('--db-seed    : To run database setup and seeding.');
            $this->info('--no-db      : To skip the database installation step.');
            $this->info('--update     : To perform only an update.');
            $this->info('--force      : To force the installation even if files already exist.');
            return;
        }

        // Proceed with installation only if --install is passed
        if ($this->option('install')) {
            $this->info('Starting Webkernel installation...');
            $this->displayWebkernelHeaderAsCiiLogo();
            $this->call('webkernel:install-check-env');
            $this->call('key:generate');
            $this->call('webkernel:install-update-composer');
            $this->call('webkernel:install-register-providers');
            $this->call('webkernel:install-update-user-model');
            $this->call('webkernel:install-composer-dependencies');

            // If --no-db is not passed, perform the database installation
            if (!$this->option('no-db')) {
                $this->info('Initializing database...');
                $this->call('webkernel:install-initial-db-setup');

                // If --db-seed is passed, perform database seeding
                if ($this->option('db-seed')) {
                    $this->info('Running database seeding...');
                    $this->call('db:seed');
                }
            }

            $this->call('webkernel:sync-composer');
            $this->info('Installation completed successfully!');
        }

        // If --update option is specified
        if ($this->option('update')) {
            $this->info('Updating Webkernel...');
            $this->call('composer update');
            $this->call('webkernel:sync-composer');
            $this->info('Update completed successfully!');
        }

        // If --force is specified
        if ($this->option('force')) {
            $this->info('Forcing installation even if files already exist...');
            // Add specific actions if needed
        }

        $this->info(str_repeat(PHP_EOL, 3));
    }

    public function displayWebkernelHeaderAsCiiLogo()
    {
        $this->info(PHP_EOL);
        $this->info(" __      __      ___.    ____  __.                         .__   ");
        $this->info("/  \\    /  \\ ____\\_ |__ |    |/ _|___________  ____   ____ |  |  ");
        $this->info("\\   \\/\\/   // __ \\| __ \\|      <_/ __ \\_  __ \\/    \\_/ __ \\|  |  ");
        $this->info(" \\        /\\  ___/| \\_\\ \\    |  \\  ___/|  | \\/   |  \\  ___/|  |__");
        $this->info("  \\__/\\  /  \\___  >___  /____|__ \\___  >__|  |___|  /\\___  >____/");
        $this->info("       \\/       \\/    \\/        \\/   \\/           \\/     \\/      ");
        $this->info(PHP_EOL);
        $this->info("By \033]8;;http://www.numerimondes.com\033\\Numerimondes\033]8;;\033\\ for Laravel and FilamentPHP");
    }
}
