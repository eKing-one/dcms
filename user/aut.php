<?php
require_once '../sys/inc/start.php';
require_once '../sys/inc/compress.php';
require_once '../sys/inc/sess.php';
require_once '../sys/inc/home.php';
require_once '../sys/inc/settings.php';
require_once '../sys/inc/db_connect.php';
require_once '../sys/inc/ipua.php';
require_once '../sys/inc/fnc.php';
$show_all = true; //所有人可见
require_once '../sys/inc/user.php';
only_unreg();

if (isset($_GET['pass']) && $_GET['pass'] = 'ok') {
	$_SESSION['message'] = '密码已通过电子邮件发送给您';
}

if ($set['guest_select'] == '1') {
	$_SESSION['message'] = "只有授权用户才能访问该网站";
}

$set['title'] = '登录账号';
require_once '../sys/inc/thead.php';
title();
aut();


if ((!isset($_SESSION['refer']) || $_SESSION['refer'] == NULL) && isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL && !preg_match('#mail\.php#', $_SERVER['HTTP_REFERER'])) {
	$_SESSION['refer'] = str_replace('&', '&amp;', preg_replace('#^http://[^/]*/#', '/', $_SERVER['HTTP_REFERER']));
}
?>

<form class="mess" method="post" action="/user/login.php">
	用户名:<br />
	<input type="text" name="nick" maxlength="32" /><br />
	密码:<br />
	<input type="password" name="pass" maxlength="32" /><br />
	<label><input type="checkbox" name="aut_save" value="1" />保存cookie</label><br />
	<input type="submit" value="登录" />
</form>
<div class="foot">尚未注册？ <br />
	<a href="/user/reg.php">注册账号</a><br />
</div>
<div class="foot">忘记密码？<br />
	<a href="/user/pass.php">密码恢复</a><br />
</div>

<?php require_once '../sys/inc/tfoot.php';