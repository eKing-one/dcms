
CREATE TABLE IF NOT EXISTS `my_guests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ank` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `read` enum('0','1') NOT NULL DEFAULT '1',
  `count` int(11) NOT NULL DEFAULT '1',
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
