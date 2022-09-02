CREATE TABLE IF NOT EXISTS `gallery_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_gallery` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `ras` varchar(4) NOT NULL,
  `type` varchar(64) NOT NULL,
  `opis` varchar(1024) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL,
  `avatar` enum('0','1') DEFAULT '0',
  `pass` varchar(11) DEFAULT NULL,
  `people` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `metka` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_gallery` (`id_gallery`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;