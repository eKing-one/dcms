CREATE TABLE `stena_komm` (
  `id` int(11) auto_increment,
  `id_user` int(11) default NULL,
  `msg` varchar(1024) CHARSET utf8mb4 COLLATE  utf8mb4_unicode_ci default NULL,
  `time` int(11) default NULL,
  `id_stena` int(11) default NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET utf8mb4 COLLATE  utf8mb4_unicode_ci  AUTO_INCREMENT=1;