<?php

namespace Webkernel\Console\Install;

use Illuminate\Console\Command;

class ComposerDependencies extends Command
{
    protected $signature = 'webkernel:install-composer-dependencies';
    protected $description = 'Install Composer dependencies for Webkernel';
    protected $hidden = true;
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
