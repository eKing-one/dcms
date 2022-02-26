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
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'forum' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /ban.php?' . SID);
	exit;
}
$set['title'] = '谁在论坛上？'; //网页标题
include_once '../sys/inc/thead.php';
title();
aut();
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > '" . (time() - 600) . "' AND `url` like '/forum/%'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `user` WHERE `date_last` > '" . (time() - 600) . "' AND `url` like '/forum/%' ORDER BY `date_last` DESC LIMIT $start, $set[p_str]");
echo "<table class='post'>";
if ($k_post == 0) {
	echo "   <tr>";
	echo "  <td class='p_t'>";
	echo "没有人。";
	echo "  </td>";
	echo "   </tr>";
}
while ($forum = dbarray($q)) {
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	echo user::nick($forum['id'],1,1,0). "</td>";
	echo "</div>";
}
echo "</table>";
if ($k_page > 1) str("?", $k_page, $page); // 输出页数
echo "<div class='foot'>
	  &laquo;<a href='/forum/'>回到论坛</a><br />
	  </div>";
include_once '../sys/inc/tfoot.php';
