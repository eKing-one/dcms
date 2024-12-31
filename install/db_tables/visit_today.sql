CREATE TABLE IF NOT EXISTS `visit_today` (
  `ip` bigint(11) NOT NULL,
  `ua` varchar(512) DEFAULT NULL,
  `ua_hash` BINARY(16) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  KEY `ip` (`ip`),
  KEY `ua_hash` (`ua_hash`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET utf8mb4 COLLATE  utf8mb4_unicode_ci;
