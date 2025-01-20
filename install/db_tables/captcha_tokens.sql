-- 启用事件调度器
SET GLOBAL event_scheduler = ON;

-- 创建 captcha_tokens 表
CREATE TABLE captcha_tokens (
	id INT AUTO_INCREMENT PRIMARY KEY,
	captcha_token VARCHAR(170) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- 记录 token 的生成时间
	expires_at TIMESTAMP,  -- 记录 token 的过期时间
	status ENUM('unused', 'used', 'expired') DEFAULT 'unused',  -- 记录 token 的状态
	UNIQUE(captcha_token)
);

-- 创建定时事件删除过期的 captcha_token
CREATE EVENT delete_expired_captcha_tokens
ON SCHEDULE EVERY 1 HOUR  -- 每小时执行一次
DO
	DELETE FROM captcha_tokens
	WHERE expires_at < NOW();
