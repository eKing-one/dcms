<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
$set['title'] = '管理工作'; //网页标题
include_once '../sys/inc/thead.php';
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
	$gr = "`group_access` > '1' AND `date_last` > '" . (time() - 600) . "'";
	$s = 1;
}
if (!isset($_GET['adm']) && !isset($_GET['mod']) && !isset($_GET['zone']) && !isset($_GET['forum']) && !isset($_GET['chat']) && !isset($_GET['notes'])  && !isset($_GET['guest'])) {
	echo "<div class = 'nav2'>";
	echo "<img src='/style/icons/adm.gif' alt='S' /> <a href='?guest'>嘉宾主持人</a>";
	echo "</div>";
	echo "<div class = 'nav1'>";
	echo "<img src='/style/icons/adm.gif' alt='S' /> <a href='?notes'>日记主持人</a>";
	echo "</div>";
	echo "<div class = 'nav2'>";
	echo "<img src='/style/icons/adm.gif' alt='S' /> <a href='?chat'>聊天主持人</a>";
	echo "</div>";
	echo "<div class = 'nav1'>";
	echo "<img src='/style/icons/adm.gif' alt='S' /> <a href='?forum'>论坛主持人</a>";
	echo "</div>";
	echo "<div class = 'nav2'>";
	echo "<img src='/style/icons/adm.gif' alt='S' /> <a href='?zone'>交流区主持人</a>";
	echo "</div>";
	echo "<div class = 'nav1'>";
	echo "<img src='/style/icons/adm.gif' alt='S' /> <a href='?mod'>主持人</a>";
	echo "</div>";
	echo "<div class = 'nav1'>";
	echo "<img src='/style/icons/adm.gif' alt='S' /> <a href='?adm'>管理员</a>";
	echo "</div>";
}
if ($s == 1) {
	echo "<div class = 'foot'>";
	echo "在线管理";
	echo "</div>";
}
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE $gr"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `user` WHERE $gr ORDER BY `date_last` DESC LIMIT $start, $set[p_str]");
echo "<table class='post'>";
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '列表为空';
	echo '</div>';
}
while ($ank = dbassoc($q)) {
	$ank = get_user($ank['id']);
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
		avatar($ank['id']);
	} elseif ($set['set_show_icon'] == 1) {
		echo "" . avatar($ank['id']) . "";
	}
	echo "<a href='/info.php?id=$ank[id]'>$ank[nick]</a>";
	echo "" . medal($ank['id']) . " " . online($ank['id']) . " <br />";
	echo "$ank[group_name]";
	if ($ank['id'] != $user['id']) {
		echo "<br /> <a href=\"/mail.php?id=$ank[id]\"><img src='/style/icons/pochta.gif' alt='*' /> 信息</a> ";
	}
	echo "</div>";
}
echo "</table>";
if ($k_page > 1) str("?", $k_page, $page); // 输出页数
include_once '../sys/inc/tfoot.php';
