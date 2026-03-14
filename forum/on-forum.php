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

if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'forum' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /user/ban.php?' . session_id());
	exit;
}
$set['title'] = '谁在论坛上？'; //网页标题
include_once '../sys/inc/thead.php';
title();
aut();

$k_post = dbresult(dbquery("SELECT COUNT(DISTINCT ul.id_user) AS online_users
                            FROM `user_log` ul
                            WHERE ul.last_online > NOW() - INTERVAL 100 SECOND
                                AND ul.ban = 0
                                AND ul.url LIKE '/forum/%'
                                AND ul.last_online = (
                                    SELECT MAX(last_online)
                                    FROM `user_log` ul2
                                    WHERE ul2.id_user = ul.id_user
                                        AND ul2.last_online > NOW() - INTERVAL 100 SECOND
                                        AND ul2.ban = 0
                                )"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];

echo "<table class='post'>";
if ($k_post == 0) {
	echo '<tr><td class="p_t">没有人。</td></tr>';
} else {
	$q = dbquery("SELECT DISTINCT ul.id_user, ul.last_online, ul.url
	              FROM `user_log` ul
	              WHERE ul.last_online > NOW() - INTERVAL 100 SECOND
	                AND ul.ban = 0
	                AND ul.url LIKE '/forum/%'
	              ORDER BY ul.last_online DESC
				  LIMIT $start, $set[p_str]");
	while ($ank = dbassoc($q)) {
		echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
		$num++;
		echo user::nick($ank['id_user'], 1, 1, 0) . '<br />';
		echo '</div>';
	}
}
echo "</table>";

if ($k_page > 1) str("?", $k_page, $page); // 输出页数

echo "<div class='foot'>
	  &laquo;<a href='/forum/'>回到论坛</a><br />
	  </div>";
include_once '../sys/inc/tfoot.php';
