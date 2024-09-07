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
if (isset($user)) $ank['id'] = $user['id'];
if (isset($_GET['id'])) $ank['id'] = intval($_GET['id']);
$ank = user::get_user($ank['id']);
if (!$ank) {
	header("Location: /index.php?" . SID);
	exit;
}
if ($ank['id'] == 0) {
	$ank = user::get_user($ank['id']);
	$set['title'] = $ank['nick'] . ' - 个人主页 '; //网页标题
	include_once '../sys/inc/thead.php';
	title();
	aut();
	echo "<span class=\"status\">$ank[group_name]</span><br />";
	if ($ank['ank_o_sebe'] != NULL) echo "<span class=\"ank_n\">关于我:</span> <span class=\"ank_d\">$ank[ank_o_sebe]</span><br />";
	if (isset($_SESSION['refer']) && $_SESSION['refer'] != NULL && otkuda($_SESSION['refer']))
		echo "<div class='foot'>&laquo;<a href='$_SESSION[refer]'>" . otkuda($_SESSION['refer']) . "</a><br /></div>";
	include_once '../sys/inc/tfoot.php';
	exit;
}
/* 查看封禁用户的主页 */
if ((!isset($user) || $user['group_access'] == 0) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'all' AND `id_user` = '$ank[id]' AND (`time` > '$time' OR `navsegda` = '1')"), 0) != 0) {
	$set['title'] = $ank['nick'] . ' - 个人主页 '; //网页标题
	include_once '../sys/inc/thead.php';
	title();
	aut();
	echo '<div class="mess">';
	echo '<b><font color=red>该用户已被封禁,无法查看个人主页</font></b><br /> ';
	echo '</div>';
	include_once '../sys/inc/tfoot.php';
	exit;
}
// 删除动态
if (isset($_GET['delete_post']) && dbresult(dbquery("SELECT COUNT(*) FROM `stena` WHERE `id` = '" . intval($_GET['delete_post']) . "'"), 0) == 1) {
	$post = dbassoc(dbquery("SELECT * FROM `stena` WHERE `id` = '" . intval($_GET['delete_post']) . "' LIMIT 1"));
	if (user_access('guest_delete') || $ank['id'] == $user['id']) {
		dbquery("DELETE FROM `stena` WHERE `id` = '$post[id]'");
		dbquery("DELETE FROM `stena_like` WHERE `id_stena` = '$post[id]'");
		$_SESSION['message'] = '动态已成功删除';
	}
}
/*-------------------------游客查看主页----------------------*/
if (isset($user) && $user['id'] != $ank['id'] && !isset($_SESSION['guest_' . $ank['id']])) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `my_guests` WHERE `id_ank` = '$ank[id]' AND `id_user` = '$user[id]' LIMIT 1"), 0) == 0) {
		dbquery("INSERT INTO `my_guests` (`id_ank`, `id_user`, `time`) VALUES ('$ank[id]', '$user[id]', '$time')");
		dbquery("UPDATE `user` SET `balls` = '" . ($ank['balls'] + 1) . "' ,`rating_tmp` = '" . ($ank['rating_tmp'] + 1) . "' WHERE `id` = '$ank[id]' LIMIT 1");
		$_SESSION['guest_' . $ank['id']] = 1;
	} elseif (!isset($_SESSION['guest_' . $ank['id']])) {
		$guest = dbarray(dbquery("SELECT * FROM `my_guests` WHERE `id_ank` = '$ank[id]' AND `id_user` = '$user[id]' LIMIT 1"));
		dbquery("UPDATE `my_guests` SET  `time` = '$time', `read` = '1' WHERE `id` = '$guest[id]' LIMIT 1");
		dbquery("UPDATE `user` SET `rating_tmp` = '" . ($ank['rating_tmp'] + 1) . "' WHERE `id` = '$ank[id]' LIMIT 1");
		$_SESSION['guest_' . $ank['id']] = 1;
	}
}
/*----------------------------------------------------*/
/*------------------------动态-----------------------*/
if (isset($user) && isset($_GET['wall']) && $_GET['wall'] == 1) {
	dbquery("UPDATE `user` SET `wall` = '1' WHERE `id` = '$user[id]'");
	header("Location: /user/info.php?id=$ank[id]");
} elseif (isset($user) && isset($_GET['wall']) && $_GET['wall'] == 0) {
	dbquery("UPDATE `user` SET `wall` = '0' WHERE `id` = '$user[id]'");
	header("Location: /user/info.php?id=$ank[id]");
}
if (isset($user))
	dbquery("UPDATE `notification` SET `read` = '1' WHERE `type` = 'stena_komm' AND `id_user` = '$user[id]' AND `id_object` = '$ank[id]'");
