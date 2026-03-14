CREATE TABLE `chat_rooms` (
	`id` int(11) NOT NULL auto_increment,	-- 聊天室 ID
	`pos` int(11) NOT NULL,								-- 排序用的 ID （越小越靠前）
	`name` varchar(32) NOT NULL,					-- 聊天室名称
	`umnik` set('0','1') default '0',			-- 是否启用答题机器人
	`shutnik` set('0','1') default '0',		-- 是否启用笑话机器人
	`opis` varchar(256) NOT NULL,					-- 聊天室描述
	PRIMARY KEY  (`id`),
	KEY `pos` (`pos`,`umnik`,`shutnik`)
);