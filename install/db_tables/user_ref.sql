DROP TABLE IF EXISTS user_ref;
CREATE TABLE IF NOT EXISTS user_ref (
  id int(10) unsigned NOT NULL auto_increment,
  `time` int(10) unsigned NOT NULL,
  id_user int(10) unsigned NOT NULL,
  url varchar(1024) default NULL,
  type_input varchar(12) default 'cookie',
  PRIMARY KEY  (id),
  KEY `time` (`time`,id_user),
  KEY type_input (type_input)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;