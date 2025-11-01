<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\WebsiteBuilder\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkernel\Aptitudes\WebsiteBuilder\Models\Theme;
use Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first project or create a default one
        $project = WebsiteProject::first();

        if (!$project) {
            $project = WebsiteProject::create([
                'name' => 'Default Project',
                'site_title_key' => 'default_site_title',
                'slug' => 'default-project',
                'description' => 'Default project for testing',
                'domain' => 'default.example.com',
                'canonical_url' => 'https://default.example.com',
                'version' => '1.0.0',
                'status' => 'active',
                'type' => 'business',
                'is_multilingual' => false,
                'main_language' => 'en',
                'main_timezone' => 'UTC',
            ]);
        }

        // Create default themes
        $themes = [
            [
                'project_id' => $project->id,
                'name' => 'Default Blue Theme',
                'slug' => 'default-blue',
                'description' => 'Default blue theme for the project',
                'theme_config' => [
                    'primary_color' => 'blue',
                    'primary_shade' => '600',
                    'dark_mode' => true,
                    'custom_colors' => [],
                    'generate_gradients' => true,
                    'fonts' => [
                        'primary' => 'Inter',
                        'secondary' => 'JetBrains Mono',
                    ],
                ],
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'project_id' => $project->id,
                'name' => 'Green Theme',
                'slug' => 'green-theme',
                'description' => 'Green theme variant',
                'theme_config' => [
                    'primary_color' => 'green',
                    'primary_shade' => '600',
                    'dark_mode' => true,
                    'custom_colors' => [],
                    'generate_gradients' => true,
                    'fonts' => [
                        'primary' => 'Inter',
                        'secondary' => 'JetBrains Mono',
                    ],
                ],
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'project_id' => $project->id,
                'name' => 'Purple Theme',
                'slug' => 'purple-theme',
                'description' => 'Purple theme variant',
                'theme_config' => [
                    'primary_color' => 'purple',
                    'primary_shade' => '600',
                    'dark_mode' => false,
                    'custom_colors' => [],
                    'generate_gradients' => true,
                    'fonts' => [
                        'primary' => 'Inter',
                        'secondary' => 'JetBrains Mono',
                    ],
                ],
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($themes as $themeData) {
            $theme = Theme::create($themeData);

            // Generate CSS for the theme
            $theme->generateCss();
        }
    }
}
