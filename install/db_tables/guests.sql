CREATE TABLE `guests` (
  `ip` bigint(20) NOT NULL,
  `ua` varchar(32) NOT NULL,
  `date_aut` int(11) NOT NULL,
  `date_last` int(11) NOT NULL,
  `url` varchar(64) NOT NULL,
  `pereh` int(11) NOT NULL default '0',
  KEY `ip_2` (`ip`,`ua`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;