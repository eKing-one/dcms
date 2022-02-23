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
if (isset($_GET['id'])) $sid = intval($_GET['id']);
else $sid = $user['id'];
$ank = user::get_user($sid);
/*
==================================
Приватность станички пользователя
Запрещаем просмотр друзей
==================================
*/
$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$ank[id]') OR (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$ank[id]') OR (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"), 0);
if ($ank['id'] != $user['id'] && $user['group_access'] == 0) {
	if (($uSet['privat_str'] == 2 && $frend != 2) || $uSet['privat_str'] == 0) // Начинаем вывод если стр имеет приват настройки
	{
		if ($ank['group_access'] > 1) echo "<div class='err'>$ank[group_name]</div>";
		echo "<div class='nav1'>";
		echo group($ank['id']) . " ";
		echo user::nick($ank['id'], 1, 1, 1);
		echo "</div>";
		echo "<div class='nav2'>";
		user::avatar($ank['id']);
		echo "</div>";
	}
	if ($uSet['privat_str'] == 2 && $frend != 2) // Если только для друзей
	{
		echo '<div class="mess">';
		echo '只有用户的好友才能查看用户的好友！';
		echo '</div>';
		// В друзья
		if (isset($user)) {
			echo '<div class="nav1">';
			if ($frend_new == 0 && $frend == 0) {
				echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?add=" . $ank['id'] . "'>添加到朋友</a><br />";
			} elseif ($frend_new == 1) {
				echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?otm=$ank[id]'>拒绝申请</a><br />";
			} elseif ($frend == 2) {
				echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?del=$ank[id]'>从朋友中删除</a><br />";
			}
			echo "</div>";
		}
		include_once '../sys/inc/tfoot.php';
		exit;
	}
	if ($uSet['privat_str'] == 0) // Если закрыта
	{
		echo '<div class="mess">';
		echo '用户已禁止查看他的朋友！';
		echo '</div>';
		include_once '../sys/inc/tfoot.php';
		exit;
	}
}
$set['title'] = "在线朋友 $ank[nick]"; //网页标题
include_once '../../sys/inc/thead.php';
title();
aut();
//---------------------Panel---------------------------------//
$on_f = dbresult(dbquery("SELECT COUNT(*) FROM `frends` INNER JOIN `user` ON `frends`.`frend`=`user`.`id` WHERE `frends`.`user` = '$ank[id]' AND `frends`.`i` = '1' AND `user`.`date_last`>'" . (time() - 600) . "'"), 0);
$f = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE `user` = '$ank[id]' AND `i` = '1'"), 0);
$add = dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$ank[id]' LIMIT 1"), 0);
echo '<div style="background:white;"><div class="pnl2H">';
echo '<div class="linecd"><span style="margin:9px;">';
echo '' . ($ank['id'] == $user['id'] ? '我的朋友们' : ' 朋友 ' . group($ank['id']) . ' ' . user::nick($ank['id'], 1, 1, 1) . '') . '';
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
	echo "<a href='index.php?id=$ank[id]' >全部 (" . dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE `user` = '$ank[id]' AND `i` = '1'"), 0) . ")</a>";
	echo "</div>";
	echo "<div class='webmenu last'>";
	echo "<a href='online.php?id=$ank[id]' class='activ'>在线 (" . dbresult(dbquery("SELECT COUNT(*) FROM `frends` INNER JOIN `user` ON `frends`.`frend`=`user`.`id` WHERE `frends`.`user` = '$ank[id]' AND `frends`.`i` = '1' AND `user`.`date_last`>'" . (time() - 600) . "'"), 0) . ")</a>";
	echo "</div>";
	if ($ank['id'] == $user['id']) {
		echo "<div class='webmenu last'>";
		echo "<a href='new.php'>好友请求 (" . dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$ank[id]' LIMIT 1"), 0) . ")</a>";
		echo "</div>";
	}
	echo "</div>";
}
//--------End Panel---------------------//
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `frends` INNER JOIN `user` ON `frends`.`frend`=`user`.`id` WHERE `frends`.`user` = '$ank[id]' AND `frends`.`i` = '1' AND `user`.`date_last`>'" . (time() - 600) . "'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `frends` INNER JOIN `user` ON `frends`.`frend`=`user`.`id` WHERE `frends`.`user` = '$ank[id]' AND `frends`.`i` = '1' AND `user`.`date_last`>'" . (time() - 600) . "' ORDER BY `user`.`date_last` DESC LIMIT $start, $set[p_str]");
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '你没有在线的朋友';
	echo '</div>';
}
while ($frend = dbassoc($q)) {
	$frend = user::get_user($frend['frend']);
	/*-----------代码-----------*/
	if ($num == 0) {
		echo '<div class="nav1">';
		$num = 1;
	} elseif ($num == 1) {
		echo '<div class="nav2">';
		$num = 0;
	}
	/*---------------------------*/
	echo '<table><td style="width:' . ($webbrowser ? '85px;' : '55px;') . '">';
	$sql = dbquery("SELECT `id`,`id_gallery`,`ras` FROM `gallery_foto` WHERE `id_user`='" . $frend['id'] . "' AND `avatar`='1' LIMIT 1");
	if (dbrows($sql) == 1) {
		$foto = dbassoc($sql);
		echo '<a href="/foto/' . $frend['id'] . '/' . $foto['id_gallery'] . '/' . $foto['id'] . '/"><img class="friends" style="width:' . ($webbrowser ? '110px;' : '50px;') . '" src="/foto/foto0/' . $foto['id'] . '.' . $foto['ras'] . '"></a>';
	} else {
		echo '<img class="friends" style="width:' . ($webbrowser ? '80px;' : '50px;') . '" src="/style/icons/avatar.png">';
	}
	echo '</td><td style="width:80%;">';
	if (isset($user) && $user['id'] == $ank['id']) echo " <input type='checkbox' name='post_$frend[id]' value='1' /> ";
	echo " " . group($frend['id']) . " ";
	echo user::nick($frend['id'], 1, 1, 1);
	echo '<br/><img src="/style/icons/alarm.png"> ' . ($webbrowser ? '邮政活动:' : null) . ' ' . vremja($frend['date_last']) . ' </td><td style="width:18px;">';
	if (isset($user)) {
		echo "<a href=\"/mail.php?id=$frend[id]\"><img src='/style/icons/pochta.gif' alt='*' /></a><br/>";
		if ($ank['id'] == $user['id'])			echo "<a href='create.php?del=$frend[id]'><img src='/style/icons/delete.gif' alt='*' /></a>";
	}
	echo '</td></table></div>';
}
if ($k_page > 1) str("?id=" . $ank['id'] . "&", $k_page, $page); // 输出页数
include_once '../../sys/inc/tfoot.php';
