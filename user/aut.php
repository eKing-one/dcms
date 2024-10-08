<?php
// Dcms-Social
// http://dcms-social.ru
// Искатель

include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
$show_all = true; //为大家展示
include_once '../sys/inc/user.php';
only_unreg();

if (isset($_GET['pass']) && $_GET['pass'] = 'ok') {
	$_SESSION['message'] = '密码已通过电子邮件发送给您';
}

if ($set['guest_select'] == '1') {
	$_SESSION['message'] = "只有授权用户才能访问该网站";
}

$set['title'] = '登录账号';
include_once '../sys/inc/thead.php';
title();
aut();


if ((!isset($_SESSION['refer']) || $_SESSION['refer'] == NULL) && isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL && !preg_match('#mail\.php#', $_SERVER['HTTP_REFERER'])) {
	$_SESSION['refer'] = str_replace('&', '&amp;', preg_replace('#^http://[^/]*/#', '/', $_SERVER['HTTP_REFERER']));
}
?>

<form class="mess" method="post" action="/user/login.php">
	账号:<br />
	<input type="text" name="login" maxlength="32" /><br />
	密码:<br />
	<input type="password" name="pass" maxlength="32" /><br />
	<label><input type="checkbox" name="aut_save" value="1" />记住我</label><br />
	<input type="submit" value="登录" />
</form>
<div class="foot">尚未登记？ <br />
	<a href="/user/reg.php">注册账号</a><br />
</div>
<div class="foot">忘记密码？<br />
	<a href="/user/pass.php">密码恢复</a><br />
</div>

<?php include_once '../sys/inc/tfoot.php'; ?>