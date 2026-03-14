<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
$temp_set = $set;
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_set_sys', null, 'index.php?' . session_id());
adm_check();
$set['title'] = '开发者选项';
include_once '../sys/inc/thead.php';
title();

if (isset($_POST['save'])) {
    $temp_set['use_mysqli'] = intval($_POST['use_mysqli']);
}
if (save_settings($temp_set)) {
    admin_log('设置', '系统', '更改开发者选项');
    msg('已成功接受设置');
} else {
    $err[] = '更改配置文件失败';
}

err();
aut();

echo "<form method=\"post\" action=\"?\">";
echo "使用MySQLi：<br />
<select name='use_mysqli'>
	<option " . (setget('use_mysqli', 1) == 1 ? " selected " : null) . " value='1'>启用</option>
	<option " . (setget('use_mysqli', 1) == 0 ? " selected " : null) . " value='0'>禁用</option>
</select><br />";
echo "* 如果选择了禁用，dbquery、dbrows、dbresult等DCMS内置的过时接口会使用PDO来执行而不是MySQLi，这可能会造成一些兼容问题<br />";
echo "<input value=\"修改\" name='save' type=\"submit\" />";
echo "</form>";

if (user_access('adm_panel_show')) {
	echo "<div class='foot'>";
	echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
	echo "</div>";
}
include_once '../sys/inc/tfoot.php';