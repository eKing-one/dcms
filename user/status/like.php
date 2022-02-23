<?
/*
=======================================
Статусы юзеров для Dcms-Social
Автор: Искатель
---------------------------------------
Этот скрипт распостроняется по лицензии
движка Dcms-Social. 
При использовании указывать ссылку на
оф. сайт http://dcms-social.ru
---------------------------------------
Контакты
ICQ: 587863132
http://dcms-social.ru
=======================================
*/
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
$set['title'] = '喜欢地位';
include_once '../../sys/inc/thead.php';
title();
if (dbresult(dbquery("SELECT COUNT(*) FROM `status` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1", $db), 0) == 0) {
	header("Location: index.php?" . SID);
	exit;
}
// Статус
$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));
// Автор
$anketa = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $status[id_user] LIMIT 1"));
err();
aut(); // форма авторизации
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/info.php?id=$anketa[id]\">$anketa[nick]</a> | <a href='index.php?id=" . $anketa['id'] . "'>状态</a> | <b>评分</b>";
echo "</div>";
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '" . intval($_GET['id']) . "'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `status_like` WHERE `id_status` = '" . intval($_GET['id']) . "' ORDER BY `id` DESC LIMIT $start, $set[p_str]");
echo "<table class='post'>";
if ($k_post == 0) {
	echo "<div class='mess'>";
	echo "他们还没有投票支持这个地位";
	echo "</div>";
}
while ($post = dbassoc($q)) {
	$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
	/*-----------代码-----------*/
	if ($num == 0) {
		echo '<div class="nav1">';
		$num = 1;
	} elseif ($num == 1) {
		echo '<div class="nav2">';
		$num = 0;
	}
	/*---------------------------*/
	echo user::avatar($ank['id']) . " <a href='/info.php?id=$ank[id]'>$ank[nick]</a> ";
	echo medal($ank['id']) . online($ank['id']) . " (" . vremja($post['time']) . ")";
	$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id_user` = '$ank[id]' AND `pokaz` = '1' LIMIT 1"));
	if ($status['id']) {
		echo '<div class="st_1"></div>';
		echo '<div class="st_2">';
		echo "<a href='/user/status/komm.php?id=$status[id]'>" . output_text($status['msg']) . "</a>";
		echo "</div>";
	}
	echo "</div>";
}
echo "</table>";
if ($k_page > 1) str("like.php?id=" . intval($_GET['id']) . '&amp;', $k_page, $page); // 输出页数
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/info.php?id=$anketa[id]\">$anketa[nick]</a> | <a href='index.php?id=" . $anketa['id'] . "'>状态</a> | <b>评分</b>";
echo "</div>";
include_once '../../sys/inc/tfoot.php';
