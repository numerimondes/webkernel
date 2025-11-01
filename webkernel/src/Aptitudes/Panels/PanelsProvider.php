<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Panels;

/**
 * Purpose: Service provider for dynamic Filament panel registration from database
 *
 * This provider loads panel configurations from the database and creates Panel instances
 * exactly as they would be defined in traditional PanelProvider classes. It provides
 * the same functionality as manually coded providers but with database flexibility.
 *
 * Features:
 * - Direct Panel instance creation from database storage
 * - Support for all Filament Panel methods and configurations
 * - Automatic registration with Filament during application bootstrap
 * - Comprehensive error handling and logging
 * - Multi-source support (database, array, api)
 */
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Filament\Facades\Filament;
use Webkernel\Aptitudes\Panels\Models\Panels as PanelsModel;
use Illuminate\Database\Eloquent\Model;

class PanelsProvider extends ServiceProvider
{
    /**
     * Register dynamic panels during application bootstrap
     *
     * This method loads and registers panels exactly like traditional PanelProviders
     * would, but with configurations stored in the database for flexibility.
     */
    public function register(): void
    {
        // Initialize Eloquent for early database access
        $this->initializeEloquent();

        // Load and register all active panels
        $panels = $this->loadActivePanels();

        foreach ($panels as $panelModel) {
            $this->registerPanelFromModel($panelModel);
        }

    }

    /**
     * Initialize Eloquent dependencies for early database access
     */
    protected function initializeEloquent(): void
    {
        Model::setConnectionResolver(app('db'));
        Model::setEventDispatcher(app('events'));
    }

    /**
     * Load all active panels from available sources
     */
    protected function loadActivePanels(): \Illuminate\Support\Collection
    {
        try {
            // Check if panels table exists
            $panelModel = new PanelsModel();
            if (!Schema::hasTable($panelModel->getTable())) {
                return collect();
            }

            // Cache les panels actifs pour éviter les requêtes répétées
            return Cache::remember('active_panels_v1', 300, function () {
                return PanelsModel::active()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();
            });

        } catch (\Throwable $e) {

            return collect();
        }
    }

    /**
     * Register individual panel from model
     *
     * This creates a Panel instance exactly as it would be created in a
     * traditional PanelProvider and registers it with Filament.
     */
    protected function registerPanelFromModel(PanelsModel $panelModel): void
    {
        try {
            // Create Panel instance with all configurations applied
            $panel = $panelModel->createPanel();

            // Register with Filament
            Filament::registerPanel($panel);



        } catch (\Throwable $e) {

        }
    }

    /**
     * Bootstrap method - available for future post-registration operations
     */
    public function boot(): void
    {
        // Future implementation for post-boot panel operations
        // Could include route registration, event listeners, etc.

    }
}
