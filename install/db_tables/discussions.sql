

CREATE TABLE IF NOT EXISTS `discussions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `avtor` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `count` int(11) DEFAULT '0',
  `msg` varchar(1024) NOT NULL,
  `time` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `id_sim` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
