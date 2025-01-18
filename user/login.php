<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/shif.php';
$show_all=true; // 给大家看
$input_page=true;
include_once '../sys/inc/user.php';
only_unreg();

// 检查用户是否成功登录
if (isset($_GET['id']) && isset($_GET['pass'])) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '" . intval($_GET['id']) . "' AND `pass` = '" . shif($_GET['pass']) . "' LIMIT 1"), 0) == 1) {
		$user = user::get_user($_GET['id']);
		$_SESSION['id_user'] = $user['id'];
		dbquery("UPDATE `user` SET `date_aut` = ".time()." WHERE `id` = '$user[id]' LIMIT 1");
		dbquery("UPDATE `user` SET `date_last` = ".time()." WHERE `id` = '$user[id]' LIMIT 1");
		dbquery("INSERT INTO `user_log` (`id_user`, `time`, `ua`, `ip`, `method`) values('$user[id]', '$time', '$user[ua]' , '$user[ip]', '0')");
	} else {
		$_SESSION['err'] = '用户名或密码不正确';
	}
} elseif (isset($_POST['nick']) && isset($_POST['pass'])) {	// 检查用户是否已经提交登录表单
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '" . my_esc($_POST['nick']) . "' AND `pass` = '" . shif($_GET['pass']) . "' LIMIT 1"), 0)) {
		$user = dbassoc(dbquery("SELECT `id` FROM `user` WHERE `nick` = '" . my_esc($_POST['nick']) . "' AND `pass` = '" . shif($_GET['pass']) . "' LIMIT 1"));
		$_SESSION['id_user'] = $user['id'];
		$user = user::get_user($user['id']);
		// 在COOKIE中保存数据
		if (isset($_POST['aut_save']) && $_POST['aut_save']) {
			setcookie('id_user', $user['id'], time()+60*60*24*365);
			setcookie('pass', cookie_encrypt($_POST['pass'],$user['id']), time()+60*60*24*365);
		}
		dbquery("UPDATE `user` SET `date_aut` = '$time', `date_last` = '$time' WHERE `id` = '$user[id]' LIMIT 1");
		dbquery("INSERT INTO `user_log` (`id_user`, `time`, `ua`, `ip`, `method`) values('{$user['id']}', '{$time}', '{$user['ua']}' , '{$user['ip']}', '1')");
	} else {
		$_SESSION['err'] = '用户名或密码不正确';
	}
} elseif (isset($_COOKIE['id_user']) && isset($_COOKIE['pass']) && $_COOKIE['id_user'] && $_COOKIE['pass']) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = " . intval($_COOKIE['id_user']) . " AND `pass` = '" . shif(cookie_decrypt($_COOKIE['pass'], intval($_COOKIE['id_user']))) . "' LIMIT 1"), 0) == 1) {
		$user = user::get_user($_COOKIE['id_user']);
		$_SESSION['id_user'] = $user['id'];
		dbquery("UPDATE `user` SET `date_aut` = '$time', `date_last` = '$time' WHERE `id` = '$user[id]' LIMIT 1");
		$user['type_input'] = 'cookie';
	} else {
		$_SESSION['err'] = 'COOKIE授权错误';
		setcookie('id_user');
		setcookie('pass');
	}
} else {
	$_SESSION['err'] = '授权错误';
}

// 检查用户是否已经登录
if (!isset($user)) {
	header('Location: /user/aut.php');
	exit;
}

// 记录用户的 ip
dbquery("UPDATE `user` SET `ip` = '{$ip}' WHERE `id` = '$user[id]' LIMIT 1");

// 记录用户的 ua
if ($ua) dbquery("UPDATE `user` SET `ua` = '".my_esc($ua)."' WHERE `id` = '$user[id]' LIMIT 1");

// Непонятная сессия
dbquery("UPDATE `user` SET `sess` = '$sess' WHERE `id` = '$user[id]' LIMIT 1");
// 浏览器类型
dbquery("UPDATE `user` SET `browser` = '" . ($webbrowser == true ? "wap" : "web") . "' WHERE `id` = '$user[id]' LIMIT 1");
// 检查相似的昵称
$collision_q = dbquery("SELECT * FROM `user` WHERE `ip` = '$iplong' AND `ua` = '".my_esc($ua)."' AND `date_last` > '".(time()-600)."' AND `id` <> '$user[id]'");
while ($collision = dbassoc($collision_q)) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user_collision` WHERE `id_user` = '$user[id]' AND `id_user2` = '$collision[id]' OR `id_user2` = '$user[id]' AND `id_user` = '$collision[id]'"), 0) == 0)
	dbquery("INSERT INTO `user_collision` (`id_user`, `id_user2`, `type`) values('$user[id]', '$collision[id]', 'ip_ua_time')");
}

/*
========================================
等级: 0
========================================
*/
if (isset($user) && $user['rating_tmp'] > 1000) {
	// 活动柜台
	$col = $user['rating_tmp']; 
	// 百分比除以百分比
	$col = $col / 1000; 
	// 四舍五入
	$col = intval($col); 
	// 添加% 级别
	dbquery("update `user` set `rating` = '" . ($user['rating'] + $col) . "' where `id` = '$user[id]' limit 1");
	// 通知
	$_SESSION['message'] = "祝贺你！你的活动是值得的 $col% 评级!"; 
	// 活动柜台余额计算
	$col = $user['rating_tmp'] - ($col * 1000); 
	// 重新设定
	dbquery("update `user` set `rating_tmp` = '$col' where `id` = '$user[id]' limit 1");
}
if (isset($_GET['return']))
header('Location: '.urldecode($_GET['return']));
else header("Location: /user/umenu.php?".SID);
exit;
