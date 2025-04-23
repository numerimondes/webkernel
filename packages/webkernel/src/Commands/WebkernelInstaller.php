<?php

namespace Webkernel\Commands;

use Illuminate\Console\Command;

class WebkernelInstaller extends Command
{
    protected $signature = 'webkernel:install';
    protected $description = 'Executes all Webkernel installation steps';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting Webkernel installation...');

        // Execute each installation step
        $this->call('webkernel:install-check-env');
        $this->call('key:generate');
        $this->call('webkernel:install-update-composer');
        $this->call('webkernel:install-register-providers');
        $this->call('webkernel:install-update-user-model');
        $this->call('webkernel:install-composer-dependencies');
        $this->call('webkernel:install-initial-db-setup');
        $this->call('webkernel:install-check-env');
        $this->call('webkernel:sync-composer');

        $this->info('Installation completed successfully!');
    }
}
