<?php
require_once '../sys/inc/start.php';
require_once '../sys/inc/compress.php';
require_once '../sys/inc/sess.php';
require_once '../sys/inc/home.php';
require_once '../sys/inc/settings.php';
require_once '../sys/inc/db_connect.php';
require_once '../sys/inc/ipua.php';
require_once '../sys/inc/fnc.php';
require_once '../sys/inc/user.php';
only_reg();

if (setget('exit', 1) == 1) {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST['confirm_yes'])) {
			$authManager->logout($user['login_id']);
			setcookie('auth_token', '', time() - 3600, '/');
			session_destroy();
			header('Location: ..');
			exit();
		} else {
			header('Location: ' . $_POST['return']);
			exit();
		}
	}
} else {
	setcookie('auth_token', '', time() - 3600, '/');
	session_destroy();
	header('Location: ..');
	exit();
}

$set['title']='退出登录';
require_once '../sys/inc/thead.php';
title();
aut();

?>
<form  method="post">
你确定退出登录吗?
	<input type="hidden" name="return" value="<?php echo $_SERVER['HTTP_REFERER']; ?>"><br />
	<input type="submit" name="confirm_yes" value="是的,我确定">
	<input type="submit" name="confirm_no" value="不是,我手滑了">
</form>

<?php require_once '../sys/inc/tfoot.php';
