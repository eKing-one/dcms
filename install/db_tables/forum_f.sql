
CREATE TABLE IF NOT EXISTS `forum_f` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `pos` int(11) NOT NULL,
  `opis` varchar(512) NOT NULL,
  `adm` set('0','1') NOT NULL DEFAULT '0',
  `icon` varchar(30) DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET  utf8mb4 COLLATE  utf8mb4_unicode_ci AUTO_INCREMENT=13 ;

INSERT INTO `forum_f` (`id`, `name`, `pos`, `opis`, `adm`, `icon`) VALUES
(1, '论坛新闻', 1, '竞赛、促销、活动、新闻', '0', 'f_news.gif'),
(2, '沟通与认识', 2, '本网站用户之间的沟通', '0', 'f_obshenie.gif'),
(3, '专题论坛', 3, '按主题分类的论坛', '0', 'f_tematijka.gif'),
(4, '性别和关系', 4, '有用的文章，爱，性，关于性的问题', '0', 'F_seks.gif'),
(5, '休闲和爱好', 5, '娱乐，旅游，电影，汽车/摩托等。', '0', 'f_dosug.gif'),
(6, '音乐', 6, '一切与音乐有关', '0', 'f_music.gif'),
(7, '关于运动', 7, '足球曲棍球和其他', '0', 'f_sport.gif'),
(8, '移动电话', 8, '模型的讨论，购买，销售', '0', 'f_mobil.gif'),
(9, '电话的一切', 9, 'Java塞班铃声图片', '0', 'f_vse_mobil.gif'),
(10, '移动通讯', 10, '所有关于运营商，WAP;GPRS;EDGE;3G;Wi-Fi;短信;彩信', '0', 'svyaz_mob.gif'),
(11, '电脑', 11, '关于电脑的一切', '0', 'f_jkomp.gif'),
(12, '无法无天', 12, 'No comments...', '0', 'bespredel.gif');
