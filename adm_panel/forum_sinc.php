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
user_access('adm_forum_sinc', null, 'index.php?' . session_id());
adm_check();
$set['title'] = '论坛表的同步';
include_once '../sys/inc/thead.php';
title();
err();
aut();

if (isset($_GET['ok']) && isset($_POST['accept'])) {
	$d_r = 0;
	$d_t = 0;
	$d_p = 0;

	// 删除分区
	$q = dbquery("SELECT `id`,`id_forum` FROM `forum_r`");
	while ($razd = dbassoc($q)) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `forum_f` WHERE `id` = '$razd[id_forum]'"), 0) == 0) {
			dbquery("DELETE FROM `forum_r` WHERE `id` = '$razd[id]' LIMIT 1");
			$d_r++;
		}
	}

	// 删除主题
	$q = dbquery("SELECT `id`, `id_razdel`, `id_user` FROM `forum_t`");
	while ($them = dbassoc($q)) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `forum_r` WHERE `id` = '$them[id_razdel]'"), 0) == 0 || dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$them[id_user]'"), 0) == 0) {
			dbquery("DELETE FROM `forum_t` WHERE `id` = '$them[id]' LIMIT 1");
			$d_t++;
		}
	}
	// 删除帖子
	$q = dbquery("SELECT `id`, `id_them`, `id_user` FROM `forum_p`");
	while ($post = dbassoc($q)) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id` = '$post[id_them]'"), 0)==0 || dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$post[id_user]'"), 0) == 0) {
			dbquery("DELETE FROM `forum_p` WHERE `id` = '{$post['id']}' LIMIT 1");
			$d_p++;
		}
	}
	msg("已删除的部分: {$d_r}, 那个: {$d_t}, 职位: {$d_p}");
}

echo "<form method=\"post\" action=\"?ok\">";
echo "<input value=\"开始\" name='accept' type=\"submit\" />";
echo "</form>";
echo "* 根据消息和主题的数量，此操作可能需要很长时间。<br />";
echo "** 建议仅在论坛计数器与真实数据不一致的情况下使用它<br />";

if (user_access('adm_panel_show')){
	echo "<div class='foot'>";
	echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
	echo "</div>";
}
include_once '../sys/inc/tfoot.php';
