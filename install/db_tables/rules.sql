CREATE TABLE IF NOT EXISTS `rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` mediumtext,
  `time` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `title` varchar(60) DEFAULT NULL,
  `url` varchar(999) DEFAULT NULL,
  `name_url` varchar(52) DEFAULT NULL,
  `pos` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `rules_p` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `msg` mediumtext NOT NULL,
  `time` int(11) NOT NULL,
  `id_news` int(11) NOT NULL,
  `pos` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;