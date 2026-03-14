CREATE TABLE IF NOT EXISTS `discussions` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`avtor` int(11) NOT NULL,
	`id_user` int(11) NOT NULL,
	`count` int(11) DEFAULT '0',
	`msg` varchar(1024),
	`time` int(11) NOT NULL,
	`type` varchar(100) NOT NULL,
	`id_sim` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `idx_user_type_sim` (`id_user`, `type`, `id_sim`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
