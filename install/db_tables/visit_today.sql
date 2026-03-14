CREATE TABLE IF NOT EXISTS `visit_today` (
	`ip_ua_hash` CHAR(32) NOT NULL,						-- 基于 ip 和 ua 的哈希值
	`ip` VARCHAR(39) NOT NULL,							-- 访客 IP 地址
	`ua` VARCHAR(128) DEFAULT NULL,						-- 用户代理字符串（可选，视需求保留）
	`hit_count` INT NOT NULL DEFAULT 1,					-- 访问次数计数
	`first_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,	-- 第一次访问时间
	`last_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,	-- 最后一次访问时间
	PRIMARY KEY `ip_ua_hash` (`ip_ua_hash`),
	KEY `ip` (`ip`),
	KEY `last_time` (`last_time`)
);
