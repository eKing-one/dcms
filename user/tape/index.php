<?php
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/adm_check.php';
include_once '../../sys/inc/user.php';

$my = null;
$frend = null;
$all = null;

only_reg();

/* 状态点赞 */

if (isset($_GET['likestatus'])) {

	// 用户状态
	$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '" . intval($_GET['likestatus']) . "' LIMIT 1"));
	$ank = user::get_user($status['id_user']);
	if ($user['id'] != $ank['id'] && dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"), 0) == 0) {
		dbquery("INSERT INTO `status_like` (`id_user`, `time`, `id_status`) values('$user[id]', '$time', '$status[id]')");
		/*
		===================================
		乐队
		===================================
		*/

		$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $user['id'] . "' AND `i` = '1'");

		while ($f = dbarray($q)) {
			$a = user::get_user($f['frend']);

			$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '" . $a['id'] . "' LIMIT 1")); // Общая настройка ленты
			if ($a['id'] != $ank['id'] && $f['lenta_status_like'] == 1 && $lentaSet['lenta_status_like'] == 1)
				dbquery("INSERT INTO `tape` (`id_user`,`ot_kogo`,  `avtor`, `type`, `time`, `id_file`) values('$a[id]', '$user[id]', '$status[id_user]', 'status_like', '$time', '$status[id]')");
		}

		header("Location: ?page=" . intval($_GET['page']));
		exit;
	}
}


$set['title'] = '信息中心';
include_once '../../sys/inc/thead.php';

/*
===============================
清除未读列表
===============================
*/
if (isset($_GET['read']) && $_GET['read'] == 'all') {
	if (isset($user)) {
		dbquery("UPDATE `tape` SET `read` = '1' WHERE `id_user` = '$user[id]'");
		$_SESSION['message'] = '已读全部';
		header("Location: ?page=" . intval($_GET['page']) . "");
		exit;
	}
}


/*
===============================
清理全部消息
===============================
*/
if (isset($_GET['delete']) && $_GET['delete'] == 'all') {
	if (isset($user)) {
		dbquery("DELETE FROM `tape` WHERE `id_user` = '$user[id]'");
		$_SESSION['message'] = ' 清理成功 ';
		header("Location: ?");
		exit;
	}
}
title();
err();
aut();

$k_notif = dbresult(dbquery("SELECT COUNT(`read`) FROM `notification` WHERE `id_user` = '$user[id]' AND `read` = '0'"), 0); // 通知

if ($k_notif > 0) {
	$k_notif = '<font color=red>(' . $k_notif . ')</font>';
} else {
	$k_notif = null;
}

$discuss = dbresult(dbquery("SELECT COUNT(`count`) FROM `discussions` WHERE `id_user` = '$user[id]' AND `count` > '0' "), 0); // 讨论

if ($discuss > 0) {
	$discuss = '<font color=red>(' . $discuss . ')</font>';
} else {
	$discuss = null;
}

$lenta = dbresult(dbquery("SELECT COUNT(`read`) FROM `tape` WHERE `id_user` = '$user[id]' AND `read` = '0' "), 0); // 乐队

if ($lenta > 0) {
	$lenta = '<font color=red>(' . $lenta . ')</font>';
} else {
	$lenta = null;
}

echo "<div id='comments' class='menus'>";
echo "<div class='webmenu'>";
echo "<a href='/user/tape/' class='activ'>信息中心 {$lenta}</a>";
echo "</div>";
echo "<div class='webmenu'>";
echo "<a href='/user/discussions/' >讨论  {$discuss}</a>";
echo "</div>";
echo "<div class='webmenu'>";
echo "<a href='/user/notification/'> 关于我的 {$k_notif}</a>";
echo "</div>";
echo "</div>";


$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `tape`  WHERE `id_user` = '$user[id]' "), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];



echo '<div class="foot">';
echo '<a href="?page=' . $page . '&amp;read=all"><img src="/style/icons/ok.gif"> 一键已读</a>';
echo '</div>';


$q = dbquery("SELECT * FROM `tape` WHERE `id_user` = '$user[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");

if ($k_post == 0) {
	echo "  <div class='mess'>";
	echo "没有新消息";
	echo "  </div>";
}

while ($post = dbassoc($q)) {
	$type = $post['type'];
	$avtor = user::get_user($post['avtor']);
	$name = null;

	if ($post['read'] == 0) {
		$s1 = "<font color='red'>";
		$s2 = "</font>";
		dbquery("UPDATE `tape` SET `read` = '1' WHERE `id` = '$post[id]'");
	} else {
		$s1 = null;
		$s2 = null;
	}

	/*
	===============================
	将消息标记为已读
	===============================
	*/



	$d = opendir('inc/');

	while ($dname = readdir($d)) {
		if ($dname != '.' && $dname != '..') {
			include 'inc/' . $dname;
		}
	}

	echo '</div>';
}

if ($k_page > 1) str('?', $k_page, $page);


echo '<div class="foot">';
echo '<a href="?page=' . $page . '&amp;delete=all"><img src="/style/icons/delete.gif"> 清除所有消息</a>';
echo '</div>';

echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($user['id'], 1, 0, 0) . '</a> | ';
echo '<b>消息</b>';
echo '</div>';

include_once '../../sys/inc/tfoot.php';
