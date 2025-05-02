# ************************************************************
# Antares - SQL Client
# Version 0.7.34
# 
# https://antares-sql.app/
# https://github.com/antares-sql/antares
# 
# Host: 127.0.0.1 (Debian n/a 11.8.1)
# Database: reprise
# Generation time: 2025-05-02T15:41:16+01:00
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table core_platform_settings
# ------------------------------------------------------------





# Dump of table render_hook_settings
# ------------------------------------------------------------

LOCK TABLES `render_hook_settings` WRITE;
/*!40000 ALTER TABLE `render_hook_settings` DISABLE KEYS */;

INSERT INTO `render_hook_settings` (`id`, `hook_key`, `icon`, `where_placed`, `scopes`, `translation_desc_key`, `view_path`, `enabled`, `customizable`, `customization_rel_ink`, `created_at`, `updated_at`) VALUES
	(7, "current_user_datetime", "heroicon-o-clock", "GLOBAL_SEARCH_BEFORE", NULL, "current_user_datetime_desc", "components.webkernel.ui.atoms.currentuserdatetime", 1, 1, NULL, "2025-05-02 02:13:57", "2025-05-02 02:13:57"),
	(8, "language_selector", "heroicon-o-language", "USER_MENU_BEFORE", NULL, "language_selector_desc", "components.webkernel.ui.molecules.language-selector", 1, 1, NULL, "2025-05-02 02:13:57", "2025-05-02 02:13:57"),
	(9, "search_hide", "heroicon-o-magnifying-glass", "GLOBAL_SEARCH_BEFORE", NULL, "search_hide_desc", "components.webkernel.ui.atoms.search-hide", 1, 1, NULL, "2025-05-02 02:13:57", "2025-05-02 02:13:57"),
	(10, "footer_partial", "heroicon-o-queue-list", "FOOTER", NULL, "footer_partial_desc", "components.partials.footer", 0, 1, NULL, "2025-05-02 02:13:57", "2025-05-02 02:13:57"),
	(11, "tenant_menu_after", "heroicon-o-building-storefront", "TENANT_MENU_AFTER", NULL, "tenant_menu_after_desc", "components.userpanels", 0, 1, NULL, "2025-05-02 02:13:57", "2025-05-02 02:13:57"),
	(12, "sidebar_collapsible", "heroicon-o-chevron-double-left", "SIDEBAR", NULL, "sidebar_collapsible_desc", "components.sidebar.collapsible", 1, 1, NULL, "2025-05-02 02:13:57", "2025-05-02 02:13:57");

/*!40000 ALTER TABLE `render_hook_settings` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table webkernel_lang
# ------------------------------------------------------------

LOCK TABLES `webkernel_lang` WRITE;
/*!40000 ALTER TABLE `webkernel_lang` DISABLE KEYS */;

INSERT INTO `webkernel_lang` (`id`, `code`, `ISO`, `label`, `is_active`, `created_at`, `updated_at`, `belongs_to`) VALUES
	(1, "en", "en-US", "English", 1, "2025-04-17 03:42:11", "2025-04-17 08:17:36", 1),
	(2, "fr", "fr-FR", "Français", 1, "2025-04-17 03:42:11", "2025-04-17 03:42:11", 1),
	(3, "ar", "ar-MA", "العربية", 1, "2025-04-17 03:42:11", "2025-04-17 05:31:17", 1);

/*!40000 ALTER TABLE `webkernel_lang` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table webkernel_lang_words
# ------------------------------------------------------------

LOCK TABLES `webkernel_lang_words` WRITE;
/*!40000 ALTER TABLE `webkernel_lang_words` DISABLE KEYS */;

