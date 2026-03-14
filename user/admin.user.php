<?php
require_once '../sys/inc/start.php';
require_once '../sys/inc/compress.php';
require_once '../sys/inc/sess.php';
require_once '../sys/inc/home.php';
require_once '../sys/inc/settings.php';
require_once '../sys/inc/db_connect.php';
require_once '../sys/inc/ipua.php';
require_once '../sys/inc/fnc.php';
require_once '../sys/inc/user.php';
$set['title'] = '管理工作'; //网页标题
require_once '../sys/inc/thead.php';
title();
aut();

$s = 0;
if (isset($_GET['adm'])) {
	$gr = "`group_access` > '7' AND `group_access` < '16'";
} else
if (isset($_GET['mod'])) {
	$gr = "`group_access` = '7'";
} else
if (isset($_GET['zone'])) {
	$gr = "`group_access` = '4'";
} else
if (isset($_GET['forum'])) {
	$gr = "`group_access` = '3'";
} else
if (isset($_GET['chat'])) {
	$gr = "`group_access` = '2'";
} else
if (isset($_GET['notes'])) {
	$gr = "`group_access` = '11'";
} else
if (isset($_GET['guest'])) {
	$gr = "`group_access` = '12'";
} else {
	$s = 1;
}

if (!isset($_GET['adm']) && !isset($_GET['mod']) && !isset($_GET['zone']) && !isset($_GET['forum']) && !isset($_GET['chat']) && !isset($_GET['notes'])  && !isset($_GET['guest'])) {
	echo "<div class = 'nav2'>";
	echo "<img src='../style/icons/adm.gif' alt='S' /> <a href='?guest'>嘉宾版主</a>";
	echo "</div>";
	echo "<div class = 'nav1'>";
	echo "<img src='../style/icons/adm.gif' alt='S' /> <a href='?notes'>日记版主</a>";
	echo "</div>";
	echo "<div class = 'nav2'>";
	echo "<img src='../style/icons/adm.gif' alt='S' /> <a href='?chat'>聊天版主</a>";
	echo "</div>";
	echo "<div class = 'nav1'>";
	echo "<img src='../style/icons/adm.gif' alt='S' /> <a href='?forum'>论坛版主</a>";
	echo "</div>";
	echo "<div class = 'nav2'>";
	echo "<img src='../style/icons/adm.gif' alt='S' /> <a href='?zone'>交流区版主</a>";
	echo "</div>";
	echo "<div class = 'nav1'>";
	echo "<img src='../style/icons/adm.gif' alt='S' /> <a href='?mod'>总版主</a>";
	echo "</div>";
	echo "<div class = 'nav1'>";
	echo "<img src='../style/icons/adm.gif' alt='S' /> <a href='?adm'>管理员</a>";
	echo "</div>";
}

if ($s == 1) {
	echo "<div class = 'foot'>在线管理</div>";
	// 计算时间戳（最近 600 秒）
	$time_threshold = date('Y-m-d H:i:s', time() - 600);

	// 计数总记录数
	$count_querySQL = "
		SELECT COUNT(DISTINCT u.id) 
		FROM `user` u
		INNER JOIN (
			SELECT id_user, MAX(last_online) AS last_online
			FROM `user_log`
			GROUP BY id_user
		) ul ON u.id = ul.id_user
		WHERE u.`group_access` > '1' 
		AND ul.`last_online` > '$time_threshold'
	";
	$k_post = dbresult(dbquery($count_querySQL), 0);
	$k_page = k_page($k_post, $set['p_str']);
	$page = page($k_page);
	$start = $set['p_str'] * $page - $set['p_str'];

	// 使用子查询获取每个用户最新的 last_online 时间
	$data_querySQL = "
	    SELECT u.*, ul.last_online 
	    FROM `user` u
	    INNER JOIN (
	        SELECT id_user, MAX(last_online) AS last_online
	        FROM `user_log`
	        GROUP BY id_user
	    ) ul ON u.id = ul.id_user
	    WHERE u.`group_access` > '1' 
	    AND ul.`last_online` > '$time_threshold'
	    ORDER BY ul.`last_online` DESC 
	    LIMIT {$start}, {$set['p_str']}
	";
} else {
	$count_querySQL = "SELECT COUNT(*) FROM `user` WHERE $gr";
	$k_post = dbresult(dbquery($count_querySQL), 0);
	$k_page = k_page($k_post, $set['p_str']);
	$page = page($k_page);
	$start = $set['p_str'] * $page - $set['p_str'];

	$data_querySQL = "SELECT * FROM `user` WHERE $gr LIMIT $start, $set[p_str]";
}

$q = dbquery($data_querySQL);

echo "<table class='post'>";
if ($k_post == 0) {
	echo '<div class="mess">列表为空</div>';
} else {
	while ($ank = dbassoc($q)) {
		$ank = user::get_user($ank['id']);
		/*-----------代码-----------*/
		if ($num == 0) {
			echo '<div class="nav1">';
			$num = 1;
		} elseif ($num == 1) {
			echo '<div class="nav2">';
			$num = 0;
		}
		/*---------------------------*/
		if ($set['set_show_icon'] == 2) {
			user::avatar($ank['id']);
		} elseif ($set['set_show_icon'] == 1) {
			echo "" . user::avatar($ank['id']) . "";
		}
		echo "" . user::nick($ank['id'],1,1,0) .  " <br />";
		echo "$ank[group_name]";
		if ($ank['id'] != $user['id']) {
			echo "<br /> <a href=\"/user/mail.php?id={$ank['id']}\"><img src='/style/icons/pochta.gif' alt='*' /> 信息</a> ";
		}
		echo "</div>";
	}
}
echo "</table>";

if ($k_page > 1) str("?", $k_page, $page); // 输出页数

require_once '../sys/inc/tfoot.php';
