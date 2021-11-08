CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` text(10000) default NULL,
  `time` int(11) NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  `private` int(11) NOT NULL DEFAULT '0',
  `tags` varchar(64) DEFAULT NULL,
  `id_user` int(11) DEFAULT '0',
  `private_komm` int(11) DEFAULT '0',
  `count` int(11) DEFAULT '0',
  `id_dir` int(11) DEFAULT '0',
  `type` int(1) DEFAULT '0',
  `share` enum('0','1') DEFAULT '0',
  `share_id` int(11) DEFAULT NULL,
  `share_text` varchar(20000) DEFAULT NULL,
  `share_name` varchar(60) DEFAULT NULL,
  `share_id_user` int(11) DEFAULT NULL,
  `share_type` varchar(10) DEFAULT 'notes',
  `share_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `notes_count` (
  `id_notes` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  KEY `id_notes` (`id_notes`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notes_dir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` varchar(1024) NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `notes_komm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_notes` varchar(50) NOT NULL,
  `msg` varchar(1024) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `notes_like` (
  `id_notes` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `like` int(11) DEFAULT '0',
  KEY `id_notes` (`id_notes`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;