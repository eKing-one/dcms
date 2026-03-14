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

/* 封禁的用户 */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'guest' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0')"), 0) != 0) {
	header('Location: ../user/ban.php');
	exit;
}

// 清除回复通知
if (isset($user)) {
	$db->query("UPDATE `notification` SET `read` = '1' WHERE `type` = 'guest' AND `id_user` = ?", [$user['id']]);
}

// 注释操作
include 'inc/admin_act.php';


// 提交评论
if (isset($_POST['msg']) && isset($user)) {
	$msg = $_POST['msg'];
	$mat = antimat($msg);
	if ($mat) $err[] = '在信息文本中发现了一个禁止字符：' . $mat;
	if (strlen2($msg) > 1024) {
		$err[] = '内容长度不能大于 1024 个字符';
	} elseif (strlen2($msg) < 2) {
		$err[] = '内容长度不能小于 2 个字符';
	}

	// 获取该用户的上一条消息
	$lastMessage = $db->query('SELECT `msg`, `time` FROM `guest` WHERE id_user = ? ORDER BY `time` DESC LIMIT 1', [$user['id']]);
	if ($lastMessage && $lastMessage['msg'] == $msg && (time() - $lastMessage['time']) < 300) {
		$err = '您的信息重复上一条信息';
	} elseif (!isset($err)) {
		// 活动积分的累积
		include_once '../sys/add/user.active.php';
		/*
		==========================
		回复通知
		==========================
		*/
		if (isset($ank_reply['id'])) {
			$notifiacation = dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '" . $ank_reply['id'] . "' LIMIT 1"));
			if ($notifiacation['komm'] == 1 && $ank_reply['id'] != $user['id'])
				dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_reply[id]', 0, 'guest', '$time')");
		}
		$db->query('INSERT INTO `guest` (id_user, time, msg) values(?, ?, ?)', [$user['id'], $time, $msg]);
		$_SESSION['message'] = '留言添加成功';
		header('Location: index.php');
		exit;
	}

// 匿名提交留言板
} elseif (!isset($user) && isset($set['write_guest']) && $set['write_guest'] == 1 && isset($_SESSION['captcha']) && isset($_POST['chislo'])) {
	$msg = $_POST['msg'];
	$mat = antimat($msg);
	if ($mat) {
		$err[] = '在信息文本中发现了一个禁止字符: ' . $mat;
	}
	if (strlen2($msg) > 1024) {
		$err = '内容长度不能大于 1024 个字符';
	} elseif ($_SESSION['captcha'] != $_POST['chislo']) {
		$err = '验证数字不正确';
	} elseif (isset($_SESSION['antiflood']) && $_SESSION['antiflood'] > $time - 300) {
		$err = '为防止 SPAM 攻击，你需要完成人机认证。';
	} elseif (strlen2($msg) < 2) {
		$err = '内容长度不能小于 2 个字符';
	}
	$lastMessage = dbassoc(dbquery("SELECT `msg`, `time` FROM `guest` WHERE `id_user` = '0' ORDER BY `time` DESC LIMIT 1"));
	if ($lastMessage && $lastMessage['msg'] == $msg && (time() - $lastMessage['time']) < 300) {
		$err = '您的信息重复上一条信息';
	} elseif (!isset($err)) {
		$_SESSION['antiflood'] = $time;
		dbquery("INSERT INTO `guest` (id_user, time, msg) values('0', '$time', '" . my_esc($msg) . "')");
		$_SESSION['message'] = '留言添加成功';
		header('Location: index.php');
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

// 留言板输入框
if (isset($user) || (isset($set['write_guest']) && $set['write_guest'] == 1 && (!isset($_SESSION['antiflood']) || $_SESSION['antiflood'] < $time - 300))) {
	echo '<form method="post" name="message" action="?page=' . $page . REPLY . '">';
	if (is_file('../style/themes/' . $set['set_them'] . '/altername_post_form.php'))
		include_once '../style/themes/' . $set['set_them'] . '/altername_post_form.php';
	else
		echo $tPanel . '<textarea name="msg">' . $insert . '</textarea><br />';
	if (!isset($user) && isset($set['write_guest']) && $set['write_guest'] == 1) {
		echo "<img src=\"../captcha.php?SESS={$sess}\" width=\"100\" height=\"30\" alt=\"Captcha\" /> <input name=\"chislo\" size=\"7\" maxlength=\"5\" value=\"\" type=\"text\" placeholder=\"验证码..\" /><br />";
	}
	echo '<input value="发送" type="submit" />';
	echo '</form>';
} elseif (!isset($user) && isset($set['write_guest']) && $set['write_guest'] == 1) {
	echo '<div class="mess">您将能够通过 <span class="on">' . abs($time - $_SESSION['antiflood'] - 300) . ' 秒.</span></div>';
}

// 输出留言板
echo '<table class="post">';
if ($k_post == 0) {
	echo '<div class="mess" id="no_object">';
	echo '没有留言';
	echo '</div>';
}
$q = dbquery("SELECT * FROM `guest` ORDER BY id DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
	$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));

	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;

	echo ($post['id_user'] != '0' ? user::avatar($post['id_user'], 0) . user::nick($post['id_user'], 1, 1, 0) : user::avatar(0, 0) . ' <b>' . '游客' . '</b> ');
	if (isset($user) && isset($ank['id']) && $user['id'] != $ank['id']) {
		echo ' <a href="?page=' . $page . '&amp;response=' . $ank['id'] . '">[@]</a> ';
	}
	echo '(' . vremja($post['time']) . ')';
	echo '<br />' . output_text($post['msg']) . '<br />';
	if (isset($user) && (((empty($ank['id']) || $user['level'] > $ank['level']) && $user['level'] != 0) || $user['id'] == $post['id_user'] || user_access('guest_delete'))) {
		echo '<div class="right">';
		echo '<a href="delete.php?id=' . $post['id'] . '&referer_page=' . $page . '"><img src="../style/icons/delete.gif" alt="*"></a>';
		echo '</div>';
	}
	echo '</div>';
}
echo '</table>';

if ($k_page > 1) str('index.php?', $k_page, $page); // 输出页数

$online_guest_users = dbresult(dbquery("SELECT COUNT(DISTINCT ul.id_user) AS online_users
										FROM `user_log` ul
										WHERE ul.last_online > NOW() - INTERVAL 100 SECOND
											AND ul.ban = 0
											AND ul.url LIKE '/guest/%'
											AND ul.last_online = (
											SELECT MAX(last_online)
											FROM `user_log` ul2
											WHERE ul2.id_user = ul.id_user
												AND ul2.last_online > NOW() - INTERVAL 100 SECOND
												AND ul2.ban = 0
											)"), 0);
echo '<div class="foot"><img src="../style/icons/str.gif" alt="*"> <a href="who.php">在线 (' . $online_guest_users . ' 人)</a><br /></div>';
// 评论清理表单
include 'inc/admin_form.php';
include_once '../sys/inc/tfoot.php';