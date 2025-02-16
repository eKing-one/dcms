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
/* 用户封禁 */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '{$user['id']}' AND (`time` > '{$time}' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /user/ban.php?' . session_id());
	exit;
}

if (isset($_GET['id'])) {
	$ank['id'] = intval($_GET['id']);
} elseif (isset($user)) {
	$ank['id'] = $user['id'];
} else {
	$ank['id'] = 0;
}
$ank = user::get_user($ank['id']);
if ($ank['id'] == 0) {
	$set['title'] = '日记 - ' . $ank['nick'] . '';
	include_once '../../sys/inc/thead.php';
	$ank = user::get_user($ank['id']);
	$err = '错误请求！';
	err();
	include_once '../../sys/inc/tfoot.php';
}

$set['title'] = '日记 - ' . $ank['nick'] . '';
include_once '../../sys/inc/thead.php';
title();
aut(); // 授权表格

if (isset($_GET['sort']) && $_GET['sort'] == 't') {
	$order = 'order by `time` desc';
} elseif (isset($_GET['sort']) && $_GET['sort'] == 'c') {
	$order = 'order by `count` desc';
} else {
	$order = 'order by `time` desc';
}

if (isset($user) && $user['id'] == $ank['id']) {
	echo '<div class="foot">';
	echo "<a href=\"add.php\">写日记</a>";
	echo '</div>';
}
if (isset($_GET['sort']) && $_GET['sort'] == 't') {
	echo '<div class="foot">';
	echo "<b>新的</b> | <a href='?id={$ank['id']}&amp;sort=c'>热门的</a>";
	echo '</div>';
} elseif (isset($_GET['sort']) && $_GET['sort'] == 'c') {
	echo '<div class="foot">';
	echo "<a href='?id={$ank['id']}&amp;sort=t'>新的</a> | <b>热门的</b>";
	echo '</div>';
} else {
	echo '<div class="foot">';
	echo "<b>新的</b> | <a href='?id={$ank['id']}&amp;sort=c'>热门的</a>";
	echo '</div>';
}

$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `id_user` = '{$ank['id']}' "), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `notes` WHERE `id_user` = '{$ank['id']}' {$order} LIMIT {$start}, {$set['p_str']}");
echo "<table class='post'>";
if ($k_post == 0) {
	echo "  <div class='mess'>";
	echo "没有日记";
	echo "  </div>";
}
$num = 0;
while ($post = dbassoc($q)) {
	/*-----------代码-----------*/
	if ($num == 0) {
		echo '<div class="nav1">';
		$num = 1;
	} elseif ($num == 1) {
		echo '<div class="nav2">';
		$num = 0;
	}
	/*---------------------------*/
	echo "<img src='/style/icons/dnev.png' alt='*'> ";
	echo "<a href='list.php?id={$post['id']}'>" . text($post['name']) . "</a>";
	echo " <span style='time'>(" . vremja($post['time']) . ")</span> <br />";
	$k_n = dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `id` = '{$post['id']}' AND `time` > '" . $ftime . "'", $db), 0);
	echo "   </div>";
}
echo "</table>";

if (isset($_GET['sort'])) $dop = "sort={$_GET['sort']}&amp;";
else $dop = '';
if ($k_page > 1) str('?id=' . $ank['id'] . '&amp;' . $dop . '', $k_page, $page); // 输出页数
include_once '../../sys/inc/tfoot.php';
