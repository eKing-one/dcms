<?php
/**
 * 删除某条留言内容
 */

require_once '../sys/inc/start.php';
require_once '../sys/inc/compress.php';
require_once '../sys/inc/sess.php';
require_once '../sys/inc/home.php';
require_once '../sys/inc/settings.php';
require_once '../sys/inc/db_connect.php';
require_once '../sys/inc/ipua.php';
require_once '../sys/inc/fnc.php';
require_once '../sys/inc/user.php';

if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `guest` WHERE `id` = '".intval($_GET['id'])."'"),0) == 1) {
	$post = dbassoc(dbquery("SELECT * FROM `guest` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));
	$page = $_GET['referer_page'] ?? 1;
	if ($post['id_user'] == 0) {
		$ank['id'] = 0;
		$ank['pol'] = 'guest';
		$ank['level'] = 0;
		$ank['nick'] = '客人';
	} else {
		$ank = user::get_user($post['id_user']);
	}
	if (isset($_POST['ok']) && $_POST['ok'] == 1) {
		if (user_access('guest_delete') || (isset($user['id']) && $user['id'] == $post['id_user'])) {
			if ($user['id'] != $post['id_user']) admin_log('留言板', '删除邮件', '从中删除消息 ' . $ank['nick']);
			dbquery("DELETE FROM `guest` WHERE `id` = '$post[id]'");
		}
		header("Location: index.php?page={$page}");
	} else {
		$set['title'] = '确认删除留言';
		require_once '../sys/inc/thead.php';
		title();
		aut();
		err();

		echo '<table class="post"><div class="nav1">';
		echo ($post['id_user'] != '0' ? user::avatar($post['id_user'], 0) . user::nick($post['id_user'], 1, 1, 0) : user::avatar(0, 0) . ' <b>' . '游客' . '</b> ');
		echo '(' . vremja($post['time']) . ')<br />';
		echo output_text($post['msg']) . '<br />';
		echo '</div></table>';

		echo "<form method='post' name='delete' action='?id={$post['id']}&referer_page={$page}'>";
		echo '<input type="hidden" name="ok" value="1">';
		echo '<input value="确认删除" type="submit" />';
		echo '</form>';
		echo '<div class="foot">';
		echo "<img src='../../style/icons/str2.gif' alt='*'> <a href='index.php?page={$page}'>留言板</a>";
		echo "</div>";
		require_once '../sys/inc/tfoot.php';
	}
}
