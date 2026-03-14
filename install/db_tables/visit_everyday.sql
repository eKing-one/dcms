CREATE TABLE IF NOT EXISTS `visit_everyday` (
	`date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- 时间
	`visitors` INT DEFAULT NULL, -- 访客数量
	`hit` INT NOT NULL, -- 点击数量
	PRIMARY KEY `date` (`date`),
	KEY `visitors` (`visitors`),
	KEY `hit` (`hit`)
);
