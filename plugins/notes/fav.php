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
/* Бан пользователя */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0')"), 0) != 0) {
	header('Location: /user/ban.php?' . session_id());
	exit;
}
$set['title'] = '已添加书签';
include_once '../../sys/inc/thead.php';
title();
aut();
if (dbresult(dbquery("SELECT COUNT(*)FROM `notes` WHERE `id`='" . intval($_GET['id']) . "' LIMIT 1"), 0) == 0) {
	echo "<div class='err'>别紧张，别紧张！没有这样的日记。</div>";
	include_once '../../sys/inc/tfoot.php';
	exit;
} else {
	$k_post = dbresult(dbquery("SELECT COUNT(*)FROM `bookmarks` WHERE `id_object`='" . intval($_GET['id']) . "' AND `type`='notes' "), 0);
	$k_page = k_page($k_post, $set['p_str']);
	$page = page($k_page);
	$start = $set['p_str'] * $page - $set['p_str'];
	if ($k_post == 0) {
		echo "<div class='mess'>没人在书签上。</div>";
	} else {
		$q = dbquery("SELECT*FROM `bookmarks` WHERE `id_object`='" . intval($_GET['id']) . "' AND `type`='notes' LIMIT $start,$set[p_str]");
		while ($post = dbassoc($q)) {
			echo "<div class='nav2'>". user::nick($post['id_user'], 1, 1, 0) . " ";
			echo "增加 " . vremja($post['time']) . "</div>";
		}
		if ($k_page > 1) str("?id=" . intval($_GET['id']) . "&amp;", $k_page, $page); // 输出页数
	}
	include_once '../../sys/inc/tfoot.php';
}
