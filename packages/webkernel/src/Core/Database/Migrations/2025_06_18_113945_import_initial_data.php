<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Activer les paramètres SQL nécessaires
        DB::statement('SET NAMES utf8mb4;');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";');
        DB::statement('SET SQL_NOTES=0;');

        // Insertion des données dans render_hook_settings
        DB::statement("
            INSERT INTO `render_hook_settings` (`id`, `hook_key`, `icon`, `where_placed`, `scopes`, `translation_desc_key`, `view_path`, `enabled`, `customizable`, `customization_rel_ink`, `created_at`, `updated_at`) VALUES
                (7, 'current_user_datetime', 'heroicon-o-clock', 'GLOBAL_SEARCH_BEFORE', NULL, 'current_user_datetime_desc', 'components.webkernel.ui.atoms.currentuserdatetime', 1, 1, NULL, '2025-05-02 02:13:57', '2025-06-01 17:26:29'),
                (8, 'language_selector', 'heroicon-o-language', 'USER_MENU_BEFORE', NULL, 'language_selector_desc', 'components.webkernel.ui.molecules.language-selector', 1, 1, NULL, '2025-05-02 02:13:57', '2025-05-27 14:31:18'),
                (9, 'search_hide', 'heroicon-o-magnifying-glass', 'GLOBAL_SEARCH_BEFORE', NULL, 'search_hide_desc', 'components.webkernel.ui.atoms.search-hide', 1, 1, NULL, '2025-05-02 02:13:57', '2025-05-27 14:24:42'),
                (10, 'footer_partial', 'heroicon-o-queue-list', 'FOOTER', NULL, 'footer_partial_desc', 'components.partials.footer', 0, 1, NULL, '2025-05-02 02:13:57', '2025-05-02 02:13:57'),
                (11, 'tenant_menu_after', 'heroicon-o-building-storefront', 'TENANT_MENU_AFTER', NULL, 'tenant_menu_after_desc', 'components.userpanels', 0, 1, NULL, '2025-05-02 02:13:57', '2025-05-02 02:13:57'),
                (12, 'sidebar_collapsible', 'heroicon-o-chevron-double-left', 'SIDEBAR', NULL, 'sidebar_collapsible_desc', 'components.sidebar.collapsible', 1, 1, NULL, '2025-05-02 02:13:57', '2025-05-02 02:13:57')
        ");

        // Insertion des données dans webkernel_lang
        DB::statement("
            INSERT INTO `webkernel_lang` (`id`, `code`, `ISO`, `label`, `is_active`, `created_at`, `updated_at`, `tenant_id`) VALUES
                (1, 'en', 'en-US', 'English', 1, '2025-04-17 03:42:11', '2025-04-17 08:17:36', 1),
                (2, 'fr', 'fr-FR', 'Français', 1, '2025-04-17 03:42:11', '2025-04-17 03:42:11', 1),
                (3, 'ar', 'ar-MA', 'العربية', 1, '2025-04-17 03:42:11', '2025-04-17 05:31:17', 1)
        ");

        // Insertion des données dans webkernel_lang_words
        DB::statement("
            INSERT INTO `webkernel_lang_words` (`id`, `lang`, `lang_ref`, `translation`, `app`, `theme`, `created_at`, `updated_at`) VALUES
                (1, 1, 'numerimondes', 'Numerimondes/', 'core', 'default', '2025-04-17 03:42:11', '2025-05-01 18:38:32'),
                (2, 2, 'numerimondes', 'Numerimondes', 'core', 'default', '2025-04-17 03:42:11', '2025-04-17 03:42:11'),
                (3, 3, 'numerimondes', 'Numerimondes', 'core', 'default', '2025-04-17 03:42:11', '2025-04-17 03:42:11'),
                (37, 1, 'form_translation_app_label', 'Application', 'core', 'none', '2025-05-01 15:46:35', '2025-05-01 15:46:35'),
                (38, 2, 'form_translation_app_label', 'Application', 'core', 'none', '2025-05-01 15:46:35', '2025-05-01 15:46:35'),
                (39, 3, 'form_translation_app_label', 'التطبيق', 'core', 'none', '2025-05-01 15:46:35', '2025-05-01 15:46:35')
        ");

        // Réactiver les contraintes
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::statement('SET SQL_NOTES=1;');
    }

    public function down()
    {
        // Suppression des données
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('DELETE FROM `render_hook_settings` WHERE `id` IN (7, 8, 9, 10, 11, 12);');
        DB::statement('DELETE FROM `webkernel_lang` WHERE `id` IN (1, 2, 3);');
        DB::statement('DELETE FROM `webkernel_lang_words` WHERE `id` IN (1, 2, 3, 37, 38, 39);');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
