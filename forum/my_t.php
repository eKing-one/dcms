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
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'forum' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
	header('Location: /ban.php?'.session_id());
	exit;
}
//网页标题
$set['title']='论坛-我的主题';
include_once '../sys/inc/thead.php';
title();
aut(); 
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" /> <a href="/forum/">论坛</a> | <b>我的主题</b>';
echo '</div>';
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `forum_t`  WHERE `id_user` = '$user[id]'"),0);
$k_page = k_page($k_post,$set['p_str']);
$page = page($k_page);
$start = $set['p_str']*$page-$set['p_str'];
echo '<table class="post">';
$q = dbquery("SELECT * FROM `forum_t` WHERE `id_user` = '$user[id]' ORDER BY `time` DESC  LIMIT $start, $set[p_str]");
// Если список пуст
if ($k_post == 0) 
{
	echo '<div class="mess">';
	echo '您的主题不在论坛中';
	echo '</div>';
}
while ($them = dbarray($q))
{
	// Определение подфорума
	$forum = dbarray(dbquery("SELECT * FROM `forum_f` WHERE `id` = '$them[id_forum]' LIMIT 1"));
	// Определение раздела
	$razdel = dbarray(dbquery("SELECT * FROM `forum_r` WHERE `id` = '$them[id_razdel]' LIMIT 1"));
	// Лесенка дивов
	if ($num == 0)
	{
		echo '<div class="nav1">';
		$num = 1;
	}
	elseif ($num == 1)
	{
		echo '<div class="nav2">';
		$num = 0;
	}
	// Иконка темы
	echo '<img src="/style/themes/' . $set['set_them'] . '/forum/14/them_' . $them['up'] . $them['close'] . '.png" alt="" /> ';
	// Ссылка на тему
	echo '<a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id'] . '/">' . text($them['name']) . '</a> 
	<a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id'] . '/?page=' . $pageEnd . '">
	(' . dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]' AND `id_them` = '$them[id]'"),0) . ')</a><br/>';
	// Подфорум и раздел
	echo '<a href="/forum/' . $forum['id'] . '/">' . text($forum['name']) . '</a> &gt; <a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/">' . text($razdel['name']) . '</a><br />';
	// Последний пост 
	$post = dbarray(dbquery("SELECT * FROM `forum_p` WHERE `id_them` = '$them[id]' AND `id_razdel` = '$razdel[id]' AND `id_forum` = '$forum[id]' ORDER BY `time` DESC LIMIT 1"));
	// Автор последнего поста
    if ($post['id'])  {
	$ank2 = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
	if ($ank2['id']) echo user::nick($ank2['id'],1,1,0) .'  ('. vremja($post['time']).')<br />'; }
	echo '</div>';
}
echo '</table>';
// Вывод cтраниц 
if ($k_page>1)str("?",$k_page,$page); 
// Меню возврата
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" /> <a href="/forum/">论坛</a> | <b>我的主题</b>';
echo '</div>';
include_once '../sys/inc/tfoot.php';
