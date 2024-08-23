<?php
include_once '../sys/inc/home.php'; 
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
only_reg();
$set['title'] = '登录历史';
include_once '../sys/inc/thead.php';
title();
aut();
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `user_log` WHERE `id_user` = '$user[id]'"),0);
$k_page = k_page($k_post,$set['p_str']);
$page = page($k_page);
$start = $set['p_str']*$page-$set['p_str'];
echo '<table class="post">';
if (empty($k_post))
{
	 echo '<div class="mess">';
	 echo '没有登录历史';
	 echo '</div>';
}	 
$q = dbquery("SELECT * FROM `user_log` WHERE `id_user` = '".$user['id']."' ORDER BY `id` DESC  LIMIT $start, $set[p_str]");
while ($post = dbassoc($q))
{
	$ank = user::get_user($user['id']);
	// Лесенка
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	echo '<img src="/style/my_menu/logout_16.png" alt="" />';
	if ($post['method'] != 1)
		echo ' 登录信息<br />';
	else
		echo ' 用户名及密码登录 (' . vremja($post['time']) . ')<br />';
	echo 'IP: ' . long2ip($post['ip']) . '<br />';
	echo '浏览器: ' . output_text($post['ua']);
	echo '</div>';
}
echo '</table>';
// 输出页数
if ($k_page > 1)str("?",$k_page,$page);  
echo '<div class="foot">';
echo '<img src="/style/icons/str.gif" alt="*" /> <a href="/user/info.php">我的页面</a><br />';
echo '<img src="/style/icons/str.gif" alt="*" /> <a href="/user/my_aut.php">我的菜单</a><br />';
echo '</div>';
include_once '../sys/inc/tfoot.php';
