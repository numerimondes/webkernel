<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('core_platform_settings')) {
            Schema::create('core_platform_settings', function (Blueprint $table) {
                $table->id()->comment('Primary key for the settings table');
                $table->string('settings_reference', 100)->comment('Unique reference for each setting');
                $table->text('value')->nullable()->comment('Actual value for the setting');
                $table->string('type', 50)->default('string')->comment('Data type: string, number, boolean, color, json, select');
                $table->string('category', 50)->comment('Setting category for organization');
                $table->string('name_lang_key', 50)->comment('Translation key for the setting name');
                $table->string('description_lang_key', 255)->nullable()->comment('Translation key for description');
                $table->json('validation_rules')->nullable()->comment('Validation rules as JSON');
                $table->json('metadata')->nullable()->comment('Additional metadata (min, max, options, etc.)');
                $table->boolean('is_public')->default(false)->comment('Whether setting can be accessed publicly');
                $table->boolean('requires_cache_clear')->default(false)->comment('Whether changing this setting requires cache clear');
                $table->boolean('is_fixed_order')->default(false)->comment('Whether this setting has fixed position');
                $table->integer('display_order')->default(999)->comment('Display order for settings');
                $table->string('card_group')->nullable()->comment('Card group for organizing related settings');
                $table->unsignedBigInteger('tenant_id')->default(1)->comment('Tenant ID for multi-tenant context');
                $table->timestamps();

                $table->unique(['settings_reference', 'tenant_id'], 'settings_ref_tenant_unique');
                $table->index('tenant_id', 'tenant_id_index');
                $table->index('category', 'category_index');
                $table->index('is_public', 'is_public_index');
                $table->index(['display_order', 'is_fixed_order'], 'display_order_index');
                $table->index('card_group', 'card_group_index');
            });
        } else {
            Schema::table('core_platform_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('core_platform_settings', 'is_fixed_order')) {
                    $table->boolean('is_fixed_order')->default(false)->comment('Whether this setting has fixed position');
                }
                if (!Schema::hasColumn('core_platform_settings', 'display_order')) {
                    $table->integer('display_order')->default(999)->comment('Display order for settings');
                    $table->index(['display_order', 'is_fixed_order'], 'display_order_index');
                }
                if (!Schema::hasColumn('core_platform_settings', 'card_group')) {
                    $table->string('card_group')->nullable()->comment('Card group for organizing related settings');
                    $table->index('card_group', 'card_group_index');
                }
            });
        }

        $defaultSettings = [
            // Fixed order settings (priority settings)
            [
                'settings_reference'   => 'PLATFORM_LICENCE',
                'value'                => 'TEST',
                'type'                 => 'string',
                'category'             => 'system',
                'name_lang_key'        => 'settings_platform_licence',
                'description_lang_key' => 'settings_platform_licence_desc',
                'validation_rules'     => json_encode(['required', 'string']),
                'is_public'            => false,
                'requires_cache_clear' => true,
                'is_fixed_order'       => true,
                'display_order'        => 1,
                'card_group'           => null,
            ],
            [
                'settings_reference'   => 'PLATFORM_ENVIRONMENT',
                'value'                => 'development',
                'type'                 => 'select',
                'category'             => 'system',
                'name_lang_key'        => 'settings_platform_environment',
                'description_lang_key' => 'settings_platform_environment_desc',
                'metadata'             => json_encode(['options' => ['development', 'staging', 'production']]),
                'is_public'            => false,
                'requires_cache_clear' => true,
                'is_fixed_order'       => true,
                'display_order'        => 2,
                'card_group'           => null,
            ],
            // General Layout Card Group
            [
                'settings_reference'   => 'GENERAL_LAYOUT',
                'value'                => 'with_side_bar_full',
                'type'                 => 'select',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_general_layout',
                'description_lang_key' => 'settings_general_layout_desc',
                'metadata'             => json_encode(['options' => ['with_side_bar_full', 'minimalis_side_bar']]),
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 10,
                'card_group'           => 'layout_general',
            ],
            [
                'settings_reference'   => 'SIDEBAR_WIDTH',
                'value'                => '16',
                'type'                 => 'number',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_sidebar_width',
                'description_lang_key' => 'settings_sidebar_width_desc',
                'metadata'             => json_encode(['min' => 12, 'max' => 24, 'unit' => 'rem']),
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 11,
                'card_group'           => 'layout_general',
            ],
            [
                'settings_reference'   => 'CONTENT_PADDING_X',
                'value'                => '1',
                'type'                 => 'number',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_content_padding_x',
                'description_lang_key' => 'settings_content_padding_x_desc',
                'metadata'             => json_encode(['min' => 0, 'max' => 10, 'unit' => '%']),
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 12,
                'card_group'           => 'layout_general',
            ],
            [
                'settings_reference'   => 'CONTENT_PADDING_Y',
                'value'                => '2',
                'type'                 => 'number',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_content_padding_y',
                'description_lang_key' => 'settings_content_padding_y_desc',
                'metadata'             => json_encode(['min' => 0, 'max' => 5, 'unit' => 'rem']),
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 13,
                'card_group'           => 'layout_general',
            ],
            // Branding Card Group
            [
                'settings_reference'   => 'PLATFORM_NAME',
                'value'                => 'Mon Application',
                'type'                 => 'string',
                'category'             => 'general',
                'name_lang_key'        => 'settings_platform_name',
                'description_lang_key' => 'settings_platform_name_desc',
                'validation_rules'     => json_encode(['required', 'string', 'max:100']),
                'is_public'            => true,
                'requires_cache_clear' => true,
                'is_fixed_order'       => false,
                'display_order'        => 20,
                'card_group'           => 'branding',
            ],
            [
                'settings_reference'   => 'PLATFORM_DESCRIPTION',
                'value'                => 'Description de mon application',
                'type'                 => 'text',
                'category'             => 'general',
                'name_lang_key'        => 'settings_platform_description',
                'description_lang_key' => 'settings_platform_description_desc',
                'is_public'            => true,
                'requires_cache_clear' => true,
                'is_fixed_order'       => false,
                'display_order'        => 21,
                'card_group'           => 'branding',
            ],
            [
                'settings_reference'   => 'PLATFORM_LOGO',
                'value'                => '/images/logo.png',
                'type'                 => 'image',
                'category'             => 'branding',
                'name_lang_key'        => 'settings_platform_logo',
                'description_lang_key' => 'settings_platform_logo_desc',
                'is_public'            => true,
                'requires_cache_clear' => true,
                'is_fixed_order'       => false,
                'display_order'        => 22,
                'card_group'           => 'branding',
            ],
            [
                'settings_reference'   => 'PLATFORM_FAVICON',
                'value'                => '/images/favicon.ico',
                'type'                 => 'image',
                'category'             => 'branding',
                'name_lang_key'        => 'settings_platform_favicon',
                'description_lang_key' => 'settings_platform_favicon_desc',
                'is_public'            => true,
                'requires_cache_clear' => true,
                'is_fixed_order'       => false,
                'display_order'        => 23,
                'card_group'           => 'branding',
            ],
            // Theme Colors Card Group
            [
                'settings_reference'   => 'THEME_PRIMARY_COLOR',
                'value'                => '#3b82f6',
                'type'                 => 'color',
                'category'             => 'theme',
                'name_lang_key'        => 'settings_theme_primary_color',
                'description_lang_key' => 'settings_theme_primary_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 30,
                'card_group'           => 'theme_colors',
            ],
            [
                'settings_reference'   => 'THEME_SECONDARY_COLOR',
                'value'                => '#64748b',
                'type'                 => 'color',
                'category'             => 'theme',
                'name_lang_key'        => 'settings_theme_secondary_color',
                'description_lang_key' => 'settings_theme_secondary_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 31,
                'card_group'           => 'theme_colors',
            ],
            // Sidebar Style Card Group
            [
                'settings_reference'   => 'SIDEBAR_BORDER_WIDTH',
                'value'                => '0.1',
                'type'                 => 'number',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_sidebar_border_width',
                'description_lang_key' => 'settings_sidebar_border_width_desc',
                'metadata'             => json_encode(['min' => 0, 'max' => 1, 'unit' => 'em']),
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 40,
                'card_group'           => 'sidebar_style',
            ],
            [
                'settings_reference'   => 'SIDEBAR_BORDER_COLOR',
                'value'                => 'rgba(var(--gray-200), 1)',
                'type'                 => 'color',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_sidebar_border_color',
                'description_lang_key' => 'settings_sidebar_border_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 41,
                'card_group'           => 'sidebar_style',
            ],
            [
                'settings_reference'   => 'SIDEBAR_DARK_BORDER_COLOR',
                'value'                => 'rgba(var(--gray-800), 1)',
                'type'                 => 'color',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_sidebar_dark_border_color',
                'description_lang_key' => 'settings_sidebar_dark_border_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 42,
                'card_group'           => 'sidebar_style',
            ],
            // Loader Settings Card Group
            [
                'settings_reference'   => 'LOADER_BACKGROUND',
                'value'                => 'rgba(255, 255, 255, 0.05)',
                'type'                 => 'color',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_loader_background',
                'description_lang_key' => 'settings_loader_background_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 50,
                'card_group'           => 'loader_settings',
            ],
            [
                'settings_reference'   => 'LOADER_DARK_BACKGROUND',
                'value'                => 'rgba(0, 0, 0, 0.3)',
                'type'                 => 'color',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_loader_dark_background',
                'description_lang_key' => 'settings_loader_dark_background_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 51,
                'card_group'           => 'loader_settings',
            ],
            [
                'settings_reference'   => 'LOADER_BORDER_COLOR',
                'value'                => 'rgba(255, 255, 255, 0.12)',
                'type'                 => 'color',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_loader_border_color',
                'description_lang_key' => 'settings_loader_border_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 52,
                'card_group'           => 'loader_settings',
            ],
            [
                'settings_reference'   => 'LOADER_SPINNER_SIZE',
                'value'                => '3',
                'type'                 => 'number',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_loader_spinner_size',
                'description_lang_key' => 'settings_loader_spinner_size_desc',
                'metadata'             => json_encode(['min' => 1, 'max' => 5, 'unit' => 'rem']),
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 53,
                'card_group'           => 'loader_settings',
            ],
            [
                'settings_reference'   => 'LOADER_SPINNER_BORDER_WIDTH',
                'value'                => '4',
                'type'                 => 'number',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_loader_spinner_border_width',
                'description_lang_key' => 'settings_loader_spinner_border_width_desc',
                'metadata'             => json_encode(['min' => 1, 'max' => 10, 'unit' => 'px']),
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 54,
                'card_group'           => 'loader_settings',
            ],
            [
                'settings_reference'   => 'LOADER_SPINNER_BORDER_COLOR',
                'value'                => 'rgba(255, 255, 255, 0.2)',
                'type'                 => 'color',
                'category'             => 'layout',
                'name_lang_key'        => 'settings_loader_spinner_border_color',
                'description_lang_key' => 'settings_loader_spinner_border_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => false,
                'is_fixed_order'       => false,
                'display_order'        => 55,
                'card_group'           => 'loader_settings',
            ],
            // PWA Settings Card Group
            [
                'settings_reference'   => 'PWA_ENABLED',
                'value'                => 'true',
                'type'                 => 'boolean',
                'category'             => 'pwa',
                'name_lang_key'        => 'settings_pwa_enabled',
                'description_lang_key' => 'settings_pwa_enabled_desc',
                'is_public'            => true,
                'requires_cache_clear' => true,
                'is_fixed_order'       => false,
                'display_order'        => 60,
                'card_group'           => 'pwa_settings',
            ],
            [
                'settings_reference'   => 'PWA_THEME_COLOR',
                'value'                => '#3b82f6',
                'type'                 => 'color',
                'category'             => 'pwa',
                'name_lang_key'        => 'settings_pwa_theme_color',
                'description_lang_key' => 'settings_pwa_theme_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => true,
                'is_fixed_order'       => false,
                'display_order'        => 61,
                'card_group'           => 'pwa_settings',
            ],
            [
                'settings_reference'   => 'PWA_BACKGROUND_COLOR',
                'value'                => '#ffffff',
                'type'                 => 'color',
                'category'             => 'pwa',
                'name_lang_key'        => 'settings_pwa_background_color',
                'description_lang_key' => 'settings_pwa_background_color_desc',
                'is_public'            => true,
                'requires_cache_clear' => true,
                'is_fixed_order'       => false,
                'display_order'        => 62,
                'card_group'           => 'pwa_settings',
            ],
            [
                'settings_reference'   => 'PWA_ORIENTATION',
                'value'                => 'portrait-primary',
                'type'                 => 'select',
                'category'             => 'pwa',
                'name_lang_key'        => 'settings_pwa_orientation',
                'description_lang_key' => 'settings_pwa_orientation_desc',
                'metadata'             => json_encode(['options' => ['portrait-primary', 'landscape-primary', 'any']]),
                'is_public'            => true,
                'requires_cache_clear' => true,
                'is_fixed_order'       => false,
                'display_order'        => 63,
                'card_group'           => 'pwa_settings',
            ],
        ];


        foreach ($defaultSettings as $setting) {
            $setting['tenant_id'] = 1;
            $setting['created_at'] = now();
            $setting['updated_at'] = now();

            DB::table('core_platform_settings')->updateOrInsert(
                [
                    'settings_reference' => $setting['settings_reference'],
                    'tenant_id' => $setting['tenant_id'],
                ],
                $setting
            );
        }
    }

    public function down()
    {
        if (Schema::hasTable('core_platform_settings')) {
            Schema::table('core_platform_settings', function (Blueprint $table) {
                $table->dropIndex('display_order_index');
                $table->dropIndex('card_group_index');
                $table->dropColumn(['is_fixed_order', 'display_order', 'card_group']);
            });
        }

        Schema::dropIfExists('core_platform_settings');
    }
};
