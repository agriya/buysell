# Dump of table social
# ------------------------------------------------------------

DROP TABLE IF EXISTS `social`;

CREATE TABLE `social` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `service` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `uid` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `social_service_uid_unique` (`service`,`uid`),
  UNIQUE KEY `social_user_id_service_unique` (`user_id`,`service`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
