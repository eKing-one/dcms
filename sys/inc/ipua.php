<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Wikimedia\IPSet;
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
	return array_filter($ips, function($ip) {
		return filter_var($ip, FILTER_VALIDATE_IP) || strpos($ip, '/') !== false; // 处理IP段
	});
}

// 读取 Cloudflare CDN IP 列表
$cloudflareIps = array_merge(loadCloudflareIps(__DIR__ . '/../dat/cloudflare-ips-v4.txt'), loadCloudflareIps(__DIR__ . '/../dat/cloudflare-ips-v6.txt'));

$ipset = new IPSet($cloudflareIps);

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
	case 'auto':
	default:
		// 自动模式，尝试从多个标头获取
		$ip2 = [];
		$ipa = [];
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']!='127.0.0.1' && preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip2['xff'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
			$ipa[] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		if(isset($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_REAL_IP']!='127.0.0.1' && preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_SERVER['HTTP_X_REAL_IP'])) {
			$ip2['xri'] = $_SERVER['HTTP_X_REAL_IP'];
			$ipa[] = $_SERVER['HTTP_X_REAL_IP'];
		}	
		/**
		 * 获取 Cloudflare 的 IP 列表并检查请求是否来自 Cloudflare。
		 * 如果是，则返回 Cloudflare 提供的真实用户 IP，否则返回请求者的 IP。
		 */
		if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && ($ipset->match($_SERVER['REMOTE_ADDR']))) {
			$ip2['cf'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
			$ipa[] = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}
		if(isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP']!='127.0.0.1' && preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_SERVER['HTTP_CLIENT_IP'])) {
			$ip2['cl'] = $_SERVER['HTTP_CLIENT_IP'];
			$ipa[] = $_SERVER['HTTP_CLIENT_IP'];
		}
		if(isset($_SERVER['REMOTE_ADDR']) && preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_SERVER['REMOTE_ADDR'])) {
			$ip2['add'] = $_SERVER['REMOTE_ADDR'];
			$ipa[] = $_SERVER['REMOTE_ADDR'];
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