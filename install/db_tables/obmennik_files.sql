
CREATE TABLE IF NOT EXISTS `obmennik_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_dir` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `ras` varchar(36) NOT NULL,
  `type` varchar(64) NOT NULL,
  `time` int(11) NOT NULL,
  `time_last` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `k_loads` int(11) DEFAULT '0',
  `time_go` int(11) DEFAULT '0',
  `opis` text NOT NULL,
  `my_dir` int(11) DEFAULT '0',
  `metka` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;