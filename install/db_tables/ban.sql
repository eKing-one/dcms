
CREATE TABLE IF NOT EXISTS `ban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_ban` int(11) NOT NULL,
  `prich` varchar(1024) NOT NULL,
  `view` set('1','0') NOT NULL DEFAULT '0',
  `razdel` varchar(10) DEFAULT 'all',
  `post` int(1) DEFAULT '0',
  `pochemu` int(11) DEFAULT '0',
  `navsegda` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`,`id_ban`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
