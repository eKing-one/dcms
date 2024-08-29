<?php
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
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $ua = $_SERVER['HTTP_USER_AGENT'];
    // 使用preg_match_all来提取浏览器名称和版本
    if (preg_match('/^(?P<name>[a-zA-Z][a-zA-Z0-9 ]+)\/(?P<version>[0-9.]+)/', $ua, $matches)) {
        $browser_name = $matches['name'];
        $browser_version = $matches['version'];
    } else {
        // 如果无法解析，则直接使用原始的UA字符串
        $browser_name = preg_replace('#[^a-z_\./ 0-9\-]#iu', "null", strtolower($ua));
        $browser_version = '';
    }
    // 根据常见的浏览器名称进行分类
    if (stripos($ua, 'Chrome') !== false) {
        $ua = "Google Chrome {$browser_version}";
    } elseif (stripos($ua, 'Firefox') !== false) {
        $ua = "Mozilla Firefox {$browser_version}";
    } elseif (stripos($ua, 'Safari') !== false) {
        $ua = "Safari {$browser_version}";
    } elseif (stripos($ua, 'Edge') !== false) {
        $ua = "Microsoft Edge {$browser_version}";
    } elseif (stripos($ua, 'Opera') !== false) {
        $ua = "Opera {$browser_version}";
    } elseif (stripos($ua, 'IE') !== false || stripos($ua, 'Trident') !== false) {
        $ua = "Internet Explorer {$browser_version}";
    } else {
        $ua = $browser_name;
    }
    // Opera mini还会发送有关手机的数据 :)
    if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && preg_match('#Opera#i',$ua)) {
        $ua_om = $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'];
		$ua_om = preg_replace('#[^a-z_\. 0-9\-]#iu', "null", $ua_om);
        $ua = 'Opera Mini (' . $ua_om . ')';
    }
} else {
    $ua = '没有可用的数据';
}
?>