CREATE TABLE `guest` (
  `id` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL,
  `msg` varchar(1024) character set utf8 collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;