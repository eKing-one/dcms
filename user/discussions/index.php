<?
/*
=======================================
讨论Dcms-社会
作者：探索者
---------------------------------------
此脚本在许可证下分发
的Dcms-社会引擎。 
使用时，指定指向
官方网站http://dcms-social.ru
---------------------------------------
联系人
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
include_once '../../sys/inc/adm_check.php';
include_once '../../sys/inc/user.php';
only_reg();

$my = null;
$frend = null;
$all = null;

if (isset($_GET['read']) && $_GET['read'] == 'all') {
	if (isset($user)) {
		dbquery("UPDATE `discussions` SET `count` = '0' WHERE `id_user` = '$user[id]'");
		$_SESSION['message'] = '未读列表已清除';
		header("Location: ?");
		exit;
	}
}

if (isset($_GET['delete']) && $_GET['delete'] == 'all') {
	if (isset($user)) {
		dbquery("DELETE FROM `discussions` WHERE `id_user` = '$user[id]'");
		$_SESSION['message'] = '讨论名单已清零';
		header("Location: ?");
		exit;
	}
}

//------------------------like к статусу-------------------------//
if (isset($_GET['likestatus'])) {
	$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '" . intval($_GET['likestatus']) . "' LIMIT 1"));
	$ank = get_user(intval($_GET['likestatus']));

	if (
		isset($user) && $user['id'] != $ank['id'] &&
		dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"), 0) == 0
	) {
		dbquery("INSERT INTO `status_like` (`id_user`, `time`, `id_status`) values('$user[id]', '$time', '$status[id]')");

		$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $user['id'] . "' AND `i` = '1'");

		while ($f = dbarray($q)) {
			$a = get_user($f['frend']);
			dbquery("INSERT INTO `tape` (`id_user`,`ot_kogo`,  `avtor`, `type`, `time`, `id_file`) 
			values('$a[id]', '$user[id]', '$status[id_user]', 'status_like', '$time', '$status[id]')");
		}

		header("Location: ?page=" . intval($_GET['page']));
		exit;
	}
}

if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions`  WHERE `id_user` = '$user[id]' AND `count` > '0' AND `avtor` = '$user[id]'"), 0) > 0)
	$count_my = " <img src='/style/icons/tochka.png' alt='*'/>";
else
	$count_my = null;

if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions`  WHERE `id_user` = '$user[id]' AND `count` > '0' AND `avtor` <> '$user[id]'"), 0) > 0)
	$count_f = " <img src='/style/icons/tochka.png' alt='*'/>";
else
	$count_f = null;

$set['title'] = '讨论';
include_once '../../sys/inc/thead.php';
title();
err();
aut();


if (isset($_GET['order']) && $_GET['order'] == 'my') {
	$order = "AND `avtor` = '$user[id]'";
	$sort = "order=my&amp;";
	$my = 'activ';
} else if (isset($_GET['order']) && $_GET['order'] == 'frends') {
	$order = "AND `avtor` != '$user[id]'";
	$sort = "order=frends&amp;";
	$frend = 'activ';
} else {
	$order = null;
	$sort = null;
	$all = 'activ';
}

// Уведомления
$k_notif = dbresult(dbquery("SELECT COUNT(`read`) FROM `notification` WHERE `id_user` = '$user[id]' AND `read` = '0'"), 0);

if ($k_notif > 0) $k_notif = '<font color=red>(' . $k_notif . ')</font>';
else $k_notif = null;

// Обсуждения
$discuss = dbresult(dbquery("SELECT COUNT(`count`) FROM `discussions` WHERE `id_user` = '$user[id]' AND `count` > '0' "), 0);

if ($discuss > 0) $discuss = '<font color=red>(' . $discuss . ')</font>';
else $discuss = null;

// Лента
$lenta = dbresult(dbquery("SELECT COUNT(`read`) FROM `tape` WHERE `id_user` = '$user[id]' AND `read` = '0' "), 0);

if ($lenta > 0) $lenta = '<font color=red>(' . $lenta . ')</font>';
else $lenta = null;

?>
<div id="comments" class="menus">
	<div class="webmenu">
		<a href="/user/tape/">录音带<?= $lenta ?></a>
	</div>
	<div class="webmenu">
		<a href="/user/discussions/" class="activ">讨论 <?= $discuss ?></a>
	</div>
	<div class="webmenu">
		<a href="/user/notification/">通知书 ?> <?= $k_notif ?></a>
	</div>
</div>

<div class="foot">
	排序:
	<a href="?"> 全部</a> |
	<a href="?order=my"> 我的 <?= $count_my ?> </a> |
	<a href="?order=frends"> 朋友 <?= $count_f ?> </a>
</div>
<?
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `discussions`  WHERE `id_user` = '$user[id]' $order"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];

$q = dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$user[id]' $order ORDER BY `time` DESC LIMIT $start, $set[p_str]");

if ($k_post == 0) {
?>
	<div class="mess">
		没有新的讨论
	</div>
<?
}

while ($post = dbassoc($q)) {
	$type = $post['type'];
	$avtor = user::get_user($post['avtor']);

	if ($post['count'] > 0) {
		$s1 = '<font color="red">';
		$s2 = '</font>';
	} else {
		$s1 = null;
		$s2 = null;
	}

	// Подгружаем типы обсуждений
	$d = opendir('inc/');

	while ($dname = readdir($d)) {
		if ($dname != '.' && $dname != '..') {
			include 'inc/' . $dname;
		}
	}
}

// 输出页数
if ($k_page > 1) str('?' . $sort, $k_page, $page);

?>
<div class='foot'>
	<a href='?read=all'><img src='/style/icons/ok.gif'> 将所有内容标记为已读</a>
</div>
<div class='foot'>
	<a href='?delete=all'><img src='/style/icons/delete.gif'> 删除所有讨论</a> | <a href='settings.php'>设置</a>
</div>
<?
include_once '../../sys/inc/tfoot.php';
?>