if (isset($_POST['msg']) && isset($user)) {
	$msg = $_POST['msg'];
	if (isset($_POST['translit']) && $_POST['translit'] == 1) $msg = translit($msg);
	$mat = antimat($msg);
	if ($mat) $err[] = '动态文本中包含特殊字符' . $mat;
	if (strlen2($msg) > 1024) {
		$err[] = '动态长于1024个字,写少些吧';
	} elseif (strlen2($msg) < 2) {
		$err[] = '动态短于2个字,多写点吧';
	} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `stena` WHERE `id_user` = '$user[id]' AND  `id_stena` = '$ank[id]' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"), 0) != 0) {
		$err = '动态重复了';
	} elseif (!isset($err)) {
		/*
		==========================
		有关回应的通知
		==========================
		*/
		if (isset($user) && $respons == TRUE) {
			$notifiacation = dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '" . $ank_otv['id'] . "' LIMIT 1"));
			if ($notifiacation['komm'] == 1 && $ank_otv['id'] != $user['id'])
				dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_otv[id]', '$ank[id]', 'stena_komm', '$time')");
		}
		dbquery("INSERT INTO `stena` (id_user, time, msg, id_stena) values('$user[id]', '$time', '" . my_esc($msg) . "', '$ank[id]')");
		dbquery("UPDATE `user` SET `balls` = '" . ($user['balls'] + 1) . "' ,`rating_tmp` = '" . ($user['rating_tmp'] + 1) . "' WHERE `id` = '$user[id]' LIMIT 1");
		$_SESSION['message'] = '动态发布成功';
		if (isset($user)) {
			$notifiacation = dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '" . $ank['id'] . "' LIMIT 1"));
			if ($notifiacation['komm'] == 1 && $user['id'] != $ank['id'])
				dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `type`, `time`) VALUES ('$user[id]', '$ank[id]', 'stena', '$time')");
		}
	}
}
/*-----------------------评价通知----------------------------*/
if ((!isset($_SESSION['refer']) || $_SESSION['refer'] == NULL)
	&& isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL &&
	!preg_match('#info\.php#', $_SERVER['HTTP_REFERER'])
)
	$_SESSION['refer'] = str_replace('&', '&amp;', preg_replace('#^http://[^/]*/#', '/', $_SERVER['HTTP_REFERER']));
