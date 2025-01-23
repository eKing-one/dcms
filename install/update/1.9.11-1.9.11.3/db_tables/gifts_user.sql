ALTER TABLE `gifts_user` RENAME `gifts_user_old`;

CREATE TABLE IF NOT EXISTS `gifts_user` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_user` int(11) NOT NULL,
	`id_ank` int(11) NOT NULL,
	`id_gift` int(11) NOT NULL,
	`anonim` TINYINT(1) NOT NULL DEFAULT 0,
	`time` int(11) NOT NULL,
	`coment` varchar(150) NOT NULL,
	`status` int(1) DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO gifts_user (`id`, `id_user`, `id_ank`, `id_gift`, `time`, `coment`, `status`) SELECT `id`, `id_user`, `id_ank`, `id_gift`, `time`, `coment`, `status` FROM gifts_user_old;

DROP TABLE gifts_user_old;