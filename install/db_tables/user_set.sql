

CREATE TABLE IF NOT EXISTS `user_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `privat_str` int(11) DEFAULT '1',
  `privat_mail` int(11) DEFAULT '1',
  `ocenka` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
