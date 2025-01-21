ALTER TABLE user_log RENAME TO user_log_old;

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


INSERT INTO user_log (id, id_user, method, date, ip, ua) SELECT id, id_user, method, FROM_UNIXTIME(time), INET_NTOA(`ip`) AS `ip`, ua FROM user_log_old;


DROP TABLE user_log_old;