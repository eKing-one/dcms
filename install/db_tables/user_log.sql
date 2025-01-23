CREATE TABLE IF NOT EXISTS `user_log` (
	`id` int NOT NULL auto_increment,
	`id_user` int NOT NULL ,
	`method` set('1','0') NOT NULL default '0',
	`date` TIMESTAMP NOT NULL,
	`ip` VARCHAR(39) DEFAULT NULL,
	`ua` varchar(128) default NULL,
	`ban` set('1','0') NOT NULL default '0',
	PRIMARY KEY  (`id`),
	KEY `id_user` (`id_user`),
	KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;