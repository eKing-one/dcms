CREATE TABLE `chat_rooms` (
  `id` int(11) NOT NULL auto_increment,
  `pos` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `umnik` set('0','1') default '0',
  `shutnik` set('0','1') default '0',
  `opis` varchar(256) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `pos` (`pos`,`umnik`,`shutnik`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;