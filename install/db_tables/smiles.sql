CREATE TABLE IF NOT EXISTS `smile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smile` varchar(64) NOT NULL,
  `dir` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET  utf8mb4 COLLATE  utf8mb4_unicode_ci AUTO_INCREMENT=13 ;
INSERT INTO `smile` (`id`, `smile`, `dir`) VALUES
(1, '.a.', 21),
(2, ':)', 21),
(3, '=)', 21),
(4, '.b.', 21),
(5, ':(', 21),
(6, ':D', 21),
(7, '.c.', 21),
(8, '.m.', 21),
(9, '.k.', 21),
(10, '.m.', 21),
(11, '.ax.', 21),
(12, '.kn.', 21);
CREATE TABLE IF NOT EXISTS `smile_dir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `opis` varchar(320) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci AUTO_INCREMENT=2 ;
INSERT INTO `smile_dir` (`id`, `name`, `opis`) VALUES
(1, '常见的', '');
