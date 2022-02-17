CREATE TABLE `guest` (
  `id` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL,
  `msg` varchar(1024) CHARSET utf8mb4 COLLATE  utf8mb4_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;