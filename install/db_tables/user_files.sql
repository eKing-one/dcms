CREATE TABLE IF NOT EXISTS `user_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `msg` varchar(256) DEFAULT NULL,
  `id_dir` int(11) DEFAULT NULL,
  `osn` int(1) DEFAULT '0',
  `type` varchar(20) DEFAULT NULL,
  `id_dires` varchar(215) DEFAULT '/',
  `pass` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci AUTO_INCREMENT=1;
