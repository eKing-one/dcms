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
 * 根据给定的 IP 地址查询数据库，检查其是否在某个 IP 范围内，如果在范围内则返回对应的 'opsos' 字段值。
 * 
 * 该函数首先检查传入的 IP 地址 `$ips` 是否有效，如果没有传入参数，则使用全局变量 `$ip`。
 * 然后，它会查询数据库中所有的 IP 范围（`min` 到 `max`）以及与之相关联的 `opsos` 字段。
 * 对于每一条记录，使用 `isIpInRangeBetweenBounds` 函数判断传入的 IP 是否在当前记录的 `min` 和 `max` 范围内。
 * 如果 IP 在范围内，则返回对应的 `opsos` 值；否则，继续检查下一条记录。
 * 如果没有找到匹配的记录，返回 `false`。
 * 
 * @param string $ips 要查询的 IP 地址。若未提供，则使用全局变量 `$ip`。
 * @return mixed 如果找到符合条件的记录，返回经过 `htmlspecialchars` 和 `stripcslashes` 处理的 `opsos` 字段值；否则返回 `false`。
 */
function opsos($ips = NULL) {
	global $ip;
	// 如果没有传入 IP 地址，使用全局变量 $ip
	if ($ips == NULL) $ips = $ip;

	// 查询数据库，获取所有的 min、max 和 opsos 字段
	$result = dbquery("SELECT min, max, opsos FROM `opsos`");
	// 遍历查询结果
	while ($row = dbassoc($result)) {
		// 使用 isIpInRangeBetweenBounds 判断 IP 是否在当前记录的 min 和 max 范围内
		if (isIpInRangeBetweenBounds($ips, $row['min'], $row['max'])) {
			// 如果 IP 在范围内，返回处理后的 opsos 值
			return stripcslashes(htmlspecialchars($row['opsos']));
		}
	}
	// 如果没有找到符合条件的记录，返回 false
	return false;
}

/**
 * 获取IP位置信息
 * 
 * @param string $ip 需要获取地理信息的IP地址
 * 
 * @return string 返回IP地址的地理位置信息
 */
function get_ip_address($ip) {
	$url = 'http://ip-api.com/json/';
	$otherParameters = '?fields=status,message,country,countryCode,region,regionName,city,district,lat,lon,isp,org,as,reverse,mobile,proxy,hosting&lang=zh-CN';
	$address = execute_curl_request($url . $ip . $otherParameters);
	
	// 处理可能的curl错误
	if (isset($address['error'])) {
		return false;
	}

	$address = json_decode($address, true);

	// 处理JSON解析错误
	if (json_last_error() !== JSON_ERROR_NONE) {
		return 'JSON error';
	}

	$location = $address['country'];
	if (!empty($address['regionName'])) {$location .= ',' . $address['regionName'];}
	if (!empty($address['city'])) {$location .= ',' . $address['city'];}
	if ($address['proxy'] == true) {$location .= ',通过代理访问';}

	return $location ?: 'N/A';
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
	header('Location: /user/ban_ip.php');
	exit;
}