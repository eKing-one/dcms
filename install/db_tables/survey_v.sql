CREATE TABLE `survey_v` (
  `id_s` int(11) NOT NULL,
  `id_r` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  KEY `id_s` (`id_s`,`id_r`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;