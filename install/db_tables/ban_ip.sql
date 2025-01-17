CREATE TABLE `ban_ip` (
  `min` VARCHAR(39) NOT NULL,
  `max` VARCHAR(39) NOT NULL,
  KEY `min` (`min`,`max`)
) ENGINE=MyISAM DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;