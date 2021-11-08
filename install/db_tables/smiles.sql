
CREATE TABLE IF NOT EXISTS `smile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smile` varchar(64) NOT NULL,
  `dir` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1187 ;


INSERT INTO `smile` (`id`, `smile`, `dir`) VALUES
(1184, '.сердит.', 21),
(1185, ':)', 21),
(331, '=)', 21),
(1168, '.че.', 21),
(1169, ':(', 21),
(1170, ':D', 21),
(1186, '.язык.', 21),
(1173, '.миг.', 21),
(1174, '.крут.', 21),
(1175, '.секрет.', 21),
(1182, '.ах.', 21),
(1183, '.кисс.', 21);


CREATE TABLE IF NOT EXISTS `smile_dir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `opis` varchar(320) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;


INSERT INTO `smile_dir` (`id`, `name`, `opis`) VALUES
(21, '常见的', '');
