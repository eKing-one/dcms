<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use UAParser\Parser;
use Wikimedia\IPUtils;

/**
 * Cloudflare IPv4 列表：https://www.cloudflare.com/ips-v4/
 * Cloudflare IPv6 列表：https://www.cloudflare.com/ips-v6/
 */
// 下载的 Cloudflare IP 文件路径
define('CLOUDFLARE_IPV4_FILE', __DIR__ . '/../dat/cloudflare-ips-v4.txt');
define('CLOUDFLARE_IPV6_FILE', __DIR__ . '/../dat/cloudflare-ips-v6.txt');
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

$ipa = false;

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
		if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && (IPUtils::isInRange($_SERVER['REMOTE_ADDR'], loadCloudflareIps(CLOUDFLARE_IPV4_FILE)) || IPUtils::isInRange($_SERVER['REMOTE_ADDR'], loadCloudflareIps(CLOUDFLARE_IPV6_FILE)))) {
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
	$browser_name = $result->ua->family ?? '未知'; // 修正对象访问
	$browser_version = $result->ua->major;
	// 特殊处理 Opera Mini 手机型号
	if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && stripos($ua, 'Opera') !== false) {
		$ua_om = preg_replace('#[^a-z_\. 0-9\-]#iu', "null", strtolower($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']));
		$browser_name = 'Opera Mini (' . $ua_om . ')';
	}
	// 构造最终的 User-Agent 字符串
	if ($browser_version == null) {
		// 如果解析不到浏览器版本就不合并版本号
		$ua = $browser_name;
	} else {
		$ua = "{$browser_name} v{$browser_version}";
	}
} else {
	$ua = 'N/A';
}