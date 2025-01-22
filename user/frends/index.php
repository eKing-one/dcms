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
if (isset($_GET['id'])) $sid = intval($_GET['id']);
else $sid = $user['id'];
$ank = user::get_user($sid);
$set['title'] = "朋友 " . $ank['nick'] . ""; //网页标题
include_once '../../sys/inc/thead.php';
title();
aut();

/*
==================================
用户页面的隐私
阻止好友查看
==================================
*/
$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
if (isset($user)) {
	$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$ank[id]') OR (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
	$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$ank[id]') OR (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"), 0);
}

if ($uSet['privat_str'] == 2) {
	if (isset($user)) {
		if ($ank['id'] != $user['id'] && $frend != 2 && $user['group_access'] <= 1 && $ank['group_access'] > $user['group_access']) {
			echo '<div class="mess">';
			echo '只有用户的好友才能查看用户的好友！';
			echo '</div>';
			echo '<div class="nav1">';
			if ($frend_new == 0 && $frend == 0) {
				echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?add=" . $ank['id'] . "'>添加到朋友</a><br />";
			} elseif ($frend_new == 1) {
				echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?otm=$ank[id]'>拒绝申请</a><br />";
			} elseif ($frend == 2) {
				echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?del=$ank[id]'>从朋友中删除</a><br />";
			}
			echo "</div>";
			include_once '../sys/inc/tfoot.php';
		}
	} else {
		echo '<div class="mess">';
		echo '只有用户的好友才能查看用户的好友！';
		echo '</div>';
		include_once '../sys/inc/tfoot.php';
	}
}

if ($uSet['privat_str'] == 0) {
	if (isset($user)) {
		if ($ank['id'] != $user['id'] && $user['group_access'] <= 1 && $ank['group_access'] > $user['group_access']) {
			echo '<div class="mess">';
			echo '用户已禁止查看他的朋友！';
			echo '</div>';
			include_once '../sys/inc/tfoot.php';
		}
	} else {
		echo '<div class="mess">';
		echo '用户已禁止查看他的朋友！';
		echo '</div>';
		include_once '../sys/inc/tfoot.php';
	}
}


