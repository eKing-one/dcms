CREATE TABLE IF NOT EXISTS `user_log` (
	`id` int NOT NULL auto_increment PRIMARY KEY,
	`id_user` int NOT NULL,								-- 用户ID
	`method` set('1','0') NOT NULL DEFAULT '0',
	`date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,			-- 登录时间
	`expire_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,	-- 登录记录过期时间
	`last_online` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,	-- 最后在线时间
	`ip` VARCHAR(39) NOT NULL,							-- 使用此Token的IP
	`ua` VARCHAR(128) DEFAULT NULL,						-- 使用此Token的UA
	`sess` varchar(32) DEFAULT NULL,					-- 使用此Token的session
	`ban` BOOLEAN NOT NULL DEFAULT 0,					-- 这条记录是否被ban
	`url` VARCHAR(2048) NOT NULL DEFAULT '/',			-- 使用此Token最后浏览的页面
	`browser` varchar(3) DEFAULT 'wap',					-- 使用此Token的浏览器类型
	INDEX (`id_user`)
);