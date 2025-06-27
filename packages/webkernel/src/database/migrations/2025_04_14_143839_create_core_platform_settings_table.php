<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1️⃣  Création de la table (seulement si elle n’existe pas déjà)
        if (! Schema::hasTable('core_platform_settings')) {
            Schema::create('core_platform_settings', function (Blueprint $table) {
                $table->id()->comment('Primary key for the settings table');
                $table->string('settings_reference', 100)->comment('Unique reference for each setting');
                $table->text('value')->nullable()->comment('Actual value for the setting (JSON for complex data)');
                $table->string('type', 50)->default('string')->comment('Data type: string, number, boolean, color, json');
                $table->string('category', 50)->comment('Setting category for organization');
                $table->string('name_lang_key', 50)->comment('Translation key for the setting name');
                $table->string('description_lang_key', 255)->nullable()->comment('Translation key for description');
                $table->json('validation_rules')->nullable()->comment('Validation rules as JSON');
                $table->json('metadata')->nullable()->comment('Additional metadata (min, max, options, etc.)');
                $table->boolean('is_public')->default(false)->comment('Whether setting can be accessed publicly');
                $table->boolean('requires_cache_clear')->default(false)->comment('Whether changing this setting requires cache clear');
                $table->unsignedBigInteger('tenant_id')->default(1)->comment('Tenant ID for multi-tenant context');
                $table->timestamps();

                // Indexes
                $table->unique(['settings_reference', 'tenant_id'], 'settings_ref_tenant_unique');
                $table->index('tenant_id', 'tenant_id_index');
                $table->index('category', 'category_index');
                $table->index('is_public', 'is_public_index');
            });
        }

        // 2️⃣  Données par défaut
        $defaultSettings = [
            // ---------- Platform Basic Settings ----------
            [
                'settings_reference'   => 'PLATFORM_NAME',
                'value'                => 'Mon Application',
                'type'                 => 'string',
                'category'             => 'general',
                'name_lang_key'        => 'settings.platform_name',
                'description_lang_key' => 'settings.platform_name_desc',
                'validation_rules'     => json_encode(['required', 'string', 'max:100']),
                'is_public'            => true,
                'requires_cache_clear' => true,
            ],
            [
                'settings_reference'   => 'PLATFORM_DESCRIPTION',
                'value'                => 'Description de mon application',
                'type'                 => 'text',
                'category'             => 'general',
                'name_lang_key'        => 'settings.platform_description',
                'description_lang_key' => 'settings.platform_description_desc',
                'is_public'            => true,
                'requires_cache_clear' => true,
            ],
            [
                'settings_reference'   => 'PLATFORM_LOGO',
                'value'                => '/images/logo.png',
                'type'                 => 'image',
                'category'             => 'branding',
                'name_lang_key'        => 'settings.platform_logo',
                'description_lang_key' => 'settings.platform_logo_desc',
                'is_public'            => true,
                'requires_cache_clear' => true,
            ],
            [
                'settings_reference'   => 'PLATFORM_FAVICON',
                'value'                => '/images/favicon.ico',
                'type'                 => 'image',
                'category'             => 'branding',
                'name_lang_key'        => 'settings.platform_favicon',
                'description_lang_key' => 'settings.platform_favicon_desc',
                'is_public'            => true,
                'requires_cache_clear' => true,
            ],

            // ---------- Theme & UI Settings ----------
            [
                'settings_reference'   => 'THEME_PRIMARY_COLOR',
                'value'                => '#3b82f6',
                'type'                 => 'color',
                'category'             => 'theme',
                'name_lang_key'        => 'settings.theme_primary_color',
                'description_lang_key' => 'settings.theme_primary_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],
            [
                'settings_reference'   => 'THEME_SECONDARY_COLOR',
                'value'                => '#64748b',
                'type'                 => 'color',
                'category'             => 'theme',
                'name_lang_key'        => 'settings.theme_secondary_color',
                'description_lang_key' => 'settings.theme_secondary_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],
            [
                'settings_reference'   => 'SIDEBAR_WIDTH',
                'value'                => '16',
                'type'                 => 'number',
                'category'             => 'layout',
                'name_lang_key'        => 'settings.sidebar_width',
                'description_lang_key' => 'settings.sidebar_width_desc',
                'metadata'             => json_encode(['min' => 12, 'max' => 24, 'unit' => 'rem']),
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],
            [
                'settings_reference'   => 'CONTENT_PADDING_X',
                'value'                => '1',
                'type'                 => 'number',
                'category'             => 'layout',
                'name_lang_key'        => 'settings.content_padding_x',
                'description_lang_key' => 'settings.content_padding_x_desc',
                'metadata'             => json_encode(['min' => 0, 'max' => 10, 'unit' => '%']),
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],
            [
                'settings_reference'   => 'CONTENT_PADDING_Y',
                'value'                => '2',
                'type'                 => 'number',
                'category'             => 'layout',
                'name_lang_key'        => 'settings.content_padding_y',
                'description_lang_key' => 'settings.content_padding_y_desc',
                'metadata'             => json_encode(['min' => 0, 'max' => 5, 'unit' => 'rem']),
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],

            // ---------- Sidebar & Loader ----------
            [
                'settings_reference'   => 'SIDEBAR_BORDER_WIDTH',
                'value'                => '0.1',
                'type'                 => 'number',
                'category'             => 'layout',
                'name_lang_key'        => 'settings.sidebar_border_width',
                'description_lang_key' => 'settings.sidebar_border_width_desc',
                'metadata'             => json_encode(['min' => 0, 'max' => 1, 'unit' => 'em']),
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],
            [
                'settings_reference'   => 'SIDEBAR_BORDER_COLOR',
                'value'                => 'rgba(var(--gray-200), 1)',
                'type'                 => 'color',
                'category'             => 'layout',
                'name_lang_key'        => 'settings.sidebar_border_color',
                'description_lang_key' => 'settings.sidebar_border_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],
            [
                'settings_reference'   => 'SIDEBAR_DARK_BORDER_COLOR',
                'value'                => 'rgba(var(--gray-800), 1)',
                'type'                 => 'color',
                'category'             => 'layout',
                'name_lang_key'        => 'settings.sidebar_dark_border_color',
                'description_lang_key' => 'settings.sidebar_dark_border_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],
            [
                'settings_reference'   => 'LOADER_BACKGROUND',
                'value'                => 'rgba(255, 255, 255, 0.05)',
                'type'                 => 'color',
                'category'             => 'layout',
                'name_lang_key'        => 'settings.loader_background',
                'description_lang_key' => 'settings.loader_background_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],
            [
                'settings_reference'   => 'LOADER_DARK_BACKGROUND',
                'value'                => 'rgba(0, 0, 0, 0.3)',
                'type'                 => 'color',
                'category'             => 'layout',
                'name_lang_key'        => 'settings.loader_dark_background',
                'description_lang_key' => 'settings.loader_dark_background_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],
            [
                'settings_reference'   => 'LOADER_BORDER_COLOR',
                'value'                => 'rgba(255, 255, 255, 0.12)',
                'type'                 => 'color',
                'category'             => 'layout',
                'name_lang_key'        => 'settings.loader_border_color',
                'description_lang_key' => 'settings.loader_border_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],
            [
                'settings_reference'   => 'LOADER_SPINNER_SIZE',          // ⚠️  clé corrigée ici
                'value'                => '3',
                'type'                 => 'number',
                'category'             => 'layout',
                'name_lang_key'        => 'settings.loader_spinner_size',
                'description_lang_key' => 'settings.loader_spinner_size_desc',
                'metadata'             => json_encode(['min' => 1, 'max' => 5, 'unit' => 'rem']),
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],
            [
                'settings_reference'   => 'LOADER_SPINNER_BORDER_WIDTH',
                'value'                => '4',
                'type'                 => 'number',
                'category'             => 'layout',
                'name_lang_key'        => 'settings.loader_spinner_border_width',
                'description_lang_key' => 'settings.loader_spinner_border_width_desc',
                'metadata'             => json_encode(['min' => 1, 'max' => 10, 'unit' => 'px']),
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],
            [
                'settings_reference'   => 'LOADER_SPINNER_BORDER_COLOR',
                'value'                => 'rgba(255, 255, 255, 0.2)',
                'type'                 => 'color',
                'category'             => 'layout',
                'name_lang_key'        => 'settings.loader_spinner_border_color',
                'description_lang_key' => 'settings.loader_spinner_border_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
            ],

            // ---------- PWA Settings ----------
            [
                'settings_reference'   => 'PWA_ENABLED',
                'value'                => 'true',
                'type'                 => 'boolean',
                'category'             => 'pwa',
                'name_lang_key'        => 'settings.pwa_enabled',
                'description_lang_key' => 'settings.pwa_enabled_desc',
                'is_public'            => true,
                'requires_cache_clear' => true,
            ],
            [
                'settings_reference'   => 'PWA_THEME_COLOR',
                'value'                => '#3b82f6',
                'type'                 => 'color',
                'category'             => 'pwa',
                'name_lang_key'        => 'settings.pwa_theme_color',
                'description_lang_key' => 'settings.pwa_theme_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => true,
            ],
            [
                'settings_reference'   => 'PWA_BACKGROUND_COLOR',
                'value'                => '#ffffff',
                'type'                 => 'color',
                'category'             => 'pwa',
                'name_lang_key'        => 'settings.pwa_background_color',
                'description_lang_key' => 'settings.pwa_background_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => true,
            ],
            [
                'settings_reference'   => 'PWA_ORIENTATION',
                'value'                => 'portrait-primary',
                'type'                 => 'select',
                'category'             => 'pwa',
                'name_lang_key'        => 'settings.pwa_orientation',
                'description_lang_key' => 'settings.pwa_orientation_desc',
                'metadata'             => json_encode(['options' => ['portrait-primary', 'landscape-primary', 'any']]),
                'is_public'            => true,
                'requires_cache_clear' => true,
            ],

            // ---------- Advanced Settings ----------
            [
                'settings_reference'   => 'PLATFORM_LICENCE',
                'value'                => 'TEST',
                'type'                 => 'string',
                'category'             => 'system',
                'name_lang_key'        => 'settings.platform_licence',
                'description_lang_key' => 'settings.platform_licence_desc',
                'validation_rules'     => json_encode(['required', 'string']),
                'is_public'            => false,
                'requires_cache_clear' => true,
            ],
            [
                'settings_reference'   => 'PLATFORM_ENVIRONMENT',
                'value'                => 'development',
                'type'                 => 'select',
                'category'             => 'system',
                'name_lang_key'        => 'settings.platform_environment',
                'description_lang_key' => 'settings.platform_environment_desc',
                'metadata'             => json_encode(['options' => ['development', 'staging', 'production']]),
                'is_public'            => false,
                'requires_cache_clear' => true,
            ],
        ];

        // 3️⃣  Insertion ou mise à jour
        foreach ($defaultSettings as $setting) {
            $setting['tenant_id']  = 1;
            $setting['created_at'] = now();
            $setting['updated_at'] = now();

            DB::table('core_platform_settings')->updateOrInsert(
                [
                    'settings_reference' => $setting['settings_reference'],
                    'tenant_id'          => $setting['tenant_id'],
                ],
                $setting
            );
        }
    }

    public function down()
    {
        Schema::dropIfExists('core_platform_settings');
    }
};
