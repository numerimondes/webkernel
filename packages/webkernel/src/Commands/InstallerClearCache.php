<?php

namespace Webkernel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallerClearCache extends Command
{
    protected $signature = 'webkernel:install-clear-cache';
    protected $description = 'Webkernel Installer Environment Checker';
    protected $hidden = true;
    public function handle(): void
    {
        $this->clearCaches();
    }

    protected function clearCaches(): void
    {
        $this->info('⚡ Clearing Laravel caches...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        $this->info('✅ Caches cleared.');
    }
}
