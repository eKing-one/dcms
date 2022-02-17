
CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `msg` varchar(1024)  CHARSET utf8mb4 COLLATE  utf8mb4_unicode_ci NOT NULL,
  `like` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `pokaz` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET utf8mb4 COLLATE  utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `status_komm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `msg` varchar(1024) CHARSET utf8mb4 COLLATE  utf8mb4_unicode_ci NOT NULL,
  `like` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `id_status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET utf8mb4 COLLATE  utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `status_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `id_status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET utf8mb4 COLLATE  utf8mb4_unicode_ci AUTO_INCREMENT=1 ;
