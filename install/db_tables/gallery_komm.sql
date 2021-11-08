CREATE TABLE `gallery_komm` (
  `id` int(11) NOT NULL auto_increment,
  `id_foto` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `time` int(11) DEFAULT NULL,
  `msg` varchar(1024) DEFAULT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_foto` (`id_foto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;