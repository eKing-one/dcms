CREATE TABLE `forum_files` (
  `id` int(11) NOT NULL auto_increment,
  `id_post` int(11) NOT NULL,
  `name` varchar(64) default NULL,
  `ras` varchar(32) NOT NULL,
  `size` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `count` int(11) NOT NULL default '0',
  `rating` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id_post` (`id_post`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;