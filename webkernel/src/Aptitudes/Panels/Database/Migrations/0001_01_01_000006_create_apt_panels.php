<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Purpose: Create apt_panels table for dynamic Filament panel configuration
 *
 * This migration creates the storage structure for panel definitions that
 * closely mirror how panels are defined in traditional PanelProvider classes.
 * Includes realistic sample data demonstrating proper usage patterns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apt_panels', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('path');
            $table->json('methods')->nullable();
            $table->enum('panel_source', ['database', 'array', 'api'])->default('database');
            $table->string('version', 10)->default('4.0');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index('is_default');
        });

        $this->insertSamplePanels();
    }

    public function down(): void
    {
        Schema::dropIfExists('apt_panels');
    }

    /**
     * Insert realistic panel examples
     */
    private function insertSamplePanels(): void
    {
        $panels = [
            [
                'id' => 'ds',
                'path' => 'ds',
                'is_default' => true,
                'methods' => [
                    'login' => true,
                    'colors' => ['primary' => 'Color::Blue'],  // ✅ Format correct : array simple
                    'discoverResources' => [
                        'in' => 'app/Filament/Resources',
                        'for' => 'App\\Filament\\Resources'
                    ],
                    'discoverPages' => [
                        'in' => 'app/Filament/Pages',
                        'for' => 'App\\Filament\\Pages'
                    ],
                    'pages' => ['Filament\\Pages\\Dashboard'],
                    'discoverWidgets' => [
                        'in' => 'app/Filament/Widgets',
                        'for' => 'App\\Filament\\Widgets'
                    ],
                    'widgets' => [
                        'Filament\\Widgets\\AccountWidget',
                        'Filament\\Widgets\\FilamentInfoWidget'
                    ]
                ],
                'is_active' => true,
                'sort_order' => 1,
                'description' => 'Default DS Panel - mirrors DsPanelProvider',
                'panel_source' => 'database',
                'version' => '4.0',
                'metadata' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'admin',
                'path' => 'admin',
                'is_default' => false,
                'methods' => [
                    'login' => true,
                    'registration' => true,
                    'profile' => true,
                    'brandName' => 'Admin Panel',
                    'colors' => ['primary' => 'Color::Amber'],  // ✅ Format correct : array simple
                    'darkMode' => true,
                    'globalSearch' => true,
                    'topNavigation' => true,
                    'sidebarCollapsibleOnDesktop' => true,
                    'pages' => ['Filament\\Pages\\Dashboard'],
                    'discoverResources' => [
                        'in' => 'app/Filament/Admin/Resources',
                        'for' => 'App\\Filament\\Admin\\Resources'
                    ]
                ],
                'is_active' => true,
                'sort_order' => 2,
                'description' => 'Admin panel with extended features',
                'panel_source' => 'database',
                'version' => '4.0',
                'metadata' => ['theme' => 'dark'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'support',
                'path' => 'support',
                'is_default' => false,
                'methods' => [
                    'login' => true,
                    'brandName' => 'Support Center',
                    'colors' => ['primary' => 'Color::Green'],  // ✅ Format correct : array simple
                    'spa' => true,
                    'maxContentWidth' => '7xl',
                    'pages' => ['Filament\\Pages\\Dashboard'],
                    'navigationGroups' => [
                        'Tickets',
                        'Knowledge Base',
                        'Reports'
                    ]
                ],
                'is_active' => true,
                'sort_order' => 3,
                'description' => 'Support panel with SPA mode',
                'panel_source' => 'database',
                'version' => '4.0',
                'metadata' => ['spa_enabled' => true],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($panels as $panel) {
            // Convert methods array to JSON string
            $panel['methods'] = json_encode($panel['methods']);

            // Convert metadata to JSON if not null
            if ($panel['metadata'] !== null) {
                $panel['metadata'] = json_encode($panel['metadata']);
            }

            DB::table('apt_panels')->insert($panel);
        }
    }
};