INSERT INTO `webkernel_lang_words` (`id`, `lang`, `lang_ref`, `translation`, `app`, `theme`, `created_at`, `updated_at`) VALUES
	(1, 1, "numerimondes", "Numerimondes/", "core", "default", "2025-04-17 03:42:11", "2025-05-01 18:38:32"),
	(2, 2, "numerimondes", "Numerimondes", "core", "default", "2025-04-17 03:42:11", "2025-04-17 03:42:11"),
	(3, 3, "numerimondes", "Numerimondes", "core", "default", "2025-04-17 03:42:11", "2025-04-17 03:42:11"),
	(22, 1, "form_translation_key_label", "Translation Key", "core", "none", "2025-05-01 15:33:05", "2025-05-01 15:33:05"),
	(23, 2, "form_translation_key_label", "Clé de traduction", "core", "none", "2025-05-01 15:33:05", "2025-05-01 15:33:05"),
	(24, 3, "form_translation_key_label", "مفتاح الترجمة", "core", "none", "2025-05-01 15:33:05", "2025-05-01 15:33:05"),
	(25, 1, "form_translation_key_cleaned_notification_title", "Key Cleaned", "core", "none", "2025-05-01 15:34:31", "2025-05-01 15:34:31"),
	(26, 2, "form_translation_key_cleaned_notification_title", "Clé nettoyée", "core", "none", "2025-05-01 15:34:31", "2025-05-01 15:34:31"),
	(27, 3, "form_translation_key_cleaned_notification_title", "تم تنظيف الكلمة المفتاح", "core", "none", "2025-05-01 15:34:31", "2025-05-01 15:34:31"),
	(28, 1, "form_translation_key_cleaned_notification_body", "Key transformed to: :cleaned", "core", "none", "2025-05-01 15:36:52", "2025-05-01 15:36:52"),
	(29, 2, "form_translation_key_cleaned_notification_body", "Clé transformée en : :cleaned", "core", "none", "2025-05-01 15:36:52", "2025-05-01 15:36:52"),
	(30, 3, "form_translation_key_cleaned_notification_body", "تم تحويل الكلمة المفتاح إلى: :cleaned", "core", "none", "2025-05-01 15:36:52", "2025-05-01 15:36:52"),
	(31, 1, "form_translation_key_exists_notification_title", "Existing Key", "core", "none", "2025-05-01 15:38:41", "2025-05-01 15:38:41"),
	(32, 2, "form_translation_key_exists_notification_title", "Clé existante", "core", "none", "2025-05-01 15:38:41", "2025-05-01 15:38:41"),
	(33, 3, "form_translation_key_exists_notification_title", "الكلمة المفتاح موجودة", "core", "none", "2025-05-01 15:38:41", "2025-05-01 15:38:41"),
	(34, 1, "form_translation_key_exists_notification_body", "The key \':cleaned\' already exists. Existing translations have been loaded.", "core", "none", "2025-05-01 15:42:55", "2025-05-01 15:42:55"),
	(35, 2, "form_translation_key_exists_notification_body", "La clé \':cleaned\' existe déjà. Les traductions existantes ont été chargées.", "core", "none", "2025-05-01 15:42:55", "2025-05-01 15:42:55"),
	(36, 3, "form_translation_key_exists_notification_body", "الكلمة المفتاحية :cleaned كانت موجودة مسبقًا. تم تحميل ترجماتها.", "core", "none", "2025-05-01 15:42:55", "2025-05-01 15:45:00"),
	(37, 1, "form_translation_app_label", "Application", "core", "none", "2025-05-01 15:46:35", "2025-05-01 15:46:35"),
	(38, 2, "form_translation_app_label", "Application", "core", "none", "2025-05-01 15:46:35", "2025-05-01 15:46:35"),
	(39, 3, "form_translation_app_label", "التطبيق", "core", "none", "2025-05-01 15:46:35", "2025-05-01 15:46:35"),
	(40, 1, "desc_renderhooks_current_user_datetime", "Show the user\'s current date and time, with possible additional info (e.g., upcoming meetings or important events). This render hook can accept modifications.", "renderhooks", "none", "2025-05-01 18:58:59", "2025-05-01 18:58:59"),
	(41, 2, "desc_renderhooks_current_user_datetime", "Afficher la date et l\'heure actuelles de l\'utilisateur, avec des informations supplémentaires possibles (par exemple, des réunions ou événements importants à venir). Ce hook de rendu peut accepter des modifications.", "renderhooks", "none", "2025-05-01 18:58:59", "2025-05-01 18:58:59"),
	(42, 3, "desc_renderhooks_current_user_datetime", " عرض تاريخ ووقت المستخدم الحالي، مع معلومات إضافية محتملة (مثل الاجتماعات أو الأحداث المهمة القادمة). يمكن لهذا الـ hook قبول التعديلات.", "renderhooks", "none", "2025-05-01 18:58:59", "2025-05-01 18:58:59");

/*!40000 ALTER TABLE `webkernel_lang_words` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of views
# ------------------------------------------------------------

# Creating temporary tables to overcome VIEW dependency errors


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

# Dump completed on 2025-05-02T15:41:17+01:00
