<?php
require_once 'sys/inc/start.php';
require_once 'sys/inc/compress.php';
require_once 'sys/inc/sess.php';
require_once 'sys/inc/home.php';
require_once 'sys/inc/settings.php';
require_once 'sys/inc/db_connect.php';
require_once 'sys/inc/ipua.php';
$ban_ip_page = true;
require_once 'sys/inc/fnc.php';
require_once 'sys/inc/user.php';

$errMsg = "未知错误";
if (isset($_GET['err']) && is_numeric($_GET['err'])) {
	$errCode = intval($_GET['err']);
	http_response_code($errCode);
	if ($errCode == '400') {
		$errMsg = "客户端发送了一个错误的请求";
	} elseif ($errCode == '401') {
		$errMsg = "请求要求用户的身份认证";
	} elseif ($errCode == '402') {
		$errMsg = "服务器拒绝服务直到用户支付费用";
	} elseif ($errCode == '403') {
		$errMsg = "拒绝访问";
	} elseif ($errCode == '404') {
		$errMsg = "请求的页面未找到";
	} elseif ($errCode == '500') {
		$errMsg = "内部服务器错误";
	} elseif ($errCode == '502') {
		$errMsg = "服务器从上游服务器接收到了一个无效的响应";
	}
}

$set['title'] = $errMsg;
require_once 'sys/inc/thead.php';
title();

//header("Refresh: 3; url=/index.php");

echo '<h2 sytle="text-align: center;">错误：' . $errMsg . '</div>';

require_once 'sys/inc/tfoot.php';