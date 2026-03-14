<?php
$current_page = 'index';
require_once 'sys/inc/start.php';
require_once 'sys/inc/compress.php';
require_once 'sys/inc/sess.php';
require_once 'sys/inc/home.php';
require_once 'sys/inc/settings.php';
require_once 'sys/inc/db_connect.php';
require_once 'sys/inc/ipua.php';
require_once 'sys/inc/fnc.php';
require_once 'sys/inc/user.php';
require_once 'sys/inc/icons.php'; // 主菜单图标
require_once 'sys/inc/thead.php';
title();
err();

// 隐藏新闻
if (isset($user) && isset($_GET['news_read'])) {
	dbquery("update `user` set `news_read` = '1' where `id` = '$user[id]' limit 1");
	msg("该消息已成功隐藏");
}

if (!$set['web']) {
	// 获取在线用户数量
	$ol_user = dbresult(dbquery("SELECT COUNT(DISTINCT ul.id_user) AS online_users FROM `user_log` ul WHERE ul.last_online > NOW() - INTERVAL 10 MINUTE AND ul.ban = 0 AND ul.last_online = (SELECT MAX(last_online) FROM `user_log` ul2 WHERE ul2.id_user = ul.id_user AND ul2.last_online > NOW() - INTERVAL 10 MINUTE AND ul2.ban = 0)"), 0);
	// 在线游客数量
	$ol_guest = dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `date_last` > ".(time()-600)." AND `pereh` > '0'"), 0);
	echo '<div class="title" sytle="text-align: center;">
	      <a href="./user/online.php" title="查看在线用户" class="user-count-online-link">
	          <span class="user-count-small-text">在线 </span>
	          <span class="user-count">' . $ol_user . '</span>
	      </a>
	      <a href="./user/online_g.php" title="查看在线游客" class="user-count-online-link">
	          <span class="user-count-small-text"> (</span>
	          <span class="user-count">+' . $ol_guest . '</span>
	          <span class="user-count-small-text"> 游客 )</span>
	      </a>
	      </div>
	      <div class="main_menu">';

	if (isset($user)) {
		echo '<div align="right">
		          <img src="./style/icons/icon_stranica.gif" alt="DS" />' . user::nick($user['id'], 1, 0, 0) . ' | <a href="./user/exit.php"><font color="#ff0000">退出</font></a>
		      </div>';
	} else {
		echo '<div align="right"><a href="./user/aut.php">登录</a> | <a href="./user/reg.php">注册</a></div>';
	}
	echo '</div>';

	// 新闻&事件 
	include 'sys/inc/news_main.php'; 
	// 主菜单
	include 'sys/inc/main_menu.php'; 
	include 'sys/inc/main_notes.php';

} else {
	// 主要网页主题
	include 'style/themes/' . $set['set_them'] . '/index.php'; 
}

require_once 'sys/inc/tfoot.php';