<?php

/**
 * 从数据库获取 CDN IP 列表
 */
function get_cdn_ips() {
	// 查询 'cdn_ips' 表中的所有数据
	$result = dbquery("SELECT * FROM cdn_ips");
	// 初始化一个空数组，用于存储所有 IP 信息
	$ipArray = [];
	// 检查查询结果是否有数据
	if (dbrows($result) > 0) {
		// 循环遍历每一行数据
		while ($row = dbarray($result)) {
			// 将每个 IP 范围添加到数组中
			$ipArray[] = $row['ip_range'];
		}
		return $ipArray; // 返回包含所有 IP 范围的数组
	} else {
		return []; // 如果没有数据，返回空数组
	}
}

if ($set['get_ip_from_header'] != 'disabled') {
	// 读取 CDN IP 列表并创建 Range 数组
	$cdnIpRanges = array_map(function ($cidr) {
		return \IPLib\Factory::parseRangeString($cidr);
	}, get_cdn_ips());
}

/**
 * 检查一个IP是否在特定IP范围内
 * @param string $ip
 * @param array $ranges
 * @return bool
 */
function isIpInRange($ip, $ranges) {
	$ipAddress = \IPLib\Factory::addressFromString($ip);
	foreach ($ranges as $range) {
		if ($range->contains($ipAddress)) {
			return true;
		}
	}
	return false;
}

// 根据不同选项获取IP
switch ($set['get_ip_from_header']) {
	case 'Forwarded':
		if (!empty($_SERVER['HTTP_FORWARDED']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
			// 遍历 Forwarded 头部
			foreach (array_map('trim', explode(',', $_SERVER['HTTP_FORWARDED'])) as $part) {
				// 如果当前部分包含 "for="，则说明这是用户的真实IP
				if (stripos($part, 'for=') !== false) {
					$ip = trim(str_ireplace('for=', '', $part));
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		break;
	case 'X-Forwarded-For':
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
			// 遍历X-Forwarded-For头部
			foreach (array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) as $ip) {
				// 如果当前IP在可信代理列表中，继续检查下一个IP
				if (isIpInRange($ip, $cdnIpRanges)) {
					continue;
				}
				break;
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		break;

	case 'X-Real-IP':
		if (!empty($_SERVER['HTTP_X_REAL_IP']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		break;

	case 'CF-Connecting-IP':
		if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		break;

	case 'True-Client-IP':
		if (!empty($_SERVER['HTTP_TRUE_CLIENT_IP']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
			$ip = $_SERVER['HTTP_TRUE_CLIENT_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		break;

	case 'disabled':
	default:
		$ip = $_SERVER['REMOTE_ADDR'];
		break;
}

// 获取 User-Agent
if (isset($_SERVER['HTTP_USER_AGENT'])) {
	$ua = $_SERVER['HTTP_USER_AGENT'];
	// 使用 uap-php 库解析 User-Agent
	$result = UAParser\Parser::create()->parse($ua);
	// 特殊处理 Opera Mini 手机型号
	if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && stripos($ua, 'Opera') !== false) {
		$ua_om = preg_replace('#[^a-z_\. 0-9\-]#iu', null, strtolower($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']));
		$ua = $result->toString();
		$ua = $ua . '(' . $ua_om . ')';
	} else {
		$ua = $result->toString();
	}
} else {
	$ua = 'N/A';
}