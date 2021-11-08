
--
-- Структура таблицы `mail`
--

CREATE TABLE IF NOT EXISTS `mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_kont` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `msg` varchar(1024) NOT NULL,
  `read` set('0','1') NOT NULL DEFAULT '0',
  `unlink` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`,`id_kont`),
  KEY `read` (`read`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
