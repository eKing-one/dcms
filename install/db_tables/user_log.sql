CREATE TABLE IF NOT EXISTS `user_log` (
	`id` int NOT NULL auto_increment,
	`id_user` int NOT NULL ,					-- 用户ID
	`method` set('1','0') NOT NULL DEFAULT '0',
	`date` TIMESTAMP DEFAULT NULL,				-- 登录时间
	`expire_date` TIMESTAMP DEFAULT NULL,		-- 登录记录过期时间
	`last_online` TIMESTAMP NOT NULL,			-- 最后在线时间
	`ip` VARCHAR(39) DEFAULT NULL,				-- 登录时的IP
	`ua` VARCHAR(128) DEFAULT NULL,				-- 登录时的UA
	`ban` set('1','0') NOT NULL DEFAULT '0',	-- 这条记录是否被ban
	`browser` VARCHAR(3) DEFAULT 'wap',			-- 浏览器类型
	`url` VARCHAR(2048) NOT NULL DEFAULT '/',	-- 浏览的页面
	PRIMARY KEY  (`id`),
	KEY `id_user` (`id_user`),
	KEY `last_online` (`last_online`)
);