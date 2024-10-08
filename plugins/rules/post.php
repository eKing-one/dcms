<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
if ($set['allow_guest_rules_page'] == '1') {
	$show_all = true; // 为游客开放
}
include_once '../../sys/inc/user.php';
include_once '../../sys/inc/thead.php';
$post = dbassoc(dbquery("SELECT * FROM `rules` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));
$set['title'] = htmlspecialchars($post['title']);
title();
aut(); // форма авторизации
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `rules`"), 0);
if (!isset($_GET['id']) && !is_numeric($_GET['id']));
if ($user['level'] > 2) {
	if (isset($_POST['msg']) && isset($user)) {
		$msg = $_POST['msg'];
		if (strlen2($msg) > 99999) {
			$err = '信息太长了';
		} elseif (strlen2($msg) < 2) {
			$err = '短消息';
		} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `rules_p` WHERE `id_news` = '" . intval($_GET['id']) . "' AND `id_user` = '$user[id]' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"), 0) != 0) {
			$err = '您的消息重复前一个';
		} elseif (!isset($err)) {
			$pos = dbresult(dbquery("SELECT MAX(`pos`) FROM `rules_p` WHERE `id_news` = '" . intval($_GET['id']) . "'"), 0) + 1;
			dbquery("INSERT INTO `rules_p` (`pos`, `id_user`, `time`, `msg`, `id_news`) values('$pos', '$user[id]', '$time', '" . my_esc($msg) . "', '" . intval($_GET['id']) . "')");
			$_SESSION['message'] = '您的帖子已被成功接受';
			header("Location: ?id=$post[id]");
			exit;
		}
	}
	if (isset($_GET['ids'])) $menu = dbassoc(dbquery("SELECT * FROM `rules_p` WHERE `id` = '" . intval($_GET['ids']) . "' LIMIT 1"));
	if (isset($_GET['ids']) && isset($_GET['act']) && $_GET['act'] == 'up') {
		dbquery("UPDATE `rules_p` SET `pos` = '" . ($menu['pos']) . "' WHERE `pos` = '" . ($menu['pos'] - 1) . "' LIMIT 1");
		dbquery("UPDATE `rules_p` SET `pos` = '" . ($menu['pos'] - 1) . "' WHERE `id` = '" . intval($_GET['ids']) . "' LIMIT 1");
		$_SESSION['message'] = '菜单项已向上移动一个位置';
		header("Location: ?id=$post[id]");
		exit;
	}
	if (isset($_GET['ids']) && isset($_GET['act']) && $_GET['act'] == 'down') {
		dbquery("UPDATE `rules_p` SET `pos` = '" . ($menu['pos']) . "' WHERE `pos` = '" . ($menu['pos'] + 1) . "' LIMIT 1");
		dbquery("UPDATE `rules_p` SET `pos` = '" . ($menu['pos'] + 1) . "' WHERE `id` = '" . intval($_GET['ids']) . "' LIMIT 1");
		$_SESSION['message'] = '菜单项已向下移动一个位置';
		header("Location: ?id=$post[id]");
		exit;
	}
}
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `rules_p` WHERE `id_news` = '" . intval($_GET['id']) . "'"), 0);
$q = dbquery("SELECT * FROM `rules_p` WHERE `id_news` = '" . intval($_GET['id']) . "' ORDER BY `pos` ASC");
echo "<table class='post'>";
while ($post2 = dbassoc($q)) {
	$ank = user::get_user($post2['id_user']);
	/*-----------代码-----------*/
	if ($num == 0) {
		echo '<div class="nav1">';
		$num = 1;
	} elseif ($num == 1) {
		echo '<div class="nav2">';
		$num = 0;
	}
	/*---------------------------*/
	echo (($user['level'] > 2) ? $post2['pos'] . ") " : "");
	echo output_text($post2['msg']) . '</br>';
	if ($user['level'] > 2) {
		echo '<a href="?ids=' . $post2['id'] . '&amp;id=' . $post['id'] . '&amp;act=up&amp;' . $passgen . '"><img src="/style/icons/up.gif" alt="*" /></a> | ';
		echo '<a href="?ids=' . $post2['id'] . '&amp;id=' . $post['id'] . '&amp;act=down&amp;' . $passgen . '"><img src="/style/icons/down.gif" alt="*" /></a> | ';
		echo '<a href="edit.php?id=' . $post2['id'] . '&amp;act=edit&amp;' . $passgen . '"><img src="/style/icons/edit.gif" alt="*" /></a> | ';
		echo '<a href="delete.php?del=' . $post2['id'] . '"><img src="/style/icons/delete.gif" alt="*" /></a>';
	}
	echo '</div>';
}
echo '</table>';
if ($user['level'] > 2) {
	if (isset($_GET['new'])) {
		echo '<form method="post" name="message" action="?id=' . intval($_GET['id']) . '">';
		if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
			include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
		else
			echo '<textarea name="msg"></textarea><br />';
		echo '<input value="添加" type="submit" />';
		echo '</form>';
	}
	echo '<div class="foot"><img src="/style/icons/ok.gif" alt="*"/> <a href="post.php?id=' . intval($_GET['id']) . '&new">新回复</a></div>';
}
echo '<div class="foot"><img src="/style/icons/str2.gif" alt="*"/> <a href="index.php">资料</a></div>';
include_once '../../sys/inc/tfoot.php';
