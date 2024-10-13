<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use IPLib\Factory;
use IPLib\Range\Range;
use UAParser\Parser;

/**
 * Cloudflare IPv4 列表：https://www.cloudflare.com/ips-v4/
 * Cloudflare IPv6 列表：https://www.cloudflare.com/ips-v6/
 */

/**
 * 从文件加载 Cloudflare IP 地址列表
 * @param string $filePath
 * @return array
 */
function loadCloudflareIps($filePath) {
	if (!file_exists($filePath)) {
		return [];
	}

	$ips = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	return array_filter($ips, function ($ip) {
		return filter_var($ip, FILTER_VALIDATE_IP) || strpos($ip, '/') !== false; // 处理IP段
	});
}

// 读取 Cloudflare CDN IP 列表
$cloudflareIps = array_merge(
	loadCloudflareIps(__DIR__ . '/../dat/cloudflare-ips-v4.txt'),
	loadCloudflareIps(__DIR__ . '/../dat/cloudflare-ips-v6.txt')
);

// 创建 Range 数组
$ipRanges = array_map(function ($cidr) {
	return Range::fromString($cidr);
}, $cloudflareIps);

// 局域网、回环地址和链路本地地址范围（包含IPv4和IPv6）
$privateRanges = [
	// IPv4 私有地址和回环地址
	Range::fromString('10.0.0.0/8'),
	Range::fromString('172.16.0.0/12'),
	Range::fromString('192.168.0.0/16'),
	Range::fromString('127.0.0.0/8'),
	// IPv6 私有地址和回环地址
	Range::fromString('::1/128'),
	Range::fromString('fc00::/7'),  // Unique Local Address (IPv6)
	Range::fromString('fe80::/10')  // Link-Local Address (IPv6)
];

/**
 * 检查一个IP是否在特定IP范围内
 * @param string $ip
 * @param array $ranges
 * @return bool
 */
function isIpInRange($ip, $ranges) {
	$ipAddress = Factory::addressFromString($ip);
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
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
		}
		break;
	case 'X-Real-IP':
		if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		}
		break;
	case 'CF-Connecting-IP':
		if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}
		break;
	case 'True-Client-IP':
		if (!empty($_SERVER['HTTP_TRUE_CLIENT_IP'])) {
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
		
		// 检查 REMOTE_ADDR 是否为局域网或回环地址
		$remoteAddr = $_SERVER['REMOTE_ADDR'];
		$isPrivateOrLoopback = isIpInRange($remoteAddr, $privateRanges);

		// 如果是局域网或回环地址，并且不是 Cloudflare 的代理，认为经过了代理服务器
		if ($isPrivateOrLoopback && !isIpInRange($remoteAddr, $ipRanges)) {
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

		// 检查是否来自 Cloudflare，如果是则返回真实客户端 IP
		if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && isIpInRange($remoteAddr, $ipRanges)) {
			$ip2['cf'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
			$ipa[] = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}

		// 最后使用 REMOTE_ADDR 作为候选 IP
		if (filter_var($remoteAddr, FILTER_VALIDATE_IP)) {
			$ip2['add'] = $remoteAddr;
			$ipa[] = $remoteAddr;
		}

		$ip = $ipa[0];
		break;
}

$iplong = ip2long($ip);

if (isset($_SERVER['HTTP_USER_AGENT'])) {
	$ua = $_SERVER['HTTP_USER_AGENT'];
	// 使用 uap-php 库解析 User-Agent
	$parser = Parser::create();
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