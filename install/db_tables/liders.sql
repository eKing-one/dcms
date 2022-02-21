CREATE TABLE IF NOT EXISTS `liders` (
  `time` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `time_p` int(11) DEFAULT NULL,
  `msg` varchar(215) DEFAULT NULL,
  `stav` int(11) NOT NULL DEFAULT '0',
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET  utf8mb4 COLLATE  utf8mb4_unicode_ci;
