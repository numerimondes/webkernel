<?php

namespace Webkernel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDbCo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webkernel:test-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test de la connexion à la base de données';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle():bool
    {
        try {
            DB::connection()->getPdo();
            $this->info('true'); // Connexion réussie
            return true;
        } catch (\Exception $e) {
            $this->info('false'); // Connexion échouée
            return false;
        }
    }
}
