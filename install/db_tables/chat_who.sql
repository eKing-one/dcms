CREATE TABLE `chat_who` (
  `id_user` int(11) NOT NULL,
  `room` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  KEY `id_user` (`id_user`,`room`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;