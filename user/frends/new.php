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
$sid = $user['id'];
$ank = user::get_user($sid);
if (!isset($user)) {
	header("Location: /index.php?" . SID);
	exit;
}
$set['title'] = "好友请求"; //网页标题
include_once '../../sys/inc/thead.php';
title();
aut();
//---------------------Panel---------------------------------//
$on_f = dbresult(dbquery("SELECT COUNT(*) FROM `frends` INNER JOIN `user` ON `frends`.`frend`=`user`.`id` WHERE `frends`.`user` = '$ank[id]' AND `frends`.`i` = '1' AND `user`.`date_last`>'" . (time() - 600) . "'"), 0);
$f = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE `user` = '$ank[id]' AND `i` = '1'"), 0);
$add = dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$ank[id]' LIMIT 1"), 0);
echo '<div style="background:white;"><div class="pnl2H">';
echo '<div class="linecd"><span style="margin:9px;">';
echo '' . ($ank['id'] == $user['id'] ? '我的朋友们' : ' 朋友 '.user::nick($ank['id'], 0, 0, 0)) . '';
echo '</span> </div></div>';
if ($set['web'] == true) {
	echo '<div class="mb4">
<nav class="acsw rnav_w"><ul class="rnav js-rnav  " style="padding-right: 45px;">';
	echo '<li class="rnav_i"><a href="index.php?id=' . $ank['id'] . '" class="ai aslnk"><span class="wlnk"><span class="slnk">所有朋友</span></span> 
<i><font color="#999">' . $f . '</font></i></a></li>';
	echo '<li class="rnav_i"><a href="online.php?id=' . $ank['id'] . '" class="ai alnk"><span class="wlnk"><span class="lnk">在线
<i><font color="#999">' . $on_f . '</font></i></a></span></span></li> ';
	if ($ank['id'] == $user['id']) {
		echo '<li class="rnav_i"><a href="new.php" class="ai alnk"><span class="wlnk"><span class="lnk">好友请求
<i><font color="#999">' . $add . '</font></i></a></span></span> </li>';
	}
	echo '</ul></nav></div></div>';
} else {
	echo "<div id='comments' class='menus'>";
	echo "<div class='webmenu'>";
	echo "<a href='index.php?id=$ank[id]'>全部 (" . dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE `user` = '$ank[id]' AND `i` = '1'"), 0) . ")</a>";
	echo "</div>";
	echo "<div class='webmenu last'>";
	echo "<a href='online.php?id=$ank[id]'>在线 (" . dbresult(dbquery("SELECT COUNT(*) FROM `frends` INNER JOIN `user` ON `frends`.`frend`=`user`.`id` WHERE `frends`.`user` = '$ank[id]' AND `frends`.`i` = '1' AND `user`.`date_last`>'" . (time() - 600) . "'"), 0) . ")</a>";
	echo "</div>";
	if ($ank['id'] == $user['id']) {
		echo "<div class='webmenu last'>";
		echo "<a href='new.php' class='activ'>好友请求 (" . dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$ank[id]' LIMIT 1"), 0) . ")</a>";
		echo "</div>";
	}
	echo "</div>";
}
//--------End Panel---------------------//
$k_post = dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$ank[id]' LIMIT 1"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `frends_new` WHERE `to` = '$user[id]' ORDER BY time DESC");
echo "<table class='post'>";
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '没有新的应用程序';
	echo '</div>';
}
while ($frend = dbassoc($q)) {
	$frend = user::get_user($frend['user']);
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
		user::avatar($frend['id']);
	} elseif ($set['set_show_icon'] == 1) {
		echo "" . user::avatar($frend['id']) . "";
	}
	echo " " . group($frend['id']) . " <a href='/info.php?id=$frend[id]'>$frend[nick]</a>";
	echo "" . medal($frend['id']) . " " . online($frend['id']) . " <br />";
	echo "[<img src='/style/icons/ok.gif' alt='*'/> <a href='/user/frends/create.php?ok=$frend[id]'>接受</a>] ";
	echo "[<img src='/style/icons/delete.gif' alt='*'/> <a href='create.php?no=$frend[id]'>拒绝</a>]";
	echo "   </div>";
}
echo "</table>";
if ($k_page > 1) str("?id=" . $ank['id'] . "&", $k_page, $page); // 输出页数
include_once '../../sys/inc/tfoot.php';
