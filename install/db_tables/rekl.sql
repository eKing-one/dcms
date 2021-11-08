
CREATE TABLE IF NOT EXISTS `rekl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `img` varchar(64) NOT NULL,
  `link` varchar(64) NOT NULL,
  `time_last` int(11) NOT NULL,
  `sel` set('1','2','3','4') NOT NULL DEFAULT '1',
  `count` int(11) NOT NULL DEFAULT '0',
  `dop_str` set('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sel` (`sel`),
  KEY `time_last` (`time_last`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

