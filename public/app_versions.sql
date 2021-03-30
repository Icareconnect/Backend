-- Adminer 4.7.6 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `app_versions`;
CREATE TABLE `app_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_type` tinyint(4) NOT NULL COMMENT '1: iOS, 2: Android',
  `app_type` tinyint(4) NOT NULL COMMENT '1: User App, 2: Doctor App',
  `version_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` bigint(20) NOT NULL,
  `update_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 no_update 1: Minor, 2: Major',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `app_versions` (`id`, `device_type`, `app_type`, `version_name`, `version`, `update_type`, `created_at`, `updated_at`) VALUES
(1,	2,	1,	'1.0.0-beta',	1,	1,	'2020-04-04 00:27:18',	'2020-04-04 00:27:18'),
(2,	2,	2,	'1.0.0-beta',	1,	1,	'2020-04-04 00:27:18',	'2020-04-04 00:27:18'),
(3,	1,	1,	'1.0.1-beta',	100,	1,	'2020-04-04 00:27:18',	'2020-04-04 00:27:18'),
(4,	1,	2,	'1.0.0-beta',	100,	1,	'2020-04-04 00:27:18',	'2020-04-04 00:27:18');

-- 2020-06-23 11:55:08
