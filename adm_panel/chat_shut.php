<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_set_chat',null,'index.php?'.session_id());
adm_check();
$set['title']='聊天笑话';
include_once '../sys/inc/thead.php';
title();

if (isset($_GET['act']) && isset($_FILES['file']['tmp_name'])) {
	if (isset($_POST['replace'])) dbquery('TRUNCATE `chat_shutnik`');
	$k_add=0;
	$list=@file($_FILES['file']['tmp_name']);
	for($i=0;$i<count($list);$i++) {
		$shut=trim($list[$i]);
		if (strlen2($shut)<10)continue;
		dbquery("INSERT INTO `chat_shutnik` (`anek`) VALUES ('".my_esc($shut)."')");
		$k_add++;
	}
	admin_log('聊天','增编',"添加 {$k_add} 笑话");
	msg("成功添加 {$k_add} 从 {$i} 笑话");
}
err();
aut();

echo "数据库中的笑话总数: ".dbresult(dbquery("SELECT COUNT(*) FROM `chat_shutnik`"),0)."<br />";
echo "<form method='post' action='?act={$passgen}' enctype='multipart/form-data'>";
echo "<input type='file' name='file' /><br />";
echo "仅支持UTF-8编码的文本文件。<br />每个笑话应该在一个单独的行。短于10个字符的笑话被忽略。<br />";
echo "<input value='更换' name='replace' type='submit' /><br />";
echo "<input value='添加' name='add' type='submit' /><br />";
echo "</form>";
echo "<div class='foot'>";
echo "&raquo;<a href='/adm_panel/settings_chat.php'>聊天设置</a><br />";
echo "&raquo;<a href='/adm_panel/chat_vopr.php'>问答题</a><br />";
if (user_access('adm_panel_show')) echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
echo "</div>";
include_once '../sys/inc/tfoot.php';
