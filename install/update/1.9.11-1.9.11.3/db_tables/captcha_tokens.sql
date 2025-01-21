-- 创建 captcha_tokens 表
CREATE TABLE captcha_tokens (
	id INT AUTO_INCREMENT PRIMARY KEY,
	captcha_token VARCHAR(170) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- 记录 token 的生成时间
	expires_at TIMESTAMP,  -- 记录 token 的过期时间
	status ENUM('unused', 'used', 'expired') DEFAULT 'unused',  -- 记录 token 的状态
	UNIQUE(captcha_token)
);