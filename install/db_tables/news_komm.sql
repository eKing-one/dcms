CREATE TABLE `news_komm` (
  `id` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL,
  `msg` varchar(1024) NOT NULL,
  `time` int(11) DEFAULT NULL,
  `id_news` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;