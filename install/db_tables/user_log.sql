CREATE TABLE IF NOT EXISTS `user_log` (
  `id` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL ,
  `method` set('1','0') NOT NULL default '0',
  `time` int(11) NOT NULL ,
  `ip` VARCHAR(39) DEFAULT NULL,
  `ua` varchar(512) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_user` (`id_user`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;