//--------------------著名的---------------------//
if (isset($user) && $user['id'] == $ank['id']) {
	if (isset($_GET['delete'])) {
		foreach ($_POST as $key => $value) {
			if (preg_match('#^post_([0-9]*)$#', $key, $postnum) && $value = '1') {
				$delpost[] = $postnum[1];
			}
		}
		if (isset($_POST['delete'])) {
			if (isset($delpost) && is_array($delpost)) {
				echo "<div class='mess'>Друзья: ";
				for ($q = 0; $q <= count($delpost) - 1; $q++) {
					if (dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$delpost[$q]') OR (`user` = '$delpost[$q]' AND `frend` = '$user[id]') LIMIT 1"), 0) == 0)
						$warn[] = '此用户不在您的联系人列表中';
					else {
						if (dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$delpost[$q]') OR (`user` = '$delpost[$q]' AND `frend` = '$user[id]')"), 0) > 0) {
							/*
							==========================
							给朋友的通知
							==========================
							*/
							dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$delpost[$q]', '$user[id]', 'del_frend', '$time')");
							dbquery("DELETE FROM `frends` WHERE `user` = '$user[id]' AND `frend` = '$delpost[$q]'");
							dbquery("DELETE FROM `frends` WHERE `user` = '$delpost[$q]' AND `frend` = '$user[id]'");
							dbquery("DELETE FROM `frends_new` WHERE `user` = '$delpost[$q]' AND `to` = '$user[id]'");
							dbquery("DELETE FROM `frends_new` WHERE `user` = '$user[id]' AND `to` = '$delpost[$q]'");
							dbquery("OPTIMIZE TABLE `frends`");
							dbquery("OPTIMIZE TABLE `frends_new`");
							$msgno = "不幸的是，用户 [b]$user[nick][/b] 从好友列表中删除了你。 ";
							dbquery("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('0', '$delpost[$q]', '$msgno', '$time')");
						}
					}
					$ank_del = user::get_user($delpost[$q]);
					echo "<font color='#395aff'><b>" . $ank_del['nick'] . "</b></font>, ";
				}
				echo " 从好友列表中删除</div>";
			} else {
				$err[] = '没有一个联系人突出显示';
			}
		}
	}
}

//---------------------Panel---------------------------------//
$on_f = dbresult(dbquery("SELECT COUNT(*) FROM `frends` INNER JOIN `user` ON `frends`.`frend`=`user`.`id` WHERE `frends`.`user` = '$ank[id]' AND `frends`.`i` = '1' AND `user`.`date_last`>'" . (time() - 600) . "'"), 0);
$f = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE `user` = '$ank[id]' AND `i` = '1'"), 0);
$add = dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$ank[id]' LIMIT 1"), 0);
/*echo '<div style="background:white;"><div class="pnl2H">';
echo '<div class="linecd"><span style="margin:9px;">';
echo ''.($ank['id']==$user['id'] ? '我的朋友们' : ' 友人 '.group($ank['id']).' '.user::nick($ank['id'],1,1,1).'').''; 
echo '</span> </div></div>';*/
/*
if ($set['web']==true) {
echo '<div class="mb4">
<nav class="acsw rnav_w"><ul class="rnav js-rnav  " style="padding-right: 45px;">';
echo '<li class="rnav_i"><a href="index.php?id='.$ank['id'].'" class="ai aslnk"><span class="wlnk"><span class="slnk">所有的朋友。</span></span> 
<i><font color="#999">'.$f.'</font></i></a></li>';
echo '<li class="rnav_i"><a href="online.php?id='.$ank['id'].'" class="ai alnk"><span class="wlnk"><span class="lnk">在线
<i><font color="#999">'.$on_f.'</font></i></a></span></span></li> ';
if($ank['id']==$user['id']){ 
echo '<li class="rnav_i"><a href="new.php" class="ai alnk"><span class="wlnk"><span class="lnk">Заявки
<i><font color="#999">'.$add.'</font></i></a></span></span> </li>'; 
}
echo '</ul></nav></div></div>'; }
*/
echo "<div id='comments' class='menus'>";
echo "<div class='webmenu'>";
echo "<a href='index.php?id=$ank[id]' class='activ'>全部 (" . dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE `user` = '$ank[id]' AND `i` = '1'"), 0) . ")</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='online.php?id=$ank[id]'>在线 (" . dbresult(dbquery("SELECT COUNT(*) FROM `frends` INNER JOIN `user` ON `frends`.`frend`=`user`.`id` WHERE `frends`.`user` = '$ank[id]' AND `frends`.`i` = '1' AND `user`.`date_last`>'" . (time() - 600) . "'"), 0) . ")</a>";
echo "</div>";
if (isset($user) && $ank['id'] == $user['id']) {
	echo "<div class='webmenu last'>";
	echo "<a href='new.php'>添加好友 (" . dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$ank[id]' LIMIT 1"), 0) . ")</a>";
	echo "</div>";
}
echo "</div>";
//--------End Panel---------------------//
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE `user` = '$ank[id]' AND `i` = '1'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `frends` WHERE `user` = '$ank[id]' AND `i` = '1' ORDER BY time DESC LIMIT $start, $set[p_str]");
if (isset($user) && $user['id'] == $ank['id']) {
	if ($k_post > 0)
		echo "<form method='post' action='?$page&amp;delete'>";
}
if ($k_post == 0) {
	echo '<div class="mess">';
	echo ' ' . (isset($user) && $ank['id'] == $user['id'] ? '你 ' : '在 ' . $ank['nick'] . '') . ' 没有朋友.';
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
	$sql = dbquery("SELECT `id`,`id_gallery`,`ras` FROM `gallery_photo` WHERE `id_user`='" . $frend['id'] . "' AND `avatar`='1' LIMIT 1");
	if (dbrows($sql) == 1) {
		$photo = dbassoc($sql);
		echo '<a href="/photo/' . $frend['id'] . '/' . $photo['id_gallery'] . '/' . $photo['id'] . '/"><img class="friends" style="width:' . ($webbrowser ? '110px;' : '50px;') . 'height:' . ($webbrowser ? '110px;' : '50px;') . '" src="/photo/photo0/' . $photo['id'] . '.' . $photo['ras'] . '"></a>';
	} else {
		echo '<img class="friends" style="width:' . ($webbrowser ? '80px;' : '50px;') . '" src="/style/icons/avatar.png">';
	}
	echo '</td><td style="width:80%;">';
	if (isset($user) && $user['id'] == $ank['id']) echo " <input type='checkbox' name='post_$frend[id]' value='1' /> ";
	echo user::nick($frend['id'], 1, 1, 0);
	echo '<br/><img src="/style/icons/alarm.png"> ' . ($webbrowser ? '最后在线:' : null) . ' ' . vremja($frend['date_last']) . ' </td><td style="width:18px;">';
	if (isset($user)) {
		echo "<a href=\"/user/mail.php?id=$frend[id]\"><img src='/style/icons/pochta.gif' alt='*' /></a><br/>";
		if ($ank['id'] == $user['id'])			echo "<a href='create.php?del=$frend[id]'><img src='/style/icons/delete.gif' alt='*' /></a>";
	}
	echo '</td></table></div>';
}
if (isset($user) && $user['id'] == $ank['id']) {
	if ($k_post > 0) {
		echo "<div class='c2'>";
		echo " 标记的朋友:<br />";
		echo "<input value=\"删除\" type=\"submit\" name=\"delete\" />";
		echo "</div>";
		echo "</form>";
	}
}
if ($k_page > 1) str("?id=" . $ank['id'] . "&", $k_page, $page); // 输出页数
include_once '../../sys/inc/tfoot.php';
