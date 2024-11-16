<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
$temp_set=$set;
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_set_sys',null,'index.php?'.SID);
adm_check();
$set['title']='CDN设置';
include_once '../sys/inc/thead.php';
title();

if (isset($_POST['save'])) {
	$temp_set['get_ip_from_header'] = in_array($_POST['get_ip_from_header'], ['auto', 'disabled', 'X-Forwarded-For', 'X-Real-IP', 'CF-Connecting-IP', 'True-Client-IP']) ? $_POST['get_ip_from_header'] : 'auto';
	
	if (save_settings($temp_set)) {
		admin_log('设置', '系统', '更改CDN设置');
		msg('已成功接受设置');
	} else {
		$err = '无权更改配置文件';
	}
	header( "Location: " . $_SERVER [ "REQUEST_URI" ]);
	exit();
}

err();
aut();
echo "<form method=\"post\" action=\"?\">";

echo "从请求标头获取用户IP：<br />
<select name='get_ip_from_header'>
	<option ".(setget('get_ip_from_header',"auto")=="auto"? " selected ":null)." value='auto'>自动识别</option>
	<option ".(setget('get_ip_from_header',"disabled")=="disabled"? " selected ":null)." value='disabled'>禁用</option>
	<option ".(setget('get_ip_from_header',"X-Forwarded-For")=="X-Forwarded-For"? " selected ":null)." value='X-Forwarded-For'>X-Forwarded-For</option>
	<option ".(setget('get_ip_from_header',"X-Real-IP")=="X-Real-IP"? " selected ":null)." value='X-Real-IP'>X-Real-IP</option>
	<option ".(setget('get_ip_from_header',"CF-Connecting-IP")=="CF-Connecting-IP"? " selected ":null)." value='CF-Connecting-IP'>CF-Connecting-IP</option>
	<option ".(setget('get_ip_from_header',"True-Client-IP")=="True-Client-IP"? " selected ":null)." value='True-Client-IP'>True-Client-IP</option>
</select>
<br />";



echo "<input value=\"保存\" name='save' type=\"submit\" />";
echo "</form>";
if (user_access('adm_panel_show')) {
	echo "<div class='foot'>";
	echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
	echo "</div>";
}
include_once '../sys/inc/tfoot.php';