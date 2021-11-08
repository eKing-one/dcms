
CREATE TABLE IF NOT EXISTS `discussions_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `disc_status` int(11) DEFAULT '1',
  `disc_foto` int(11) DEFAULT '1',
  `disc_files` int(11) DEFAULT '1',
  `disc_forum` int(11) DEFAULT '1',
  `disc_notes` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
