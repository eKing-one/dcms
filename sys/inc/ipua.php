<?php
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * 从文件加载 CDN IP 地址列表
 * @param string $filePath
 * @return array
 */
function loadCdnIps($filePath) {
	if (!file_exists($filePath)) {
		return [];
	}

	$ips = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	return array_filter($ips, function ($ip) {
		return filter_var($ip, FILTER_VALIDATE_IP) || strpos($ip, '/') !== false; // 处理IP段
	});
}

// 读取 CDN IP 列表并创建 Range 数组
$cdnIpRanges = array_map(function ($cidr) {
	return \IPLib\Factory::parseRangeString($cidr);
}, array_merge(loadCdnIps(__DIR__ . '/../dat/cdn-ips.txt')));

// 局域网、回环地址和链路本地地址范围（包含IPv4和IPv6）
$privateRanges = [
	// IPv4 私有地址和回环地址
	\IPLib\Factory::parseRangeString('10.0.0.0/8'),
	\IPLib\Factory::parseRangeString('172.16.0.0/12'),
	\IPLib\Factory::parseRangeString('192.168.0.0/16'),
	\IPLib\Factory::parseRangeString('127.0.0.0/8'),
	// IPv6 私有地址和回环地址
	\IPLib\Factory::parseRangeString('::1/128'),
	\IPLib\Factory::parseRangeString('fc00::/7'),  // Unique Local Address (IPv6)
	\IPLib\Factory::parseRangeString('fe80::/10')  // Link-Local Address (IPv6)
];

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
	case 'X-Forwarded-For':
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
			$ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
		}
		break;

	case 'X-Real-IP':
		if (!empty($_SERVER['HTTP_X_REAL_IP']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		}
		break;

	case 'CF-Connecting-IP':
		if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}
		break;

	case 'True-Client-IP':
		if (!empty($_SERVER['HTTP_TRUE_CLIENT_IP']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
			$ip = $_SERVER['HTTP_TRUE_CLIENT_IP'];
		}
		break;

	case 'disabled':
		$ip = $_SERVER['REMOTE_ADDR'];
		break;

	case 'auto':
	default:
		// 自动模式，尝试从多个标头获取
		$ip2 = [];
		$ipa = [];

		// 检查是否来自 Cloudflare，如果是则返回真实客户端 IP
		if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
			$ip2['cf'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
			$ipa[] = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}

		// 如果是局域网或回环地址，认为经过了代理服务器
		if (isIpInRange($_SERVER['REMOTE_ADDR'], $privateRanges)) {
			// 处理代理头
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
				$ip2['xff'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
				$ipa[] = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			if (isset($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
				$ip2['xri'] = $_SERVER['HTTP_X_REAL_IP'];
				$ipa[] = $_SERVER['HTTP_X_REAL_IP'];
			}
			if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
				$ip2['cl'] = $_SERVER['HTTP_CLIENT_IP'];
				$ipa[] = $_SERVER['HTTP_CLIENT_IP'];
			}
		}

		// 最后使用 REMOTE_ADDR 作为候选 IP
		if (filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
			$ip2['add'] = $_SERVER['REMOTE_ADDR'];
			$ipa[] = $_SERVER['REMOTE_ADDR'];
		}

		$ip = $ipa[0];
		break;
}

if (!$ip) {$ip = $_SERVER['REMOTE_ADDR'];}

$iplong = ip2long($ip);

if (isset($_SERVER['HTTP_USER_AGENT'])) {
	$ua = $_SERVER['HTTP_USER_AGENT'];
	// 使用 uap-php 库解析 User-Agent
	$parser = UAParser\Parser::create();
	$result = $parser->parse($ua);
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