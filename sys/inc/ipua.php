<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use UAParser\Parser;

$ipa = false;
if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']!='127.0.0.1' && preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_SERVER['HTTP_X_FORWARDED_FOR']))
{
	$ip2['xff'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$ipa[] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
if(isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP']!='127.0.0.1' && preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_SERVER['HTTP_CLIENT_IP']))
{
	$ip2['cl'] = $_SERVER['HTTP_CLIENT_IP'];
	$ipa[] = $_SERVER['HTTP_CLIENT_IP'];
}
if(isset($_SERVER['REMOTE_ADDR']) && preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_SERVER['REMOTE_ADDR']))
{
	$ip2['add'] = $_SERVER['REMOTE_ADDR'];
	$ipa[] = $_SERVER['REMOTE_ADDR'];
}
$ip = $ipa[0];
$iplong = ip2long($ip);
function cleanUAString($ua) {
    return preg_replace('#[^a-z_\. 0-9\-]#iu', "null", strtolower($ua));
}
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $ua = $_SERVER['HTTP_USER_AGENT'];
    // 使用 uap-php 库解析 User-Agent
    $parser = Parser::create();
    $result = $parser->parse($ua);
    $browser_name = $result->ua->family ?? '未知'; // 修正对象访问
    $browser_version = $result->ua->major ?? '';
    // 特殊处理 Opera Mini 手机型号
    if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && stripos($ua, 'Opera') !== false) {
        $ua_om = cleanUAString($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']);
        $browser_name = 'Opera Mini (' . $ua_om . ')';
    }
    // 构造最终的 User-Agent 字符串
    $ua = "{$browser_name} v{$browser_version}";
} else {
    $ua = '没有可用的数据';
}
?>
