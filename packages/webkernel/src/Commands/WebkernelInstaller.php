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
        $this->displayWebkernelHeaderAsCiiLogo();
        $this->call('webkernel:install-check-env');
        $this->call('key:generate');
        $this->call('webkernel:install-update-composer');
        $this->call('webkernel:install-register-providers');
        $this->call('webkernel:install-update-user-model');
        $this->call('webkernel:install-composer-dependencies');
        $this->call('webkernel:install-initial-db-setup');
        $this->call('webkernel:install-check-env');
        $this->call('webkernel:sync-composer');

        $this->displayWebkernelHeaderAsCiiLogo();
        $this->info('Installation completed successfully!');
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
