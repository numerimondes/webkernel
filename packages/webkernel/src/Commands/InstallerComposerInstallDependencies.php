<?php

namespace Webkernel\Commands;

use Illuminate\Console\Command;

class InstallerComposerInstallDependencies extends Command
{
    protected $signature = 'webkernel:install-composer-dependencies';
    protected $description = 'Install Composer dependencies for Webkernel';

    public function handle(): void
    {
        $this->runComposerCommands();
    }

    protected function runComposerCommands(): void
    {
        $this->info('[✓] Running Composer install...');
        exec('composer install --no-interaction --prefer-dist', $output, $returnValue);
        $this->line(implode("\n", $output));

        if ($returnValue === 0) {
            $this->info('[✓] Composer install completed.');
        } else {
            $this->error('[! ERROR !] Composer install failed.');
        }
    }
}
