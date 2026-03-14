<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
$show_all = true;
include_once '../sys/inc/user.php';
only_unreg();

if ((isset($_POST['nick']) && isset($_POST['pass'])) || (isset($_GET['id']) && isset($_GET['pass']))) {
	// 选择了“记住我”
	if ((isset($_POST['aut_save']) && $_POST['aut_save']) || (isset($_GET['aut_save']) && $_GET['aut_save'])) {
		$expiration = time() + 60 * 60 * 24 * 365;
	} else {
		$expiration = time() + 3600 * 24;
	}
	if (isset($_POST['nick']) && isset($_POST['pass'])) {
		$authManagerLoginResult = $authManager->login($_POST['nick'], $_POST['pass'], $expiration, 'nick');
	} else {
		$authManagerLoginResult = $authManager->login($_GET['id'], $_GET['pass'], $expiration, 'id');
	}
	if ($authManagerLoginResult['status']) {
		$_SESSION['id_user'] = $authManagerLoginResult['data']['user_id'];
		$_SESSION['login_id'] = $authManagerLoginResult['data']['login_id'];
		setcookie('auth_token', $authManagerLoginResult['data']['token'], $expiration, '/');

		$user = user::get_user($authManagerLoginResult['data']['user_id']);
	} else {
		$_SESSION['err'] = '用户名或密码不正确';
	}
} else {
	$_SESSION['err'] = '授权错误';
}

// 检查用户是否登录失败
if (!isset($user)) {
	header('Location: aut.php');
	exit;
}

// 难以理解的会话
dbquery("UPDATE `user_log` SET `sess` = '{$sess}' WHERE `id` = '{$authManagerLoginResult['data']['user_id']}' LIMIT 1");

// 浏览器类型
dbquery("UPDATE `user_log` SET `browser` = '" . ($webbrowser == true ? "web" : "wap") . "' WHERE `id` = '{$authManagerLoginResult['data']['user_id']}' LIMIT 1");

// 检查相似的昵称
// 一定时间范围内检查是否有多个用户在相同的IP、相同的用户代理和相似的登录时间（10分钟内）之间产生了碰撞，如果有碰撞，则将这两个用户的信息记录在 user_collision 表中
$collision_q = dbquery("SELECT * FROM `user_log` WHERE `last_online` > '" . date("Y-m-d H:i:s", (time() - 600)) . "' AND `ip` = '$ip' AND `ua` = '" . my_esc($ua) . "' AND `id_user` <> '$user[id]'");
while ($collision = dbassoc($collision_q)) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user_collision` WHERE (`id_user` = '$user[id]' AND `id_user2` = '$collision[id_user]') OR (`id_user2` = '$user[id]' AND `id_user` = '$collision[id_user]')"), 0) == 0) {
		dbquery("INSERT INTO `user_collision` (`id_user`, `id_user2`, `type`) values('$user[id]', '$collision[id_user]', 'ip_ua_time')");
	}
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
	$_SESSION['message'] = "祝贺你！你的活动是值得的 {$col}% 评级!"; 
	// 活动柜台余额计算
	$col = $user['rating_tmp'] - ($col * 1000); 
	// 重新设定
	dbquery("update `user` set `rating_tmp` = '{$col}' where `id` = '{$user['id']}' limit 1");
}
if (isset($_GET['return'])) {
	header('Location: ' . urldecode($_GET['return']));
} else {
	header('Location: umenu.php');
}