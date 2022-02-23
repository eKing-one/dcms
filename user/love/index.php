<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
$set['title'] = '熟人'; //网页标题
include_once '../../sys/inc/thead.php';
title();
aut();
if (isset($_GET['orders']) && $_GET['orders'] == 'man') {
	$_SESSION['orders'] = " AND `pol` = '1'";
	$_SESSION['nav1'] = 'activ';
	$_SESSION['nav2'] = NULL;
	$_SESSION['nav3'] = NULL;
} elseif (isset($_GET['orders']) && $_GET['orders'] == 'woman') {
	$_SESSION['orders'] = " AND `pol` = '0'";
	$_SESSION['nav1'] = NULL;
	$_SESSION['nav2'] = 'activ';
	$_SESSION['nav3'] = NULL;
} elseif (isset($_GET['orders']) && $_GET['orders'] == 'all') {
	$_SESSION['orders'] = NULL;
	$_SESSION['nav1'] = NULL;
	$_SESSION['nav2'] = NULL;
	$_SESSION['nav3'] = 'activ';
} elseif (!isset($_SESSION['orders'])) {
	$_SESSION['orders'] = NULL;
	$_SESSION['nav1'] = NULL;
	$_SESSION['nav2'] = NULL;
	$_SESSION['nav3'] = 'activ';
}
$cel = "(
	`ank_lov_1` = '1' OR 
	`ank_lov_2` = '1' OR 
	`ank_lov_3` = '1' OR 
	`ank_lov_4` = '1' OR 
	`ank_lov_5` = '1' OR 
	`ank_lov_6` = '1' OR 
	`ank_lov_7` = '1' OR 
	`ank_lov_8` = '1' OR 
	`ank_lov_9` = '1' OR 
	`ank_lov_10` = '1' OR 
	`ank_lov_11` = '1' OR 
	`ank_lov_12` = '1' OR 
	`ank_lov_13` = '1' OR 
	`ank_lov_14` = '1'
	)";
$opar = "(
	`ank_o_par` IS NOT NULL 
	)";
$osebe = "(
	`ank_o_sebe` IS NOT NULL 
	)";
echo "<div id='comments' class='menus'>";
echo "<div class='webmenu'>";
echo "<a href='?orders=all' class='$_SESSION[nav3]'>所有</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='?orders=woman' class='$_SESSION[nav2]'>女孩</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='?orders=man' class='$_SESSION[nav1]'>男孩</a>";
echo "</div>";
echo "</div>";
/*==============================================这个脚本输出1个随机的”领导者”和一个链接到他们的整个列表。(с) DCMS-Social==============================================*/
$k_lider = dbresult(dbquery("SELECT COUNT(*) FROM `liders` WHERE `time` > '$time'"), 0);
$liders = dbassoc(dbquery("SELECT * FROM `liders` WHERE `time` > '$time' ORDER BY rand() LIMIT 1"));
if ($k_lider > 0) {
	echo '<div class="main">';
	$lider = user::get_user($liders['id_user']);
	echo user::avatar($lider['id']);
	echo group($lider['id']), ' <a href="/info.php?id=' . $lider['id'] . '">' . $lider['nick'] . '</a> ';
	echo medal($lider['id']), online($lider['id']), '<br />';
	if ($liders['msg']) echo output_text($liders['msg']) . '<br />';
	echo '<img src="/style/icons/lider.gif" alt="S"/> <a href="/user/liders/">所有领导人</a> (' . $k_lider . ')';
	echo '</div>';
}
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE $cel AND $opar AND $osebe $_SESSION[orders] AND `date_last` > '" . (time() - 259200) . "'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT ank_o_sebe,nick,id FROM `user` WHERE $cel AND $opar AND $osebe $_SESSION[orders] AND `date_last` > '" . (time() - 259200) . "' order BY `date_last` DESC LIMIT $start, $set[p_str]");
echo '<table class="post">';
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '列表为空';
	echo '</div>';
}
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
	echo user::avatar($ank['id']);
	echo group($ank['id']) . ' <a href="/info.php?id=' . $ank['id'] . '">' . $ank['nick'] . '</a> ';
	echo medal($ank['id']) . online($ank['id']) . '<br />';
	echo output_text($ank['ank_o_sebe']);
	echo '</div>';
}
echo '</table>';
if ($k_page > 1) str("?", $k_page, $page); // 输出页数
include_once '../../sys/inc/tfoot.php';
