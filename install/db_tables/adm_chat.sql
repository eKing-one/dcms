
CREATE TABLE IF NOT EXISTS `adm_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL,
  `msg` varchar(1024) CHARSET  utf8mb4 COLLATE  utf8mb4_unicode_ci  DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET  utf8mb4 COLLATE  utf8mb4_unicode_ci AUTO_INCREMENT=1 ;
