CREATE TABLE `news` (
  `id` int(11) NOT NULL auto_increment,
  `msg` varchar(10024) CHARSET utf8mb4 COLLATE  utf8mb4_unicode_ci default NULL,
  `time` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `title` varchar(32) default NULL,
  `main_time` int(11) NOT NULL default '0',
  `link` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;