DROP TABLE IF EXISTS `core_platform_settings`;

CREATE TABLE `core_platform_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `settings_reference` varchar(255) NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`value`)),
  `default_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`default_value`)),
  `icon` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `tenant_id` int(11) NOT NULL DEFAULT 1,
  `module` varchar(255) DEFAULT NULL,
  `type` enum('tinyint','smallint','mediumint','int','bigint','float','double','decimal','char','varchar','text','tinytext','mediumtext','longtext','date','datetime','timestamp','time','year','binary','varbinary','blob','tinyblob','mediumblob','longblob','enum','set','json','point','linestring','polygon','geometry','geometrycollection','string','boolean','array','file','uuid','email','url','image','currency','phone') NOT NULL DEFAULT 'string',
  `is_editable` tinyint(1) NOT NULL DEFAULT 0,
  `belongs_to` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `core_platform_settings_settings_reference_unique` (`settings_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `webkernel_lang`;

CREATE TABLE `webkernel_lang` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `ISO` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `belongs_to` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webkernel_lang_code_unique` (`code`),
  UNIQUE KEY `webkernel_lang_iso_unique` (`ISO`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `webkernel_lang` WRITE;

INSERT INTO `webkernel_lang` (`id`, `code`, `ISO`, `label`, `is_active`, `created_at`, `updated_at`, `belongs_to`) VALUES
	(1, "en", "en-US", "English", 1, "2025-04-17 03:42:11", "2025-04-17 08:17:36", 1),
	(2, "fr", "fr-FR", "Français", 1, "2025-04-17 03:42:11", "2025-04-17 03:42:11", 1),
	(3, "ar", "ar-MA", "العربية", 1, "2025-04-17 03:42:11", "2025-04-17 05:31:17", 1);

UNLOCK TABLES;

DROP TABLE IF EXISTS `webkernel_lang_words`;

CREATE TABLE `webkernel_lang_words` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lang` bigint(20) unsigned NOT NULL,
  `lang_ref` varchar(255) NOT NULL,
  `translation` text DEFAULT NULL,
  `app` varchar(255) NOT NULL DEFAULT 'core',
  `theme` varchar(255) NOT NULL DEFAULT 'none',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `belongs_to` bigint(20) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webkernel_lang_words_lang_ref_lang_app_theme_unique` (`lang_ref`,`lang`,`app`,`theme`),
  KEY `webkernel_lang_words_lang_foreign` (`lang`),
  KEY `webkernel_lang_words_lang_ref_lang_index` (`lang_ref`,`lang`),
  KEY `webkernel_lang_words_app_theme_index` (`app`,`theme`),
  KEY `webkernel_lang_words_belongs_to_index` (`belongs_to`),
  CONSTRAINT `webkernel_lang_words_lang_foreign` FOREIGN KEY (`lang`) REFERENCES `webkernel_lang` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `webkernel_lang_words` WRITE;

INSERT INTO `webkernel_lang_words` (`id`, `lang`, `lang_ref`, `translation`, `app`, `theme`, `created_at`, `updated_at`, `belongs_to`) VALUES
	(1, 1, "numerimondes", "Numerimondes", "core", "default", "2025-04-17 03:42:11", "2025-04-17 03:42:11", 1),
	(2, 2, "numerimondes", "Numerimondes", "core", "default", "2025-04-17 03:42:11", "2025-04-17 03:42:11", 1),
	(3, 3, "numerimondes", "Numerimondes", "core", "default", "2025-04-17 03:42:11", "2025-04-17 03:42:11", 1),
	(4, 1, "core", "Core of the application", "core", "none", "2025-04-17 03:51:41", "2025-04-17 03:51:41", 1),
	(5, 2, "core", "Coeur de l\'application", "core", "none", "2025-04-17 03:51:41", "2025-04-17 03:51:41", 1),
	(6, 3, "core", "Coeur de l\'application", "core", "none", "2025-04-17 03:51:41", "2025-04-17 05:56:30", 1),
	(7, 1, "col_translation_app", "Belongs to app", "core", "none", "2025-04-17 05:09:44", "2025-04-17 05:10:06", 1),
	(8, 2, "col_translation_app", "Appartient à l\'application", "core", "none", "2025-04-17 05:09:44", "2025-04-17 05:10:06", 1),
	(9, 3, "col_translation_app", "ينتمي إلى التطبيق", "core", "none", "2025-04-17 05:09:44", "2025-04-17 05:10:06", 1),
	(10, 1, "repeater_title_translation", "Translation", "core", "none", "2025-04-17 05:30:32", "2025-04-17 05:30:32", 1),
	(11, 2, "repeater_title_translation", "Traduction", "core", "none", "2025-04-17 05:30:32", "2025-04-17 05:30:32", 1),
	(12, 3, "repeater_title_translation", "ترجمة", "core", "none", "2025-04-17 05:30:32", "2025-04-17 05:30:32", 1),
	(13, 1, "available_languages", "Available Languages OUDZLFZPEKFEZKPFKPEZ", "core", "none", "2025-04-17 06:16:52", "2025-04-17 17:40:01", 1),
	(14, 2, "available_languages", "Langues Disponibles", "core", "none", "2025-04-17 06:16:52", "2025-04-17 06:16:52", 1),
	(15, 3, "available_languages", "اللغات المتوفرة", "core", "none", "2025-04-17 06:16:52", "2025-04-17 06:16:52", 1),
	(16, 1, "memory_usage", "Memory usage  :name", "core", "none", "2025-04-17 17:30:14", "2025-04-17 17:39:30", 1),
	(17, 2, "memory_usage", "Usage de la mémoire", "core", "none", "2025-04-17 17:30:14", "2025-04-17 17:30:14", 1),
	(18, 3, "memory_usage", "استخدام الذاكرة", "core", "none", "2025-04-17 17:30:14", "2025-04-17 17:30:14", 1);

UNLOCK TABLES;


