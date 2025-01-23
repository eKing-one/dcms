<?php
include_once 'sys/inc/start.php';
include_once 'sys/inc/compress.php';
include_once 'sys/inc/sess.php';
include_once 'sys/inc/home.php';
include_once 'sys/inc/settings.php';
include_once 'sys/inc/db_connect.php';
include_once 'sys/inc/ipua.php';
include_once 'sys/inc/fnc.php';
include_once 'sys/inc/user.php';
include_once 'sys/inc/thead.php';
title();
aut();
if (isset($_SESSION['captcha']) && isset($_POST['chislo'])) {
	if ($_SESSION['captcha'] == $_POST['chislo']) {
		msg('验证通过');
	} else {
		$err = '验证码错误';
	}
}
err();
?>

当前设备类型为：<?php echo $webbrowser ? 'PC' : 'NoPC'; ?><br>
当前设备UA为：<?php echo $_SERVER['HTTP_USER_AGENT']; ?><br>
当前设备IP为：<?php echo $ip; ?><br>
<form method='post'>验证码测试：<img src='/captcha.php' alt='验证码图像' /><br /><input name='chislo' type='text' /><br/><input type='submit' value='继续' /></form>

<?php
include_once 'sys/inc/tfoot.php';
