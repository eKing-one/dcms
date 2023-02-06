CREATE TABLE IF NOT EXISTS `user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET  utf8mb4 COLLATE  utf8mb4_unicode_ci AUTO_INCREMENT=16 ;
INSERT INTO `user_group` (`id`, `name`, `level`) VALUES
(1, '用户', 0),
(2, '聊天版主', 1),
(3, '论坛版主', 1),
(4, '交流区版主', 1),
(5, '图书馆版主', 1),
(6, '照片廊版主', 1),
(7, '总版主', 2),
(8, '管理员', 3),
(9, '总管理员', 9),
(11, '日记版主', 1),
(12, '嘉宾版主', 1),
(15, '站长', 10);