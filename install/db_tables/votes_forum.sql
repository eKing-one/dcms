CREATE TABLE `votes_forum` (
  `id` int(11) NOT NULL auto_increment,
  `them` int(11) NOT NULL,
  `var` varchar(32) NOT NULL,
  `num` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_forum` (`them`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `votes_user` (
  `id` int(11) NOT NULL auto_increment,
  `them` int(11) NOT NULL,
  `var` varchar(32) NOT NULL,
  `id_user` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;