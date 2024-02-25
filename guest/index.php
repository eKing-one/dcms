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
/* Бан пользователя */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'guest' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0')"), 0) != 0) {
	header('Location: /user/ban.php?' . SID);
	exit;
}
// Очищаем уведомления об ответах
if (isset($user))
	dbquery("UPDATE `notification` SET `read` = '1' WHERE `type` = 'guest' AND `id_user` = '$user[id]'");
// Действия с комментариями
include 'inc/admin_act.php';
// Отправка комментариев
if (isset($_POST['msg']) && isset($user)) {
	$msg = $_POST['msg'];
	$mat = antimat($msg);
	if ($mat) $err[] = '在信息文本中发现了一个禁止字符：' . $mat;
	if (strlen2($msg) > 1024) {
		$err[] = '信息长于 1024 字节。试着压缩一下？';
	} elseif (strlen2($msg) < 2) {
		$err[] = '信息短于 2 字节。试着扩充一下？';
	} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `guest` WHERE `id_user` = '$user[id]' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"), 0) != 0) {
		$err = '您的信息重复上一条信息';
	} elseif (!isset($err)) {
		// Начисление баллов за активность
		include_once H . 'sys/add/user.active.php';
		/*
		==========================
		Уведомления об ответах
		==========================
		*/
		if (isset($ank_reply['id'])) {
			$notifiacation = dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '" . $ank_reply['id'] . "' LIMIT 1"));
			if ($notifiacation['komm'] == 1 && $ank_reply['id'] != $user['id'])
				dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `type`, `time`) VALUES ('$user[id]', '$ank_reply[id]', 'guest', '$time')");
		}
		dbquery("INSERT INTO `guest` (id_user, time, msg) values('$user[id]', '$time', '" . my_esc($msg) . "')");
		$_SESSION['message'] = '信息已成功添加';
		header("Location: index.php" . SID);
		exit;
	}
} elseif (!isset($user) && isset($set['write_guest']) && $set['write_guest'] == 1 && isset($_SESSION['captcha']) && isset($_POST['chislo'])) {
	$msg = $_POST['msg'];
	$mat = antimat($msg);
	if ($mat) {
		$err[] = '在信息文本中发现了一个禁止字符: ' . $mat;
	}
	if (strlen2($msg) > 1024) {
		$err = '信息长于 1024 字节。试着压缩一下？';
	} elseif ($_SESSION['captcha'] != $_POST['chislo']) {
		$err = '验证数字不正确';
	} elseif (isset($_SESSION['antiflood']) && $_SESSION['antiflood'] > $time - 300) {
		$err = '为防止 SPAM 攻击，你需要完成人机认证。';
	} elseif (strlen2($msg) < 2) {
		$err = '信息短于 2 字节。试着扩充一下？';
	} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `guest` WHERE `id_user` = '0' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"), 0) != 0) {
		$err = '您的信息重复上一条信息';
	} elseif (!isset($err)) {
		$_SESSION['antiflood'] = $time;
		dbquery("INSERT INTO `guest` (id_user, time, msg) values('0', '$time', '" . my_esc($msg) . "')");
		$_SESSION['message'] = '信息已成功添加';
		header("Location: index.php" . SID);
		exit;
	}
}
//网页标题
$set['title'] = '留言板';
include_once '../sys/inc/thead.php';
title();
aut();
err();
$k_post = dbresult(dbquery("SELECT COUNT(id) FROM `guest`"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
// Форма для комментариев
if (isset($user) || (isset($set['write_guest']) && $set['write_guest'] == 1 && (!isset($_SESSION['antiflood']) || $_SESSION['antiflood'] < $time - 300))) {
	echo '<form method="post" name="message" action="?page=' . $page . REPLY . '">';
	if (is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
		include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
	else
		echo $tPanel . '<textarea name="msg">' . $insert . '</textarea><br />';
	if (!isset($user) && isset($set['write_guest']) && $set['write_guest'] == 1) {
?>
		<img src="/captcha.php?SESS=<?= $sess ?>" width="100" height="30" alt="Captcha" /> <input name="chislo" size="7" maxlength="5" value="" type="text" placeholder="验证码.." /><br />
	<?
	}
	echo '<input value="发送" type="submit" />';
	echo '</form>';
} elseif (!isset($user) && isset($set['write_guest']) && $set['write_guest'] == 1) {
	?>
	<div class="mess">您将能够通过 <span class="on"><?= abs($time - $_SESSION['antiflood'] - 300) ?> 秒.</span></div>
	<?
	}
	echo '<table class="post">';
	if ($k_post == 0) {
		echo '<div class="mess" id="no_object">';
		echo '没有评论';
		echo '</div>';
	}
	$q = dbquery("SELECT * FROM `guest` ORDER BY id DESC LIMIT $start, $set[p_str]");
	while ($post = dbassoc($q)) {
		$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
		// Лесенка
		echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
		$num++;
		echo ($post['id_user'] != '0' ? user::avatar($ank['id'], 0) . user::nick($ank['id'], 1, 1, 0) : user::avatar(0, 0) . ' <b>' . '游客' . '</b> ');
		if (isset($user) && $user['id'] != $ank['id'])
			echo ' <a href="?page=' . $page . '&amp;response=' . $ank['id'] . '">[@]</a> (' . vremja($post['time']) . ')<br />';
		echo output_text($post['msg']) . '<br />';
		if (isset($user) && ($user['level'] > $ank['level'] || $user['level'] != 0 && $user['id'] == $ank['id']) && user_access('guest_delete')) {
			echo '<div class="right">';
			echo '<a href="delete.php?id=' . $post['id'] . '"><img src="/style/icons/delete.gif" alt="*"></a>';
			echo '</div>';
		}
		echo '</div>';
	}
	echo '</table>';
	if ($k_page > 1) str('index.php?', $k_page, $page); // 输出页数
	echo '<div class="foot">';
	echo '<img src="/style/icons/str.gif" alt="*"> <a href="who.php">在线 (' . dbresult(dbquery("SELECT COUNT(id) FROM `user` WHERE `date_last` > '" . (time() - 100) . "' AND `url` like '/guest/%'"), 0) . ' 人)</a><br />';
	echo '</div>';
	// Форма очистки комментов
	include 'inc/admin_form.php';
	include_once '../sys/inc/tfoot.php';
		?>