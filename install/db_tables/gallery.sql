
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `time_create` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `opis` varchar(256) DEFAULT NULL,
  `set_password` int(11) DEFAULT NULL,
  `foto_password` varchar(32) DEFAULT NULL,
  `my` int(11) NOT NULL DEFAULT '0',
  `pass` varchar(11) DEFAULT NULL,
  `privat` enum('0','1','2') NOT NULL DEFAULT '0',
  `privat_komm` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

