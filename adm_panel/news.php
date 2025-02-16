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
user_access('adm_news',null,'index.php?' . session_id());
adm_check();
$set['title'] = '新闻设置';
include_once '../sys/inc/thead.php';
title();

if (isset($_POST['save'])) {
	$temp_set['daily_news'] = intval($_POST['daily_news']);
	if (save_settings($temp_set)) {
		admin_log('设置', '新闻', '更改新闻设置');
		msg('已成功接受设置');
	} else {
		$err = '无权更改配置文件';
	}
	header( "Location: " . $_SERVER [ "REQUEST_URI" ]);
	exit();
}

echo "<form method=\"post\" action=\"?\">
    	每日新闻：<br />
    	<select name='daily_news'>
			<option ".(setget('daily_news',1)==1? " selected " : null) . " value='1'>已启用</option>
			<option ".(setget('daily_news',1)==0? " selected " : null) . " value='0'>已禁用</option>
		</select>
		<br />
		<input value=\"修改\" name='save' type=\"submit\" />
	</form>";

if (user_access('adm_panel_show')){
	echo "<div class='foot'>";
	if (user_access('adm_news')) echo "<a href='/news/add.php'>添加新闻</a><br />";
	echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
	echo "</div>";
}
include_once '../sys/inc/tfoot.php';