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
/* 用户面板 */ 
if (isset($user) && dbresult(dbquery("SELECT COUNT(id) FROM `ban` WHERE `razdel` = 'guest' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /user/ban.php?' . session_id());
	exit;
}
$set['title'] = '留言板'; //网页标题
include_once '../sys/inc/thead.php';
title();
aut();

$k_post = dbresult(dbquery("SELECT COUNT(DISTINCT ul.id_user) AS online_users
                            FROM `user_log` ul
                            WHERE ul.last_online > NOW() - INTERVAL 100 SECOND
                                AND ul.ban = 0
                                AND ul.url LIKE '/guest/%'
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

echo '<div class="foot"><img src="/style/icons/str2.gif" /> <a href="index.php">留言板</a></div>';
echo '<table class="post">';
if ($k_post == 0) {
	echo '<div class="mess" id="no_object">';
	echo '这里没有人';
	echo '</div>';
} else {
	$query = dbquery("SELECT DISTINCT ul.id_user, ul.last_online, ul.url
	                  FROM `user_log` ul
	                  WHERE ul.last_online > NOW() - INTERVAL 100 SECOND
	                    AND ul.ban = 0
	                    AND ul.url LIKE '/guest/%'
	                  ORDER BY ul.last_online DESC
					  LIMIT $start, $set[p_str]");
	while ($ank = dbassoc($query)) {
		echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
		$num++;
		echo user::nick($ank['id_user'], 1, 1, 0) . '<br />';
		echo '</div>';
	}
}
echo '</table>';

echo '<div class="foot"><img src="/style/icons/str2.gif" /> <a href="index.php">在线</a></b></div>';
if ($k_page > 1) str('who.php?', $k_page, $page); // 输出页数

include_once '../sys/inc/tfoot.php';
