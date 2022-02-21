CREATE TABLE IF NOT EXISTS `user_music` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `dir` varchar(64) NOT NULL,
  `id_file` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci AUTO_INCREMENT=1;
