CREATE TABLE `daily_news_cache` (
	`id` INT PRIMARY KEY AUTO_INCREMENT,
	`data` TEXT NOT NULL,
	`time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;