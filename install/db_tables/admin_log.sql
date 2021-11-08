CREATE TABLE `admin_log` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_user` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `mod` int(11) NOT NULL,
  `act` int(11) NOT NULL,
  `opis` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `mod` (`mod`),
  KEY `act` (`act`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;