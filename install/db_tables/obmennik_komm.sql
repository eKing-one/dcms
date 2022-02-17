CREATE TABLE `obmennik_komm` (
  `id` int(11) NOT NULL auto_increment,
  `id_file` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `time` int(11) DEFAULT NULL,
  `msg` varchar(1024) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_file` (`id_file`)
) ENGINE=MyISAM DEFAULT CHARSET  utf8mb4 COLLATE  utf8mb4_unicode_ci;