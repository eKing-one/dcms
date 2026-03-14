<?php
//网页标题
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';

/* 封禁的用户 */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'chat' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /user/ban.php?' . session_id());
	exit;
}

$set['title'] = '聊天室-谁在这里？'; // 页面标题
$in_chat_room = 1;
include_once '../sys/inc/thead.php';
title();
aut();

$k_post = dbresult(dbquery("SELECT COUNT(DISTINCT ul.id_user) AS online_users
                            FROM `user_log` ul
                            WHERE ul.last_online > NOW() - INTERVAL 100 SECOND
                                AND ul.ban = 0
                                AND ul.url LIKE '/chat/%'
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
	echo '<tr><td class="p_t">这里并没有人。</td></tr>';
} else {
	$q = dbquery("SELECT DISTINCT ul.id_user, ul.last_online, ul.url
	              FROM `user_log` ul
	              WHERE ul.last_online > NOW() - INTERVAL 100 SECOND
	                AND ul.ban = 0
	                AND ul.url LIKE '/chat/%'
	              ORDER BY ul.last_online DESC
				  LIMIT $start, $set[p_str]");
	while ($chat = dbarray($q)) {
		echo "<tr>";
		if ($set['set_show_icon'] == 2) {
			echo "<td class='icon48' rowspan='2'>";
			user::avatar($chat['id_user']);
			echo "</td>";
		} elseif ($set['set_show_icon'] == 1) {
			echo "<td class='icon14'>";
			echo user::avatar($chat['id_user']);
			echo "</td>";
		}
		echo "<td class='p_t'>";
		echo user::nick($chat['id_user'], 1, 1, 0);
		echo "</td>";
		echo "</tr>";
	}
}
echo "</table>";

if ($k_page > 1) str("?", $k_page, $page); // 输出页数

include_once '../sys/inc/tfoot.php';
