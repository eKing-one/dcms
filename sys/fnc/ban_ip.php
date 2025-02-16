<?php

/**
 * 检查IP是否被封禁
 * 
 * @param string $ip 需要检查的IP地址
 * 
 * @return string 如果IP被封禁返回封禁信息，否则返回空字符串
 */
function checkBanIp($ip) {
	// 查询封禁IP段
	$result = dbquery("SELECT `min`, `max` FROM `ban_ip`");
	if (dbrows($result) > 0) {
		if ($result) {
			// 遍历每个封禁IP段
			while ($row = mysqli_fetch_assoc($result)) {
				// 使用函数判断IP是否在该范围内
				if (isIpInRangeBetweenBounds($ip, $row['min'], $row['max'])) {
					return true;
				}
			}
		}
		return false;
	} else {
		return false;
	}
}

if (checkBanIp($ip)) {
	header('Location: /user/ban_ip.php');
	exit;
}