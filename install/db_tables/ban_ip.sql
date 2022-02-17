CREATE TABLE `ban_ip` (
  `min` bigint(20) NOT NULL,
  `max` bigint(20) NOT NULL,
  KEY `min` (`min`,`max`)
) ENGINE=MyISAM DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;