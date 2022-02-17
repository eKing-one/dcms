CREATE TABLE `media_info` (
  `id` int(11) NOT NULL auto_increment,
  `file` varchar(64) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `lenght` varchar(32) NOT NULL,
  `bit` varchar(32) NOT NULL,
  `codec` varchar(32) DEFAULT NULL,
  `wh` varchar(32) DEFAULT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `file` (`file`,`size`)
) ENGINE=MyISAM DEFAULT CHARSET  utf8mb4 COLLATE  utf8mb4_unicode_ci;