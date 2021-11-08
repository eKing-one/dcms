
CREATE TABLE IF NOT EXISTS `frends` (
  `user` int(11) NOT NULL DEFAULT '0',
  `frend` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL,
  `i` int(1) DEFAULT '0',
  `lenta_forum` int(11) NOT NULL DEFAULT '1',
  `lenta_obmen` int(11) NOT NULL DEFAULT '1',
  `lenta_foto` int(11) NOT NULL DEFAULT '1',
  `lenta_notes` int(11) NOT NULL DEFAULT '1',
  `lenta_avatar` int(11) DEFAULT '1',
  `lenta_frends` int(1) DEFAULT '1',
  `lenta_status` int(1) DEFAULT '1',
  `lenta_status_like` int(1) DEFAULT '1',
  `disc_forum` int(11) NOT NULL DEFAULT '1',
  `disc_obmen` int(11) NOT NULL DEFAULT '1',
  `disc_foto` int(11) NOT NULL DEFAULT '1',
  `disc_notes` int(11) NOT NULL DEFAULT '1',
  `disc_frends` int(1) DEFAULT '1',
  `disc_status` int(1) DEFAULT '1',
  PRIMARY KEY (`user`,`frend`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `frends_new` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT '0',
  `to` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
