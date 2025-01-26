<?php
function validatePasswordResetToken($token, $userId) {
	global $db;

	// 查询数据库，检查 token 是否存在且有效
	$result = dbassoc(dbquery("SELECT * FROM `password_reset_tokens` WHERE `token` = '$token' AND `user_id` = '$userId' AND `status` = 'active' LIMIT 1"));

	if ($result) {
		// 检查 token 是否在 6 小时内有效
		$createdAt = strtotime($result['created_at']);
		if (time() > $createdAt) {
			// 超过 6 小时，token 已过期
			dbquery("UPDATE `password_reset_tokens` SET `status` = 'expired' WHERE `token` = '$token'");
			return ['status' => 'error', 'message' => "此链接已过期 ($createdAt)"];
		}
		
		return ['status' => 'success', 'message' => '验证通过'];
	} else {
		return ['status' => 'error', 'message' => '无效的 token'];
	}
}

function markValidatePasswordResetTokenAsUsed($token) {
	global $db;
	dbquery("UPDATE `password_reset_tokens` SET `status` = 'used' WHERE `token` = '$token'");
}
