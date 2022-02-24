<?php
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
/* Бан пользователя */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /ban.php?' . SID);
	exit;
}
$set['title'] = '日记';
include_once '../../sys/inc/thead.php';
title();
aut(); // форма авторизации
echo "<div id='comments' class='menus'>";
echo "<div class='webmenu'>";
echo "<a href='index.php' >日记</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='dir.php'>类别</a>";
echo "</div>";
echo "<div class='webmenu'>";
echo "<a href='search.php' class='activ'>搜索</a>";
echo "</div>";
echo "</div>";
$usearch = NULL;
if (isset($_SESSION['usearch'])) $usearch = $_SESSION['usearch'];
if (isset($_POST['usearch'])) $usearch = $_POST['usearch'];
if ($usearch == NULL)
	unset($_SESSION['usearch']);
else
	$_SESSION['usearch'] = $usearch;
$usearch = preg_replace("#( ){1,}#", "", $usearch);
$order = 'order by `time` desc';
echo "<form method=\"post\" action=\"search.php?go\">日记搜索<br />";
$usearch = stripcslashes(htmlspecialchars($usearch));
echo "<input type=\"text\" name=\"usearch\" maxlength=\"16\" value=\"$usearch\" /><br />";
echo "<input type=\"submit\" value=\"寻找\" />";
echo "</form>";
if (isset($_GET['go'])) {
	$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `notes` where `name` like '%" . my_esc($usearch) . "%'"), 0);
	$k_page = k_page($k_post, $set['p_str']);
	$page = page($k_page);
	$start = $set['p_str'] * $page - $set['p_str'];
	$q = dbquery("SELECT * FROM `notes` WHERE `name` like '%" . my_esc($usearch) . "%' $order LIMIT $start, $set[p_str]");
	echo "<table class='post'>";
	if ($k_post == 0) {
		echo "<div class='mess'>";
		echo "没有记录。";
		echo "</div>";
	}
	$num = 0;
	while ($post = dbassoc($q)) {
		/*-----------代码-----------*/
		if ($num == 0) {
			echo '<div class="nav1">';
			$num = 1;
		} elseif ($num == 1) {
			echo '<div class="nav2">';
			$num = 0;
		}
		/*---------------------------*/
		echo "<img src='/style/icons/dnev.png' alt='*'> ";
		echo "<a href='list.php?id=$post[id]'>" . text($post['name']) . "</a> ";
		echo " <span style='time'>(" . vremja($post['time']) . ")</span>";
		$k_n = dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `id` = $post[id] AND `time` > '" . $ftime . "'", $db), 0);
		if ($k_n != 0) echo " <img src='/style/icons/new.gif' alt='*'>";
		echo "  </div>";
	}
	echo "</table>";
	if ($k_page > 1) str('?go&amp;', $k_page, $page); // 输出页数
}
include_once '../../sys/inc/tfoot.php';
