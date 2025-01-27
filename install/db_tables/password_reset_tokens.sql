CREATE TABLE `password_reset_tokens` (
	`id` INT AUTO_INCREMENT PRIMARY KEY,
	`user_id` INT NOT NULL,
	`token` VARCHAR(255) NOT NULL,
	`created_at` TIMESTAMP NOT NULL,
	`status` ENUM('active', 'used', 'expired') DEFAULT 'active'
);
