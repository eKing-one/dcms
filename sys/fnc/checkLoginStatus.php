<?php
/**
 * 检查用户是否登录
 * @return array
 */
function checkLoginStatus() {
	global $set;
	global $db;

	function jwt_get_user_info($jwt, $set, $db) {
		// 解码 JWT，验证其签名和有效性
		try {
			$decoded = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key($set['shif'], 'HS256'));
		} catch (Exception $e) {
			// 解码失败，返回 false
			return ['status' => 'false', 'message' => 'Failed to decode JWT: ' . $e->getMessage()];
		}

		// 检查 JWT 是否过期
		if ($decoded->exp <= time()) {
			// JWT 过期，返回 false
			return ['status' => 'false', 'message' => 'JWT expired'];
		}
		// 查询用户是否存在并检测登录记录是否可用
		return get_user_info($decoded->user_id, $decoded->jwt_id, $db);
	}

	function get_user_info($user_id, $log_id, $db) {
		// 查询用户是否存在
		$result = dbquery("SELECT * FROM `user` WHERE `id` = '$user_id' LIMIT 1");
		$user_data = dbassoc($result);
		if (!$user_data) {
			return ['status' => 'false', 'message' => 'User does not exist'];
		}

		// 检查数据库中的登录日志，确保 JWT 对应的 log_id 没有标记为"ban"
		$result = dbquery("SELECT ban FROM `user_log` WHERE `id` = '$log_id' AND `id_user` = '$user_id'");
		$user_log_ban = dbassoc($result);
		if (!$user_log_ban) {
			// 找不到此登录记录
			return ['status' => 'false', 'message' => 'Login log not found'];
		}
		if ($user_log_ban['ban'] != '0') {
			// 登录记录被 ban
			return ['status' => 'false', 'message' => 'Login log is banned'];
		}

		// 验证通过，说明用户已经登录
		// 更新用户的最后登录时间
		dbquery("UPDATE `user_log` SET `last_online` = '" . date('Y-m-d H:i:s') . "' WHERE `id` = '$log_id' LIMIT 1");
		dbquery("UPDATE `user` SET `date_last` = '" . time() . "' WHERE `id` = '$user_id' LIMIT 1");

		$user_info = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$user_id' LIMIT 1"));
		$user_info['login_id'] = $log_id;
		return [
			'status' => 'true',
			'info' => $user_info
		];
	}

	if (isset($_SESSION['id_user'], $_SESSION['login_id'])) {
		// 使用 session 检测登录状态
		$user = get_user_info($_SESSION['id_user'], $_SESSION['login_id'], $db);
		if ($user['status'] === 'true') {
			$user['info']['type_input'] = 'session';
			return ['status' => 'true', 'data' => $user['info']];
		}
		session_unset();
		return ['status' => 'false', 'message' => 'Session error: ' . $user['message']];
	}

	if (isset($_COOKIE['auth_token'])) {    // 从Cookie获取Tocken
		$user = jwt_get_user_info($_COOKIE['auth_token'], $set, $db);
		if ($user['status'] === 'true') {
			$_SESSION['id_user'] = $user['info']['id'];
			$_SESSION['login_id'] = $user['info']['login_id'];
			$user['info']['type_input'] = 'cookie';
			return ['status' => 'true', 'data' => $user['info']];
		}
		setcookie('auth_token', '', time() - 3600, '/');
		return ['status' => 'false', 'message' => 'Cookie error: ' . $user['message']];
	}

	if (!empty($_SERVER['HTTP_AUTHORIZATION']) && strpos($_SERVER['HTTP_AUTHORIZATION'], 'Bearer ') === 0) {    // 检查 Authorization 头部中是否有 Bearer Token
		$jwt = preg_split('/\s+/', $_SERVER['HTTP_AUTHORIZATION'])[1];
		$user = jwt_get_user_info($jwt, $set, $db);
		if ($user['status'] === 'true') {
			$user['info']['type_input'] = 'authorization';
			return ['status' => 'true', 'data' => $user['info']];
		}
		return ['status' => 'false', 'message' => 'JWT invalid: ' . $user['message']];
	}

	return ['status' => 'false', 'message' => 'No parameters'];
}
