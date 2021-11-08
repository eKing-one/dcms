
--
-- Структура таблицы `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('link','razd') NOT NULL DEFAULT 'link',
  `name` varchar(32) NOT NULL,
  `url` varchar(32) NOT NULL,
  `counter` varchar(32) NOT NULL,
  `pos` int(11) NOT NULL,
  `icon` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pos` (`pos`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='主菜单' AUTO_INCREMENT=15 ;

--
-- Дамп данных таблицы `menu`
--

INSERT INTO `menu` (`id`, `type`, `name`, `url`, `counter`, `pos`, `icon`) VALUES
(1, 'link', '新闻中心', '/news/', 'news/count.php', 1, 'news.png'),
(2, 'link', '聊天', '/chat/', 'chat/count.php', 7, 'chat.png'),
(4, 'link', '客人', '/guest/', 'guest/count.php', 9, 'guest.png'),
(5, 'link', '交换区', '/obmen/', 'obmen/count.php', 5, 'obmen.png'),
(6, 'link', '论坛', '/forum/', 'forum/count.php', 6, 'forum.png'),
(7, 'link', '照片廊', '/foto/', 'foto/count.php', 10, 'foto.png'),
(11, 'link', '领导者', '/user/liders/', '/user/liders/count.php', 4, 'lider.gif'),
(10, 'link', '日记', '/plugins/notes/', 'plugins/notes/count.php', 8, 'zametki.gif'),
(12, 'link', '熟人', '/user/love/', '/user/love/count.php', 3, 'meets.gif'),
(13, 'link', '资料', '/plugins/rules/', '', 12, 'info.gif'),
(14, 'link', '居民', '/user/users.php', '/user/count.php', 11, 'druzya.png');
