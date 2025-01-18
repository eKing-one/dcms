<?php

/**
 * 检查一个IP地址是否位于给定的最小IP和最大IP之间
 *
 * 该函数验证传入的 `minIp`、`maxIp` 和 `detectIp` 是否是有效的IP地址，
 * 然后判断 `detectIp` 是否在 `minIp` 和 `maxIp` 之间的范围内。
 * 
 * @param string $detectIp 要检测的IP地址
 * @param string $minIp 最小IP地址（范围的下限）
 * @param string $maxIp 最大IP地址（范围的上限）
 * @return bool 如果 `detectIp` 在给定范围内，返回 `true`，否则返回 `false`。
 * 
 * @throws InvalidArgumentException 如果任何IP地址无效，将返回 `false`。
 */
function isIpInRangeBetweenBounds($detectIp, $minIp, $maxIp) {
	if ((filter_var($detectIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($detectIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) && (filter_var($minIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($minIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) && (filter_var($maxIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($maxIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))) {
		if (\IPLib\Factory::parseAddressString($detectIp)->matches(\IPLib\Factory::getRangeFromBoundaries($minIp, $maxIp))) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

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
	die("IP地址 $ip 被封禁");
}