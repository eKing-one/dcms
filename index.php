<?php
include_once 'sys/inc/start.php';
include_once 'sys/inc/compress.php';
include_once 'sys/inc/sess.php';
include_once 'sys/inc/home.php';
include_once 'sys/inc/settings.php';
include_once 'sys/inc/db_connect.php';
include_once 'sys/inc/ipua.php';
include_once 'sys/inc/fnc.php';
include_once 'sys/inc/user.php';
include_once 'sys/inc/icons.php'; // 主菜单图标
include_once 'sys/inc/thead.php';
title();
err();
if (!$set['web'])
{
	?>
	<div class="title">
	<center>
	<a href="/online.php" title="在线" style="color:#cdcecf; text-decoration: none">
	<font color="#fee300" size="2">在线 </font>
	<font color="#ffffff"><?=dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > ".(time()-600).""), 0)?></font>
	</a>
	<font color="#fee300" size="2"> (</font>
	<font color="#ffffff">+<?=dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `date_last` > ".(time()-600)." AND `pereh` > '0'"), 0)?></font>
	<font color="#fee300" size="2"> 在线游客 )</font>
	</center>
	</div>
	<div class='main_menu'>
	<?php
	if (isset($user))
	{
		?>
		<div align="right">
		<img src="/style/icons/icon_stranica.gif" alt="DS" />
		<?=user::nick($user['id'])?> | <a href="exit.php"><font color="#ff0000">退出</font></a>
		</div>
		<?php
	}
	else
	{
		?>
		<div align="right">
		<a href="/aut.php">登录</a> | <a href="/reg.php">注册</a>
		</div>
		<?
	}
	?></div><?
	// 新闻&事件 
	include_once 'sys/inc/news_main.php'; 
	// 主菜单
	include_once 'sys/inc/main_menu.php'; 
	include_once H.'sys/inc/main_notes.php';
}
else
{
	// 主要网页主题
	include_once 'style/themes/' . $set['set_them'] . '/index.php'; 
}
include_once 'sys/inc/tfoot.php';
?>