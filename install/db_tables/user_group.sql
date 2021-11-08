
CREATE TABLE IF NOT EXISTS `user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

INSERT INTO `user_group` (`id`, `name`, `level`) VALUES
(1, '用户', 0),
(2, '聊天版主', 1),
(3, '论坛主持人', 1),
(4, '交流区主持人', 1),
(5, '图书馆主持人', 1),
(6, '照片廊主持人', 1),
(7, '主持人', 2),
(8, '署长', 3),
(9, '总行政主任', 9),
(15, '站长', 10),
(11, '日记主持人', 1),
(12, '嘉宾主持人', 1);