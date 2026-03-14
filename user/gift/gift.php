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
only_reg();

// 向浏览器显示礼物的大小
$width = ($webbrowser == 'web' ? '100' : '70');
// 礼物
$post = dbassoc(dbquery("SELECT id,status,coment,id_gift,id_ank,id_user,time FROM `gifts_user` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));
// 如果没有记录，则将其扔到主页
if (!$post['id']) {
	header("Location: ../../index.php?");
}

// 礼物本身
$gift = dbassoc(dbquery("SELECT id,name FROM `gift_list` WHERE `id` = '" . $post['id_gift'] . "' LIMIT 1"));
// 谁收到了礼物
$ank = user::get_user($post['id_user']);
// 谁捐赠的
$anketa = user::get_user($post['id_ank']);

// 接受礼物
if ($post['status'] == 0 && isset($_GET['ok']) && $user['id'] == $ank['id']) {
	dbquery("UPDATE `gifts_user` SET `status` = '1' WHERE `id` = '$post[id]' LIMIT 1");
	/*
	==========================
	通知
	==========================
	*/
	dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$anketa[id]', '$gift[id]', 'ok_gift', '$time')");
	// 消息 
	$_SESSION['message'] = '来自 ' . $anketa['nick'] . ' 通过';
	header("Location: gift.php?id=$post[id]");
	exit;
}

// 拒绝赠与服务
if ($post['status'] == 0 && isset($_GET['no']) && $user['id'] == $ank['id']) {
	dbquery("DELETE FROM `gifts_user` WHERE `id` = '$post[id]' LIMIT 1");
	/*
	==========================
	通知
	==========================
	*/
	dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$anketa[id]', '$gift[id]', 'no_gift', '$time')");
	$_SESSION['message'] = '来自 ' . $anketa['nick'] . ' 被拒绝';
	header("Location: ?new");
	exit;
}

// 删除礼品
if (isset($_GET['delete']) && ($ank['id'] == $user['id']  || $user['level'] > 2)) {
	// 请求删除
	dbquery("DELETE FROM `gifts_user` WHERE `id` = '$post[id]' LIMIT 1");
	// 消息 
	$_SESSION['message'] = '来自 ' . $anketa['nick'] . ' 已删除';
	header("Location: index.php");
	exit;
}

//网页标题
$set['title'] = '给 ' . $ank['nick'] . ' 的礼物：' . htmlspecialchars($gift['name']);
include_once '../../sys/inc/thead.php';
title();
aut();

/*
==================================
用户礼品提现
==================================
*/
echo '<div class="foot">';
echo '<img src="../../style/icons/str2.gif" alt="*" /> ' . user::nick($ank['id'], 1, 0, 0) . '</a> | <a href="index.php?id=' . $ank['id'] . '">礼物</a> | <b>' . htmlspecialchars($gift['name']) . '</b>';
echo '</div>';
// 礼物
echo '<div class="nav2">';
echo '<img src="../../files/gift/' . $gift['id'] . '.png" style="max-width:' . $width . 'px;" alt="*" /><br />';
echo htmlspecialchars($gift['name']) . ' :: ' . vremja($post['time']) . '<br />';
echo '</div>';
// 礼物的作者
echo '<div class="nav1">';
echo user::nick($anketa['id'],1,1,0);
if ($post['coment']) echo '评论: <br />' . output_text($post['coment']);
echo '</div>';
if ($ank['id'] == $user['id']) {
	echo '<div class="nav2">';
	if ($post['status'] == 0) {
		// 新礼物 - 行动
		echo '<center><img src="../../style/icons/ok.gif" alt="*" /> <a href="?id=' . $post['id'] . '&amp;ok">接受</a> ';
		echo '<img src="../../style/icons/delete.gif" alt="*" /> <a href="?id=' . $post['id'] . '&amp;no">拒绝</a></center>';
	} else {
		echo '<img src="../../style/icons/delete.gif" alt="*" /> <a href="?id=' . $post['id'] . '&amp;delete">删除</a>';
	}
	echo '</div>';
}
echo '<div class="foot">';
echo '<img src="../../style/icons/str2.gif" alt="*" /> ' . user::nick($ank['id'], 1, 0, 0) . '</a> | <a href="index.php?id=' . $ank['id'] . '">礼物</a> | <b>' . htmlspecialchars($gift['name']) . '</b>';
echo '</div>';
include_once '../../sys/inc/tfoot.php';