if (isset($_POST['rating']) && isset($user)  && $user['id'] != $ank['id'] && $user['balls'] >= 50 && dbresult(dbquery("SELECT SUM(`rating`) FROM `user_voice2` WHERE `id_kont` = '$user[id]'"), 0) >= 0) {
	$new_r = min(max(@intval($_POST['rating']), -2), 2);
	dbquery("DELETE FROM `user_voice2` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' LIMIT 1");
	if ($new_r)
		dbquery("INSERT INTO `user_voice2` (`rating`, `id_user`, `id_kont`) VALUES ('$new_r','$user[id]','$ank[id]')");
	$ank['rating'] = intval(dbresult(dbquery("SELECT SUM(`rating`) FROM `user_voice2` WHERE `id_kont` = '$ank[id]'"), 0));
	dbquery("UPDATE `user` SET `rating` = '$ank[rating]' WHERE `id` = '$ank[id]' LIMIT 1");
	if ($new_r > 0)
		dbquery("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('0', '$ank[id]', '$user[nick] 对你表示赞赏! [url=/who_rating.php]你的个人资料[/url]', '$time')");
	if ($new_r < 0)
		dbquery("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('0', '$ank[id]', '$user[nick] 对你感到失望... [url=/who_rating.php]你的个人资料[/url]', '$time')");
	if ($new_r == 0)
		dbquery("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('0', '$ank[id]', '$user[nick] 保持中立 [url=/who_rating.php]你的个人资料[/url]', '$time')");
	msg('对用户的评价已修改');
}
//-------------状态记录-----------//
if (isset($_POST['status']) && isset($user) && $user['id'] == $ank['id']) {
	$msg = $_POST['status'];
	if (isset($_POST['translit']) && $_POST['translit'] == 1) $msg = translit($msg);
	$mat = antimat($msg);
	if ($mat) $err[] = '在状态文本中发现了一个禁止字符: ' . $mat;
	if (strlen2($msg) > 512) {
		$err = '状态长于512个字,写少点吧';
	} elseif (strlen2($msg) < 2) {
		$err = '状态短于2个字,多写点吧';
	} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `status` WHERE `id_user` = '$user[id]' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"), 0) != 0) {
		$err = '状态重复';
	} elseif (!isset($err)) {
		dbquery("UPDATE `status` SET `pokaz` = '0' WHERE `id_user` = '$user[id]'");
		dbquery("INSERT INTO `status` (`id_user`, `time`, `msg`, `pokaz`) values('$user[id]', '$time', '" . my_esc($msg) . "', '1')");
		$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id_user` = '$ank[id]' AND `pokaz` = '1' LIMIT 1"));
		######################Лента
		$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $user['id'] . "' AND `i` = '1'");
		while ($f = dbarray($q)) {
			$a = user::get_user($f['frend']);
			$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '" . $a['id'] . "' LIMIT 1")); 
			if ($f['lenta_status'] == 1 && $lentaSet['lenta_status'] == 1)
				dbquery("INSERT INTO `tape` (`id_user`,`ot_kogo`,  `avtor`, `type`, `time`, `id_file`) values('$a[id]', '$user[id]', '$status[id_user]', 'status', '$time', '$status[id]')");
		}
		#######################Конец
		$_SESSION['message'] = '新增状态';
		header("Location: ?id=$ank[id]");
		exit;
	}
}
if (isset($_GET['off'])) {
	if ($ank['id'] == $user['id']) {
		dbquery("UPDATE `status` SET `pokaz` = '0' WHERE `id_user` = '$user[id]'");
		$_SESSION['message'] = '状态已禁用';
		header("Location: ?id=$ank[id]");
		exit;
	}
}
//-------------------------------------// 
// 用户状态
$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id_user` = '$ank[id]' AND `pokaz` = '1' LIMIT 1"));
/* 状态类 */
if (isset($_GET['like']) && $user['id'] != $ank['id'] && dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"), 0) == 0) {
	dbquery("INSERT INTO `status_like` (`id_user`, `time`, `id_status`) values('$user[id]', '$time', '$status[id]')");
	######################Лента
	$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $user['id'] . "' AND `i` = '1'");
	while ($f = dbarray($q)) {
		$a = user::get_user($f['frend']);
		$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '" . $a['id'] . "' LIMIT 1")); // Общая настройка ленты
		if ($a['id'] != $ank['id'] && $f['lenta_status_like'] == 1 && $lentaSet['lenta_status_like'] == 1)
			dbquery("INSERT INTO `tape` (`id_user`,`ot_kogo`,  `avtor`, `type`, `time`, `id_file`) values('$a[id]', '$user[id]', '$status[id_user]', 'status_like', '$time', '$status[id]')");
	}
	#######################终极
	header("Location: ?id=$ank[id]");
	exit;
}
/*
=================================
添加到书签
=================================
*/
if (isset($_GET['fav']) && isset($user)) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '" . $ank['id'] . "' AND `type`='people' LIMIT 1"), 0) == 0 && $_GET['fav'] == 1) {
		dbquery("INSERT INTO `bookmarks` (`id_object`, `id_user`, `time`,`type`) VALUES ('$ank[id]', '$user[id]', '$time','notes')");
		$_SESSION['message'] = $ank['nick'] . ' 添加到书签';
	}
	if (dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '" . $ank['id'] . "' AND `type`='people' LIMIT 1"), 0) == 1 && $_GET['fav'] == 0) {
		dbquery("DELETE FROM `mark_people` WHERE `id_user` = '$user[id]' AND  `id_object` = '$ank[id]' AND `type`='people'");
		$_SESSION['message'] = $ank['nick'] . ' 从书签中删除';
	}
	header("Location: /user/info.php?id=$ank[id]");
	exit;
}
/*------------------------статус like-----------------------*/
if (isset($user) && isset($_GET['like']) && ($_GET['like'] == 0 || $_GET['like'] == 1) && dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_user` = '$user[id]' AND `id_status`='$status[id]' LIMIT 1"), 0) == 0 && $user['id'] != $ank['id']) {
	dbquery("INSERT INTO `status_like` (`id_user`, `id_status`, `like`) VALUES ('$user[id]', '$status[id]', '" . intval($_GET['like']) . "')");
	dbquery("UPDATE `user` SET `balls` = '" . ($ank['balls'] + 3) . "' ,`rating_tmp` = '" . ($ank['rating_tmp'] + 3) . "' WHERE `id` = '$ank[id]' LIMIT 1");
}
/*----------------------------------------------------------*/
/*
================================
用户投诉模块
和他的消息或内容
视区段而定
================================
*/
if (isset($_GET['spam'])  && $ank['id'] != 0 && isset($user)) {
	$mess = dbassoc(dbquery("SELECT * FROM `stena` WHERE `id` = '" . intval($_GET['spam']) . "' limit 1"));
	$spamer = user::get_user($mess['id_user']);
	if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'stena'"), 0) == 0) {
		if (isset($_POST['spamus'])) {
			if ($mess['id_user'] != $user['id']) {
				$msg = my_esc($_POST['spamus']);
				if (strlen2($msg) < 3) $err = '请更详细地说明举报原因';
				if (strlen2($msg) > 1024) $err = '文本的长度超过1024字的限制';
				if (isset($_POST['types'])) $types = intval($_POST['types']);
				else $types = '0';
				if (!isset($err)) {
					dbquery("INSERT INTO `spamus` (`id_object`, `id_user`, `msg`, `id_spam`, `time`, `types`, `razdel`, `spam`) values('$ank[id]', '$user[id]', '$msg', '$spamer[id]', '$time', '$types', 'stena', '" . my_esc($mess['msg']) . "')");
					$_SESSION['message'] = '举报已发出,管理员将会火速处理';
					header("Location: ?id=$ank[id]&spam=$mess[id]&page=" . intval($_GET['page']) . "");
					exit;
				}
			}
		}
	}
	$set['title'] = $ank['nick'] . ' - 举报 '; //网页标题
	include_once '../sys/inc/thead.php';
	title();
	aut();
	err();
	if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'stena'"), 0) == 0) {
		echo "<div class='mess'>若你认为某条言论不合适、违反了网站规则，可以举报，管理员收到后会尽快处理。
		但是，请不要瞎举报给管理添乱，若多次发出无意义的举报，将同样会按网站规则进行处罚。
		如果你真的很讨厌某位用户的言论，你可以选择将其拉黑，而不是将消息逐条举报。逐条举报会大大降低管理员处理举报的效率，甚至导致举报处理任务大量积压。</div>"; 
		echo "<form class='nav1' method='post' action='/user/info.php?id=$ank[id]&amp;spam=$mess[id]&amp;page=" . intval($_GET['page']) . "'>";
		echo "<b>用户：</b> ";
		echo " " . user::nick($spamer['id'], 1, 1, 0) . " (" . vremja($mess['time']) . ")<br />";
		echo "<b>违规行为：</b> <font color='green'>" . output_text($mess['msg']) . "</font><br />";
		echo "原因：<br /><select name='types'>";
		echo "<option value='1' selected='selected'>垃圾邮件/广告/日记/帖子</option>";
		echo "<option value='2' selected='selected'>诈骗行为</option>";
		echo "<option value='3' selected='selected'>引战</option>"; 
		echo "<option value='4' selected='selected'>网络暴力</option>"; 
		echo "<option value='0' selected='selected'>其他</option>";
		echo "</select><br />";
		echo "附加解释：$tPanel";
		echo "<textarea name=\"spamus\"></textarea><br />";
		echo "<input value=\"提交举报\" type=\"submit\" />";
		echo "</form>";
	} else {
		echo "<div class='mess'>有关<font color='green'>$spamer[nick]</font>的举报管理将尽快处理，请耐心等待。</div>";
	}
	echo "<div class='foot'>";
	echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/user/info.php?id=$ank[id]'>返回</a><br />";
	echo "</div>";
	include_once '../sys/inc/tfoot.php';
}
/*
==================================
The End
==================================
*/
$set['title'] = $ank['nick'] . ' - 个人主页 '; //网页标题
include_once '../sys/inc/thead.php';
title();
aut();
/*
==================================
用户页面的隐私
==================================
*/
$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$ank[id]') OR (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$ank[id]') OR (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"), 0);
if ($ank['id'] != $user['id'] && $user['group_access'] == 0) {
	if (($uSet['privat_str'] == 2 && $frend != 2) || $uSet['privat_str'] == 0) // 页面有个人设置时开始打印
	{
		if ($ank['group_access'] > 1) echo "<div class='err'>$ank[group_name]</div>";
		echo "<div class='nav1'>";
		echo user::nick($ank['id'], 1, 1, 1);
		echo "</div>";
		echo "<div class='nav2'>";
		echo user::avatar($ank['id']);
		echo "<br />";
	}
	if ($uSet['privat_str'] == 2 && $frend != 2) // 只要有朋友的话
	{
		echo '<div class="mess">';
		echo '由于用户的隐私设置，只有该用户的好友才能查看主页'; 
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
	if ($uSet['privat_str'] == 0) // 关闭时
	{
		echo '<div class="mess">';
		echo '由于用户的隐私设置，已禁止查看这位用户的主页';
		echo '</div>';
		include_once '../sys/inc/tfoot.php';
		exit;
	}
}
if ($set['web'] == true)
	include_once H . "user/info/web.php";
else
	include_once H . "user/info/wap.php";
include_once '../sys/inc/tfoot.php';