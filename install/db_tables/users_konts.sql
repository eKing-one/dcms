CREATE TABLE IF NOT EXISTS `users_konts` (
  `id_user` int(11) NOT NULL,
  `id_kont` int(11) NOT NULL,
  `time` int(11) DEFAULT NULL,
  `new_msg` int(11) NOT NULL default '0',
  `type` enum('common','ignor','favorite','deleted') NOT NULL default 'common',
  `name` varchar(64) default NULL,
  UNIQUE KEY `id_user` (`id_user`,`id_kont`),
  KEY `type` (`type`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;
