<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Website projects table
        Schema::create('apt_website_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index('idx_proj_name');
            $table->string('site_title_key')->nullable();
            $table->string('slug')->nullable()->unique('uq_proj_slug');
            $table->string('description')->nullable();
            $table->string('domain')->unique('uq_proj_domain');
            $table->string('canonical_url')->nullable()->comment('Canonical URL for SEO and reverse proxies');
            $table->string('version')->default('1.0.0');
            $table->foreignId('status_id')->nullable()->constrained('apt_global_enums')->index('idx_proj_status');
            $table->foreignId('type_id')->nullable()->constrained('apt_global_enums')->index('idx_proj_type');
            $table->boolean('is_multilingual')->default(false);
            $table->string('main_language')->nullable()->index('idx_proj_lang'); // FK to apt_languages
            $table->string('main_timezone')->nullable()->index('idx_proj_tz'); // FK to apt_timezones
            $table->boolean('no_accessibility')->default(false)
                ->comment('Disable animations/cursors for accessibility');
            $table->boolean('preserve_url_parameters')->default(false)
                ->comment('Preserve UTM and query params when navigating');
            $table->string('favicon_path')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('og_image_path')->nullable();
            $table->string('og_title_key')->nullable();
            $table->string('og_description_key')->nullable();
            $table->string('apple_touch_icon_path')->nullable();
            $table->string('password_protection')->nullable();
            $table->text('custom_starthead_tags')->nullable();
            $table->text('custom_endhead_tags')->nullable();
            $table->text('custom_startbody_tags')->nullable();
            $table->text('custom_endbody_tags')->nullable();
            $table->timestamps();
        });

        // Pages table - stores page configuration and metadata
        Schema::create('apt_website_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('apt_website_projects')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->index('idx_page_slug');
            $table->string('path'); // Full URL path
            $table->string('template')->default('default'); // Template identifier
            $table->string('status')->index('idx_page_status'); // FK to apt_global_enums
            $table->string('type')->index('idx_page_type'); // FK to apt_global_enums
            $table->json('seo_config')->nullable(); // SEO meta tags, titles, descriptions
            $table->json('page_config')->nullable(); // Page-specific configuration
            $table->json('blocks_config')->nullable(); // Configuration for all blocks on page
            $table->integer('sort_order')->default(0);
            $table->boolean('is_homepage')->default(false);
            $table->string('language')->index('idx_page_lang'); // FK to apt_languages
            $table->foreignId('parent_page_id')->nullable()->constrained('apt_website_pages');
            $table->timestamps();

            $table->unique(['project_id', 'path', 'language'], 'uq_page_path_lang');
            $table->index(['project_id', 'status'], 'idx_page_proj_status');
        });

        // Redirections table
        Schema::create('apt_website_redirections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('apt_website_projects')->cascadeOnDelete();
            $table->string('source_url')->index('idx_redir_source');
            $table->string('destination_url');
            $table->string('status_code')->default('301'); // Changed from enum for SQLite compatibility
            $table->string('status')->index('idx_redir_status'); // FK to apt_global_enums
            $table->string('type')->index('idx_redir_type');     // FK to apt_global_enums
            $table->integer('priority')->default(0)->index('idx_redir_priority');
            $table->timestamps();

            $table->index(['project_id', 'source_url'], 'idx_proj_source');
        });

        // Well-known files table
        Schema::create('apt_website_well_known_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('apt_website_projects')->cascadeOnDelete();
            $table->string('name')->index('idx_wkf_name');
            $table->string('status')->index('idx_wkf_status'); // FK to apt_global_enums
            $table->string('path');
            $table->text('content');
            $table->string('mime_type')->default('text/plain');
            $table->timestamps();

            $table->unique(['project_id', 'path'], 'uq_proj_wkf_path');
        });

        // Block library - stores available block types and their configuration
        Schema::create('apt_website_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique('uq_block_id'); // e.g., 'hero_carousel'
            $table->string('name'); // Display name
            $table->string('category')->index('idx_block_cat'); // FK to apt_global_enums
            $table->string('blade_template'); // Path to blade template
            $table->json('default_config')->nullable(); // Default configuration schema
            $table->json('config_schema')->nullable(); // JSON schema for validation
            $table->string('preview_image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('version')->default('1.0.0');
            $table->timestamps();
        });

        // Collections - CMS-like content management
        Schema::create('apt_website_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('apt_website_projects')->cascadeOnDelete();
            $table->string('name'); // e.g., 'blog', 'products', 'testimonials'
            $table->string('slug')->index('idx_coll_slug');
            $table->string('table_name')->index('idx_coll_table'); // Generated table name
            $table->json('fields_schema'); // Field definitions
            $table->string('status')->index('idx_coll_status'); // FK to apt_global_enums
            $table->boolean('is_multilingual')->default(false);
            $table->timestamps();

            $table->unique(['project_id', 'slug'], 'uq_proj_coll_slug');
        });

        // Code overrides - custom PHP functions
        Schema::create('apt_website_code_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('apt_website_projects')->cascadeOnDelete();
            $table->string('name');
            $table->string('function_name')->index('idx_override_func');
            $table->text('php_code');
            $table->string('target_type'); // 'block', 'page', 'global'
            $table->string('target_identifier')->nullable(); // Block ID or page ID
            $table->string('status')->index('idx_override_status'); // FK to apt_global_enums
            $table->timestamps();

            $table->index(['project_id', 'target_type'], 'idx_proj_target');
        });

        // Cache management table
        Schema::create('apt_website_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('apt_website_projects')->cascadeOnDelete();
            $table->string('cache_key')->index('idx_cache_key');
            $table->string('cache_type'); // 'page', 'block', 'collection'
            $table->string('file_path'); // Path to cached file
            $table->timestamp('expires_at')->nullable();
            $table->json('dependencies')->nullable(); // Cache invalidation dependencies
            $table->timestamps();

            $table->unique(['project_id', 'cache_key'], 'uq_proj_cache_key');
        });

        $this->addWebsiteProjectEnums();
    }

    public function down(): void
    {
        Schema::dropIfExists('apt_website_cache');
        Schema::dropIfExists('apt_website_code_overrides');
        Schema::dropIfExists('apt_website_collections');
        Schema::dropIfExists('apt_website_blocks');
        Schema::dropIfExists('apt_website_well_known_files');
        Schema::dropIfExists('apt_website_redirections');
        Schema::dropIfExists('apt_website_pages');
        Schema::dropIfExists('apt_website_projects');
    }

    /**
     * Add website project enums
     */
    private function addWebsiteProjectEnums(): void
    {
        $enums = [
            // Website Project Status
            [
                'type' => 'website_project_status',
                'key' => 'draft',
                'label_key' => 'enum.website_project_status.draft',
                'default_label' => 'Brouillon',
                'description_key' => 'enum.website_project_status.draft.desc',
                'icon' => 'heroicon-o-document-text',
                'css_class' => 'text-gray-500',
                'sort_order' => 1,
                'is_active' => true,
                'metadata' => json_encode(['color' => '#6b7280']),
            ],
            [
                'type' => 'website_project_status',
                'key' => 'active',
                'label_key' => 'enum.website_project_status.active',
                'default_label' => 'Actif',
                'description_key' => 'enum.website_project_status.active.desc',
                'icon' => 'heroicon-o-check-circle',
                'css_class' => 'text-green-500',
                'sort_order' => 2,
                'is_active' => true,
                'metadata' => json_encode(['color' => '#10b981']),
            ],
            [
                'type' => 'website_project_status',
                'key' => 'maintenance',
                'label_key' => 'enum.website_project_status.maintenance',
                'default_label' => 'Maintenance',
                'description_key' => 'enum.website_project_status.maintenance.desc',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'css_class' => 'text-yellow-500',
                'sort_order' => 3,
                'is_active' => true,
                'metadata' => json_encode(['color' => '#f59e0b']),
            ],
            [
                'type' => 'website_project_status',
                'key' => 'archived',
                'label_key' => 'enum.website_project_status.archived',
                'default_label' => 'Archivé',
                'description_key' => 'enum.website_project_status.archived.desc',
                'icon' => 'heroicon-o-archive-box',
                'css_class' => 'text-gray-400',
                'sort_order' => 4,
                'is_active' => true,
                'metadata' => json_encode(['color' => '#9ca3af']),
            ],

            // Website Project Type
            [
                'type' => 'website_project_type',
                'key' => 'business',
                'label_key' => 'enum.website_project_type.business',
                'default_label' => 'Site d\'entreprise',
                'description_key' => 'enum.website_project_type.business.desc',
                'icon' => 'heroicon-o-building-office',
                'css_class' => 'text-blue-500',
                'sort_order' => 1,
                'is_active' => true,
                'metadata' => json_encode(['color' => '#3b82f6']),
            ],
            [
                'type' => 'website_project_type',
                'key' => 'ecommerce',
                'label_key' => 'enum.website_project_type.ecommerce',
                'default_label' => 'Boutique en ligne',
                'description_key' => 'enum.website_project_type.ecommerce.desc',
                'icon' => 'heroicon-o-shopping-cart',
                'css_class' => 'text-purple-500',
                'sort_order' => 2,
                'is_active' => true,
                'metadata' => json_encode(['color' => '#8b5cf6']),
            ],
            [
                'type' => 'website_project_type',
                'key' => 'blog',
                'label_key' => 'enum.website_project_type.blog',
                'default_label' => 'Blog',
                'description_key' => 'enum.website_project_type.blog.desc',
                'icon' => 'heroicon-o-newspaper',
                'css_class' => 'text-green-500',
                'sort_order' => 3,
                'is_active' => true,
                'metadata' => json_encode(['color' => '#10b981']),
            ],
            [
                'type' => 'website_project_type',
                'key' => 'portfolio',
                'label_key' => 'enum.website_project_type.portfolio',
                'default_label' => 'Portfolio',
                'description_key' => 'enum.website_project_type.portfolio.desc',
                'icon' => 'heroicon-o-photo',
                'css_class' => 'text-pink-500',
                'sort_order' => 4,
                'is_active' => true,
                'metadata' => json_encode(['color' => '#ec4899']),
            ],
            [
                'type' => 'website_project_type',
                'key' => 'landing',
                'label_key' => 'enum.website_project_type.landing',
                'default_label' => 'Page d\'atterrissage',
                'description_key' => 'enum.website_project_type.landing.desc',
                'icon' => 'heroicon-o-rocket-launch',
                'css_class' => 'text-orange-500',
                'sort_order' => 5,
                'is_active' => true,
                'metadata' => json_encode(['color' => '#f97316']),
            ],
            [
                'type' => 'website_project_type',
                'key' => 'other',
                'label_key' => 'enum.website_project_type.other',
                'default_label' => 'Autre',
                'description_key' => 'enum.website_project_type.other.desc',
                'icon' => 'heroicon-o-ellipsis-horizontal',
                'css_class' => 'text-gray-500',
                'sort_order' => 6,
                'is_active' => true,
                'metadata' => json_encode(['color' => '#6b7280']),
            ],
        ];

        foreach ($enums as $enum) {
            DB::table('apt_global_enums')->insertOrIgnore($enum);
        }
    }
};
