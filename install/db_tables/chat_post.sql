DROP TABLE IF EXISTS `chat_post`;
CREATE TABLE IF NOT EXISTS `chat_post` (
  `id` int(11) NOT NULL auto_increment,
  `room` int(11) NOT NULL,
  `id_user` int(11) default NULL,
  `time` int(11) NOT NULL,
  `msg` varchar(1024) default NULL,
  `vopros` int(11) default NULL,
  `umnik_st` set('0','1','2','3','4') default '0',
  `shutnik` set('0','1') NOT NULL default '0',
  `privat` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `room` (`room`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;