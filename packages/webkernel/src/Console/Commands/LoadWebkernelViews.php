<?php

namespace Webkernel\Console\Commands;

use Illuminate\Console\Command;

class LoadWebkernelViews extends Command
{
    /**
     * Nom de la commande (signature).
     */
    protected $signature = 'webkernel:load-views';

    /**
     * Description dans `php artisan list`.
     */
    protected $description = 'Load all Webkernel views and components';

    /**
     * Laravel 12+ : surcharge propre des aliases via méthode.
     */
    public function getAliases(): array
    {
        return ['webkernel:loadviews', 'webkernel:lv'];
    }

    /**
     * Exécution de la commande.
     */
    public function handle(): void
    {
        $this->info('Clearing caches and optimizing...');

        $this->callSilent('optimize:clear');
        $this->callSilent('filament:optimize-clear');
        $this->callSilent('clear-compiled');
        $this->callSilent('config:clear');
        $this->callSilent('cache:clear');
        $this->callSilent('view:clear');

        $this->info('✅ Webkernel views and components refreshed successfully.');
    }
}
