<?php
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
$set['title'] = '网站管理'; //网页标题
include_once '../../sys/inc/thead.php';
title();
aut();

if (user_access('adm_panel_show')) {
	echo "<div class='mess'>";
	echo "<center><span style='font-size:16px;'><strong>DCMS-Social v.$set[dcms_version]</strong></span></center>";
	echo "<center><span style='font-size:14px;'>官方支持网站 <a href='//dcms-social.ru'>" . get_http_type() . "://dcms-social.ru</a></span></center>";
	echo "";

	$status_version_data = getLatestStableRelease();
	if ($status_version_data['success'] && version_compare($set['dcms_version'], $status_version_data['version']) <= 0) {
		echo "<center><font color='green'>最新版本</font></center>	";
	} elseif ($status_version_data['success']) {
		echo "<center><font color='red'>有个新版本 - " . $status_version_data['version'] . "! <a href='../../adm_panel/update.php'>更详细</a></font></center>";
	} else {
		echo "<center><font color='red'>无法检查更新" . ($status_version_data['error'] ? ': ' . $status_version_data['error'] : NULL) . "</font></center>";
	}
	echo "</div>";

	echo "<div class='main'>";
	echo "<img src='../../style/icons/spam.gif' alt='S' /> <a href='spam'>投诉</a> ";
	include_once "spam/count.php";
	echo "</div>";

	echo "<div class='main'>";
	echo "<img src='../../style/icons/chat.gif' alt='S' /> <a href='chat'>聊天</a> ";
	include_once "chat/count.php";
	echo "</div>";

	if (user_access('adm_panel_show')) {
		echo "<div class='main_seriy'>";
		echo "<div class='main'>";
		echo "<img src='../../style/icons/settings.png' alt='S' /> <a href='../../adm_panel/'>管理面板</a> ";
		echo "</div>";
		echo "</div>";
	}
}
include_once '../../sys/inc/tfoot.php';
