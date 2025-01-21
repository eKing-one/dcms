ALTER TABLE ban_ip RENAME TO ban_ip_old;

CREATE TABLE `ban_ip` (
	`min` VARCHAR(39) NOT NULL,
	`max` VARCHAR(39) NOT NULL,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	KEY `min` (`min`,`max`)
) ENGINE=MyISAM DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO ban_ip (min, max) SELECT INET_NTOA(`min`) AS `min`, INET_NTOA(`max`) AS `max` FROM ban_ip_old;

DROP TABLE ban_ip_old;