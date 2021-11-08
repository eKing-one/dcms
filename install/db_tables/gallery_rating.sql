
CREATE TABLE IF NOT EXISTS `gallery_rating` (
  `id_foto` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `like` int(11) NOT NULL DEFAULT '0',
  `avtor` int(11) NOT NULL DEFAULT '0',
  `ready` int(11) NOT NULL DEFAULT '1',
  `time` int(11) NOT NULL DEFAULT '0',
  `read` int(1) DEFAULT '1',
  KEY `id_foto` (`id_foto`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
