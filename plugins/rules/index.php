<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
if ($set['allow_guest_help_page'] == '1') {
	$show_all = true; // 为游客开放
} else {
	// 临时使用，需要改进
	// 不知道禁止游客访问是怎么实现的，暂时先直接转跳到登录页
	header("Location: /user/aut.php");
	exit;
}
include_once '../../sys/inc/user.php';
$set['title']='网站资料与帮助';
include_once '../../sys/inc/thead.php';
title();
aut(); // форма авторизации

if (isset($user) && $user['level'] > 2) {
	if (isset($_GET['del']) && is_numeric($_GET['del']) && dbresult(dbquery("SELECT COUNT(*) FROM `rules` WHERE `id` = '".intval($_GET['del'])."' LIMIT 1",$db), 0)==1) {
		dbquery("DELETE FROM `rules` WHERE `id` = '".intval($_GET['del'])."' LIMIT 1");
		dbquery("OPTIMIZE TABLE `rules`");
		$_SESSION['message'] = '项目成功删除';
		header("Location: ?");
		exit;
	}
}

if (isset($_GET['id']) && isset($_GET['act']) && dbresult(dbquery("SELECT COUNT(*) FROM `rules` WHERE `id` = '".intval($_GET['id'])."'"),0)) {
	$menu=dbassoc(dbquery("SELECT * FROM `rules` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));
	if ($_GET['act']=='up' && $user['level'] > 2) {
		dbquery("UPDATE `rules` SET `pos` = '".($menu['pos'])."' WHERE `pos` = '".($menu['pos']-1)."' LIMIT 1");
		dbquery("UPDATE `rules` SET `pos` = '".($menu['pos']-1)."' WHERE `id` = '".intval($_GET['id'])."' LIMIT 1");
		$_SESSION['message'] = '该项目已成功上移';
		header("Location: ?");
		exit;
	}
	if ($_GET['act']=='down' && $user['level'] > 2) {
		dbquery("UPDATE `rules` SET `pos` = '".($menu['pos'])."' WHERE `pos` = '".($menu['pos']+1)."' LIMIT 1");
		dbquery("UPDATE `rules` SET `pos` = '".($menu['pos']+1)."' WHERE `id` = '".intval($_GET['id'])."' LIMIT 1");
		$_SESSION['message'] = '该项目已成功下移';
		header("Location: ?");
		exit;
	}
}
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `rules`"),0);
$q = dbquery("SELECT * FROM `rules` ORDER BY `pos` ASC");
echo '<table class="post">';
if ($k_post==0) {
	echo '<div class="mess">';
	echo '信息部分没有填写';
	echo '</div>';
}
while ($post = dbassoc($q)) {
	/*-----------代码-----------*/
	if ($num==0) {
		echo '<div class="nav1">';
		$num=1;
	} elseif ($num==1) {
		echo '<div class="nav2">';
		$num=0;
	}
	/*---------------------------*/
	if ($post['title'])echo (($user['level'] > 2) ? $post['pos'] . ") " : "") . ' <a href="post.php?id=' . $post['id'] . '">' . output_text($post['title']) . '</a> ';
	if ($post['url'])echo (($user['level'] > 2) ? $post['pos'] . ") " : "") . ' <a href="' . htmlspecialchars($post['url']) . '">' . output_text($post['name_url']) . '</a> ';
	if ($post['msg'])echo (($user['level'] > 2)? $post['pos'] . ") " : "") . output_text($post['msg']) . ' ';
	if ($user['level'] > 2) {
		echo '<a href="?id=' . $post['id'] . '&amp;act=up&amp;' . $passgen . '"><img src="/style/icons/up.gif" alt="*" /></a> | ';
		echo '<a href="?id=' . $post['id'] . '&amp;act=down&amp;' . $passgen . '"><img src="/style/icons/down.gif" alt="*" /></a> | ';
		echo '<a href="edit.php?id=' . $post['id'] . '&amp;act=edits&amp;' . $passgen . '"><img src="/style/icons/edit.gif" alt="*" /></a> | ';
		echo '<a href="index.php?del=' . $post['id'] . '"><img src="/style/icons/delete.gif" alt="*" /></a>';
	}
	echo '</div>';
}
echo '</table>';
if ($user['level'] > 2) {
	echo '<div class="foot"><img src="/style/icons/ok.gif" alt="*" /> <a href="new.php?msg">添加一段文本</a></div>';
	echo '<div class="foot"><img src="/style/icons/ok.gif" alt="*" /> <a href="new.php?post">添加一个项目</a></div>';
	echo '<div class="foot"><img src="/style/icons/ok.gif" alt="*" /> <a href="new.php?url">添加一个链接</a></div>';
}
include_once '../../sys/inc/tfoot.php';
