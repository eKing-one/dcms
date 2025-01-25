<?php
function get_http_type() {
	global $set;
	// 优先检查 HTTPS
	$http_type = 'http';
	
	// 检查 HTTPS 是否开启
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
		$http_type = 'https';
	} elseif ($set['get_ip_from_header'] != 'disabled') {	// 检查 X-Forwarded-Proto 或 Forwarded 头部
		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			$http_type = 'https';
		} elseif (isset($_SERVER['HTTP_FORWARDED'])) {
			// 解析 Forwarded 头部并检查 proto
			if (preg_match('/proto=https/', $_SERVER['HTTP_FORWARDED'])) {
				$http_type = 'https';
			}
		}
	}
	
	return $http_type;
}
