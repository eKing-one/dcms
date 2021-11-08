CREATE TABLE `reg_mail` (
  `id` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL,
  `mail` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `mail` (`mail`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
