<?
/*
=======================================
Статусы юзеров для Dcms-Social
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
// Автор статусов
if (isset($_GET['id']))
	$anketa = user::get_user(intval($_GET['id']));
else
	$anketa = user::get_user($user['id']);
if (!$anketa['id']) {
	header("Location: /index.php");
	exit;
}
if (isset($_GET['reset'])) {
	$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '" . intval($_GET['reset']) . "' LIMIT 1"));
	if ($status['id_user'] == $user['id']) {
		dbquery("UPDATE `status` SET `pokaz` = '0' WHERE `id_user` = '$user[id]'");
		dbquery("UPDATE `status` SET `pokaz` = '1' WHERE `id` = '$status[id]'");
		$_SESSION['message'] = '状态仓促启用';
		header("Location: index.php?id=$anketa[id]");
		exit;
	}
}
$set['title'] = '状态 ' . $anketa['nick'];
include_once '../../sys/inc/thead.php';
title();
err();
aut(); // форма авторизации
/*
==================================
Приватность станички пользователя
Запрещаем просмотр статусов
==================================
*/
$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$anketa[id]'  LIMIT 1"));
$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$anketa[id]') OR (`user` = '$anketa[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$anketa[id]') OR (`user` = '$anketa[id]' AND `to` = '$user[id]') LIMIT 1"), 0);
if ($anketa['id'] != $user['id'] && $user['group_access'] == 0) {
	if (($uSet['privat_str'] == 2 && $frend != 2) || $uSet['privat_str'] == 0) // Начинаем вывод если стр имеет приват настройки
	{
		if ($anketa['group_access'] > 1) echo "<div class='err'>$anketa[group_name]</div>";
		echo "<div class='nav1'>";
		echo group($anketa['id']) . " $anketa[nick] ";
		echo medal($anketa['id']) . " " . online($anketa['id']) . " ";
		echo "</div>";
		echo "<div class='nav2'>";
		user::avatar($anketa['id']);
		echo "</div>";
	}
	if ($uSet['privat_str'] == 2 && $frend != 2) // Если только для друзей
	{
		echo '<div class="mess">';
		echo '只有他的朋友可以查看用户的状态！';
		echo '</div>';
		// В друзья
		if (isset($user)) {
			echo '<div class="nav1">';
			if ($frend_new == 0 && $frend == 0) {
				echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?add=" . $anketa['id'] . "'>添加到好友</a><br />";
			} elseif ($frend_new == 1) {
				echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?otm=$anketa[id]'>拒绝申请</a><br />";
			} elseif ($frend == 2) {
				echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?del=$anketa[id]'>从朋友中删除</a><br />";
			}
			echo "</div>";
		}
		include_once '../../sys/inc/tfoot.php';
		exit;
	}
	if ($uSet['privat_str'] == 0) // Если закрыта
	{
		echo '<div class="mess">';
		echo '用户已禁止查看他的状态！';
		echo '</div>';
		include_once '../../sys/inc/tfoot.php';
		exit;
	}
}
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> " . user::nick($anketa['id'], 1, 0, 0) . " | <b>状态</b>";
echo "</div>";
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `status` WHERE `id_user` = '" . $anketa['id'] . "'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `status` WHERE `id_user` = '" . $anketa['id'] . "' ORDER BY `id` DESC LIMIT $start, $set[p_str]");
echo "<table class='post'>";
if ($k_post == 0) {
	echo "<div class='mess'>";
	echo "没有状态";
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
	echo '<div class="st_1"></div>';
	echo '<div class="st_2">';
	echo output_text($post['msg']);
	echo "</div>";
	echo "<a href='komm.php?id=$post[id]'><img src='/style/icons/bbl4.png' alt=''/>" . dbresult(dbquery("SELECT COUNT(*) FROM `status_komm` WHERE `id_status` = '$post[id]'"), 0) . "</a> ";
	if ($post['pokaz'] == 0) {
		if (isset($user) && ($user['level'] != 0 || $user['id'] == $ank['id']))
			echo "[<a href=\"index.php?id=" . $anketa['id'] . "&amp;reset=$post[id]\"><img src='/style/icons/ok.gif' alt=''/> вкл</a>]";
		if (isset($user) && ($user['level'] > $ank['level'] || $user['level'] != 0 || $user['id'] == $ank['id']))
			echo " [<a href=\"delete.php?id=$post[id]\"><img src='/style/icons/delete.gif' alt=''/> 删除</a>]";
	} else {
		if (isset($user) && ($user['level'] > $ank['level'] || $user['level'] != 0 || $user['id'] == $ank['id']))
			echo " <font color='green'>已安装</font>";
		if (isset($user) && ($user['level'] > $ank['level'] || $user['level'] != 0 || $user['id'] == $ank['id']))
			echo " [<a href=\"delete.php?id=$post[id]\"><img src='/style/icons/delete.gif' alt=''/> 删除</a>]";
	}
	echo '</div>';
}
echo "</table>";
if ($k_page > 1) str("index.php?id=" . $anketa['id'] . '&amp;', $k_page, $page); // 输出页数
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> " . user::nick($anketa['id'], 1, 0, 0) . " | <b>状态</b>";
echo "</div>";
include_once '../../sys/inc/tfoot.php';
