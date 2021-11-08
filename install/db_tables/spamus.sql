

CREATE TABLE IF NOT EXISTS `spamus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `msg` varchar(512) DEFAULT NULL,
  `razdel` varchar(40) DEFAULT NULL,
  `id_spam` int(11) NOT NULL,
  `types` int(11) DEFAULT '0',
  `time` int(12) DEFAULT NULL,
  `id_post` int(111) DEFAULT NULL,
  `spam` varchar(1000) DEFAULT NULL,
  `id_object` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
