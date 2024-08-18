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
    // 清理 UA 字符串，保留有用的信息
    $ua = preg_replace('/[^a-zA-Z0-9\/\.\-\s]/', '', $ua);
    // 特殊处理 Opera Mini
    if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && preg_match('#Opera#i', $ua)) {
        $ua_om = $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'];
        $ua_om = trim(strtok($ua_om, '/'));
        $ua_om = trim(strtok('', '('));
        $ua_om = preg_replace('/[^a-zA-Z0-9\/\.\-\s]/', '', $ua_om);
        $ua = "Opera Mini ($ua_om)";
    }
} else {
    $ua = 'unknown';  // 设置一个更标准的默认 UA 字符串
}
?>