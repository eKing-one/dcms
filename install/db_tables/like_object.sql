
CREATE TABLE IF NOT EXISTS `like_object` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `type` varchar(11) DEFAULT NULL,
  `like` set('1','0') NOT NULL DEFAULT '0',
  `id_object` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
