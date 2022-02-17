CREATE TABLE `admin_log_act` (
  `id` int(11) NOT NULL auto_increment,
  `id_mod` int(11) NOT NULL,
  `name` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `act` (`name`),
  KEY `id_mod` (`id_mod`)
) ENGINE=MyISAM DEFAULT CHARSET  utf8mb4 COLLATE  utf8mb4_unicode_ci;