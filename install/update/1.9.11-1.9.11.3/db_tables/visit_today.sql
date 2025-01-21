ALTER TABLE visit_today RENAME TO visit_today_old;

CREATE TABLE IF NOT EXISTS `visit_today` (
  `ip` VARCHAR(39) NOT NULL,
  `ua` varchar(512) DEFAULT NULL,
  `ua_hash` CHAR(32) NOT NULL,
  `time` int(11) DEFAULT NULL,
  KEY `ip` (`ip`),
  KEY `ua_hash` (`ua_hash`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET utf8mb4 COLLATE  utf8mb4_unicode_ci;

INSERT INTO visit_today (ip, ua, ua_hash, time) SELECT INET_NTOA(`ip`) AS `ip`, ua, MD5(`ua`), time FROM visit_today_old;

DROP TABLE visit_today_old;