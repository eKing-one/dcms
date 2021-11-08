CREATE TABLE `mail_to_send` (
  `id` int(11) NOT NULL auto_increment,
  `mail` varchar(64) NOT NULL,
  `them` varchar(32) NOT NULL,
  `msg` varchar(1000) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;