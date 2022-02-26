<?
/*
=======================================
Статусы юзеров для Dcms-Social
Автор: Искатель
---------------------------------------
此脚本在许可下被破坏
DCMS-Social 引擎。
使用时，指定引用到
网址 http://dcms-social.ru
---------------------------------------
接点
ICQ：587863132
http://dcms-social.ru
=======================================
*/
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
$set['title'] = '状态-评论';
include_once '../../sys/inc/thead.php';
title();
if (dbresult(dbquery("SELECT COUNT(*) FROM `status` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1", $db), 0) == 0) {
	header("Location: index.php?" . SID);
	exit;
}
// Статус
$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));
// Автор
$anketa = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $status[id_user] LIMIT 1"));
/*
==================================
Приватность станички пользователя
Запрещаем просмотр статусов
==================================
*/
$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$anketa[id]'  LIMIT 1"));
$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$anketa[id]') OR (`user` = '$anketa[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$anketa[id]') OR (`user` = '$anketa[id]' AND `to` = '$user[id]') LIMIT 1"), 0);
if ($anketa['id'] != $user['id'] && $user['group_access'] == 0) {
	if (($uSet['privat_str'] == 2 && $frend != 2) || $uSet['privat_str'] == 0) // Начинаем вывод если стр имеет приват настройки
	{
		if ($anketa['group_access'] > 1) echo "<div class='err'>$anketa[group_name]</div>";
		echo "<div class='nav1'>";
		echo group($anketa['id']) . " $anketa[nick] ";
		echo medal($anketa['id']) . " " . online($anketa['id']) . " ";
		echo "</div>";
		echo "<div class='nav2'>";
		user::avatar($anketa['id']);
		echo "</div>";
	}
	if ($uSet['privat_str'] == 2 && $frend != 2) // Если только для друзей
	{
		echo '<div class="mess">';
		echo '只有他的朋友可以评论用户的状态！';
		echo '</div>';
		// В друзья
		if (isset($user)) {
			echo '<div class="nav1">';
			if ($frend_new == 0 && $frend == 0) {
				echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?add=" . $anketa['id'] . "'>添加到好友</a><br />";
			} elseif ($frend_new == 1) {
				echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?otm=$anketa[id]'>拒绝申请</a><br />";
			} elseif ($frend == 2) {
				echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?del=$anketa[id]'>从朋友中删除</a><br />";
			}
			echo "</div>";
		}
		include_once '../../sys/inc/tfoot.php';
		exit;
	}
	if ($uSet['privat_str'] == 0) // Если закрыта
	{
		echo '<div class="mess">';
		echo '用户禁止评论他的状态!';
		echo '</div>';
		include_once '../../sys/inc/tfoot.php';
		exit;
	}
}
/*
================================
Модуль жалобы на пользователя
и его сообщение либо контент
в зависимости от раздела
================================
*/
if (isset($_GET['spam'])  && isset($user)) {
	$mess = dbassoc(dbquery("SELECT * FROM `status_komm` WHERE `id` = '" . intval($_GET['spam']) . "' limit 1"));
	$spamer = user::get_user($mess['id_user']);
	if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'status_komm' AND `spam` = '" . $mess['msg'] . "'"), 0) == 0) {
		if (isset($_POST['msg'])) {
			if ($mess['id_user'] != $user['id']) {
				$msg = my_esc($_POST['msg']);
				if (strlen2($msg) < 3) $err = '更详细地说明投诉的原因';
				if (strlen2($msg) > 1512) $err = '文本的长度超过512个字符的限制';
				if (isset($_POST['types'])) $types = intval($_POST['types']);
				else $types = '0';
				if (!isset($err)) {
					dbquery("INSERT INTO `spamus` (`id_object`, `id_user`, `msg`, `id_spam`, `time`, `types`, `razdel`, `spam`) values('$status[id]', '$user[id]', '$msg', '$spamer[id]', '$time', '$types', 'status_komm', '" . my_esc($mess['msg']) . "')");
					$_SESSION['message'] = '考虑申请已送交';
					header("Location: ?id=$status[id]&spam=$mess[id]&page=" . intval($_GET['page']) . "");
					exit;
				}
			}
		}
	}
	aut();
	err();
	if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'status_komm'"), 0) == 0) {
		echo "<div class='mess'>虚假信息会导致昵称被屏蔽。 
如果你经常被一个写各种讨厌的东西的人惹恼，你可以把他加入黑名单。</div>";
		echo "<form class='nav1' method='post' action='?id=$status[id]&amp;spam=$mess[id]&amp;page=" . intval($_GET['page']) . "'>";
		echo "<b>用户:</b> ";
		echo " " . user::avatar($spamer['id']) . "  " . user::nick($spamer['id'], 1, 1, 0) . " (" . vremja($mess['time']) . ")<br />";
		echo "<b>违规：</b> <font color='green'>" . output_text($mess['msg']) . "</font><br />";
		echo "原因：<br /><select name='types'>";
		echo "<option value='1' selected='selected'>垃圾邮件/广告</option>";
		echo "<option value='2' selected='selected'>欺诈行为</option>";
		echo "<option value='3' selected='selected'>进攻</option>";
		echo "<option value='0' selected='selected'>其他</option>";
		echo "</select><br />";
		echo "评论:$tPanel";
		echo "<textarea name=\"msg\"></textarea><br />";
		echo "<input value=\"发送\" type=\"submit\" />";
		echo "</form>";
	} else {
		echo "<div class='mess'>投诉有关<font color='green'>$spamer[nick]</font> 它将在不久的将来考虑。</div>";
	}
	echo "<div class='foot'>";
	echo "<img src='/style/icons/str2.gif' alt='*'> <a href='?id=$status[id]&page=" . intval($_GET['page']) . "'>返回</a><br />";
	echo "</div>";
	include_once '../../sys/inc/tfoot.php';
	exit;
}
/*
==================================
The End
==================================
*/
/*------------очищаем счетчик этого обсуждения-------------*/
if (isset($user)) {
	dbquery("UPDATE `discussions` SET `count` = '0' WHERE `id_user` = '$user[id]' AND `type` = 'status' AND `id_sim` = '$status[id]' LIMIT 1");
}
/*---------------------------------------------------------*/
if (isset($user))
	dbquery("UPDATE `notification` SET `read` = '1' WHERE `type` = 'status_komm' AND `id_user` = '$user[id]' AND `id_object` = '$status[id]'");
