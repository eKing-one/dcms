<?
/*
=======================================
Подарки для Dcms-Social
Автор: Искатель
---------------------------------------
此脚本在许可下被破坏
DCMS-Social 引擎。
使用时，指定引用到
网址 http://dcms-social.ru
---------------------------------------
接点
ICQ：587863132
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
only_reg();
$width = ($webbrowser == 'web' ? '100' : '70'); // Размер подарков при выводе в браузер
if (isset($_GET['id'])) $ank['id'] = intval($_GET['id']);
else $ank['id'] = $user['id']; // Определяем юзера
$ank = user::get_user($ank['id']);
if (!$ank || $ank['id'] == 0) {
	header("Location: /index.php?" . SID);
	exit;
}
$set['title'] = '送给 ' . $ank['nick'] . ' 的礼物';
include_once '../../sys/inc/thead.php';
title();
aut();
/*
==================================
Вывод подарков пользователя
==================================
*/
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" /> '.user::nick($ank['id'],1,0,0) .' | <b>礼物</b>';
echo '</div>';
// Список подарков
$k_post = dbresult(dbquery("SELECT COUNT(id) FROM `gifts_user` WHERE `id_user` = '$ank[id]'" . ($ank['id'] != $user['id'] ? " AND `status` = '1' " : "") . ""), 0);
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '目前没有人送礼物。';
	echo '</div>';
}
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT id,status,coment,id_gift,id_ank,time FROM `gifts_user` WHERE `id_user` = '$ank[id]'" . ($ank['id'] != $user['id'] ? " AND `status` = '1' " : "") . " ORDER BY `time` DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
	$gift = dbassoc(dbquery("SELECT id,name FROM `gift_list` WHERE `id` = '$post[id_gift]' LIMIT 1"));
	$anketa = user::get_user($post['id_ank']);
	/*-----------代码-----------*/
	if ($num == 0) {
		echo '<div class="nav1">';
		$num = 1;
	} elseif ($num == 1) {
		echo '<div class="nav2">';
		$num = 0;
	}
	/*---------------------------*/
	echo '<img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:' . $width . 'px;" alt="*" /><br />';
	echo '<img src="/style/icons/present.gif" alt="*" /> <a href="gift.php?id=' . $post['id'] . '"><b>' . htmlspecialchars($gift['name']) . '</b></a> :: ';
	echo '由 ' . user::nick($anketa['id'], 1, 1, 0) . ' 在 ' . vremja($post['time']) . ' 送出';
	if ($post['status'] == 0) echo ' <font color=red>NEW</font> ';
	echo '</div>';
}
if ($k_page > 1) str('index.php?id=' . intval($_GET['id']) . '&amp;', $k_page, $page); // 输出页数
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" /> ' . user::nick($ank['id'],1,0,0) . '</a> | <b>礼物</b>';
echo '</div>';
include_once '../../sys/inc/tfoot.php';
