CREATE TABLE `chat_vopros` (
  `id` int(11) NOT NULL auto_increment,
  `vopros` varchar(1024) NOT NULL,
  `otvet` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;