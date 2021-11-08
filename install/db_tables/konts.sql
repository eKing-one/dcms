CREATE TABLE `konts` (
  `id_user` int(11) NOT NULL,
  `id_kont` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  KEY `id_user` (`id_user`,`id_kont`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;