if (isset($_POST['msg']) && isset($user)) {
	$msg = $_POST['msg'];
	if (isset($_POST['translit']) && $_POST['translit'] == 1) $msg = translit($msg);
	$mat = antimat($msg);
	if ($mat) $err[] = '在消息的文本中发现了一个将死者: ' . $mat;
	if (strlen2($msg) > 1024) {
		$err = '消息太长了';
	} elseif (strlen2($msg) < 2) {
		$err = '短消息';
	} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `status_komm` WHERE `id_status` = '" . intval($_GET['id']) . "' AND `id_user` = '$user[id]' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"), 0) != 0) {
		$err = '您的消息重复前一个';
	} elseif (!isset($err)) {
		/*
		==========================
		Уведомления об ответах
		==========================
		*/
		if (isset($user) && $respons == TRUE) {
			$notifiacation = dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '" . $ank_otv['id'] . "' LIMIT 1"));
			if ($notifiacation['komm'] == 1 && $ank_otv['id'] != $user['id'])
				dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_otv[id]', '$status[id]', 'status_komm', '$time')");
		}
		/*
====================================
Обсуждения
====================================
*/
		$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $status['id_user'] . "' AND `i` = '1'");
		while ($f = dbarray($q)) {
			$a = user::get_user($f['frend']);
			$discSet = dbarray(dbquery("SELECT * FROM `discussions_set` WHERE `id_user` = '" . $a['id'] . "' LIMIT 1")); // Общая настройка обсуждений
			if ($f['disc_status'] == 1 && $discSet['disc_status'] == 1) /* Фильтр рассылки */ {
				// друзьям автора
				if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$a[id]' AND `type` = 'status' AND `id_sim` = '$status[id]' LIMIT 1"), 0) == 0) {
					if ($status['id_user'] != $a['id'] || $a['id'] != $user['id'])
						dbquery("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$a[id]', '$status[id_user]', 'status', '$time', '$status[id]', '1')");
				} else {
					$disc = dbarray(dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$status[id_user]' AND `type` = 'status' AND `id_sim` = '$status[id]' LIMIT 1"));
					if ($status['id_user'] != $a['id'] || $a['id'] != $user['id'])
						dbquery("UPDATE `discussions` SET `count` = '" . ($disc['count'] + 1) . "', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'status' AND `id_sim` = '$status[id]' LIMIT 1");
				}
			}
		}
		// отправляем автору
		if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$status[id_user]' AND `type` = 'status' AND `id_sim` = '$status[id]' LIMIT 1"), 0) == 0) {
			if ($status['id_user'] != $user['id'])
				dbquery("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$status[id_user]', '$status[id_user]', 'status', '$time', '$status[id]', '1')");
		} else {
			$disc = dbarray(dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$status[id_user]' AND `type` = 'status' AND `id_sim` = '$status[id]' LIMIT 1"));
			if ($status['id_user'] != $user['id'])
				dbquery("UPDATE `discussions` SET `count` = '" . ($disc['count'] + 1) . "', `time` = '$time' WHERE `id_user` = '$status[id_user]' AND `type` = 'status' AND `id_sim` = '$status[id]' LIMIT 1");
		}
		dbquery("INSERT INTO `status_komm` (`id_user`, `time`, `msg`, `id_status`) values('$user[id]', '$time', '" . my_esc($msg) . "', '" . intval($_GET['id']) . "')");
		dbquery("UPDATE `user` SET `balls` = '" . ($user['balls'] + 1) . "' WHERE `id` = '$user[id]' LIMIT 1");
		$_SESSION['message'] = '消息被匆忙发送';
		header("Location: komm.php?id=$status[id]");
		exit;
	}
}
err();
aut(); // форма авторизации
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> " . user::nick($anketa['id'], 1, 0, 0) . " | <a href='index.php?id=" . $status['id_user'] . "'>状态</a> | <b>评论</b>";
echo "</div>";
echo '<div class="main">';
echo user::avatar($anketa['id']);
echo user::nick($anketa['id'], 1, 1, 0) . " <br />";
if ($status['id']) {
	echo '<div class="st_1"></div>';
	echo '<div class="st_2">';
	echo output_text($status['msg']) . ' <font size="small">' . vremja($status['time']) . '</font>';
	echo "</div>";
}
echo "</div>";
echo "<div class='foot'>";
echo "评论：";
echo "</div>";
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `status_komm` WHERE `id_status` = '" . intval($_GET['id']) . "'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `status_komm` WHERE `id_status` = '" . intval($_GET['id']) . "' ORDER BY `id` DESC LIMIT $start, $set[p_str]");
echo "<table class='post'>";
if ($k_post == 0) {
	echo "<div class='mess'>";
	echo "没有留言";
	echo "</div>";
}
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
	$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
	echo user::nick($ank['id'], 1, 1, 0);
	if (isset($user) && $ank['id'] != $user['id']) echo "<a href='?id=$status[id]&amp;response=$ank[id]'>[*]</a> ";
	echo " (" . vremja($post['time']) . ")<br />";
	$postBan = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE (`razdel` = 'all') AND `post` = '1' AND `id_user` = '$ank[id]' AND (`time` > '$time' OR `navsegda` = '1')"), 0);
	if ($postBan == 0) // Блок сообщения
	{
		echo output_text($post['msg']) . "<br />";
	} else {
		echo output_text($banMess) . '<br />';
	}
	if (isset($user) && ($user['level'] > $ank['level'] ||  $user['id'] == $ank['id'])) {
		echo "<div style='text-align:right;'>";
		if ($ank['id'] != $user['id']) echo "<a href=\"?id=$status[id]&amp;spam=$post[id]&amp;page=$page\"><img src='/style/icons/blicon.gif' alt='*' title='这是垃圾邮件'></a> ";
		echo " <a href='delete_komm.php?id=$post[id]'><img src='/style/icons/delete.gif' alt='*'></a>";
		echo "</div>";
	}
	echo "</div>";
}
if ($k_page > 1) str("komm.php?id=" . intval($_GET['id']) . '&amp;', $k_page, $page); // 输出页数
if (isset($user)) {
	echo "<form method=\"post\" name='message' action=\"?id=" . intval($_GET['id']) . "&amp;page=$page" . $go_otv . "\">";
	if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
		include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
	else
		echo "$tPanel<textarea name=\"msg\">$otvet</textarea><br />";
	echo "<input value=\"发送\" type=\"submit\" />";
	echo "</form>";
}
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> " . user::nick($anketa['id'], 1, 0, 0)." | <a href='index.php?id=" . $status['id_user'] . "'>状态</a> | <b>评论</b>";
echo "</div>";
include_once '../../sys/inc/tfoot.php';
