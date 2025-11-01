<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\WebsiteBuilder\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkernel\Aptitudes\WebsiteBuilder\Models\Theme;
use Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject;

class ComprehensiveThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test project if it doesn't exist
        $project = WebsiteProject::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Test Project',
                'slug' => 'test-project',
                'description' => 'Test project for comprehensive theme system',
                'is_active' => true,
            ]
        );

        // Create comprehensive theme with multiple modes
        $comprehensiveTheme = Theme::create([
            'project_id' => $project->id,
            'name' => 'Comprehensive Design System',
            'slug' => 'comprehensive-design-system',
            'description' => 'A comprehensive theme with multiple color modes and design system',
            'theme_config' => [
                'useCustomProperties' => true,
                'initialColorMode' => 'system',
                'colors' => [
                    'text' => '#000000',
                    'background' => '#ffffff',
                    'primary' => '#2563eb',
                    'secondary' => '#6b7280',
                    'muted' => '#f6f6f6',
                    'highlight' => '#efeffe',
                    'gray' => '#777777',
                    'accent' => '#609',
                    'success' => '#059669',
                    'warning' => '#d97706',
                    'danger' => '#dc2626',
                    'info' => '#2563eb',
                    'modes' => [
                        'dark' => [
                            'text' => '#ffffff',
                            'background' => '#060606',
                            'primary' => '#3b82f6',
                            'secondary' => '#9ca3af',
                            'muted' => '#191919',
                            'highlight' => '#29112c',
                            'gray' => '#999999',
                            'accent' => '#c0f',
                            'success' => '#10b981',
                            'warning' => '#f59e0b',
                            'danger' => '#ef4444',
                            'info' => '#3b82f6'
                        ],
                        'deep' => [
                            'text' => 'hsl(210, 50%, 96%)',
                            'background' => 'hsl(230, 25%, 18%)',
                            'primary' => 'hsl(260, 100%, 80%)',
                            'secondary' => 'hsl(290, 100%, 80%)',
                            'highlight' => 'hsl(260, 20%, 40%)',
                            'accent' => 'hsl(290, 100%, 80%)',
                            'muted' => 'hsla(230, 20%, 0%, 20%)',
                            'gray' => 'hsl(210, 50%, 60%)',
                            'success' => 'hsl(120, 100%, 80%)',
                            'warning' => 'hsl(45, 100%, 80%)',
                            'danger' => 'hsl(0, 100%, 80%)',
                            'info' => 'hsl(200, 100%, 80%)'
                        ],
                        'swiss' => [
                            'text' => 'hsl(10, 20%, 20%)',
                            'background' => 'hsl(10, 10%, 98%)',
                            'primary' => 'hsl(10, 80%, 50%)',
                            'secondary' => 'hsl(10, 60%, 50%)',
                            'highlight' => 'hsl(10, 40%, 90%)',
                            'accent' => 'hsl(250, 60%, 30%)',
                            'muted' => 'hsl(10, 20%, 94%)',
                            'gray' => 'hsl(10, 20%, 50%)',
                            'success' => 'hsl(120, 60%, 40%)',
                            'warning' => 'hsl(45, 80%, 50%)',
                            'danger' => 'hsl(0, 80%, 50%)',
                            'info' => 'hsl(200, 80%, 50%)'
                        ]
                    ]
                ],
                'fonts' => [
                    'body' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif',
                    'heading' => 'inherit',
                    'monospace' => 'Menlo, monospace'
                ],
                'fontSizes' => [12, 14, 16, 20, 24, 32, 48, 64, 72],
                'fontWeights' => [
                    'body' => 400,
                    'heading' => 700,
                    'display' => 900
                ],
                'lineHeights' => [
                    'body' => 1.5,
                    'heading' => 1.25
                ],
                'space' => [0, 4, 8, 16, 32, 64],
                'breakpoints' => [360, 600, 1024]
            ],
            'is_active' => true,
            'is_default' => true,
        ]);

        // Create a minimal theme for comparison
        $minimalTheme = Theme::create([
            'project_id' => $project->id,
            'name' => 'Minimal Theme',
            'slug' => 'minimal-theme',
            'description' => 'A minimal theme with basic light/dark modes',
            'theme_config' => [
                'useCustomProperties' => true,
                'initialColorMode' => 'light',
                'colors' => [
                    'text' => '#000000',
                    'background' => '#ffffff',
                    'primary' => '#3b82f6',
                    'secondary' => '#6b7280',
                    'muted' => '#f3f4f6',
                    'highlight' => '#f0f9ff',
                    'gray' => '#6b7280',
                    'accent' => '#8b5cf6',
                    'success' => '#10b981',
                    'warning' => '#f59e0b',
                    'danger' => '#ef4444',
                    'info' => '#06b6d4',
                    'modes' => [
                        'dark' => [
                            'text' => '#ffffff',
                            'background' => '#111827',
                            'primary' => '#60a5fa',
                            'secondary' => '#9ca3af',
                            'muted' => '#374151',
                            'highlight' => '#1e3a8a',
                            'gray' => '#9ca3af',
                            'accent' => '#a78bfa',
                            'success' => '#34d399',
                            'warning' => '#fbbf24',
                            'danger' => '#f87171',
                            'info' => '#22d3ee'
                        ]
                    ]
                ],
                'fonts' => [
                    'body' => 'Inter, system-ui, sans-serif',
                    'heading' => 'Inter, system-ui, sans-serif',
                    'monospace' => 'JetBrains Mono, monospace'
                ],
                'fontSizes' => [12, 14, 16, 18, 20, 24, 30, 36, 48, 60, 72],
                'fontWeights' => [
                    'body' => 400,
                    'heading' => 600,
                    'display' => 800
                ],
                'lineHeights' => [
                    'body' => 1.6,
                    'heading' => 1.2
                ],
                'space' => [0, 2, 4, 8, 12, 16, 20, 24, 32, 40, 48, 64, 80, 96],
                'breakpoints' => [640, 768, 1024, 1280, 1536]
            ],
            'is_active' => true,
            'is_default' => false,
        ]);

        $this->command->info('Comprehensive themes created successfully!');
        $this->command->info('Available themes:');
        $this->command->info('- Comprehensive Design System (modes: light, dark, deep, swiss)');
        $this->command->info('- Minimal Theme (modes: light, dark)');
    }
}
