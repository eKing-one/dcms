CREATE TABLE `stena_komm` (
  `id` int(11) auto_increment,
  `id_user` int(11) default NULL,
  `msg` varchar(1024) default NULL,
  `time` int(11) default NULL,
  `id_stena` int(11) default NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;