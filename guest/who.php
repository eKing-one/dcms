<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
/* Бан пользователя */ 
if (isset($user) && dbresult(dbquery("SELECT COUNT(id) FROM `ban` WHERE `razdel` = 'guest' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0)
{
	header('Location: /ban.php?' . SID);
	exit;
}
$set['title'] = '在线游客'; //网页标题
include_once '../sys/inc/thead.php';
title();
aut();
$k_post = dbresult(dbquery("SELECT COUNT(id) FROM `user` WHERE `date_last` > '".(time()-100)."' AND `url` like '/guest/%'"), 0);
$k_page = k_page($k_post,$set['p_str']);
$page = page($k_page);
$start = $set['p_str']*$page-$set['p_str'];
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" /> <a href="index.php">在线游客</a>';
echo '</div>';
echo '<table class="post">';
if ($k_post == 0)
{
	echo '<div class="mess" id="no_object">';
	echo '这里没有人';
	echo '</div>';
}
$q = dbquery("SELECT id FROM `user` WHERE `date_last` > '".(time()-100)."' AND `url` like '/guest/%' ORDER BY `date_last` DESC LIMIT $start, $set[p_str]");
while ($ank = dbassoc($q))
{
	// Лесенка
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	echo user::nick($ank['id'], 1, 1, 0) . '<br />';
	echo '</div>';
}
echo '</table>';
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" /> <a href="index.php">在线游客</a></b>';
echo '</div>';
if ($k_page > 1)str('who.php?', $k_page, $page); // 输出页数
include_once '../sys/inc/tfoot.php';
