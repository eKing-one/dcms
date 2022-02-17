CREATE TABLE IF NOT EXISTS `stena` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_stena` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `msg` varchar(1024) CHARSET utf8mb4 COLLATE  utf8mb4_unicode_ci  DEFAULT NULL,
  `read` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci  AUTO_INCREMENT=7 ;
alter table `stena` add `info_1` varchar(18) default '0';
alter table `stena` add `type` varchar(8) default 'notes';
alter table `stena` add `info` varchar(256) default 'Error. 信息不可用';
CREATE TABLE IF NOT EXISTS `stena_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_stena` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci ;