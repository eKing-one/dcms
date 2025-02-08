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
only_reg();

if (setget('exit', 1) == 1) {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST['confirm_yes'])) {
			setcookie('auth_token', '', time() - 3600, '/');
			session_destroy();
			header('Location: /?' . session_id());
			exit();
		} else {
			header('Location: ' . $_POST['return']);
			exit();
		}
	}
} else {
	setcookie('auth_token', '', time() - 3600, '/');
	session_destroy();
	header('Location: /?' . session_id());
	exit();
}

$set['title']='退出登录';
include_once '../sys/inc/thead.php';
title();
aut();

echo '<form  method="post">
你确定退出登录吗?
	<input type="hidden" name="return" value="' . $_SERVER['HTTP_REFERER'] . '">
	<input type="submit" name="confirm_yes" value="是的,我确定">
	<input type="submit" name="confirm_no" value="不是,我手滑了">
</form>';

include_once '../sys/inc/tfoot.php';
