
CREATE TABLE IF NOT EXISTS `tape` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `id_file` int(11) NOT NULL,
  `avtor` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `type` varchar(24) NOT NULL,
  `read` set('0','1') NOT NULL DEFAULT '0',
  `avatar` int(11) DEFAULT '0',
  `ot_kogo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`,`avtor`),
  KEY `read` (`read`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tape_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `lenta_status_like` int(11) DEFAULT '1',
  `lenta_status` int(11) DEFAULT '1',
  `lenta_foto` int(11) DEFAULT '1',
  `lenta_files` int(11) DEFAULT '1',
  `lenta_forum` int(11) DEFAULT '1',
  `lenta_notes` int(11) DEFAULT '1',
  `lenta_avatar` int(11) DEFAULT '1',
  `lenta_frends` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
