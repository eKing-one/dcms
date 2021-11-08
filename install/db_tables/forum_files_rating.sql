CREATE TABLE `forum_files_rating` (
  `id_file` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rating` int(11) default '0',
  KEY `id_file` (`id_file`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;