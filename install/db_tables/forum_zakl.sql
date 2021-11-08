CREATE TABLE `forum_zakl` (
  `id_user` int(11) NOT NULL,
  `id_them` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `time_obn` int(11) NOT NULL,
  KEY `id_user` (`id_user`,`id_them`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;