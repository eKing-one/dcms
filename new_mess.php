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
only_reg();
$set['title'] = '新邮件';
include_once 'sys/inc/thead.php';
title();
aut();
$k_post = dbresult(dbquery("SELECT COUNT(DISTINCT `mail`.`id_user`) FROM `mail`
 LEFT JOIN `users_konts` ON `mail`.`id_user` = `users_konts`.`id_kont` AND `users_konts`.`id_user` = '$user[id]'
 WHERE `mail`.`id_kont` = '$user[id]' AND (`users_konts`.`type` IS NULL OR `users_konts`.`type` = 'common' OR `users_konts`.`type` = 'favorite') AND `mail`.`read` = '0'"), 0);
//echo mysql_error(),"<br />";
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo "<table class='post'>";
if ($k_post == 0) {
	echo "  <div class='mess'>";
	echo "没有新消息";
	echo "  </div";
} else {
	$q = dbquery("SELECT MAX(`mail`.`time`) AS `last_time`, COUNT(`mail`.`id`) AS `count`, `mail`.`id_user`, `users_konts`.`name` FROM `mail`
 LEFT JOIN `users_konts` ON `mail`.`id_user` = `users_konts`.`id_kont` AND `users_konts`.`id_user` = '$user[id]'
 WHERE `mail`.`id_kont` = '$user[id]' AND (`users_konts`.`type` IS NULL  OR `users_konts`.`type` = 'common' OR `users_konts`.`type` = 'favorite') AND `mail`.`read` = '0'
  GROUP BY `mail`.`id_user` ORDER BY `count` DESC LIMIT $start, $set[p_str]");
	//echo mysql_error(),"<br />";
	while ($kont = dbassoc($q)) {
		$ank = get_user($kont['id_user']);
		/*-----------зебра-----------*/
		if ($num == 0) {
			echo "  <div class='nav1'>";
			$num = 1;
		} elseif ($num == 1) {
			echo "  <div class='nav2'>";
			$num = 0;
		}
		/*---------------------------*/
		if ($ank)
			echo status($ank['id']) . group($ank['id']) . " <a href='/info.php?id=$ank[id]'>" . ($kont['name'] ? $kont['name'] : $ank['nick']) . "</a> " . medal($ank['id']) . " " . online($ank['id']) . " ";
		else
			echo "<a href='/mail.php?id=$ank[id]'>[DELETED] (+$kont[count])";
		echo "<font color='#1e00ff'>" . vremja($kont['last_time']) . "</font><br />";
		echo "<img src='/style/icons/new_mess.gif' alt='*' /> ";
		echo "<a href='/mail.php?id=$ank[id]'>信息</a> <font color='red'>+$kont[count]</font><br />";
		echo "  </div>";
	}
}
echo "</table>";
if ($k_page > 1) str('?', $k_page, $page); // Вывод страниц
echo "<div class='foot'>";
echo "<img src='/style/icons/konts.png' alt='*' /> <a href='/konts.php?$passgen'>联络人</a><br />";
echo "</div>";
include_once 'sys/inc/tfoot.php';