CREATE TABLE `forum_r` (
  `id` int(11) NOT NULL auto_increment,
  `id_forum` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `time` int(11) NOT NULL,
  `opis` varchar(256) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_forum` (`id_forum`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;